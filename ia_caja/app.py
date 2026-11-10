from flask import Flask, request, jsonify
from sklearn.ensemble import IsolationForest
import numpy as np
import joblib
import os

app = Flask(__name__)
MODEL_PATH = os.path.join(os.path.dirname(__file__), "modelo_caja.pkl")

FEATURES = [
    "diferencia_relativa",
    "cantidad_tickets",
    "ticket_promedio",
    "num_descuentos",
    "duracion_turno_horas",
    "hora_cierre",
]

def vectorizar(registro):
    return [float(registro.get(f, 0)) for f in FEATURES]

@app.route("/salud", methods=["GET"])
def salud():
    return jsonify({"status": True, "modelo_entrenado": os.path.exists(MODEL_PATH)})

@app.route("/entrenar", methods=["POST"])
def entrenar():
    data = request.get_json(force=True)
    historico = data.get("historico", [])

    if len(historico) < 15:
        return jsonify({
            "status": False,
            "msg": f"Se necesitan al menos 15 cierres para entrenar (hay {len(historico)})."
        }), 400

    X = np.array([vectorizar(r) for r in historico])
    modelo = IsolationForest(
        n_estimators=150,
        contamination=0.08, 
        random_state=42
    )
    modelo.fit(X)
    joblib.dump(modelo, MODEL_PATH)

    return jsonify({"status": True, "msg": f"Modelo entrenado con {len(historico)} cierres.", "registros": len(historico)})

@app.route("/evaluar", methods=["POST"])
def evaluar():
    if not os.path.exists(MODEL_PATH):
        return jsonify({"status": False, "msg": "El modelo aún no fue entrenado."}), 400

    registro = request.get_json(force=True)
    modelo = joblib.load(MODEL_PATH)
    x = np.array([vectorizar(registro)])

    prediccion = modelo.predict(x)[0]              # -1 = anómalo, 1 = normal
    score_bruto = modelo.decision_function(x)[0]    # más alto = más "normal"
    score_anomalia = round(max(0, min(1, 0.5 - score_bruto)), 4)

    return jsonify({
        "status": True,
        "es_anomalia": bool(prediccion == -1),
        "score_anomalia": score_anomalia
    })

if __name__ == "__main__":
    app.run(host="127.0.0.1", port=5001)