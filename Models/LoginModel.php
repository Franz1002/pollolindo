<?php
class LoginModel extends Mysql
{
    public $intIdUsuario;
    public $strUser;
    public $strPassword;
    public function __construct()
    {
        parent::__construct();
    }
    public function getUsuario(string $user, string $password)
    {
        $this->strUser = $user;
        $this->strPassword = $password;
        $sql = "SELECT id, tipo FROM usuario WHERE cedula = '$this->strUser'
        AND password = '$this->strPassword'";
        $data = $this->select($sql);

        return $data;
    }
    public function sessionLogin(int $idusuario)
    {
        $this->intIdUsuario = $idusuario;

        $sql = "SELECT *
                FROM usuario u
                INNER JOIN tipo t
                ON u.tipo = t.id_tipo
                WHERE u.id = $this->intIdUsuario";
        $request = $this->select($sql);
        //$_SESSION['userData'] = $request;
        return $request;
    }
}
