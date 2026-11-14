<?php

require __DIR__ . '/../vendor/autoload.php';

use Fpdf\FPDF;  // Corregir el espacio de nombres aquí

class Reportes extends Controllers
{
    public $views;
    public $model;

    public function __construct()
    {
        parent::__construct();
        $this->esAdmin();
    }

    public function compras()
    {
        $data['title'] = "Reporte compras";
        $this->views->getView($this, "compras", $data);
    }

    public function rventas()
    {
        $data['title'] = "Reporte venta";
        $this->views->getView($this, "rventas", $data);
    }

    /*public function reporteArqueoActual()
    {
        $fechaInicio = date('Y-m-d 00:00:01'); // Desde las 00:00:01 del día actual
        $fechaFin = date('Y-m-d H:i:s');  // Hora actual
        $ventas = $this->model->getVentasArqueoActual($fechaInicio, $fechaFin);

        if (empty($ventas)) {
            die("No se encontraron ventas para el rango de fechas especificado.");
        }

        $pdf = new FPDF('P', 'mm', 'Letter'); // 'P' = Portrait (vertical), 'Letter' = Tamaño carta
        $pdf->AddPage();
        

        // Mapeo manual de días y meses en español
        $dia = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
        $mes = ['', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

        // Obtener la fecha actual
        $fecha = getdate();

        // Formatear la fecha como "Viernes 14 de Marzo de 2025 23:45:00"
        $fecha_formateada = ucfirst($dia[$fecha['wday']]) . ' ' . $fecha['mday'] . ' de ' . $mes[$fecha['mon']] . ' de ' . $fecha['year'] . ' ' . date('H:i:s');
        // Inserta la imagen en la esquina superior izquierda (x = 10, y = 10) con tamaño automático
        $pdf->Image('C:/xampp/htdocs/pollo/Assets/images/opacidadPollo.png', 10, 10, 195.9, 259.4);



        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'Reporte de Arqueo Actual', 0, 1, 'C');
        $pdf->Ln(5);

        // Establecer la fuente para mostrar la fecha
        $pdf->SetFont('Arial', '', 12);

        // Mostrar la fecha en el PDF
        $pdf->Cell(20); // Esto mueve la posición actual 20 unidades a la derecha

        $pdf->Cell(0, 10, 'Fecha emitidos ' . $fecha_formateada, 0, 1, 'L');

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(20); // Esto mueve la posición actual 20 unidades a la derecha
        $pdf->Cell(30, 10, 'Cantidad', 1, 0, 'C');
        $pdf->Cell(60, 10, 'Menu', 1, 0, 'C');
        $pdf->Cell(30, 10, 'Precio', 1, 0, 'C');
        $pdf->Cell(30, 10, 'Subtotal', 1, 1, 'C');

        $pdf->SetFont('Arial', '', 12);
        $totalGeneral = 0;
        foreach ($ventas as $venta) {
            $cantidad = isset($venta['cantidad_total']) ? $venta['cantidad_total'] : '';
            $menu = isset($venta['menu']) ? $venta['menu'] : 'N/A';
            $precio = isset($venta['precio_unitario']) ? number_format($venta['precio_unitario'], 2) . ' Bs.' : '';
            $subtotal = isset($venta['subtotal']) ? number_format($venta['subtotal'], 2) . ' Bs.' : '';
            $pdf->Cell(20); // Esto mueve la posición actual 20 unidades a la derecha
            $pdf->Cell(30, 10, $cantidad, 1, 0, 'C');
            $pdf->Cell(60, 10, $menu, 1, 0, 'L');
            $pdf->Cell(30, 10, $precio, 1, 0, 'C');
            $pdf->Cell(30, 10, $subtotal, 1, 1, 'C');


            // Sumar al total general solo si no es la fila TOTAL
            if ($menu !== 'TOTAL') {
                // Solo sumar valores numéricos (evitar texto)
                $totalGeneral += floatval($venta['subtotal']);
            }
        }

        // Ahora manejar el total final directamente
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(20); // Esto mueve la posición actual 20 unidades a la derecha
        $pdf->Cell(120, 10, 'TOTAL', 1, 0, 'R');
        // Si la fila 'TOTAL' ya existe en la consulta, simplemente mostrar el total calculado en la consulta SQL
        // El total general no se debe sumar en el PDF si ya se ha incluido en la consulta SQL.
        $pdf->Cell(30, 10, number_format($totalGeneral, 2) . ' Bs.', 1, 1, 'C');

        $pdf->Output('I', 'reporte_arqueo.pdf');
    }*/

