<?php
class Clientes extends Controllers
{
    public $model;

    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['login'])) {
            header('location: ' . base_url());
        }
    }

    public function getClientes()
    {
        $arrData = $this->model->getClientes();
        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function setCliente()
    {
        $json = file_get_contents('php://input');
        error_log("Datos recibidos: " . $json); // Solo en logs, no imprime en pantalla

        $data = json_decode($json, true);
        if ($data && isset($data['nombre'])) {
            $nombre = trim($data['nombre']);
           

         

            // 🔹 Importante: Verificar que `getClienteByNombre()` NO imprima nada
            $cliente = $this->model->getClienteByNombre($nombre);

            if ($cliente && isset($cliente[0]['id_cliente'])) {
                $arrResponse = ['status' => true, 'id_cliente' => $cliente[0]['id_cliente']];
            } else {
                // Si el cliente no existe, se registra
                $id_cliente = $this->model->insertarCliente($nombre);
                if ($id_cliente > 0) {
                    $arrResponse = ['status' => true, 'id_cliente' => $id_cliente];
                } else {
                    $arrResponse = ['status' => false, 'msg' => 'Error al registrar el cliente'];
                }
            }

            // 🔹 Asegurarse de no imprimir nada más que el JSON
            header('Content-Type: application/json');
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            die();
        } else {
            echo json_encode(['status' => false, 'msg' => 'No se recibieron datos válidos.'], JSON_UNESCAPED_UNICODE);
            die();
        }
    }
}
