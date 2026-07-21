<?php
class ComprasModel extends Mysql
{

    public function __construct()
    {
        parent::__construct();
    }
    public function insertCompra(int $nombreProductoId, int $cantidadCompra, string $fechaCompra, float $totalCompra)
    {
        // Inicializa la variable de retorno
        $return = "";

        // Inserta la compra en la tabla 'compra'
        $queryCompra = "INSERT INTO compra (total_compra, cantidad_compra, fecha_compra, producto_id) VALUES (?, ?, ?, ?)";
        $arrDataCompra = array($totalCompra, $cantidadCompra, $fechaCompra, $nombreProductoId);
        $requestCompra = $this->insert($queryCompra, $arrDataCompra);

        // Verifica si la inserción en 'compra' fue exitosa
        if ($requestCompra > 0) {
            // Actualizar cantidad y precio en la tabla 'producto'
            $nuevoPrecio = round($totalCompra / $cantidadCompra, 2);
            $queryUpdateProducto = "UPDATE producto SET cantidad_producto = cantidad_producto + ?, precio_producto = ? WHERE id_producto = ?";
            $arrDataUpdateProducto = array($cantidadCompra, $nuevoPrecio, $nombreProductoId);
            $requestUpdateProducto = $this->update($queryUpdateProducto, $arrDataUpdateProducto);

            // Verifica si la actualización en 'producto' fue exitosa
            if ($requestUpdateProducto > 0) {
                $return = "Compra registrada y producto actualizado exitosamente.";
            } else {
                $return = "Compra registrada, pero hubo un error al actualizar el producto.";
            }
        } else {
            $return = "Hubo un error al registrar la compra.";
        }

        return $return;
    }
    public function selectCompras()
    {
        $sql = "SELECT * from compra INNER JOIN producto
        on producto_id = id_producto";
        $request = $this->select_all($sql);
        return $request;
    }
    public function selectVentas()
    {
        $sql = "SELECT
        v.id_venta,
        vp.producto_id,
        p.nombre_producto,
        p.precio_producto,
        vp.cantidad AS cantidad_vendida,
        u.nombre AS nombre_usuario,
        u.email AS email_usuario,
        v.fecha_venta,
        v.total_venta
    FROM
        venta v
    INNER JOIN
        venta_producto vp ON v.venta_producto_id = vp.id_venta_producto
    INNER JOIN
        producto p ON vp.producto_id = p.id_producto
    INNER JOIN
        usuario u ON v.usuario_id = u.id;
    ";
        $request = $this->select_all($sql);
        return $request;
    }
}
