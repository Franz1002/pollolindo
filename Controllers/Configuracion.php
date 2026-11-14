<?php
// Ajustes del negocio. Solo el administrador puede verlos y cambiarlos
class Configuracion extends Controllers
{
    public $views;
    public $model;

    // Tope de mesas: evita que un error de tipeo llene la pantalla Vender de botones
    const MAX_MESAS = 60;

    public function __construct()
    {
        parent::__construct();
        $this->esAdmin();
    }

    public function configuracion()
    {
        $data['title'] = "Configuración";
        $data['page_functions_js'] = "functions_configuracion.js";
        $data['numero_mesas'] = (int) $this->model->leerValor('numero_mesas', '15');
        $data['max_mesas'] = self::MAX_MESAS;
        $this->views->getView($this, "configuracion", $data);
    }

    public function guardarConfiguracion()
    {
        header('Content-Type: application/json; charset=utf-8');

        $texto = trim((string) ($_POST['numero_mesas'] ?? ''));
        // Solo dígitos: no se aceptan decimales, negativos ni letras
        if (!ctype_digit($texto) || (int) $texto < 1 || (int) $texto > self::MAX_MESAS) {
            echo json_encode(['status' => false, 'msg' => 'La cantidad de mesas debe ser un número entre 1 y ' . self::MAX_MESAS . '.'], JSON_UNESCAPED_UNICODE);
            die();
        }

        $this->model->guardarValor('numero_mesas', (string) (int) $texto);
        echo json_encode(['status' => true, 'msg' => 'Configuración guardada. Vender ya muestra ' . (int) $texto . ' mesas.'], JSON_UNESCAPED_UNICODE);
        die();
    }
}
