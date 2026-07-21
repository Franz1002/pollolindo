<?php
class Inventario extends Controllers
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

    public function inventario()
    {
        //if (!empty($_SESSION['userData']['tipo'] == 1)) {

        $data['title'] = "Inventario";
        $data['page_functions_js'] = "functions_inventario.js";
        $this->views->getView($this, "inventario", $data);
        // } else {
        //    header('location: ' . base_url());
        //  }
    }

    public function agotados()
    {
        $arrData = $this->model->selectAgotados();
        foreach ($arrData as &$data) {
            $data['precio_producto'] = $data['precio_producto'] . ' Bs.';
            $data['cantidad_vendida'] = $data['cantidad_vendida'] . ' Unidades';
            $data['total_ingresos'] = $data['total_ingresos'] . ' Bs.';
        }
        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function porAgotar()
    {
        $arrData = $this->model->selectPorAgotar();
        foreach ($arrData as &$data) {
            $data['cantidad_producto'] = $data['cantidad_producto'] . ' Unidades';
            $data['precio_producto'] = $data['precio_producto'] . ' Bs.';
            $data['cantidad_vendida'] = $data['cantidad_vendida'] . ' Unidades';
            $data['total_ingresos'] = $data['total_ingresos'] . ' Bs.';
        }
        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function disponible()
    {
        $arrData = $this->model->selectDisponible();
        foreach ($arrData as &$data) {
            $data['cantidad_producto'] = $data['cantidad_producto'] . ' Unidades';
            $data['precio_producto'] = $data['precio_producto'] . ' Bs.';
            $data['cantidad_vendida'] = $data['cantidad_vendida'] . ' Unidades';
            $data['total_ingresos'] = $data['total_ingresos'] . ' Bs.';
        }
        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }
    public function masVendidos()
    {
        $arrData = $this->model->selectVendidos();
        foreach ($arrData as &$data) {
            $data['cantidad_producto'] = $data['cantidad_producto'] . ' Unidades';
            $data['precio_producto'] = $data['precio_producto'] . ' Bs.';
            $data['cantidad_vendida'] = $data['cantidad_vendida'] . ' Unidades';
            $data['total_ingresos'] = $data['total_ingresos'] . ' Bs.';
        }
        echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
        die();
    }

}
