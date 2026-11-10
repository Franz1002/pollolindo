-- Registra los intentos fallidos de inicio de sesión.
-- Sirve para frenar a quien prueba contraseñas una y otra vez.
CREATE TABLE IF NOT EXISTS `intento_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cedula` varchar(30) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_cedula_fecha` (`cedula`, `fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;