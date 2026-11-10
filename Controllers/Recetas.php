<?php
class Recetas extends Controllers
{
    public $views;
    public $model;

    public function __construct()
    {
        parent::__construct();
     
    }

    public function recetas()
    {
        $this->esAdmin();
        $data['title'] = "Recetas";
        $data['page_functions_js'] = "functions_recetas.js";
        $this->views->getView($this, "recetas", $data);
    }

    public function getMenusReceta()
    {
        $this->esAdmin();
        $arrData = $this->model->selectMenusConReceta();

        for ($i = 0; $i < count($arrData); $i++) {
            if ($arrData[$i]['total_insumos'] > 0) {
                $arrData[$i]['estado_receta'] = '<span class="badge badge-outline-success">' . $arrData[$i]['total_insumos'] . ' insumo(s)</span>';
            } else {
                $arrData[$i]['estado_receta'] = '<span class="badge badge-outline-danger">Sin receta</span>';
            }
            $nombreEscapado = addslashes($arrData[$i]['nombre_menu']);
            $arrData[$i]['options'] = '<button class="btn btn-outline-success btn-rounded btn-icon" onClick="editarReceta(' . $arrData[$i]['id_menu'] . ', \'' . $nombreEscapado . '\')" title="Editar receta"><i class="mdi mdi-pencil"></i></button>';
        }

        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getInsumos()
    {
        $this->esAdmin();
        $arrData = $this->model->selectInsumos();
        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function getReceta($id_menu)
    {
        $this->esAdmin();
        $idMenu = intval($id_menu);
        $arrData = $this->model->selectRecetaByMenu($idMenu);
        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function guardarReceta()
    {
        $this->esAdmin();
        if ($_POST) {
            $idMenu = intval($_POST['id_menu']);
            $arrInsumos = isset($_POST['insumo_id']) ? $_POST['insumo_id'] : [];
            $arrCantidades = isset($_POST['cantidad']) ? $_POST['cantidad'] : [];

            if ($idMenu > 0) {
                $this->model->guardarReceta($idMenu, $arrInsumos, $arrCantidades);
                $arrResponse = array('status' => true, 'msg' => 'Receta guardada correctamente.');
            } else {
                $arrResponse = array('status' => false, 'msg' => 'Plato no válido.');
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}
