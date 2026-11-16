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

		$desde = $this->leerFecha($_GET['desde'] ?? '') ?? new DateTime('today');
		$hasta = $this->leerFecha($_GET['hasta'] ?? '') ?? clone $desde;
		if ($hasta < $desde) {
			$hasta = clone $desde;
		}
		if ($desde->diff($hasta)->days >= self::MAX_DIAS) {
			$this->responder(false, 'Elige un rango de ' . self::MAX_DIAS . ' días como máximo.');
		}

		// La consulta va hasta el inicio del día siguiente para incluir todo el día final
		$finExclusivo = (clone $hasta)->modify('+1 day');
		$tickets = $this->model->selectTickets($desde->format('Y-m-d 00:00:00'), $finExclusivo->format('Y-m-d 00:00:00'));

		$this->responder(true, '', ['data' => $tickets]);
	}

	// Anula una venta. Solo el administrador, con motivo obligatorio y mientras la caja
	// en la que se cobró siga abierta. La venta no se borra: queda marcada como anulada.
	public function anularVenta()
	{
		$this->esAdmin();

		$idTicket = (int) ($_POST['id_ticket'] ?? 0);
		$motivo = trim((string) ($_POST['motivo'] ?? ''));

		if ($idTicket <= 0) {
			$this->responder(false, 'No se indicó qué venta anular.');
		}
		// Se cuentan letras, no caracteres: un punto o unos espacios no valen como motivo
		if (preg_match_all('/\p{L}/u', $motivo) < 5) {
			$this->responder(false, 'Escribe el motivo de la anulación (al menos 5 letras).');
		}
		if (mb_strlen($motivo) > 200) {
			$this->responder(false, 'El motivo no puede pasar de 200 caracteres.');
		}

		try {
			// Quién anula sale de la sesión, no de lo que mande el navegador
			$resultado = $this->model->anularVentaDeTicket($idTicket, (int) $_SESSION['idUsuario']['id'], $motivo);
		} catch (\Throwable $e) {
			error_log('No se pudo anular la venta del ticket ' . $idTicket . ': ' . $e->getMessage());
			$this->responder(false, 'No se pudo anular la venta. Inténtalo de nuevo.');
		}

		if (!$resultado['ok']) {
			$this->responder(false, $resultado['msg']);
		}

		// Qué hay que devolverle al cliente, según cómo pagó
		$efectivo = (float) $resultado['monto_efectivo'];
		$qr = (float) $resultado['monto_qr'];
		$devolver = [];
		if ($efectivo > 0) {
			$devolver[] = number_format($efectivo, 2) . ' Bs en efectivo';
		}
		if ($qr > 0) {
			$devolver[] = number_format($qr, 2) . ' Bs por QR';
		}
		$msg = 'Pedido #' . $resultado['numero_pedido'] . ' anulado.';
		if ($devolver) {
			$msg .= ' Devuelve al cliente ' . implode(' y ', $devolver) . '.';
		}

		$this->responder(true, $msg, ['devolver_efectivo' => $efectivo, 'devolver_qr' => $qr]);
	}

	// Devuelve la fecha solo si viene bien escrita como AAAA-MM-DD
	private function leerFecha($texto)
	{
		$fecha = DateTime::createFromFormat('!Y-m-d', (string) $texto);
		return ($fecha && $fecha->format('Y-m-d') === $texto) ? $fecha : null;
	}

	private function responder(bool $status, string $msg, array $extra = [])
	{
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode(array_merge(['status' => $status, 'msg' => $msg], $extra), JSON_UNESCAPED_UNICODE);
		die();
	}
}
