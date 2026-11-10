<?php
class MenusModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function selectMenus()
    {
        $sql = "SELECT
    m.id_menu,
    m.nombre_menu,
    m.precio_menu,
    m.categoria_id,
    m.detalle_menu,
    m.img_menu,
    m.estado AS estado_menu,
    c.id_categoria,
    c.nombre_categoria,
    c.descripcion_categoria
FROM menu m
JOIN categoria c ON m.categoria_id = c.id_categoria";
        $request = $this->select_all($sql);
        return $request;
    }

    public function selectMenuById($id_menu)
    {
        $sql = "SELECT * FROM menu WHERE id_menu = ?";
        return $this->select1($sql, array($id_menu));
    }

    public function registerMenu(
        string $nombre_menu,
        float $precio_menu,
        int $categoria_id,
        string $detalle_menu,
        string $img_menu
    ) {
        $query = "INSERT INTO menu(nombre_menu, precio_menu, categoria_id, detalle_menu, img_menu, estado) VALUES (?, ?, ?, ?, ?, ?)";
        $arrData = array(
            $nombre_menu,
            $precio_menu,
            $categoria_id,
            $detalle_menu,
            $img_menu,
            1 // Estado activo por defecto
        );
        $request = $this->insert($query, $arrData);
        return $request;
    }

    public function updateMenu($id, $nombre, $precio, $categoria, $detalle, $img, $estado)
    {
        $sql = "UPDATE menu SET 
		nombre_menu = ?, 
		precio_menu = ?, 
		categoria_id = ?, 
		detalle_menu = ?, 
		img_menu = ?, 
		estado = ? 
		WHERE id_menu = ?";

        $arrData = array($nombre, $precio, $categoria, $detalle, $img, $estado, $id);
        $request = $this->update($sql, $arrData);
        return $request;
    }

    public function deleteMenu($idMenu)
    {
        $sql = "DELETE FROM menu WHERE id_menu = ?";
        return $this->delete($sql, [$idMenu]);
    }
}