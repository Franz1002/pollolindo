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
    public function getUsuario(string $user)
    {
        $this->strUser = $user;
        $sql = "SELECT id, tipo, password, estado FROM usuario WHERE cedula = ?";
        $data = $this->select1($sql, [$this->strUser]);

        return $data[0] ?? null;
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
