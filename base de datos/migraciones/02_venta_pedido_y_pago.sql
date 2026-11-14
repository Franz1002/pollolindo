-- Prepara la base para la nueva pantalla Vender:
-- tipo de pedido (mesa o para llevar), número de mesa, nota para cocina y forma de pago.
-- Solo AGREGA columnas y una tabla: no borra ni modifica ninguna venta existente.
-- Se puede correr más de una vez sin dañar nada (usa IF NOT EXISTS).
-- Antes de correrla: respaldar la base `pollo`.

-- 1) Datos del pedido y del pago en cada venta.
--    Las ventas anteriores quedan con tipo_pedido y mesa vacíos (no se registraban)
--    y con metodo_pago 'efectivo', que es como el sistema las contaba hasta ahora.
ALTER TABLE `venta`
  ADD COLUMN IF NOT EXISTS `caja_id` int(11) DEFAULT NULL COMMENT 'Caja abierta en la que se cobró',
  ADD COLUMN IF NOT EXISTS `tipo_pedido` enum('mesa','llevar') DEFAULT NULL COMMENT 'NULL en ventas anteriores a este cambio',
  ADD COLUMN IF NOT EXISTS `mesa` tinyint(3) UNSIGNED DEFAULT NULL COMMENT 'Número de mesa; NULL si es para llevar',
  ADD COLUMN IF NOT EXISTS `nota_pedido` varchar(120) DEFAULT NULL COMMENT 'Nota para cocina, por ejemplo la presa',
  ADD COLUMN IF NOT EXISTS `metodo_pago` enum('efectivo','qr','mixto') NOT NULL DEFAULT 'efectivo',
  ADD COLUMN IF NOT EXISTS `monto_efectivo` decimal(10,2) DEFAULT NULL COMMENT 'Parte cobrada en efectivo',
  ADD COLUMN IF NOT EXISTS `monto_qr` decimal(10,2) DEFAULT NULL COMMENT 'Parte cobrada por QR',
  ADD INDEX IF NOT EXISTS `idx_venta_caja` (`caja_id`);

-- 2) Precio al que se vendió cada producto.
--    Así un cambio de precio en el futuro no altera las ventas ya hechas.
ALTER TABLE `detalle_venta`
  ADD COLUMN IF NOT EXISTS `precio_unitario` decimal(10,2) DEFAULT NULL COMMENT 'Precio del producto al momento de la venta';

-- 3) Ajustes que el dueño puede cambiar sin tocar el código.
CREATE TABLE IF NOT EXISTS `configuracion` (
  `clave` varchar(50) NOT NULL,
  `valor` varchar(255) NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Cantidad de mesas del local. INSERT IGNORE no pisa el valor si ya existe.
INSERT IGNORE INTO `configuracion` (`clave`, `valor`, `descripcion`) VALUES
  ('numero_mesas', '15', 'Cantidad de mesas que se muestran en Vender');
