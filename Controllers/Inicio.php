<?php
class Inicio extends Controllers
{
    public $views;
    public $model;

    public function __construct()
    {
        parent::__construct();
    }

    public function inicio()
    {
        // El panel muestra las ventas del día: solo lo ve el admin. El cajero va directo a vender
        if (empty($_SESSION['idUsuario']['tipo']) || $_SESSION['idUsuario']['tipo'] != 1) {
            header('Location: ' . base_url() . '/ventas');
            exit;
        }

        $data['title'] = "Inicio";

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