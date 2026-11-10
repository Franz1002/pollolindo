<?php

class Login extends Controllers
{
    public function __construct()
    {
        parent::__construct();
    }

    public function login()
    {
        // Si ya inició sesión, lo mandamos directo al inicio
        if (!empty($_SESSION['login'])) {
            header('Location: ' . base_url() . '/inicio');
            exit;
        }
        $data['expirada'] = isset($_GET['expirada']);
        $this->views->getView($this, "login", $data);
    }

    public function log()
    {
        if (!$_POST) {
            die();
        }

        $strUser = strtolower(trim($_POST['user'] ?? ''));
        $strPassword = $_POST['password'] ?? '';
        if ($strUser === '' || $strPassword === '') {
            $this->responder(false, 'Escribe tu usuario y tu contraseña.');
        }

        // Si alguien falló muchas veces seguidas con este usuario, tiene que esperar
        $minutos = $this->model->minutosDeBloqueo($strUser);
        if ($minutos > 0) {
            $this->responder(false, "Demasiados intentos fallidos. Espera {$minutos} min y vuelve a intentar.");
        }

        $requestUser = $this->model->getUsuario($strUser);
        if (empty($requestUser) || !password_verify($strPassword, $requestUser['password'])) {
            $this->model->registrarIntentoFallido($strUser, $_SERVER['REMOTE_ADDR'] ?? '');
            $this->responder(false, 'El usuario o la contraseña es incorrecto.');
        }
        if ((int)$requestUser['estado'] !== 1) {
            $this->responder(false, 'Tu usuario está dado de baja. Habla con el administrador.');
        }

        $this->model->limpiarIntentos($strUser);

        // Identificador de sesión nuevo al entrar: así no sirve uno que alguien haya copiado antes
        session_regenerate_id(true);
        $_SESSION['login'] = true;
        $_SESSION['idUsuario'] = $this->model->sessionLogin((int)$requestUser['id']);
        $_SESSION['ultima_actividad'] = time();

        $this->responder(true, 'Bienvenido.');
    }

    // El navegador lo llama mientras se usa el sistema, para que la sesión no se cierre
    public function ping()
    {
        echo json_encode(['status' => !empty($_SESSION['login'])]);
        die();
    }

    public function logout()
    {
        $_SESSION = [];
        session_destroy();
        setcookie(session_name(), '', ['expires' => time() - 3600, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);

        $destino = isset($_GET['expirada']) ? '/login?expirada=1' : '/login';
        header('Location: ' . base_url() . $destino);
        exit;
    }

    private function responder(bool $status, string $msg)
    {
        echo json_encode(['status' => $status, 'msg' => $msg], JSON_UNESCAPED_UNICODE);
        die();
    }
}