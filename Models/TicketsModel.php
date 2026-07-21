<?php

class TicketsModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function selectTickets()
    {
        $sql = "SELECT
    t.id_ticket,
    t.fecha_emision,
    t.numero_pedido,
    t.emitido_por,
    t.descuento,
    v.id_venta,
    v.fecha_venta,
    v.total_venta,
    c.id_cliente,
    c.nombre_cliente,
    c.cedula_cliente,
    c.celular_cliente,
    u.id AS usuario_id,
    CONCAT(u.nombre, ' ', u.apellido) AS nombre_usuario_completo,  -- Combina nombre y apellido
    u.email AS email_usuario,
    u.cedula AS cedula_usuario,
    u.celular AS celular_usuario,
    dv.id_detalle,
    dv.cantidad,
    dv.subtotal,
    m.id_menu,
    m.nombre_menu,
    m.precio_menu,
    m.detalle_menu,
    m.img_menu,
    m.estado AS estado_menu
FROM ticket t
JOIN venta v ON t.venta_id = v.id_venta
JOIN cliente c ON t.cliente_id = c.id_cliente
JOIN usuario u ON t.emitido_por = u.id
JOIN detalle_venta dv ON v.id_venta = dv.venta_id
JOIN menu m ON dv.menu_id = m.id_menu;
";
        $request = $this->select_all($sql);
        return $request;
    }
}
