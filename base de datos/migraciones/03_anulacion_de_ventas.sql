-- Permite anular una venta sin borrarla.
-- La venta anulada queda guardada como evidencia: quién la anuló, cuándo y por qué.
-- Solo AGREGA columnas a `venta`: no borra ni modifica ninguna venta existente
-- (todas las que ya están quedan como 'vigente').
-- Se puede correr más de una vez sin dañar nada (usa IF NOT EXISTS).
-- Antes de correrla: respaldar la base `pollo`.

ALTER TABLE `venta`
  ADD COLUMN IF NOT EXISTS `estado` enum('vigente','anulada') NOT NULL DEFAULT 'vigente' COMMENT 'Una venta anulada no cuenta en caja ni en reportes',
  ADD COLUMN IF NOT EXISTS `anulada_por` int(11) DEFAULT NULL COMMENT 'Administrador que autorizó la anulación',
  ADD COLUMN IF NOT EXISTS `fecha_anulacion` datetime DEFAULT NULL COMMENT 'Cuándo se anuló',
  ADD COLUMN IF NOT EXISTS `motivo_anulacion` varchar(200) DEFAULT NULL COMMENT 'Motivo obligatorio de la anulación';
