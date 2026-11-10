<?php

class Controllers
{
	public $views;
	public $model;

	public function __construct()
	{
		$this->views = new Views();
		$this->loadModel();
	}

	protected function loadModel()
	{
		// Ej.: Ventas -> Models/VentasModel.php
		$model = get_class($this) . "Model";
		$routClass = "Models/" . $model . ".php";
		if (file_exists($routClass)) {
			require_once($routClass);
			$this->model = new $model();
		}
	}

	protected function esAdmin()
	{
		if (empty($_SESSION['idUsuario']['tipo']) || $_SESSION['idUsuario']['tipo'] != 1) {
			header('Location: ' . base_url());
			exit;
		}
	}
}