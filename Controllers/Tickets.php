<?php
class Tickets extends Controllers
{
	public $views;
	public $model;

	// Días que se pueden consultar de una vez, para no traer todo el historial
	const MAX_DIAS = 31;

	public function __construct()
	{
		parent::__construct();
	}

	public function tickets()
	{
		$this->esAdmin();
		$data['title'] = "Tickets";
		$data['page_functions_js'] = "functions_tickets.js";
		$data['hoy'] = date('Y-m-d');
		$this->views->getView($this, "tickets", $data);
	}

	// Tickets entre dos fechas (?desde=AAAA-MM-DD&hasta=AAAA-MM-DD). Sin fechas, los de hoy
	public function getTickets()
	{
		$this->esAdmin();
		header('Content-Type: application/json; charset=utf-8');

		$desde = $this->leerFecha($_GET['desde'] ?? '') ?? new DateTime('today');
		$hasta = $this->leerFecha($_GET['hasta'] ?? '') ?? clone $desde;
		if ($hasta < $desde) {
			$hasta = clone $desde;
		}
		if ($desde->diff($hasta)->days >= self::MAX_DIAS) {
			echo json_encode(['status' => false, 'msg' => 'Elige un rango de ' . self::MAX_DIAS . ' días como máximo.'], JSON_UNESCAPED_UNICODE);
			die();
		}

		// La consulta va hasta el inicio del día siguiente para incluir todo el día final
		$finExclusivo = (clone $hasta)->modify('+1 day');
		$tickets = $this->model->selectTickets($desde->format('Y-m-d 00:00:00'), $finExclusivo->format('Y-m-d 00:00:00'));

		echo json_encode(['status' => true, 'data' => $tickets], JSON_UNESCAPED_UNICODE);
		die();
	}

	// Devuelve la fecha solo si viene bien escrita como AAAA-MM-DD
	private function leerFecha($texto)
	{
		$fecha = DateTime::createFromFormat('!Y-m-d', (string) $texto);
		return ($fecha && $fecha->format('Y-m-d') === $texto) ? $fecha : null;
	}
}
