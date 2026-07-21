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
		session_start();
		if (empty($_SESSION['login'])) {
			header('location: ' . base_url());
		}
	}

	public function ventas()
	{
		$data['title'] = "Registrar Venta";
		$data['page_functions_js'] = "functions_ventas.js";
		$this->views->getView($this, "ventas", $data);
	}

	public function getMenu()
	{
		echo json_encode($this->model->selectMenu(), JSON_UNESCAPED_UNICODE);
		die();
	}

	public function getBebida()
	{
		echo json_encode($this->model->selectBebida(), JSON_UNESCAPED_UNICODE);
		die();
	}

	public function getCombo()
	{
		echo json_encode($this->model->selectCombo(), JSON_UNESCAPED_UNICODE);
		die();
	}

	public function registrarVenta()
	{
		$data = json_decode(file_get_contents("php://input"), true);

		if (isset($data['usuario_id'], $data['total_venta'])) {
			$intUsuarioId = intval($data['usuario_id']);
			$totalVenta = floatval($data['total_venta']);

			$request_venta = $this->model->insertarVenta($intUsuarioId, $totalVenta);

			// Verificar si la venta se insertó correctamente
			if ($request_venta > 0) {
				$arrResponse = array('status' => true, 'msg' => 'Venta registrada correctamente.', 'id_venta' => $request_venta);
			} else {
				$arrResponse = array('status' => false, 'msg' => 'Error al registrar la venta.');
			}
		} else {
			$arrResponse = array('status' => false, 'msg' => 'Faltan datos necesarios.');
		}

		echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		die();
	}


	public function registrarDetalleVenta()
	{
		// Leer el cuerpo de la solicitud
		$data = json_decode(file_get_contents("php://input"), true); // Decodifica el JSON a un array

		// Verifica si se recibieron los datos correctamente
		if (isset($data['venta_id']) && isset($data['productos'])) {
			$venta_id = $data['venta_id'];
			$productos = $data['productos'];

			// Aquí puedes llamar a la función para registrar el detalle de la venta
			// Si lo necesitas, puedes hacer la inserción en la base de datos
			foreach ($productos as $producto) {
				$menu_id = $producto['menu_id'];
				$cantidad = $producto['cantidad'];
				$subtotal = $producto['subtotal'];

				// Realizar la inserción en la base de datos
				// Aquí llamas a la función que tienes en el modelo
				$this->model->insertarDetalleVenta($venta_id, $menu_id, $cantidad, $subtotal);
			}

			$arrResponse = array('status' => true, 'msg' => 'Detalles de venta registrados correctamente.');
		} else {
			$arrResponse = array('status' => false, 'msg' => 'No se enviaron detalles de venta.');
		}

		// Devolver la respuesta en formato JSON
		echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		die();
	}
	public function obtenerNumeroPedido()
	{
		$request_numero_pedido = $this->model->obtenerNumPedido();
		return $request_numero_pedido ?? 1; // En caso de que no haya registros, devuelve 1
	}

	public function registrarTicketDoble()
	{
		$data = json_decode(file_get_contents("php://input"), true);

		if (
			isset($data['venta_id'], $data['emitido_por'], $data['total'], $data['efectivo_recibido'], $data['cambio']) &&
			is_numeric($data['venta_id']) && is_numeric($data['emitido_por']) &&
			is_numeric($data['total'])
		) {
			$venta_id = (int)$data['venta_id'];
			$emitido_por = (int)$data['emitido_por'];
			$total = (float)$data['total'];
			$efectivo_recibido = (float)$data['efectivo_recibido'];
			$cambio = (float)$data['cambio'];
			$cliente_id = isset($data['cliente_id']) && $data['cliente_id'] > 0 ? (int)$data['cliente_id'] : null;
			$descuento = isset($data['descuento']) ? (float)$data['descuento'] : 0.0;

			$numero_pedido = $this->obtenerNumeroPedido(); // Generar el numero de pedido

			// Registrar el ticket con los valores de efectivo y cambio
			$ticket_id = $this->model->insertarTicket($venta_id, $cliente_id, $numero_pedido, $emitido_por, $descuento, $total, $efectivo_recibido, $cambio);

			if ($ticket_id > 0) {
				// Enviar la respuesta de éxito y proceder a la impresión
				$response = [
					'status' => true,
					'msg' => 'Ticket registrado e impreso correctamente.',
					'ticket_id' => $ticket_id,
					'numero_pedido' => $numero_pedido
				];
				// Proceder con la impresión del ticket, pasando solo el ticket_id
				$this->imprimirTicket($ticket_id);	
				$this->imprimirTicketDoble($ticket_id);
			} else {
				// Enviar la respuesta de error si no se pudo registrar el ticket
				$response = ['status' => false, 'msg' => 'Error al registrar el ticket.'];
			}
		} else {
			// Enviar la respuesta de error si faltan parámetros o son inválidos
			$response = ['status' => false, 'msg' => 'Faltan parámetros o son inválidos.'];
		}

		// Enviar una única respuesta JSON
		echo json_encode($response);
		flush();
	}

	public function registrarTicket()
	{
		$data = json_decode(file_get_contents("php://input"), true);

		if (
			isset($data['venta_id'], $data['emitido_por'], $data['total'], $data['efectivo_recibido'], $data['cambio']) &&
			is_numeric($data['venta_id']) && is_numeric($data['emitido_por']) &&
			is_numeric($data['total'])
		) {
			$venta_id = (int)$data['venta_id'];
			$emitido_por = (int)$data['emitido_por'];
			$total = (float)$data['total'];
			$efectivo_recibido = (float)$data['efectivo_recibido'];
			$cambio = (float)$data['cambio'];
			$cliente_id = isset($data['cliente_id']) && $data['cliente_id'] > 0 ? (int)$data['cliente_id'] : null;
			$descuento = isset($data['descuento']) ? (float)$data['descuento'] : 0.0;

			$numero_pedido = $this->obtenerNumeroPedido(); // Generar el numero de pedido

			// Registrar el ticket con los valores de efectivo y cambio
			$ticket_id = $this->model->insertarTicket($venta_id, $cliente_id, $numero_pedido, $emitido_por, $descuento, $total, $efectivo_recibido, $cambio);

			if ($ticket_id > 0) {
				// Enviar la respuesta de éxito y proceder a la impresión
				$response = [
					'status' => true,
					'msg' => 'Ticket registrado e impreso correctamente.',
					'ticket_id' => $ticket_id,
					'numero_pedido' => $numero_pedido
				];
				// Proceder con la impresión del ticket, pasando solo el ticket_id
				$this->imprimirTicketDoble($ticket_id);
			} else {
				// Enviar la respuesta de error si no se pudo registrar el ticket
				$response = ['status' => false, 'msg' => 'Error al registrar el ticket.'];
			}
		} else {
			// Enviar la respuesta de error si faltan parámetros o son inválidos
			$response = ['status' => false, 'msg' => 'Faltan parámetros o son inválidos.'];
		}

		// Enviar una única respuesta JSON
		echo json_encode($response);
		flush();
	}

	/*public function imprimirTicket($ticket_id)
	{
		// Obtener los detalles del ticket
		try {
			$ticket_details = $this->model->getTicketById($ticket_id);

			if ($ticket_details) {
				// Crear el objeto TCPDF
				$pdf = new TCPDF();
				$pdf->AddPage();

				// Encabezado
				$pdf->SetFont('Helvetica', 'B', 16);
				$pdf->Cell(0, 10, "CHICKEN LINDO A LA LEÑA", 0, 1, 'C');
				$pdf->SetFont('Helvetica', '', 12);
				$pdf->Cell(0, 10, "FECHA DE EMISIÓN: " . $ticket_details[0]['fecha_venta'], 0, 1);
				$pdf->Cell(0, 10, "CLIENTE: " . ($ticket_details[0]['nombre_cliente'] ?? 'N/A'), 0, 1);
				$pdf->Ln(10);

				// Tabla de productos
				$pdf->Cell(20, 10, "Cant.", 1);
				$pdf->Cell(60, 10, "Descripción", 1);
				$pdf->Cell(30, 10, "Precio", 1);
				$pdf->Cell(30, 10, "Subtotal", 1);
				$pdf->Ln();

				foreach ($ticket_details as $item) {
					$pdf->Cell(20, 10, $item['cantidad'], 1);
					$pdf->Cell(60, 10, $item['nombre_menu'], 1);
					$pdf->Cell(30, 10, number_format($item['precio_menu'], 2), 1);
					$pdf->Cell(30, 10, number_format($item['subtotal'], 2), 1);
					$pdf->Ln();
				}

				// Total, efectivo y cambio
				$pdf->Ln(10);
				$pdf->Cell(0, 10, "MONTO A PAGAR: " . number_format($ticket_details[0]['total'], 2), 0, 1, 'R');
				$pdf->Cell(0, 10, "EFECTIVO: " . number_format($ticket_details[0]['efectivo_recibido'], 2), 0, 1, 'R');
				$pdf->Cell(0, 10, "CAMBIO: " . number_format($ticket_details[0]['cambio'], 2), 0, 1, 'R');
				$pdf->Ln(10);

				// Pedido y Usuario
				$pdf->Cell(0, 10, "PEDIDO: #" . $ticket_details[0]['numero_pedido'], 0, 1, 'C');
				$pdf->Cell(0, 10, "EMITIDO POR: " . $ticket_details[0]['usuario_nombre'] . " " . $ticket_details[0]['usuario_apellido'], 0, 1, 'C');
				$pdf->Ln(10);

				// Finalizar
				$pdf->Cell(0, 10, "  ¡Gracias por su compra!  ", 0, 1, 'C');

				// Output the PDF (mostrar en navegador)
				$pdf->Output('I', 'ticket_' . $ticket_id . '.pdf'); // Muestra el PDF en el navegador
				exit();  // Detiene la ejecución del script aquí, evitando que se envíe más contenido HTML
			} else {
				echo json_encode(['status' => false, 'msg' => 'No se encontró el ticket para imprimir.']);
			}
		} catch (Exception $e) {
			echo json_encode(['status' => false, 'msg' => 'Error al generar el PDF: ' . $e->getMessage()]);
		}
	}*/





	// Asegúrate de incluir la librería de EscPos

	public function imprimirTicket($ticket_id)
	{
		// Obtener el número de mesa desde el POST
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
							str_pad($item['precio_menu'], 5) . "        " .
							str_pad($item['subtotal'], 5) . "\n"
					);
				}

				// Total, efectivo y cambio

				$escpos->setJustification(Printer::JUSTIFY_RIGHT);	//Alinear a la derecha
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
			} else {
				echo json_encode(['status' => false, 'msg' => 'No se encontró el ticket para imprimir.']);
			}
		} catch (Exception $e) {
			echo json_encode(['status' => false, 'msg' => 'Error al imprimir el ticket: ' . $e->getMessage()]);
		}
	}

	public function imprimirTicketDoble($ticket_id)
	{
		// Obtener el número de mesa desde el POST
		try {
			$ticket_details = $this->model->getTicketById($ticket_id);

			if ($ticket_details) {
				$printer = new WindowsPrintConnector("GA-C80250 Series");
				$escpos = new Printer($printer);

				$escpos->setJustification(Printer::JUSTIFY_CENTER); //Centrar el texto
				$escpos->setEmphasis(true);
				$escpos->text("PARA: " . ($ticket_details[0]['nombre_cliente'] ?? 'MESA') . "\n\n");
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
			} else {
				echo json_encode(['status' => false, 'msg' => 'No se encontró el ticket para imprimir.']);
			}
		} catch (Exception $e) {
			echo json_encode(['status' => false, 'msg' => 'Error al imprimir el ticket: ' . $e->getMessage()]);
		}
	}

}