    public function reporteArqueoActual()
    {
        $fechaInicio = date('Y-m-d 00:00:01');
        $fechaFin = date('Y-m-d H:i:s');
        $ventas = $this->model->getVentasArqueoActual($fechaInicio, $fechaFin);

        if (empty($ventas)) {
            die("No se encontraron ventas para el rango de fechas especificado.");
        }

        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();

        $dia = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
        $mes = ['', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        $fecha = getdate();
        $fecha_formateada = ucfirst($dia[$fecha['wday']]) . ' ' . $fecha['mday'] . ' de ' . $mes[$fecha['mon']] . ' de ' . $fecha['year'] . ' ' . date('H:i:s');
        $pdf->Image('C:/xampp/htdocs/pollo/Assets/images/opacidadPollo.png', 10, 10, 195.9, 259.4);

        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'Reporte de Arqueo Actual', 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(20);
        $pdf->Cell(0, 10, 'Fecha emitido: ' . $fecha_formateada, 0, 1, 'L');

        $bebidas = ['coca', 'fanta', 'sprite', 'del valle', 'vaso', 'jarra', 'jugo', 'refresco', 'agua'];
        $combos = ['combo'];
        $menus = [];
        $refrescos = [];
        $otros = [];
        $descuento = null;
        $totalVenta = null;

        foreach ($ventas as $venta) {
            $nombre = strtolower($venta['menu']);
            if ($nombre === 'descuento') {
                $descuento = $venta;
            } elseif ($nombre === 'total') {
                $totalVenta = $venta;
            } elseif (array_filter($bebidas, fn($b) => strpos($nombre, $b) !== false)) {
                $refrescos[] = $venta;
            } elseif (array_filter($combos, fn($c) => strpos($nombre, $c) !== false)) {
                $otros[] = $venta;
            } else {
                $menus[] = $venta;
            }
        }

        function imprimirTabla($pdf, $titulo, $items)
        {
            $pdf->Ln(8);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, $titulo, 0, 1, 'L');
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(20);
            $pdf->Cell(30, 8, 'Cantidad', 1, 0, 'C');
            $pdf->Cell(60, 8, 'Producto', 1, 0, 'C');
            $pdf->Cell(30, 8, 'Precio', 1, 0, 'C');
            $pdf->Cell(30, 8, 'Subtotal', 1, 1, 'C');
            $pdf->SetFont('Arial', '', 11);
            $total = 0;
            foreach ($items as $item) {
                $pdf->Cell(20);
                $pdf->Cell(30, 8, $item['cantidad_total'], 1, 0, 'C');
                $pdf->Cell(60, 8, $item['menu'], 1, 0, 'L');
                $pdf->Cell(30, 8, number_format($item['precio_unitario'], 2) . ' Bs.', 1, 0, 'C');
                $pdf->Cell(30, 8, number_format($item['subtotal'], 2) . ' Bs.', 1, 1, 'C');
                $total += floatval($item['subtotal']);
            }
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(20);
            $pdf->Cell(120, 8, 'TOTAL ' . strtoupper($titulo), 1, 0, 'R');
            $pdf->Cell(30, 8, number_format($total, 2) . ' Bs.', 1, 1, 'C');
        }

        imprimirTabla($pdf, 'MENUS', $menus);
        imprimirTabla($pdf, 'REFRESCOS', $refrescos);
        if (!empty($otros)) imprimirTabla($pdf, 'COMBOS U OTROS', $otros);

        // Descuento
        if ($descuento) {
            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(20);
            $pdf->Cell(120, 8, 'DESCUENTO', 1, 0, 'R');
            $pdf->Cell(30, 8, number_format($descuento['subtotal'], 2) . ' Bs.', 1, 1, 'C');
        }

        // Total general
        if ($totalVenta) {
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(20);
            $pdf->Cell(120, 10, 'TOTAL GENERAL', 1, 0, 'R');
            $pdf->Cell(30, 10, number_format($totalVenta['subtotal'], 2) . ' Bs.', 1, 1, 'C');
        }

        $pdf->Output('I', 'reporte_arqueo.pdf');
    }

