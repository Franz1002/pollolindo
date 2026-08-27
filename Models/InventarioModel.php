<?php
class InventarioModel extends Mysql
{
    public $intIdProducto;
    public $strNombre;
    public $strUsuario;
    public $intCategoria;
    public $strMarca;
    public $intPrecio;
    public $strDescripcion;
    public $imgProducto;
    public function __construct()
    {
        parent::__construct();
    }



    public function selectAgotados()
    {
        $sql = "SELECT
        id_insumo,
        nombre_insumo AS producto,
        tipo_insumo,
        unidad_medida,
        stock_actual,
        'Agotado' AS estado,
        'agotado' AS estado_css
    FROM insumo
    WHERE estado = 1 AND stock_actual <= 0
    ORDER BY nombre_insumo ASC;";

        $request = $this->select_all($sql);
        return $request;
    }

    public function selectPorAgotar()
    {
        $sql = "SELECT
        id_insumo,
        nombre_insumo AS producto,
        tipo_insumo,
        unidad_medida,
        stock_actual,
        'Poca cantidad' AS estado,
        'poca_cantidad' AS estado_css
    FROM insumo
    WHERE estado = 1 AND stock_actual > 0 AND stock_actual < 10
    ORDER BY nombre_insumo ASC;";

        $request = $this->select_all($sql);
        return $request;
    }

    
}
