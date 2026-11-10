<?php
class UsuariosModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    // Lista de usuarios activos (nunca se envía la contraseña al navegador)
    public function selectUsuarios()
    {
        $sql = "SELECT id, nombre, apellido, email, cedula, celular, tipo, estado
                FROM usuario
                WHERE estado != 0";
        return $this->select_all($sql);
    }

    public function selectCargos()
    {
        $sql = "SELECT id_tipo, nombre_tipo FROM tipo";
        return $this->select_all($sql);
    }

    public function selectCargosTipo($idTipo)
    {
        $sql = "SELECT nombre_tipo FROM tipo WHERE id_tipo = ?";
        return $this->select($sql, [intval($idTipo)]);
    }

    public function getSelectUsuario($id)
    {
        $sql = "SELECT u.id, u.nombre, u.apellido, u.email, u.cedula, u.celular, u.tipo, u.estado, t.nombre_tipo
                FROM usuario u
                INNER JOIN tipo t ON u.tipo = t.id_tipo
                WHERE u.id = ?";
        return $this->select($sql, [intval($id)]);
    }

    public function registerUsuario(
        string $nombre,
        string $apellido,
        string $email,
        string $password,
        string $cedula,
        int $celular,
        int $tipo,
        int $estado
    ) {
        $sql = "SELECT id FROM usuario WHERE cedula = ? OR email = ?";
        $existe = $this->select_all($sql, [$cedula, $email]);
        if (!empty($existe)) {
            return 0;
        }

        $query = "INSERT INTO usuario(nombre, apellido, email, password, cedula, celular, tipo, estado)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        return $this->insert($query, [$nombre, $apellido, $email, $password, $cedula, $celular, $tipo, $estado]);
    }

    // Si $password viene vacío, se mantiene la contraseña actual
    public function editUsuario(
        int $id,
        string $nombre,
        string $apellido,
        string $email,
        ?string $password,
        string $cedula,
        int $celular,
        int $tipo,
        int $estado
    ) {
        $sql = "SELECT id FROM usuario WHERE (cedula = ? OR email = ?) AND id != ?";
        $existe = $this->select_all($sql, [$cedula, $email, $id]);
        if (!empty($existe)) {
            return 0;
        }

        if (empty($password)) {
            $query = "UPDATE usuario SET nombre = ?, apellido = ?, email = ?, cedula = ?, celular = ?, tipo = ?, estado = ?
                      WHERE id = ?";
            $arrData = [$nombre, $apellido, $email, $cedula, $celular, $tipo, $estado, $id];
        } else {
            $query = "UPDATE usuario SET nombre = ?, apellido = ?, email = ?, password = ?, cedula = ?, celular = ?, tipo = ?, estado = ?
                      WHERE id = ?";
            $arrData = [$nombre, $apellido, $email, $password, $cedula, $celular, $tipo, $estado, $id];
        }
        return $this->update($query, $arrData);
    }

    public function deletUsuario(int $id)
    {
        $query = "DELETE FROM usuario WHERE id = ?";
        return $this->delete($query, [$id]);
    }
}
