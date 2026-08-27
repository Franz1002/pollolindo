<?php
class ComprasModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insertCompra(int $insumoId, float $cantidadCompra, string $fechaCompra, float $totalCompra, int $usuarioId)
    {
        // 1. Registra la compra
        $queryCompra = "INSERT INTO compra (insumo_id, usuario_id, cantidad, costo_total, fecha_compra) VALUES (?, ?, ?, ?, ?)";
        $requestCompra = $this->insert($queryCompra, [$insumoId, $usuarioId, $cantidadCompra, $totalCompra, $fechaCompra]);

        if ($requestCompra <= 0) {
            return false;
        }

        // 2. Trae el stock y costo promedio actuales del insumo
        $insumo = $this->select1("SELECT stock_actual, costo_promedio FROM insumo WHERE id_insumo = ?", [$insumoId]);
        $stockAnterior = $insumo[0]['stock_actual'];
        $costoAnterior = $insumo[0]['costo_promedio'];

        // 3. Calcula el costo promedio ponderado
        $costoEstaCompra = $totalCompra / $cantidadCompra;
        $stockNuevo = $stockAnterior + $cantidadCompra;
        $costoPromedioNuevo = (($stockAnterior * $costoAnterior) + ($cantidadCompra * $costoEstaCompra)) / $stockNuevo;

        // 4. Actualiza el insumo
        $queryUpdate = "UPDATE insumo SET stock_actual = ?, costo_promedio = ? WHERE id_insumo = ?";
        $this->update($queryUpdate, [$stockNuevo, round($costoPromedioNuevo, 4), $insumoId]);

        return true;
    }

    public function selectCompras()
    {
        $sql = "SELECT c.id_compra, i.nombre_insumo, i.unidad_medida, c.cantidad, c.costo_total, c.fecha_compra, u.nombre AS nombre_usuario
                FROM compra c
                INNER JOIN insumo i ON i.id_insumo = c.insumo_id
                LEFT JOIN usuario u ON u.id = c.usuario_id
                ORDER BY c.fecha_compra DESC";
        return $this->select_all($sql);
    }

    public function selectInsumosActivos()
    {
        $sql = "SELECT id_insumo, nombre_insumo, unidad_medida, stock_actual, costo_promedio FROM insumo WHERE estado = 1 ORDER BY nombre_insumo ASC";
        return $this->select_all($sql);
    }
}