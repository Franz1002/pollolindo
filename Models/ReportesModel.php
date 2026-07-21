<?php
class ReportesModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getVentasArqueoActual($fechaInicio, $fechaFin)
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
        
        UNION ALL
        
        SELECT 
            NULL AS cantidad_total, 
            'TOTAL' AS menu, 
            NULL AS precio_unitario, 
            SUM(v.total_venta) AS subtotal
        FROM venta v
        WHERE v.fecha_venta BETWEEN :fechaInicio AND :fechaFin";

        $params = [':fechaInicio' => $fechaInicio, ':fechaFin' => $fechaFin];
        return $this->select1($sql, $params);
    }

      public function getVentasArqueoMensual($fechaInicio, $fechaFin)
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
        
        UNION ALL
        
        SELECT 
            NULL AS cantidad_total, 
            'TOTAL' AS menu, 
            NULL AS precio_unitario, 
            SUM(v.total_venta) AS subtotal
        FROM venta v
        WHERE v.fecha_venta BETWEEN :fechaInicio AND :fechaFin";

        $params = [':fechaInicio' => $fechaInicio, ':fechaFin' => $fechaFin];
        return $this->select1($sql, $params);
    }
}
