<?php

class TicketsModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    // Una fila por ticket emitido entre dos fechas (la fecha final no se incluye).
    // Los productos van resumidos en una sola columna para que el ticket no se repita,
    // y los LEFT JOIN dejan pasar las ventas sin cliente.
    public function selectTickets(string $desde, string $hastaExclusivo)
    {
        $sql = "SELECT
                    t.id_ticket,
                    t.numero_pedido,
                    t.fecha_emision,
                    t.descuento,
                    t.total,
                    v.tipo_pedido,
                    v.mesa,
                    v.nota_pedido,
                    v.metodo_pago,
                    c.nombre_cliente,
                    CONCAT(u.nombre, ' ', u.apellido) AS cajero,
                    (SELECT GROUP_CONCAT(CONCAT(dv.cantidad, ' x ', m.nombre_menu) ORDER BY dv.id_detalle SEPARATOR ', ')
                       FROM detalle_venta dv
                       INNER JOIN menu m ON m.id_menu = dv.menu_id
                      WHERE dv.venta_id = t.venta_id) AS productos
                FROM ticket t
                LEFT JOIN venta v ON v.id_venta = t.venta_id
                LEFT JOIN cliente c ON c.id_cliente = t.cliente_id
                LEFT JOIN usuario u ON u.id = t.emitido_por
                WHERE t.fecha_emision >= ? AND t.fecha_emision < ?
                ORDER BY t.fecha_emision DESC, t.id_ticket DESC";
        return $this->select_all($sql, [$desde, $hastaExclusivo]);
    }
}
