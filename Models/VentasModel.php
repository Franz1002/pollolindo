<?php
class VentasModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    // Productos activos con su categoría, para armar la pantalla Vender
    public function selectCatalogo()
    {
        $sql = "SELECT m.id_menu, m.nombre_menu, m.precio_menu, m.categoria_id, c.nombre_categoria
                FROM menu m
                INNER JOIN categoria c ON c.id_categoria = m.categoria_id
                WHERE m.estado = 1
                ORDER BY m.categoria_id ASC, m.id_menu ASC";
        return $this->select_all($sql);
    }

    // Precio y nombre actuales de los productos pedidos (solo los activos)
    public function selectProductosPorIds(array $ids)
    {
        if (empty($ids)) {
            return [];
        }
        $marcas = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT id_menu, nombre_menu, precio_menu FROM menu WHERE estado = 1 AND id_menu IN ($marcas)";
        return $this->select_all($sql, array_values($ids));
    }

    // Cantidad de mesas que definió el dueño (15 si todavía no hay configuración)
    public function numeroMesas()
    {
        $fila = $this->select("SELECT valor FROM configuracion WHERE clave = 'numero_mesas'");
        $mesas = $fila ? (int) $fila['valor'] : 15;
        return $mesas > 0 ? $mesas : 15;
    }

    // Guarda la venta, su detalle y su ticket en una sola transacción:
    // si algo falla a la mitad, no queda nada guardado
    public function guardarVentaCompleta(array $venta, array $lineas)
    {
        return $this->transaccion(function () use ($venta, $lineas) {
            $idVenta = $this->insert(
                "INSERT INTO venta (usuario_id, fecha_venta, total_venta, caja_id, tipo_pedido, mesa, nota_pedido, metodo_pago, monto_efectivo, monto_qr)
                 VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $venta['usuario_id'], $venta['total'], $venta['caja_id'], $venta['tipo_pedido'], $venta['mesa'],
                    $venta['nota_pedido'], $venta['metodo_pago'], $venta['monto_efectivo'], $venta['monto_qr']
                ]
            );

            foreach ($lineas as $linea) {
                $this->insert(
                    "INSERT INTO detalle_venta (venta_id, menu_id, cantidad, subtotal, precio_unitario) VALUES (?, ?, ?, ?, ?)",
                    [$idVenta, $linea['menu_id'], $linea['cantidad'], $linea['subtotal'], $linea['precio_unitario']]
                );
            }

            // El número de pedido del día se calcula dentro de la transacción y con bloqueo,
            // para que dos cajas cobrando a la vez no reciban el mismo número
            $fila = $this->select(
                "SELECT COALESCE(MAX(numero_pedido), 0) + 1 AS nuevo_numero
                 FROM ticket WHERE DATE(fecha_emision) = CURDATE() FOR UPDATE"
            );
            $numeroPedido = $fila ? (int) $fila['nuevo_numero'] : 1;

            $idTicket = $this->insert(
                "INSERT INTO ticket (venta_id, cliente_id, fecha_emision, numero_pedido, emitido_por, descuento, total, efectivo_recibido, cambio)
                 VALUES (?, NULL, NOW(), ?, ?, ?, ?, ?, ?)",
                [
                    $idVenta, $numeroPedido, $venta['usuario_id'], $venta['descuento'],
                    $venta['total'], $venta['efectivo_recibido'], $venta['cambio']
                ]
            );

            return ['id_venta' => (int) $idVenta, 'id_ticket' => (int) $idTicket, 'numero_pedido' => $numeroPedido];
        });
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
                        t.cambio,
                        dv.precio_unitario,
                        v.tipo_pedido,
                        v.mesa,
                        v.nota_pedido,
                        v.metodo_pago
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