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
	public function registrarCompra()
	{
		$this->esAdmin();
		// Obtiene el arreglo de compras desde la solicitud POST
		$compras = json_decode($_POST['compras'], true);

		// Variable para rastrear el estado de la solicitud
		$status = false;
		$msg = '';

		// Realiza alguna acción con los datos de compra
		foreach ($compras as $compra) {
			// Accede a los valores individuales de cada compra
			$nombreProductoId = $compra['nombreProductoId'];
			$cantidadCompra = $compra['cantidadCompra'];
			$fechaCompra = $compra['fechaCompra'];
			$totalCompra = $compra['totalCompra'];

			// Realiza la acción deseada, por ejemplo, insertar en la base de datos
			$result = $this->model->insertCompra($nombreProductoId, $cantidadCompra, $fechaCompra, $totalCompra);

			// Verifica el resultado de la inserción
			if ($result >= 0) {
				$status = true;
				$msg = 'Compra(s) registrada(s) exitosamente.';
			} else {
				$status = false;
				$msg = 'Hubo un error al registrar la(s) compra(s).';
				break; // Si ocurre un error, detén el bucle
			}
		}

		// Crea una respuesta JSON para enviar de vuelta al cliente
		$response = ['status' => $status, 'msg' => $msg];
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
		die();
	}
	//dep($compras);die();








}
