<?php
class RecetasModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    // Lista todos los platos del menú con la cantidad de insumos que tiene su receta
    public function selectMenusConReceta()
    {
        $sql = "SELECT m.id_menu, m.nombre_menu, m.precio_menu,
                       COUNT(r.id_receta) AS total_insumos
                FROM menu m
                LEFT JOIN receta r ON r.menu_id = m.id_menu
                GROUP BY m.id_menu, m.nombre_menu, m.precio_menu
                ORDER BY m.id_menu ASC";
        return $this->select_all($sql);
    }

    // Lista de insumos disponibles para armar el formulario (Pollo, Arroz, Papa, Fideo, etc.)
    public function selectInsumos()
    {
        $sql = "SELECT id_insumo, nombre_insumo, unidad_medida FROM insumo WHERE estado = 1 ORDER BY nombre_insumo ASC";
        return $this->select_all($sql);
    }

    // Receta actual de un plato específico
    public function selectRecetaByMenu(int $id_menu)
    {
        $sql = "SELECT r.insumo_id, r.cantidad_usada, i.nombre_insumo, i.unidad_medida
                FROM receta r
                INNER JOIN insumo i ON i.id_insumo = r.insumo_id
                WHERE r.menu_id = ?";
        return $this->select1($sql, [$id_menu]);
    }

    // Reemplaza la receta completa de un plato: borra la anterior y guarda la nueva
    public function guardarReceta(int $id_menu, array $arrInsumos, array $arrCantidades)
    {
        // 1. Borra la receta anterior de este plato
        $sqlDelete = "DELETE FROM receta WHERE menu_id = ?";
        $this->update($sqlDelete, [$id_menu]);

        // 2. Inserta las filas nuevas
        $total = count($arrInsumos);
        for ($i = 0; $i < $total; $i++) {
            $idInsumo = intval($arrInsumos[$i]);
            $cantidad = floatval($arrCantidades[$i]);
            if ($idInsumo > 0 && $cantidad > 0) {
                $sqlInsert = "INSERT INTO receta (menu_id, insumo_id, cantidad_usada) VALUES (?, ?, ?)";
                $this->insert($sqlInsert, [$id_menu, $idInsumo, $cantidad]);
            }
        }
        return true;
    }
}