    public function reporteArqueoMensual()
    {

        $fechaInicio = $_POST['fechaInicio'] . ' 00:00:00';
        $fechaFin = $_POST['fechaFin'] . ' 23:59:59';

        $ventas = $this->model->getVentasArqueoMensual($fechaInicio, $fechaFin);

        if (empty($ventas)) {
            die("No se encontraron ventas para el rango de fechas especificado.");
        }

        $pdf = new FPDF('P', 'mm', 'Letter');
        $pdf->AddPage();

        $dia = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
        $mes = ['', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        $fecha = getdate();
        $fecha_formateada = ucfirst($dia[$fecha['wday']]) . ' ' . $fecha['mday'] . ' de ' . $mes[$fecha['mon']] . ' de ' . $fecha['year'] . ' ' . date('H:i:s');
        $pdf->Image('C:/xampp/htdocs/pollo/Assets/images/opacidadPollo.png', 10, 10, 195.9, 259.4);

        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'Reporte de Arqueo del ' . $_POST['fechaInicio'] . ' al ' . $_POST['fechaFin'], 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(20);
        $pdf->Cell(0, 10, 'Fecha emitido: ' . $fecha_formateada, 0, 1, 'L');

        $bebidas = ['coca', 'fanta', 'sprite', 'del valle', 'vaso', 'jarra', 'jugo', 'refresco', 'agua'];
        $combos = ['combo'];
        $menus = [];
        $refrescos = [];
        $otros = [];
        $descuento = null;
        $totalVenta = null;

        foreach ($ventas as $venta) {
            $nombre = strtolower($venta['menu']);
            if ($nombre === 'descuento') {
                $descuento = $venta;
            } elseif ($nombre === 'total') {
                $totalVenta = $venta;
            } elseif (array_filter($bebidas, fn($b) => strpos($nombre, $b) !== false)) {
                $refrescos[] = $venta;
            } elseif (array_filter($combos, fn($c) => strpos($nombre, $c) !== false)) {
                $otros[] = $venta;
            } else {
                $menus[] = $venta;
            }
        }

        function imprimirTablaMensual($pdf, $titulo, $items)
        {
            $pdf->Ln(8);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, $titulo, 0, 1, 'L');
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(20);
            $pdf->Cell(30, 8, 'Cantidad', 1, 0, 'C');
            $pdf->Cell(60, 8, 'Producto', 1, 0, 'C');
            $pdf->Cell(30, 8, 'Precio', 1, 0, 'C');
            $pdf->Cell(30, 8, 'Subtotal', 1, 1, 'C');
            $pdf->SetFont('Arial', '', 11);
            $total = 0;
            foreach ($items as $item) {
                $pdf->Cell(20);
                $pdf->Cell(30, 8, $item['cantidad_total'], 1, 0, 'C');
                $pdf->Cell(60, 8, $item['menu'], 1, 0, 'L');
                $pdf->Cell(30, 8, number_format($item['precio_unitario'], 2) . ' Bs.', 1, 0, 'C');
                $pdf->Cell(30, 8, number_format($item['subtotal'], 2) . ' Bs.', 1, 1, 'C');
                $total += floatval($item['subtotal']);
            }
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(20);
            $pdf->Cell(120, 8, 'TOTAL ' . strtoupper($titulo), 1, 0, 'R');
            $pdf->Cell(30, 8, number_format($total, 2) . ' Bs.', 1, 1, 'C');
        }

        imprimirTablaMensual($pdf, 'MENUS', $menus);
        imprimirTablaMensual($pdf, 'REFRESCOS', $refrescos);
        if (!empty($otros)) imprimirTablaMensual($pdf, 'COMBOS U OTROS', $otros);

        // Descuento
        if ($descuento) {
            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(20);
            $pdf->Cell(120, 8, 'DESCUENTO', 1, 0, 'R');
            $pdf->Cell(30, 8, number_format($descuento['subtotal'], 2) . ' Bs.', 1, 1, 'C');
        }

        // Total general
        if ($totalVenta) {
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(20);
            $pdf->Cell(120, 10, 'TOTAL GENERAL', 1, 0, 'R');
            $pdf->Cell(30, 10, number_format($totalVenta['subtotal'], 2) . ' Bs.', 1, 1, 'C');
        }

        $pdf->Output('I', 'reporte_arqueo.pdf');
    }
}
