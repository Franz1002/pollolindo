<?php
require_once("Config/Config.php");
require_once("Helpers/Helpers.php");
// Una sola sesión para todo el sistema
// Una sola sesión para todo el sistema, con la cookie protegida
if (session_status() === PHP_SESSION_NONE) {
	ini_set('session.use_strict_mode', '1');
	ini_set('session.use_only_cookies', '1');
	session_name('CHICKENLINDO');
	session_set_cookie_params([
		'lifetime' => 0,
		'path' => '/',
		'httponly' => true,
		'samesite' => 'Lax',
		'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
	]);
	session_start();
}
$url = !empty($_GET['url']) ? $_GET['url'] : 'Login/login';
$arrUrl = explode("/", $url);
$controller = $arrUrl[0];
$method = $arrUrl[0];
$params = "";

if (!empty($arrUrl[1])) {
	if ($arrUrl[1] != "") {
		$method = $arrUrl[1];
	}
}

if (!empty($arrUrl[2])) {
	if ($arrUrl[2] != "") {
		for ($i = 2; $i < count($arrUrl); $i++) {
			$params .=  $arrUrl[$i] . ',';
			# code...
		}
		$params = trim($params, ',');
	}
}
require_once("Libraries/Core/Autoload.php");
require_once("Libraries/Core/Load.php");
