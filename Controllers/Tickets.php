<?php
class Tickets extends Controllers
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

	public function tickets()
	{
		//	if (!empty($_SESSION['userData']['tipo'] == 1)) {
		$data['title'] = "Tickets";
		$data['page_functions_js'] = "functions_tickets.js";
		$this->views->getView($this, "tickets", $data);
		//} else {
		//	header('location: ' . base_url());
		//	}
	}

	public function getTickets()
	{


		$arrData = $this->model->selectTickets();
		for ($i = 0; $i < count($arrData); $i++) {

			if ($arrData[$i]['descuento'] == null) {
				$arrData[$i]['descuento'] = '<span class="badge badge-outline-success">Sin descuento</span>';
			} else {
			}
			$arrData[$i]['ticket'] = '<span class="badge badge-outline-danger">Descargar Ticket</span>';
		}

		// Envía los datos como JSON
		echo json_encode($arrData, JSON_UNESCAPED_UNICODE);

		die();
	}
}
