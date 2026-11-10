<?php
class Conexion
{
	private static $pdo = null;

	// Una sola conexión compartida por todos los modelos durante la petición
	public static function obtener()
	{
		if (self::$pdo === null) {
			$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";" . DB_CHARSET;
			try {
				self::$pdo = new PDO($dsn, DB_USER, DB_PASSWORD, [
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				]);
			} catch (PDOException $e) {
				// El detalle va al log de PHP; al usuario solo un mensaje claro
				error_log('Error de conexión a la base de datos: ' . $e->getMessage());
				http_response_code(500);
				exit('No se pudo conectar a la base de datos. Revisa que MySQL esté encendido en XAMPP.');
			}
		}
		return self::$pdo;
	}

	public function conect()
	{
		return self::obtener();
	}
}