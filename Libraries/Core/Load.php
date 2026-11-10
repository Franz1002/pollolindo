<?php
$controller = ucwords($controller);

// Solo se aceptan nombres simples de controlador y método (evita rutas como ..\Config)
if (!preg_match('/^[A-Za-z]+$/', $controller) || !preg_match('/^[A-Za-z][A-Za-z0-9]*$/', $method)) {
	require_once("Controllers/Error.php");
	exit;
}

// Todo el sistema pide sesión iniciada, menos el login
$controladoresPublicos = ['Login'];
if (!in_array($controller, $controladoresPublicos, true) && empty($_SESSION['login'])) {
	header('Location: ' . base_url() . '/login');
	exit;
}

$controllerFile = "Controllers/" . $controller . ".php";
if (file_exists($controllerFile)) {
	require_once($controllerFile);
	$controller = new $controller();
	// is_callable respeta public/private: solo los métodos públicos se pueden llamar por URL
	if (is_callable([$controller, $method])) {
		$controller->{$method}($params);
	} else {
		require_once("Controllers/Error.php");
	}
} else {
	require_once("Controllers/Error.php");
}
