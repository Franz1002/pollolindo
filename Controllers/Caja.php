<?php
require_once __DIR__ . '/../Libraries/IA/IAClienteCaja.php';

class Caja extends Controllers
{
    public $views;
    public $model;

    public function __construct()
    {
        parent::__construct();
     
    }

    public function caja()
    {
        $data['title'] = "Caja";
        $data['page_functions_js'] = "functions_caja.js";
        $this->views->getView($this, "caja", $data);
    }

    public function getEstadoCaja()
    {
        $usuarioId = $_SESSION['idUsuario']['id'];
        $cajaAbierta = $this->model->obtenerCajaAbierta($usuarioId);

        if (!$cajaAbierta) {
            echo json_encode(['abierta' => false]);
            die();
        }

        $resumen = $this->model->resumenVentasPeriodo($cajaAbierta['fecha_apertura'], date('Y-m-d H:i:s'));
        $cajaAbierta['abierta'] = true;
        // El esperado es solo efectivo; lo cobrado por QR se informa aparte
        $cajaAbierta['efectivo_esperado_actual'] = $resumen['total_efectivo'];
        $cajaAbierta['ventas_qr_actual'] = $resumen['total_qr'];
        $cajaAbierta['cantidad_tickets_actual'] = $resumen['cantidad_tickets'];

        echo json_encode($cajaAbierta, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function abrirCaja()
    {
        $usuarioId = $_SESSION['idUsuario']['id'];
        if ($this->model->obtenerCajaAbierta($usuarioId)) {
            echo json_encode(['status' => false, 'msg' => 'Ya tienes una caja abierta.']);
            die();
        }
        $this->model->abrirCaja($usuarioId);
        echo json_encode(['status' => true, 'msg' => 'Caja aperturada correctamente.']);
        die();
    }

    public function cerrarCaja()
    {
        if (!$_POST) die();

        $usuarioId = $_SESSION['idUsuario']['id'];
        $efectivoDeclarado = floatval($_POST['efectivo_declarado']);

        $cajaAbierta = $this->model->obtenerCajaAbierta($usuarioId);
        if (!$cajaAbierta) {
            echo json_encode(['status' => false, 'msg' => 'No hay ninguna caja abierta para cerrar.']);
            die();
        }

        $fechaCierre = date('Y-m-d H:i:s');
        $resumen = $this->model->resumenVentasPeriodo($cajaAbierta['fecha_apertura'], $fechaCierre);

        // Se compara efectivo contra efectivo: las ventas por QR no pasan por el cajón
        $efectivoEsperado = floatval($resumen['total_efectivo']);
        $totalVentas = floatval($resumen['total_ventas']);
        $cantidadTickets = intval($resumen['cantidad_tickets']);
        $numDescuentos = intval($resumen['num_descuentos']);
        $diferencia = round($efectivoDeclarado - $efectivoEsperado, 2);
        $duracionHoras = (strtotime($fechaCierre) - strtotime($cajaAbierta['fecha_apertura'])) / 3600;

        $features = [
            'diferencia_relativa'  => $efectivoEsperado > 0 ? round($diferencia / $efectivoEsperado, 4) : 0,
            'cantidad_tickets'     => $cantidadTickets,
            'ticket_promedio'      => $cantidadTickets > 0 ? round($totalVentas / $cantidadTickets, 2) : 0,
            'num_descuentos'       => $numDescuentos,
            'duracion_turno_horas' => round($duracionHoras, 2),
            'hora_cierre'          => intval(date('G', strtotime($fechaCierre))),
        ];

        $ia = new IAClienteCaja();
        $resultadoIA = $ia->evaluarCierre($features);

        if ($resultadoIA !== null && !empty($resultadoIA['status'])) {
            $esAnomalia = $resultadoIA['es_anomalia'];
            $scoreAnomalia = $resultadoIA['score_anomalia'];
            $motivo = $esAnomalia
                ? 'Detectado por el modelo de IA (Isolation Forest) como un cierre con un patrón distinto al habitual.'
                : null;
        } else {
            // El servicio de IA no respondió o el modelo todavía no fue entrenado:
            // aplicamos una regla de respaldo para no dejar el arqueo sin ningún control.
            $esAnomalia = abs($features['diferencia_relativa']) > 0.15;
            $scoreAnomalia = null;
            $motivo = $esAnomalia
                ? 'Diferencia mayor al 15% del efectivo esperado (regla de respaldo; IA no disponible aún).'
                : null;
        }

        $this->model->cerrarCaja(
            $cajaAbierta['id_caja'],
            $efectivoEsperado,
            $efectivoDeclarado,
            $cantidadTickets,
            $numDescuentos,
            $usuarioId,
            $scoreAnomalia,
            $esAnomalia,
            $motivo
        );

        echo json_encode([
            'status' => true,
            'msg' => 'Caja cerrada correctamente.',
            'es_anomalia' => $esAnomalia,
            'diferencia' => $diferencia,
            'motivo' => $motivo
        ], JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getHistorialCaja()
    {
        $this->esAdmin();
        $arrData = $this->model->historialCierres(100);

        for ($i = 0; $i < count($arrData); $i++) {
            $arrData[$i]['estado_texto'] = $arrData[$i]['es_anomalia']
                ? '<span class="badge badge-outline-danger" title="' . htmlspecialchars($arrData[$i]['motivo_anomalia'] ?? '') . '"><i class="mdi mdi-alert"></i> Revisar</span>'
                : '<span class="badge badge-outline-success">Normal</span>';
            $arrData[$i]['responsable'] = trim(($arrData[$i]['nombre'] ?? '') . ' ' . ($arrData[$i]['apellido'] ?? ''));
        }

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function reentrenarModelo()
    {
        $this->esAdmin();
        $historico = $this->model->historicoParaEntrenamiento();

        $registros = [];
        foreach ($historico as $c) {
            $registros[] = [
                'diferencia_relativa'  => $c['efectivo_esperado'] > 0 ? round($c['diferencia'] / $c['efectivo_esperado'], 4) : 0,
                'cantidad_tickets'     => intval($c['cantidad_tickets']),
                'ticket_promedio'      => $c['cantidad_tickets'] > 0 ? round($c['efectivo_esperado'] / $c['cantidad_tickets'], 2) : 0,
                'num_descuentos'       => intval($c['num_descuentos']),
                'duracion_turno_horas' => floatval($c['duracion_turno_horas']),
                'hora_cierre'          => intval($c['hora_cierre']),
            ];
        }

        $ia = new IAClienteCaja();
        $resultado = $ia->entrenarModelo($registros);

        if ($resultado === null) {
            echo json_encode(['status' => false, 'msg' => 'No se pudo contactar al servicio de IA. Verifica que esté corriendo en el puerto 5001.']);
        } else {
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}