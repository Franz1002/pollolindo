<?php
// Ajustes del negocio que el dueño cambia sin tocar el código (tabla `configuracion`)
class ConfiguracionModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    // Devuelve el valor guardado, o $porDefecto si la clave todavía no existe
    public function leerValor(string $clave, string $porDefecto = '')
    {
        $fila = $this->select("SELECT valor FROM configuracion WHERE clave = ?", [$clave]);
        return $fila ? $fila['valor'] : $porDefecto;
    }

    // Crea la clave o actualiza su valor
    public function guardarValor(string $clave, string $valor)
    {
        $sql = "INSERT INTO configuracion (clave, valor) VALUES (?, ?)
                ON DUPLICATE KEY UPDATE valor = VALUES(valor)";
        return $this->update($sql, [$clave, $valor]);
    }
}
