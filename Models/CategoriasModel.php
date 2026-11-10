<?php
class CategoriasModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function selectCategorias()
    {
        $sql = "SELECT * FROM categoria";
        return $this->select_all($sql);
    }

    public function getSelectCategoria($id_categoria)
    {
        $sql = "SELECT * FROM categoria WHERE id_categoria = ?";
        return $this->select($sql, [intval($id_categoria)]);
    }

    public function registerCategoria(string $nombre_categoria, string $descripcion_categoria)
    {
        $sql = "SELECT id_categoria FROM categoria WHERE nombre_categoria = ?";
        $existe = $this->select_all($sql, [$nombre_categoria]);
        if (!empty($existe)) {
            return 0;
        }

        $query = "INSERT INTO categoria(nombre_categoria, descripcion_categoria) VALUES (?, ?)";
        return $this->insert($query, [$nombre_categoria, $descripcion_categoria]);
    }

    public function updateCategoria($id, $nombre, $descripcion)
    {
        $sql = "UPDATE categoria SET nombre_categoria = ?, descripcion_categoria = ? WHERE id_categoria = ?";
        return $this->update($sql, [$nombre, $descripcion, $id]);
    }

    public function deletCategoria(int $id_categoria)
    {
        $query = "DELETE FROM categoria WHERE id_categoria = ?";
        return $this->delete($query, [$id_categoria]);
    }
}
