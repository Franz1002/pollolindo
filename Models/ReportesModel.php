<?php
class ReportesModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getVentasArqueoActual($fechaInicio, $fechaFin)
    {
        return $this->resumenVentas($fechaInicio, $fechaFin);
    }

    public function getVentasArqueoMensual($fechaInicio, $fechaFin)
    {
        return $this->resumenVentas($fechaInicio, $fechaFin);
    }

    // Lo vendido entre dos fechas: una fila por producto, más las filas DESCUENTO y TOTAL.
    // Los dos reportes (el del día y el mensual) usan la misma consulta.
    // Las ventas anuladas no entran en ninguna de las tres partes.
    private function resumenVentas($fechaInicio, $fechaFin)
    {
        $sql = "SELECT
            SUM(dv.cantidad) AS cantidad_total,
            m.nombre_menu AS menu,
            m.precio_menu AS precio_unitario,
            SUM(dv.subtotal) AS subtotal
        FROM detalle_venta dv
        INNER JOIN menu m ON dv.menu_id = m.id_menu
        INNER JOIN venta v ON dv.venta_id = v.id_venta
        WHERE v.fecha_venta BETWEEN :fechaInicio AND :fechaFin
          AND v.estado = 'vigente'
        GROUP BY m.id_menu

        UNION ALL

        SELECT
            NULL AS cantidad_total,
            'DESCUENTO' AS menu,
            NULL AS precio_unitario,
            SUM(t.descuento) AS subtotal
        FROM ticket t
        INNER JOIN venta v ON t.venta_id = v.id_venta
        WHERE v.fecha_venta BETWEEN :fechaInicio AND :fechaFin
          AND v.estado = 'vigente'

        UNION ALL

        SELECT
            NULL AS cantidad_total,
            'TOTAL' AS menu,
            NULL AS precio_unitario,
            SUM(v.total_venta) AS subtotal
        FROM venta v
        WHERE v.fecha_venta BETWEEN :fechaInicio AND :fechaFin
          AND v.estado = 'vigente'";

        $params = [':fechaInicio' => $fechaInicio, ':fechaFin' => $fechaFin];
        return $this->select1($sql, $params);
    }
}
