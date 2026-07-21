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
        $this->views->getView($this, "inicio", $data);
    }
}
