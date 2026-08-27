<?php
class InsumosModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function selectInsumos()
    {
        $sql = "SELECT id_insumo, nombre_insumo, tipo_insumo, unidad_medida, stock_actual, costo_promedio, estado
                FROM insumo ORDER BY nombre_insumo ASC";
        return $this->select_all($sql);
    }

    public function selectInsumo(int $id_insumo)
    {
        $sql = "SELECT id_insumo, nombre_insumo, tipo_insumo, unidad_medida, stock_actual, costo_promedio, estado
                FROM insumo WHERE id_insumo = ?";
        return $this->select1($sql, [$id_insumo]);
    }

    public function registrarInsumo(string $nombre, string $tipo, string $unidad)
    {
        $sql = "INSERT INTO insumo (nombre_insumo, tipo_insumo, unidad_medida, stock_actual, costo_promedio, estado)
                VALUES (?, ?, ?, 0, 0, 1)";
        return $this->insert($sql, [$nombre, $tipo, $unidad]);
    }

    public function actualizarInsumo(int $id_insumo, string $nombre, string $tipo, string $unidad, int $estado)
    {
        $sql = "UPDATE insumo SET nombre_insumo = ?, tipo_insumo = ?, unidad_medida = ?, estado = ? WHERE id_insumo = ?";
        return $this->update($sql, [$nombre, $tipo, $unidad, $estado, $id_insumo]);
    }

    public function eliminarInsumo(int $id_insumo)
    {
        // Desactiva en vez de borrar: si el insumo ya se usó en una receta o compra,
        // borrarlo de verdad rompería esos registros históricos.
        $sql = "UPDATE insumo SET estado = 0 WHERE id_insumo = ?";
        return $this->update($sql, [$id_insumo]);
    }
}
