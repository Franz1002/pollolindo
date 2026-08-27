<?php
class Compras extends Controllers
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

	public function compras()
	{
		$this->esAdmin();
		$data['title'] = "Registrar Compra";
		$data['page_functions_js'] = "functions_compras.js";
		$this->views->getView($this, "compras", $data);
	}
	public function verCompras()
	{
		$this->esAdmin();
		$data['title'] = "Lista de Compra";
		$data['page_functions_js'] = "functions_verCompras.js";
		$this->views->getView($this, "verCompras", $data);
	}
	public function getCompras()
	{
		$this->esAdmin();
		$arrData = $this->model->selectCompras();
		echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
		die();
	}

	public function getInsumosActivos()
	{
		$this->esAdmin();
		$arrData = $this->model->selectInsumosActivos();
		echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
		die();
	}
public function registrarCompra()
{
    $this->esAdmin();
    if ($_POST) {
        $insumoId = intval($_POST['insumo_id']);
        $cantidad = floatval($_POST['cantidad']);
        $totalCompra = floatval($_POST['costo_total']);
        $fecha = $_POST['fecha_compra'];
        $usuarioId = $_SESSION['idUsuario']['id'];

        if ($insumoId > 0 && $cantidad > 0 && $totalCompra > 0) {
            $ok = $this->model->insertCompra($insumoId, $cantidad, $fecha, $totalCompra, $usuarioId);
            $arrResponse = $ok
                ? array('status' => true, 'msg' => 'Compra registrada y stock actualizado correctamente.')
                : array('status' => false, 'msg' => 'Hubo un error al registrar la compra.');
        } else {
            $arrResponse = array('status' => false, 'msg' => 'Todos los campos son obligatorios.');
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
    }
    die();
}






}
