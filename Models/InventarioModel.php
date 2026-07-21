<?php
class InventarioModel extends Mysql
{
    public $intIdProducto;
    public $strNombre;
    public $strUsuario;
    public $intCategoria;
    public $strMarca;
    public $intPrecio;
    public $strDescripcion;
    public $imgProducto;
    public function __construct()
    {
        parent::__construct();
    }



    public function selectAgotados()
    {
        $sql = "SELECT
        p.id_producto,
        p.nombre_producto AS producto,
        p.marca_producto,
        p.cantidad_producto,
        p.precio_producto,
        CONCAT(u.nombre, ' ', u.apellido) AS usuario,
        c.nombre_categoria AS categoria,
        CASE
            WHEN p.cantidad_producto > 10 THEN 'Disponible'
            WHEN p.cantidad_producto < 11 AND p.cantidad_producto > 0 THEN 'Poca cantidad'
            ELSE 'Agotado'
        END AS estado,
        CASE
            WHEN p.cantidad_producto > 10 THEN 'disponible'
            WHEN p.cantidad_producto < 11 AND p.cantidad_producto > 0 THEN 'poca_cantidad'
            ELSE 'agotado'
        END AS estado_css,
        COALESCE((SELECT SUM(v.cantidad_venta) FROM venta AS v WHERE v.producto_id = p.id_producto), 0) AS cantidad_vendida,
        COALESCE((SELECT SUM(v.cantidad_venta * p.precio_producto) FROM venta AS v WHERE v.producto_id = p.id_producto), 0) AS total_ingresos
    FROM producto AS p
    INNER JOIN usuario AS u ON p.usuario_id = u.id
    INNER JOIN categoria AS c ON p.categoria_id = c.id_categoria 
    WHERE p.cantidad_producto = 0 ORDER BY producto ASC;
    ";


        $request = $this->select_all($sql);
        return $request;
    }

    public function selectPorAgotar()
    {
        $sql = "SELECT
        p.id_producto,
        p.nombre_producto AS producto,
        p.marca_producto,
        p.cantidad_producto,
        p.precio_producto,
        CONCAT(u.nombre, ' ', u.apellido) AS usuario,
        c.nombre_categoria AS categoria,
        CASE
            WHEN p.cantidad_producto > 10 THEN 'Disponible'
            WHEN p.cantidad_producto < 11 AND p.cantidad_producto > 0 THEN 'Poca cantidad'
            ELSE 'Agotado'
        END AS estado,
        CASE
            WHEN p.cantidad_producto > 10 THEN 'disponible'
            WHEN p.cantidad_producto < 11 AND p.cantidad_producto > 0 THEN 'poca_cantidad'
            ELSE 'agotado'
        END AS estado_css,
        COALESCE((SELECT SUM(v.cantidad_venta) FROM venta AS v WHERE v.producto_id = p.id_producto), 0) AS cantidad_vendida,
        COALESCE((SELECT SUM(v.cantidad_venta * p.precio_producto) FROM venta AS v WHERE v.producto_id = p.id_producto), 0) AS total_ingresos
    FROM producto AS p
    INNER JOIN usuario AS u ON p.usuario_id = u.id
    INNER JOIN categoria AS c ON p.categoria_id = c.id_categoria 
    WHERE p.cantidad_producto < 11 AND p.cantidad_producto > 0 ORDER BY producto ASC;
    ";


        $request = $this->select_all($sql);
        return $request;
    }

    public function selectDisponible()
    {
        $sql = "SELECT
        p.id_producto,
        p.nombre_producto AS producto,
        p.marca_producto,
        p.cantidad_producto,
        p.precio_producto,
        CONCAT(u.nombre, ' ', u.apellido) AS usuario,
        c.nombre_categoria AS categoria,
        CASE
            WHEN p.cantidad_producto > 10 THEN 'Disponible'
            WHEN p.cantidad_producto < 11 AND p.cantidad_producto > 0 THEN 'Poca cantidad'
            ELSE 'Agotado'
        END AS estado,
        CASE
            WHEN p.cantidad_producto > 10 THEN 'disponible'
            WHEN p.cantidad_producto < 11 AND p.cantidad_producto > 0 THEN 'poca_cantidad'
            ELSE 'agotado'
        END AS estado_css,
        COALESCE((SELECT SUM(v.cantidad_venta) FROM venta AS v WHERE v.producto_id = p.id_producto), 0) AS cantidad_vendida,
        COALESCE((SELECT SUM(v.cantidad_venta * p.precio_producto) FROM venta AS v WHERE v.producto_id = p.id_producto), 0) AS total_ingresos
    FROM producto AS p
    INNER JOIN usuario AS u ON p.usuario_id = u.id
    INNER JOIN categoria AS c ON p.categoria_id = c.id_categoria 
    WHERE p.cantidad_producto > 10 ORDER BY producto ASC;
    ";
        $request = $this->select_all($sql);
        return $request;
    }

    public function selectVendidos()
    {
        $sql = "SELECT
        p.id_producto,
        p.nombre_producto AS producto,
        p.marca_producto,
        p.cantidad_producto,
        p.precio_producto,
        CONCAT(u.nombre, ' ', u.apellido) AS usuario,
        c.nombre_categoria AS categoria,
        CASE
            WHEN p.cantidad_producto > 10 THEN 'Disponible'
            WHEN p.cantidad_producto < 11 AND p.cantidad_producto > 0 THEN 'Poca cantidad'
            ELSE 'Agotado'
        END AS estado,
        CASE
            WHEN p.cantidad_producto > 10 THEN 'disponible'
            WHEN p.cantidad_producto < 11 AND p.cantidad_producto > 0 THEN 'poca_cantidad'
            ELSE 'agotado'
        END AS estado_css,
        COALESCE((SELECT SUM(v.cantidad_venta) FROM venta AS v WHERE v.producto_id = p.id_producto), 0) AS cantidad_vendida,
        COALESCE((SELECT SUM(v.cantidad_venta * p.precio_producto) FROM venta AS v WHERE v.producto_id = p.id_producto), 0) AS total_ingresos
    FROM producto AS p
    INNER JOIN usuario AS u ON p.usuario_id = u.id
    INNER JOIN categoria AS c ON p.categoria_id = c.id_categoria 
    WHERE p.cantidad_producto >= 0 ORDER BY total_ingresos ASC;
    ";


        $request = $this->select_all($sql);
        return $request;
    }
}
