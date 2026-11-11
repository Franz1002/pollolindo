<?php
$controller = ucwords($controller);

// Corta la petición con un error: en JSON si la pidió el JavaScript, en texto si es una pantalla
function cortarPeticion(int $codigo, string $mensaje, bool $esPeticionJs)
{
	http_response_code($codigo);
	if ($esPeticionJs) {
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode(['status' => false, 'msg' => $mensaje], JSON_UNESCAPED_UNICODE);
	} else {
		header('Content-Type: text/plain; charset=utf-8');
		echo $mensaje;
	}
	exit;
}

// Solo se aceptan nombres simples de controlador y método (evita rutas como ..\Config)
if (!preg_match('/^[A-Za-z]+$/', $controller) || !preg_match('/^[A-Za-z][A-Za-z0-9]*$/', $method)) {
	require_once("Controllers/Error.php");
	exit;
}

// Peticiones hechas desde JavaScript (fetch, jQuery) y no al abrir una pantalla
$esPeticionJs = (isset($_SERVER['HTTP_SEC_FETCH_MODE']) && $_SERVER['HTTP_SEC_FETCH_MODE'] !== 'navigate')
	|| !empty($_SERVER['HTTP_X_REQUESTED_WITH']);

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
	if ($esPeticionJs) {
		cortarPeticion(401, 'Tu sesión terminó. Vuelve a iniciar sesión.', true);
	}
	header('Location: ' . base_url() . '/login' . ($sesionExpirada ? '?expirada=1' : ''));
	exit;
}

$esPost = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';

// Las acciones que guardan, cambian o borran datos solo se aceptan por POST
if (!$esPost && preg_match('/^(registrar|actualizar|edit|delet|eliminar|guardar|abrir|cerrar|set|reentrenar|anular)/i', $method)) {
	cortarPeticion(405, 'Acción no permitida.', $esPeticionJs);
}

// Todo POST tiene que traer el token de la sesión (protección CSRF)
if ($esPost) {
	$tokenEnviado = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf_token'] ?? '');
	if (empty($_SESSION['csrf_token']) || !is_string($tokenEnviado) || !hash_equals($_SESSION['csrf_token'], $tokenEnviado)) {
		header('X-CSRF-Invalido: 1');
		cortarPeticion(403, 'La página estuvo abierta mucho tiempo. Recárgala (F5) e inténtalo de nuevo.', $esPeticionJs);
	}
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
