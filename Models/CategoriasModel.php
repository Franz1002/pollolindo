<?php
class CategoriasModel extends Mysql
{
    public $intIdCategoria;
    public $strNombre;
    public $strDescripcion;

    public function __construct()
    {
        parent::__construct();
    }

    public function selectCategorias()
    {
        $sql = "SELECT * FROM categoria";  // Asumiendo que tienes una tabla 'categorias'.
        $request = $this->select_all($sql);  // Aquí 'select_all' es un método que ejecuta la consulta y devuelve los resultados.
        return $request;
    }


    public function getSelectCategoria($id_categoria)
    {
        $intIdCategoria = $id_categoria;
        $sql = "SELECT * FROM categoria WHERE id_categoria = $intIdCategoria";
        $arrData = $this->select($sql);
        return $arrData;
    }

    public function registerCategoria(string $nombre_categoria, string $descripcion_categoria)
    {

        $return = "";
        $this->strNombre = $nombre_categoria;
        $this->strDescripcion = $descripcion_categoria;


        $sql = "SELECT * from categoria WHERE nombre_categoria = '{$this->strNombre}'";
        $existe = $this->select_all($sql);

        if (empty($existe)) {
            $query = "INSERT INTO categoria(nombre_categoria,descripcion_categoria) VALUES (?,?)";
            $arrData = array($this->strNombre, $this->strDescripcion);
            $request = $this->insert($query, $arrData);
            $return = $request;
        } else {
            $return = 0;
        }
        return $return;
    }

    public function updateCategoria($id, $nombre, $descripcion)
    {
        $sql = "UPDATE categoria SET nombre_categoria = ?, descripcion_categoria = ? WHERE id_categoria = ?";
        $arrData = array($nombre, $descripcion, $id);
        $request = $this->update($sql, $arrData);
        return $request;
    }



    public function deletCategoria(int $id_categoria)
    {
        $this->intIdCategoria = $id_categoria;
        $query = "DELETE FROM categoria where id_categoria = ?";
        $arrData = array($this->intIdCategoria);
        $request = $this->update($query, $arrData);
        return $request;
    }
}
