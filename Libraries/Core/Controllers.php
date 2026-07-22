<?php

class Controllers
{
	public function __construct()
	{
		$this->views = new Views();
		$this->loadModel();
	}

	public function loadModel()
	{
		//HomeModel.php
		$model = get_class($this) . "Model";
		$routClass = "Models/" . $model . ".php";
		if (file_exists($routClass)) {
			require_once($routClass);
			$this->model = new $model();
		}
	}
	public function esAdmin()
	{
		if (empty($_SESSION['idUsuario']['tipo']) || $_SESSION['idUsuario']['tipo'] != 1) {
			header('location: ' . base_url());
			die();
		}
	}
}


?>