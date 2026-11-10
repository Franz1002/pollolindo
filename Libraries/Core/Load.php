<?php
$controller = ucwords($controller);

// Solo se aceptan nombres simples de controlador y método (evita rutas como ..\Config)
if (!preg_match('/^[A-Za-z]+$/', $controller) || !preg_match('/^[A-Za-z][A-Za-z0-9]*$/', $method)) {
	require_once("Controllers/Error.php");
	exit;
}

// Si pasó mucho tiempo sin usar el sistema, la sesión se cierra sola
$sesionExpirada = false;
if (!empty($_SESSION['login'])) {
	$ultimaActividad = $_SESSION['ultima_actividad'] ?? time();
	if (time() - $ultimaActividad > MINUTOS_INACTIVIDAD * 60) {
		$_SESSION = [];
		session_regenerate_id(true);
		$sesionExpirada = true;
	} else {
		$_SESSION['ultima_actividad'] = time();
	}
}

// Todo el sistema pide sesión iniciada, menos el login
$controladoresPublicos = ['Login'];
if (!in_array($controller, $controladoresPublicos, true) && empty($_SESSION['login'])) {
	// Las peticiones hechas desde JavaScript reciben un 401; las pantallas van al login
	$esPeticionJs = (isset($_SERVER['HTTP_SEC_FETCH_MODE']) && $_SERVER['HTTP_SEC_FETCH_MODE'] !== 'navigate')
		|| !empty($_SERVER['HTTP_X_REQUESTED_WITH']);
	if ($esPeticionJs) {
		http_response_code(401);
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode(['status' => false, 'msg' => 'Tu sesión terminó. Vuelve a iniciar sesión.'], JSON_UNESCAPED_UNICODE);
		exit;
	}
	header('Location: ' . base_url() . '/login' . ($sesionExpirada ? '?expirada=1' : ''));
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
