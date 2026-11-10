<?php
class VentasModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function selectMenu()
    {
        $sql = "SELECT * FROM menu WHERE categoria_id = 1 AND estado != 0";
        return $this->select_all($sql);
    }

    public function selectBebida()
    {
        $sql = "SELECT * FROM menu WHERE categoria_id = 2 AND estado != 0";
        return $this->select_all($sql);
    }

    public function selectCombo()
    {
        $sql = "SELECT * FROM menu WHERE categoria_id = 3 AND estado != 0";
        return $this->select_all($sql);
    }

    public function insertarVenta($usuarioId,  $totalVenta)
    {
        // Preparar la consulta SQL para insertar una venta en la tabla 'venta'
        $sql = "INSERT INTO venta (usuario_id, fecha_venta, total_venta) VALUES (?, NOW(), ?)";

        // Ejecutar la consulta utilizando el método 'query' de la clase 'Modelo'
        $arrData = array($usuarioId, $totalVenta);

        // Retornar el resultado de la consulta (id de la venta registrada)
        $request = $this->insert($sql, $arrData);
        return $request;
    }
    public function insertarDetalleVenta($ventaId, $menuId, $cantidad, $subtotal)
    {
        // Preparar la consulta SQL para insertar el detalle de la venta
        $sql = "INSERT INTO detalle_venta (venta_id, menu_id, cantidad, subtotal) VALUES (?, ?, ?, ?)";

        // Ejecutar la consulta utilizando el método 'insert' de la clase Modelo
        $arrData = array($ventaId, $menuId, $cantidad, $subtotal);

        // Retornar el resultado de la consulta (verifica si se insertó correctamente)
        $request = $this->insert($sql, $arrData);
        return $request;
    }


    public function insertarTicket($venta_id, $cliente_id, $numero_pedido, $emitido_por, $descuento, $total, $efectivo_recibido, $cambio)
    {
        $sql = "INSERT INTO ticket (venta_id, cliente_id, fecha_emision, numero_pedido, emitido_por, descuento, total, efectivo_recibido, cambio) 
            VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?)";

        // Si cliente_id es null, pásalo como NULL en lugar de 0
        $arrData = array($venta_id, $cliente_id ?? null, $numero_pedido, $emitido_por, $descuento, $total, $efectivo_recibido, $cambio);

        // Ejecutar la consulta de inserción
        $request = $this->insert($sql, $arrData);

        // Retornar el resultado de la consulta (ID del ticket insertado)
        return $request;
    }


    public function obtenerNumPedido()
    {
        $sql = "SELECT COALESCE(MAX(numero_pedido), 0) + 1 AS nuevo_numero 
                FROM ticket 
                WHERE DATE(fecha_emision) = CURDATE()";

        $request = $this->select($sql);

        return $request ? $request['nuevo_numero'] : 1; // Asegurar que siempre retorne un número
    }
    public function getTicketById($ticket_id)
    {
        if (is_numeric($ticket_id)) {
            $sql = "SELECT 
                        t.id_ticket, 
                        v.fecha_venta, 
                        c.nombre_cliente, 
                        dv.cantidad, 
                        m.nombre_menu, 
                        m.precio_menu, 
                        dv.subtotal, 
                        t.descuento, 
                        t.total, 
                        t.numero_pedido, 
                        u.nombre AS usuario_nombre, 
                        u.apellido AS usuario_apellido,
                        t.efectivo_recibido,
                        t.cambio
                    FROM ticket t
                    LEFT JOIN venta v ON t.venta_id = v.id_venta
                    LEFT JOIN cliente c ON t.cliente_id = c.id_cliente
                    LEFT JOIN detalle_venta dv ON v.id_venta = dv.venta_id
                    LEFT JOIN menu m ON dv.menu_id = m.id_menu
                    LEFT JOIN usuario u ON v.usuario_id = u.id
                    WHERE t.id_ticket = ?";

            // Ejecutar la consulta y obtener todos los registros
            $request = $this->select_all($sql, [(int)$ticket_id]);

            return $request;
        } else {
            return null;
        }
    }
}