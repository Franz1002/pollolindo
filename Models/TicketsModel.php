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
    // puede_anular vale 1 solo si la venta sigue vigente y la caja en la que se cobró sigue abierta.
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
                    COALESCE(v.estado, 'vigente') AS estado,
                    v.motivo_anulacion,
                    v.fecha_anulacion,
                    CONCAT(ua.nombre, ' ', ua.apellido) AS anulada_por_nombre,
                    CASE WHEN v.estado = 'vigente' AND cj.estado = 'abierta' THEN 1 ELSE 0 END AS puede_anular,
                    c.nombre_cliente,
                    CONCAT(u.nombre, ' ', u.apellido) AS cajero,
                    (SELECT GROUP_CONCAT(CONCAT(dv.cantidad, ' x ', m.nombre_menu) ORDER BY dv.id_detalle SEPARATOR ', ')
                       FROM detalle_venta dv
                       INNER JOIN menu m ON m.id_menu = dv.menu_id
                      WHERE dv.venta_id = t.venta_id) AS productos
                FROM ticket t
                LEFT JOIN venta v ON v.id_venta = t.venta_id
                LEFT JOIN caja cj ON cj.id_caja = v.caja_id
                LEFT JOIN cliente c ON c.id_cliente = t.cliente_id
                LEFT JOIN usuario u ON u.id = t.emitido_por
                LEFT JOIN usuario ua ON ua.id = v.anulada_por
                WHERE t.fecha_emision >= ? AND t.fecha_emision < ?
                ORDER BY t.fecha_emision DESC, t.id_ticket DESC";
        return $this->select_all($sql, [$desde, $hastaExclusivo]);
    }

    // Anula la venta de un ticket sin borrarla: queda marcada con quién, cuándo y por qué.
    // Va en una transacción y con las filas bloqueadas (FOR UPDATE), para que dos anulaciones
    // a la vez, o un cierre de caja en ese instante, no se pisen.
    // Devuelve ['ok' => true, ...datos de la venta] o ['ok' => false, 'msg' => motivo del rechazo].
    public function anularVentaDeTicket(int $idTicket, int $idAdmin, string $motivo)
    {
        return $this->transaccion(function () use ($idTicket, $idAdmin, $motivo) {
            $venta = $this->select(
                "SELECT v.id_venta, v.estado, v.caja_id, v.metodo_pago, v.monto_efectivo, v.monto_qr,
                        t.total, t.numero_pedido, cj.estado AS estado_caja
                   FROM ticket t
                   INNER JOIN venta v ON v.id_venta = t.venta_id
                   LEFT JOIN caja cj ON cj.id_caja = v.caja_id
                  WHERE t.id_ticket = ?
                    FOR UPDATE",
                [$idTicket]
            );

            if (!$venta) {
                return ['ok' => false, 'msg' => 'No se encontró esa venta.'];
            }
            if ($venta['estado'] === 'anulada') {
                return ['ok' => false, 'msg' => 'Esa venta ya estaba anulada.'];
            }
            // Las ventas anteriores a la pantalla nueva no guardaron en qué caja se cobraron
            if (empty($venta['caja_id'])) {
                return ['ok' => false, 'msg' => 'No se puede anular: esta venta es anterior al control por caja.'];
            }
            // Una caja cerrada ya fue contada: anular ahora cambiaría un arqueo terminado
            if ($venta['estado_caja'] !== 'abierta') {
                return ['ok' => false, 'msg' => 'No se puede anular: la caja en la que se cobró esta venta ya está cerrada.'];
            }

            $this->update(
                "UPDATE venta
                    SET estado = 'anulada', anulada_por = ?, fecha_anulacion = NOW(), motivo_anulacion = ?
                  WHERE id_venta = ? AND estado = 'vigente'",
                [$idAdmin, $motivo, $venta['id_venta']]
            );

            return ['ok' => true] + $venta;
        });
    }
}
