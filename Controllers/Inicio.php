<?php
// Asegúrate de que la ruta de autoload.php sea correcta
require __DIR__ . '/../vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class Inicio extends Controllers
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

    public function imprimir()
    {
        try {
            // El nombre de la impresora compartida en Windows
            $connector = new WindowsPrintConnector("GA-C80250 Series");

            // Crear una instancia de la impresora
            $printer = new Printer($connector);

            // Imprimir texto
            $printer->text("Hello World!\n");
            $printer->text("ARIEL\n");

            // Cortar el recibo
            $printer->cut();

            // Cerrar la conexión con la impresora
            $printer->close();

            // Mensaje de éxito
            echo "✅ Impresión realizada correctamente.";
        } catch (Exception $e) {
            echo "❌ No se pudo imprimir: " . $e->getMessage() . "\n";
        }
    }


    public function inicio()
    {
        $data['title'] = "Inicio";
        $data['page_functions_js'] = "functions_inicio.js";

        // --- Ventas: se agrega por día lo que el modelo ya entrega por día+usuario ---
        $ventasCrudas = $this->model->selectVentaDiarioGrafico();
        $porDia = [];
        foreach ($ventasCrudas as $fila) {
            $fecha = $fila['FECHA'];
            if (!isset($porDia[$fecha])) {
                $porDia[$fecha] = 0;
            }
            $porDia[$fecha] += (float) $fila['VENTA'];
        }

        $hoy = date('d-M-y');
        $data['ventaHoy'] = $porDia[$hoy] ?? 0;
        $data['ventaMes'] = array_sum($porDia);

        // Últimos 14 días con venta registrada, para la mini gráfica del dashboard
        $ultimos14 = array_slice($porDia, -14, 14, true);
        $data['graficoDias'] = $ultimos14;
        $data['graficoMax'] = !empty($ultimos14) ? max($ultimos14) : 0;

        // --- Insumos: reutiliza las consultas que ya existen en Inventario ---
        require_once("Models/InventarioModel.php");
        $inventarioModel = new InventarioModel();
        $agotados = $inventarioModel->selectAgotados();
        $porAgotar = $inventarioModel->selectPorAgotar();

        $data['totalAgotados'] = count($agotados);
        $data['totalPorAgotar'] = count($porAgotar);
        $data['alertasInsumos'] = array_slice(array_merge($agotados, $porAgotar), 0, 5);

        $this->views->getView($this, "inicio", $data);
    }
}
