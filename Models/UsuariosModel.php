<?php
class UsuariosModel extends Mysql
{
    public $intIdUsuario;
    public $strCiUsuario;
    public $strNombres;
    public $strApellidos;
    public $strCedula;
    public $intCelular;
    public $strEmail;
    public $strPassword;
    public $intTipoUsuario;
    public $intEstado;
    public function __construct()
    {
        parent::__construct();
    }
    public function selectUsuarios()
    {
        $sql = "SELECT * from usuario
        WHERE estado != 0 ";
        $request = $this->select_all($sql);
        return $request;
    }
    public function selectCargos()
    {

        //EXTRAE CARGOS
        $sql = "SELECT * FROM tipo";
        $request = $this->select_all($sql);
        return $request;
    }

    public function selectCargosTipo($idTipo)
    {
        $intIdTipo = $idTipo;
        $sql = "SELECT nombre_tipo FROM tipo WHERE id_tipo = $intIdTipo";
        $arrData = $this->select($sql);
        return $arrData;
    }


    public function getSelectUsuario($id)
    {
        $intIdUsuario = $id;
        $sql = "SELECT * FROM usuario INNER JOIN tipo ON tipo = id_tipo WHERE id = $intIdUsuario";
        $arrData = $this->select($sql);
        return $arrData;
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

        $return = "";
        $this->strNombres = $nombre;
        $this->strApellidos = $apellido;
        $this->strEmail = $email;
        $this->strPassword = $password;
        $this->strCedula = $cedula;
        $this->intCelular = $celular;
        $this->intTipoUsuario = $tipo;
        $this->intEstado = $estado;

        $sql = "SELECT * from usuario WHERE cedula = '{$this->strCedula}' or email = '{$this->strEmail}'";
        $existe = $this->select_all($sql);

        if (empty($existe)) {
            $query = "INSERT INTO usuario(nombre,apellido,email,password,cedula,celular,tipo,estado) VALUES (?,?,?,?,?,?,?,?)";
            $arrData = array(
                $this->strNombres,
                $this->strApellidos,
                $this->strEmail,
                $this->strPassword,
                $this->strCedula,
                $this->intCelular,
                $this->intTipoUsuario,
                $this->intEstado
            );
            $request = $this->insert($query, $arrData);
            $return = $request;
        } else {
            $return = 0;
        }
        return $return;
    }


    public function editUsuario(
        int $id,
        string $apellido,
        string $nombre,
        string $cedula,
        int $celular,
        string $email,
        string $password,
        int $tipo,
        int $estado
    ) {

        $this->intIdUsuario = $id;
        $this->strApellidos = $apellido;
        $this->strNombres = $nombre;
        $this->strCedula = $cedula;
        $this->intCelular = $celular;
        $this->strEmail = $email;
        $this->strPassword = $password;
        $this->intTipoUsuario = $tipo;
        $this->intEstado = $estado;

        $sql = "SELECT * FROM usuario WHERE email = '{$this->strEmail}' AND id != $this->intIdUsuario";
        $request = $this->select_all($sql);
        if (empty($request)) {
            $query = "UPDATE usuario SET apellido = ?, nombre = ?, cedula = ?, celular = ?, email = ?,
            password = ?, tipo = ?, estado = ?  WHERE id = $this->intIdUsuario";
            $arrData = array(
                $this->strApellidos,
                $this->strNombres,
                $this->strCedula,
                $this->intCelular,
                $this->strEmail,
                $this->strPassword,
                $this->intTipoUsuario,
                $this->intEstado
            );
            $request = $this->update($query, $arrData);
        } else {
            $request = 0;
        }
        return $request;
    }

    public function deletUsuario(int $id)
    {
        $this->intIdUsuario = $id;
        $query = "DELETE FROM usuario where id = ?";
        $arrData = array($this->intIdUsuario);
        $request = $this->update($query, $arrData);
        return $request;
    }
}
