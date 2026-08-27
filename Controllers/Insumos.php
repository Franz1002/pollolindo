<?php
class Insumos extends Controllers
{
    public $views;
    public $model;

    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['login'])) {
            header('location: ' . base_url());
        }
    }

    public function insumos()
    {
        $this->esAdmin();
        $data['title'] = "Insumos";
        $data['page_functions_js'] = "functions_insumos.js";
        $this->views->getView($this, "insumos", $data);
    }

    public function getInsumos()
    {
        $this->esAdmin();
        $arrData = $this->model->selectInsumos();

        for ($i = 0; $i < count($arrData); $i++) {
            $arrData[$i]['estado_texto'] = $arrData[$i]['estado'] == 1
                ? '<span class="badge badge-outline-success">Activo</span>'
                : '<span class="badge badge-outline-danger">Inactivo</span>';
            $arrData[$i]['options'] = '<button class="btn btn-outline-info btn-rounded btn-icon" onClick="editInsumo(' . $arrData[$i]['id_insumo'] . ')" title="Editar"><i class="mdi mdi-pencil"></i></button> '
                . '<button class="btn btn-outline-danger btn-rounded btn-icon" onClick="eliminarInsumo(' . $arrData[$i]['id_insumo'] . ')" title="Desactivar"><i class="mdi mdi-close-circle"></i></button>';
        }

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getInsumo($id_insumo)
    {
        $this->esAdmin();
        $arrData = $this->model->selectInsumo(intval($id_insumo));
        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function registrarInsumo()
    {
        $this->esAdmin();
        if ($_POST) {
            $nombre = trim($_POST['nombre_insumo']);
            $tipo = $_POST['tipo_insumo'];
            $unidad = trim($_POST['unidad_medida']);

            if ($nombre != "") {
                $this->model->registrarInsumo($nombre, $tipo, $unidad);
                $arrResponse = array('status' => true, 'msg' => 'Insumo registrado correctamente.');
            } else {
                $arrResponse = array('status' => false, 'msg' => 'El nombre es obligatorio.');
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function actualizarInsumo()
    {
        $this->esAdmin();
        if ($_POST) {
            $id = intval($_POST['id_insumo']);
            $nombre = trim($_POST['nombre_insumo']);
            $tipo = $_POST['tipo_insumo'];
            $unidad = trim($_POST['unidad_medida']);
            $estado = intval($_POST['estado']);

            $this->model->actualizarInsumo($id, $nombre, $tipo, $unidad, $estado);
            $arrResponse = array('status' => true, 'msg' => 'Insumo actualizado correctamente.');
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    public function eliminarInsumo()
    {
        $this->esAdmin();
        if ($_POST) {
            $id = intval($_POST['id_insumo']);
            $this->model->eliminarInsumo($id);
            $arrResponse = array('status' => true, 'msg' => 'Insumo desactivado correctamente.');
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}
