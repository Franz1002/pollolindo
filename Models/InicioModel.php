<?php
class InicioModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function selectVentaDiarioGrafico()
    {
        $sql = "SELECT
        DATE_FORMAT(fecha_venta, '%d-%b-%y') AS FECHA,
        DATE_FORMAT(fecha_venta, '%W') AS DIA,
        SUM(total_venta) AS VENTA,
        CONCAT(usuario.nombre,' ', usuario.apellido) AS USUARIO
    FROM
        venta
    INNER JOIN
        usuario ON venta.usuario_id = usuario.id
    WHERE
        MONTH(fecha_venta) = MONTH(CURRENT_DATE())
    GROUP BY
        FECHA, DIA, usuario.nombre
    ORDER BY
        fecha_venta ASC
   ";
        $request = $this->select_all($sql);
        return $request;
    }
}
