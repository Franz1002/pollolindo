<?php
class LoginModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getUsuario(string $user)
    {
        $sql = "SELECT id, tipo, password, estado FROM usuario WHERE cedula = ?";
        $data = $this->select($sql, [$user]);
        return $data ?: null;
    }

    // Datos que quedan en la sesión: sin la contraseña
    public function sessionLogin(int $idusuario)
    {
        $sql = "SELECT u.id, u.nombre, u.apellido, u.email, u.tipo, u.estado, t.nombre_tipo
                FROM usuario u
                INNER JOIN tipo t ON u.tipo = t.id_tipo
                WHERE u.id = ?";
        return $this->select($sql, [$idusuario]);
    }
}
