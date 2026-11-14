<?php
// Asegúrate de que la ruta de autoload.php sea correcta
require __DIR__ . '/../vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class Ventas extends Controllers
{
	public $views;
	public $model;
	public function __construct()
	{

		parent::__construct();
		
	}

	public function ventas()
	{
		require_once __DIR__ . '/../Models/CajaModel.php';

		$usuarioId = $_SESSION['idUsuario']['id'];
		$cajaModel = new CajaModel();
		$cajaAbierta = $cajaModel->obtenerCajaAbierta($usuarioId);

		if (!$cajaAbierta) {
			header('location: ' . base_url() . '/caja?aviso=caja_cerrada');
			die();
		}

		$data['title'] = "Registrar Venta";
		$data['page_functions_js'] = "functions_ventas.js";
		$this->views->getView($this, "ventas", $data);
	}

	// Productos activos y cantidad de mesas, para armar la pantalla Vender
	public function getCatalogo()
	{
		echo json_encode([
			'productos' => $this->model->selectCatalogo(),
			'mesas' => $this->model->numeroMesas(),
		], JSON_UNESCAPED_UNICODE);
		die();
	}

	// Registra la venta completa en una sola petición.
	// El navegador solo dice QUÉ se pidió y CÓMO se paga: precios, total y cambio los calcula el servidor.
	public function registrarVentaCompleta()
	{
		require_once __DIR__ . '/../Models/CajaModel.php';

		$data = json_decode(file_get_contents("php://input"), true);
		if (!is_array($data)) {
			$this->responderVenta(false, 'No llegaron los datos de la venta.');
		}

		// Usuario y caja salen de la sesión, no de lo que mande el navegador
		$usuarioId = (int) $_SESSION['idUsuario']['id'];
		$cajaAbierta = (new CajaModel())->obtenerCajaAbierta($usuarioId);
		if (!$cajaAbierta) {
			$this->responderVenta(false, 'Debes abrir caja antes de registrar ventas.', ['requiere_caja' => true]);
		}

		// Productos: se suman las cantidades si un producto llega repetido
		$cantidades = [];
		foreach (($data['productos'] ?? []) as $item) {
			$idMenu = (int) ($item['menu_id'] ?? 0);
			$cantidad = (int) ($item['cantidad'] ?? 0);
			if ($idMenu <= 0 || $cantidad <= 0) {
				$this->responderVenta(false, 'Hay un producto con cantidad no válida.');
			}
			$cantidades[$idMenu] = ($cantidades[$idMenu] ?? 0) + $cantidad;
		}
		if (empty($cantidades)) {
			$this->responderVenta(false, 'Agrega al menos un producto.');
		}

		// Precios desde la base: lo que diga el navegador sobre precios no se usa
		$productos = [];
		foreach ($this->model->selectProductosPorIds(array_keys($cantidades)) as $fila) {
			$productos[(int) $fila['id_menu']] = $fila;
		}
		$lineas = [];
		$subtotal = 0;
		foreach ($cantidades as $idMenu => $cantidad) {
			if (!isset($productos[$idMenu])) {
				$this->responderVenta(false, 'Uno de los productos ya no está disponible. Recarga la página (F5).');
			}
			if ($cantidad > 200) {
				$this->responderVenta(false, 'La cantidad de "' . $productos[$idMenu]['nombre_menu'] . '" es demasiado alta.');
			}
			$precio = (float) $productos[$idMenu]['precio_menu'];
			$importe = round($precio * $cantidad, 2);
			$lineas[] = ['menu_id' => $idMenu, 'cantidad' => $cantidad, 'precio_unitario' => $precio, 'subtotal' => $importe];
			$subtotal += $importe;
		}
		$subtotal = round($subtotal, 2);

		// Descuento opcional, en porcentaje
		$descuento = round((float) ($data['descuento'] ?? 0), 2);
		if ($descuento < 0 || $descuento > 100) {
			$this->responderVenta(false, 'El descuento debe estar entre 0 y 100 %.');
		}
		$total = round($subtotal * (1 - $descuento / 100), 2);
		if ($total <= 0) {
			$this->responderVenta(false, 'El total de la venta no puede ser 0.');
		}

		// Tipo de pedido: es opcional. Si no se indica, la venta queda sin mesa ni "para llevar"
		$tipoPedido = null;
		$mesa = null;
		if (($data['tipo_pedido'] ?? '') === 'llevar') {
			$tipoPedido = 'llevar';
		} elseif (($data['tipo_pedido'] ?? '') === 'mesa') {
			$tipoPedido = 'mesa';
			$mesa = (int) ($data['mesa'] ?? 0);
			if ($mesa < 1 || $mesa > $this->model->numeroMesas()) {
				$this->responderVenta(false, 'Ese número de mesa no existe.');
			}
		}
		$nota = mb_substr(trim((string) ($data['nota'] ?? '')), 0, 120);

		// Forma de pago
		$metodo = $data['metodo_pago'] ?? 'efectivo';
		$recibido = round((float) ($data['efectivo_recibido'] ?? 0), 2);
		if ($metodo === 'qr') {
			$montoQr = $total;
			$montoEfectivo = 0;
			$recibido = 0;
		} elseif ($metodo === 'mixto') {
			$montoQr = round((float) ($data['monto_qr'] ?? 0), 2);
			if ($montoQr <= 0 || $montoQr >= $total) {
				$this->responderVenta(false, 'En pago mixto, el monto por QR debe ser mayor que 0 y menor que el total.');
			}
			$montoEfectivo = round($total - $montoQr, 2);
		} else {
			$metodo = 'efectivo';
			$montoQr = 0;
			$montoEfectivo = $total;
		}
		if ($recibido < $montoEfectivo) {
			$this->responderVenta(false, 'El efectivo recibido no alcanza: faltan ' . number_format($montoEfectivo - $recibido, 2) . ' Bs.');
		}
		$cambio = round($recibido - $montoEfectivo, 2);

		try {
			$guardado = $this->model->guardarVentaCompleta([
				'usuario_id' => $usuarioId,
				'caja_id' => (int) $cajaAbierta['id_caja'],
				'total' => $total,
				'descuento' => $descuento,
				'tipo_pedido' => $tipoPedido,
				'mesa' => $mesa,
				'nota_pedido' => $nota !== '' ? $nota : null,
				'metodo_pago' => $metodo,
				'monto_efectivo' => $montoEfectivo,
				'monto_qr' => $montoQr,
				'efectivo_recibido' => $recibido,
				'cambio' => $cambio,
			], $lineas);
		} catch (\Throwable $e) {
			error_log('No se pudo guardar la venta: ' . $e->getMessage());
			$this->responderVenta(false, 'No se pudo guardar la venta. No se cobró nada: inténtalo de nuevo.');
		}

		// La impresión va después de guardar: si la impresora falla, la venta ya quedó registrada
		$impreso = true;
		if ((int) ($data['tickets'] ?? 1) === 2) {
			$impreso = $this->imprimirTicket($guardado['id_ticket']) && $impreso;
		}
		$impreso = $this->imprimirTicketDoble($guardado['id_ticket']) && $impreso;

		$this->responderVenta(true, 'Venta registrada.', [
			'id_venta' => $guardado['id_venta'],
			'numero_pedido' => $guardado['numero_pedido'],
			'subtotal' => $subtotal,
			'total' => $total,
			'cambio' => $cambio,
			'impreso' => $impreso,
		]);
	}

	private function responderVenta(bool $status, string $msg, array $extra = [])
	{
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode(array_merge(['status' => $status, 'msg' => $msg], $extra), JSON_UNESCAPED_UNICODE);
		die();
	}

	// Ticket del cliente. Devuelve true si se pudo imprimir

	private function imprimirTicket($ticket_id)
	{
		set_error_handler(function ($severity, $message, $file, $line) {
			throw new \ErrorException($message, 0, $severity, $file, $line);
		});

		try {
			$ticket_details = $this->model->getTicketById($ticket_id);

			if ($ticket_details) {
				$printer = new WindowsPrintConnector("GA-C80250 Series");
				$escpos = new Printer($printer);

				// Encabezado
				$escpos->feedReverse(6); // Mueve el papel hacia arriba 3 unidades

				$escpos->setTextSize(2, 2);
				$escpos->setJustification(Printer::JUSTIFY_CENTER); //Centrar el texto
				$escpos->setEmphasis(true);
				$escpos->text("CHICKEN LINDO A LA LEÑA" . "\n");
				$escpos->setTextSize(1, 1);

				$escpos->text("Pedidos: 68461425" . "\n");
				$escpos->setTextSize(1, 1);


				$escpos->setEmphasis(false); // Desactivar negrilla

				// Fecha y Cliente
				$escpos->setTextSize(1, 1);

				$escpos->setJustification(Printer::JUSTIFY_LEFT); //Alinear a la izquierda (por defecto)
				$escpos->text($ticket_details[0]['fecha_venta'] . "\n\n");

				// Tabla de productos
				$escpos->setJustification(Printer::JUSTIFY_RIGHT);	//Alinear a la derecha
				$escpos->text("Cant.    Descripción       Precio      Subtotal\n");
				$escpos->text("-----------------------------------------------\n");
				foreach ($ticket_details as $item) {
					$escpos->text(
						str_pad($item['cantidad'], 3) . "   " .
						str_pad($item['nombre_menu'], 18) . "   " .
						str_pad($item['precio_unitario'] ?? $item['precio_menu'], 5) . "        " .
						str_pad($item['subtotal'], 5) . "\n"
					);
				}

				// Total, efectivo y cambio

				$escpos->setJustification(Printer::JUSTIFY_RIGHT);	//Alinear a la derecha
				if ((float) $ticket_details[0]['descuento'] > 0) {
					$escpos->text("Descuento: " . rtrim(rtrim(number_format($ticket_details[0]['descuento'], 2), "0"), ".") . " %\n");
				}
				$escpos->setEmphasis(true);
				$escpos->text("MONTO A PAGAR: " . number_format($ticket_details[0]['total'], 2) . "\n");
				$escpos->setEmphasis(false);

				// Pedido y Usuario
				$escpos->setJustification(Printer::JUSTIFY_CENTER); //Centrar el texto
				$escpos->setTextSize(2, 2);
				$escpos->text("TICKET: " . "#" . $ticket_details[0]['numero_pedido'] . "\n");
				$escpos->setTextSize(1, 1);
				$escpos->text("Shinahota frente a la terminal, Av. Panamaricana" . "\n");

				// Finalizar
				$escpos->text("  ¡Gracias por su compra!  \n");

				$escpos->cut();
				$escpos->close();
				return true;
			} else {
				error_log('No se encontró el ticket ' . $ticket_id . ' para imprimir.');
			}
		} catch (\Throwable $e) {
			error_log('No se pudo imprimir el ticket ' . $ticket_id . ': ' . $e->getMessage());
		} finally {
			restore_error_handler();
		}
		return false;
	}

	// Primera línea de la comanda: mesa o para llevar. Vacío si el pedido no lo indica
	private function destinoPedido(array $ticket)
	{
		if ($ticket['tipo_pedido'] === 'llevar') {
			return 'PARA LLEVAR';
		}
		if ($ticket['tipo_pedido'] === 'mesa' && !empty($ticket['mesa'])) {
			return 'MESA ' . $ticket['mesa'];
		}
		// Ventas antiguas: se anotaba un nombre en lugar de la mesa
		return !empty($ticket['nombre_cliente']) ? 'PARA: ' . $ticket['nombre_cliente'] : '';
	}

	// Comanda de cocina. Devuelve true si se pudo imprimir
	private function imprimirTicketDoble($ticket_id)
	{
		set_error_handler(function ($severity, $message, $file, $line) {
			throw new \ErrorException($message, 0, $severity, $file, $line);
		});

		try {
			$ticket_details = $this->model->getTicketById($ticket_id);

			if ($ticket_details) {
				$printer = new WindowsPrintConnector("GA-C80250 Series");
				$escpos = new Printer($printer);

				$escpos->setJustification(Printer::JUSTIFY_CENTER); //Centrar el texto
				$escpos->setEmphasis(true);
				$destino = $this->destinoPedido($ticket_details[0]);
				if ($destino !== '') {
					$escpos->text($destino . "\n");
				}
				if (!empty($ticket_details[0]['nota_pedido'])) {
					$escpos->text("NOTA: " . $ticket_details[0]['nota_pedido'] . "\n");
				}
				$escpos->text("\n");
				$escpos->setTextSize(1, 1);

				// Tabla de productos
				$escpos->setTextSize(1, 1);

				foreach ($ticket_details as $item) {
					$escpos->text(
						str_pad($item['cantidad'], 3) . "   " .
						str_pad($item['nombre_menu'], 18) . "              " .
						str_pad($item['subtotal'], 5) . "\n"
					);
				}

				// Total, efectivo y cambio
				$escpos->setJustification(Printer::JUSTIFY_RIGHT);	//Alinear a la derecha
				$escpos->setEmphasis(true);
				$escpos->setEmphasis(false);

				// ticket y Usuario
				$escpos->setJustification(Printer::JUSTIFY_CENTER); //Centrar el texto
				$escpos->setTextSize(2, 2);
				$escpos->text("TICKET: " . $ticket_details[0]['numero_pedido'] . "\n");

				$escpos->cut();
				$escpos->close();
				return true;
			} else {
				error_log('No se encontró el ticket ' . $ticket_id . ' para imprimir.');
			}
		} catch (\Throwable $e) {
			error_log('No se pudo imprimir el ticket ' . $ticket_id . ': ' . $e->getMessage());
		} finally {
			restore_error_handler();
		}
		return false;
	}
}





