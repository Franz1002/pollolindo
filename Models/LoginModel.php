<?php
class LoginModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getUsuario(string $user)
    {
        $sql = "SELECT id, tipo, password, estado FROM usuario WHERE cedula = ?";
        $data = $this->select($sql, [$user]);
        return $data ?: null;
    }

    // Datos que quedan en la sesión: sin la contraseña
    public function sessionLogin(int $idusuario)
    {
        $sql = "SELECT u.id, u.nombre, u.apellido, u.email, u.tipo, u.estado, t.nombre_tipo
                FROM usuario u
                INNER JOIN tipo t ON u.tipo = t.id_tipo
                WHERE u.id = ?";
        return $this->select($sql, [$idusuario]);
    }

    // Devuelve cuántos minutos faltan para poder intentar de nuevo (0 = puede intentar ya)
    public function minutosDeBloqueo(string $cedula)
    {
        $desde = date('Y-m-d H:i:s', time() - MINUTOS_BLOQUEO * 60);
        $sql = "SELECT COUNT(*) AS total, MIN(fecha) AS primero
                FROM intento_login
                WHERE cedula = ? AND fecha >= ?";
        $fila = $this->select($sql, [$cedula, $desde]);
        if ((int)$fila['total'] < MAX_INTENTOS_LOGIN) {
            return 0;
        }
        $libreDesde = strtotime($fila['primero']) + MINUTOS_BLOQUEO * 60;
        return max(1, (int)ceil(($libreDesde - time()) / 60));
    }

    public function registrarIntentoFallido(string $cedula, string $ip)
    {
        // Los intentos de hace más de 30 días ya no sirven
        $this->delete("DELETE FROM intento_login WHERE fecha < ?", [date('Y-m-d H:i:s', strtotime('-30 days'))]);

        $sql = "INSERT INTO intento_login (cedula, ip, fecha) VALUES (?, ?, ?)";
        return $this->insert($sql, [substr($cedula, 0, 30), substr($ip, 0, 45), date('Y-m-d H:i:s')]);
    }

    public function limpiarIntentos(string $cedula)
    {
        return $this->delete("DELETE FROM intento_login WHERE cedula = ?", [$cedula]);
    }
}