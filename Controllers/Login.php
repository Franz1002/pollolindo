<?php

class Login extends Controllers
{
    public $views;
    public $model;
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
        $this->views->getView($this, "login");
    }

    public function log()
    {

        if ($_POST) {
            if (empty($_POST['user']) || empty($_POST['password'])) {
                $arrResponse = array('status' => false, 'msg' => 'Error de datos');
            } else {
                $strUser = strtolower($_POST['user']);
                $strPassword = $_POST['password'];
                $requestUser = $this->model->getUsuario($strUser);
                if (empty($requestUser) || !password_verify($strPassword, $requestUser['password'])) {
                    $arrResponse = array('status' => false, 'msg' => 'El usuario o la contraseña es incorrecto.');
                    //incorrecto
                } else {
                    $arrData = $requestUser;
                    if ($arrData['estado'] == 1) {
                        $_SESSION['idUsuario'] = $arrData['id'];
                        $_SESSION['login'] = true;
                        $_SESSION['timeout'] = true;
                        $_SESSION['inicio'] = time();

                        $arrData = $this->model->sessionLogin($_SESSION['idUsuario']);

                        $_SESSION['idUsuario'] = $arrData;

                        $arrResponse = 1; //ok entro

                    } else {
                        $arrResponse = array('status' => false, 'msg' => 'Usuario inactivo.');
                    }
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }

        die();
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        header('Location: ' . base_url());
        exit;
    }
}