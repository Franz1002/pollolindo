<?php
class CajaModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function obtenerCajaAbierta(int $usuarioId)
    {
        $sql = "SELECT * FROM caja WHERE usuario_id = ? AND estado = 'abierta' ORDER BY id_caja DESC LIMIT 1";
        $resultado = $this->select1($sql, [$usuarioId]);
        return $resultado ? $resultado[0] : null;
    }

    public function abrirCaja(int $usuarioId)
    {
        $sql = "INSERT INTO caja (usuario_id, fecha_apertura, estado) VALUES (?, NOW(), 'abierta')";
        return $this->insert($sql, [$usuarioId]);
    }

    public function resumenVentasPeriodo(string $fechaInicio, string $fechaFin)
    {
        $sql = "SELECT
                COUNT(t.id_ticket) AS cantidad_tickets,
                COALESCE(SUM(t.total), 0) AS total_ventas,
                COALESCE(SUM(CASE WHEN t.descuento > 0 THEN 1 ELSE 0 END), 0) AS num_descuentos
            FROM ticket t
            INNER JOIN venta v ON v.id_venta = t.venta_id
            WHERE v.fecha_venta BETWEEN ? AND ?";
        $resultado = $this->select1($sql, [$fechaInicio, $fechaFin]);
        return $resultado ? $resultado[0] : ['cantidad_tickets' => 0, 'total_ventas' => 0, 'num_descuentos' => 0];
    }

    public function cerrarCaja(int $idCaja, float $efectivoEsperado, float $efectivoDeclarado, int $cantidadTickets, int $numDescuentos, int $cerradoPor, ?float $scoreAnomalia, bool $esAnomalia, ?string $motivoAnomalia)
    {
        $diferencia = round($efectivoDeclarado - $efectivoEsperado, 2);
        $sql = "UPDATE caja SET
                    fecha_cierre = NOW(),
                    total_cierre = ?,
                    efectivo_esperado = ?,
                    efectivo_declarado = ?,
                    diferencia = ?,
                    cantidad_tickets = ?,
                    num_descuentos = ?,
                    score_anomalia = ?,
                    es_anomalia = ?,
                    motivo_anomalia = ?,
                    estado = 'cerrada',
                    cerrado_por = ?
                WHERE id_caja = ?";
        return $this->update($sql, [
            $efectivoEsperado,
            $efectivoEsperado,
            $efectivoDeclarado,
            $diferencia,
            $cantidadTickets,
            $numDescuentos,
            $scoreAnomalia,
            $esAnomalia ? 1 : 0,
            $motivoAnomalia,
            $cerradoPor,
            $idCaja
        ]);
    }

    public function historialCierres(int $limit = 100)
    {
        $sql = "SELECT c.*, u.nombre, u.apellido
                FROM caja c
                LEFT JOIN usuario u ON u.id = c.cerrado_por
                WHERE c.estado = 'cerrada'
                ORDER BY c.fecha_cierre DESC
                LIMIT " . intval($limit); // $limit es fijo desde el controlador, no viene del usuario
        return $this->select_all($sql);
    }

    public function historicoParaEntrenamiento()
    {
        $sql = "SELECT
                    id_caja,
                    TIMESTAMPDIFF(MINUTE, fecha_apertura, fecha_cierre) / 60 AS duracion_turno_horas,
                    HOUR(fecha_cierre) AS hora_cierre,
                    cantidad_tickets,
                    num_descuentos,
                    efectivo_esperado,
                    diferencia
                FROM caja
                WHERE estado = 'cerrada' AND efectivo_esperado > 0";
        return $this->select_all($sql);
    }
}