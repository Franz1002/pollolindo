<?php
class MenusModel extends Mysql
{
    public $intIdProducto;
    public $strNombre;
    public $strUsuario;
    public $intCategoria;
    public $strMarca;
    public $intPrecio;
    public $strDescripcion;
    public $imgProducto;
    public $strNombreMenu;
    public $pintPrecioMenu;
    public $strDetalleMenu;
    public $intPrecioMenu;
    public $imgMenu;


    public function __construct()
    {
        parent::__construct();
    }
    public function selectMenus()
    {
        $sql = "SELECT
    m.id_menu,
    m.nombre_menu,
    m.precio_menu,
    m.categoria_id,
    m.detalle_menu,
    m.img_menu,
    m.estado AS estado_menu,
    c.id_categoria,
    c.nombre_categoria,
    c.descripcion_categoria
FROM menu m
JOIN categoria c ON m.categoria_id = c.id_categoria";
        $request = $this->select_all($sql);
        return $request;
    }

    public function selectMenuById($id_menu)
    {
        // Cambiamos la consulta para usar el formato adecuado con la clase Mysql
        $sql = "SELECT * FROM menu WHERE id_menu = ?";

        // Usamos el método select1 ya que necesita pasar parámetros
        $arrData = $this->select1($sql, array($id_menu));

        // Devolver los resultados obtenidos
        return $arrData;
    }



    public function registerMenu(
        string $nombre_menu,
        float $precio_menu,
        int $categoria_id,
        string $detalle_menu,
        string $img_menu
    ) {
        $query = "INSERT INTO menu(nombre_menu, precio_menu, categoria_id, detalle_menu, img_menu, estado) VALUES (?, ?, ?, ?, ?, ?)";
        $arrData = array(
            $nombre_menu,
            $precio_menu,
            $categoria_id,
            $detalle_menu,
            $img_menu,
            1 // Estado activo por defecto
        );
        $request = $this->insert($query, $arrData);
        return $request;
    }

    public function updateMenu($id, $nombre, $precio, $categoria, $detalle, $img, $estado)
    {
        $sql = "UPDATE menu SET 
		nombre_menu = ?, 
		precio_menu = ?, 
		categoria_id = ?, 
		detalle_menu = ?, 
		img_menu = ?, 
		estado = ? 
		WHERE id_menu = ?";

        $arrData = array($nombre, $precio, $categoria, $detalle, $img, $estado, $id);
        $request = $this->update($sql, $arrData);
        return $request;
    }



    public function editProducto(
        int $id_producto,
        string $nombre_producto,
        int $categoria_id,
        string $marca_producto,
        float $precio_producto,
        string $descripcion,
        string $img_producto
    ) {

        $this->intIdProducto = $id_producto;
        $this->strNombre = $nombre_producto;
        $this->intCategoria = $categoria_id;
        $this->strMarca = $marca_producto;
        $this->intPrecio = $precio_producto;
        $this->strDescripcion = $descripcion;
        $this->imgProducto = $img_producto;

        $sql = "SELECT * FROM producto WHERE nombre_producto = '{$this->strNombre}' AND id_producto != $this->intIdProducto";
        $request = $this->select_all($sql);
        if (empty($request)) {
            $query = "UPDATE producto SET nombre_producto = ?, categoria_id = ?,
             marca_producto = ?, precio_producto = ?, descripcion = ?, img_producto = ? WHERE id_producto = $this->intIdProducto";
            $arrData = array(
                $this->strNombre,
                $this->intCategoria,
                $this->strMarca,
                $this->intPrecio,
                $this->strDescripcion,
                $this->imgProducto
            );
            $request = $this->update($query, $arrData);
        } else {
            $request = 0;
        }
        return $request;
    }

    public function deleteMenu($idMenu)
    {
        $sql = "DELETE FROM menu WHERE id_menu = ?";
        $arrData = array($idMenu);
        return $this->delete($sql, $arrData);
    }

    public function getSelectProductoPrecio($id_producto)
    {
        // Validar la entrada para evitar inyección SQL
        $intIdProducto = intval($id_producto);

        // Utilizar alias de tabla para hacer la consulta más legible
        $sql = "SELECT p.*, c.*, CONCAT(u.nombre, ' ', u.apellido) AS nombre_completo
            FROM producto AS p
            INNER JOIN categoria AS c ON p.categoria_id = c.id_categoria
            INNER JOIN usuario AS u ON p.usuario_id = u.id
            WHERE p.id_producto = $intIdProducto";

        // Realizar la consulta de manera segura utilizando métodos seguros de la clase que interactúa con la base de datos
        $arrData = $this->select($sql);

        return $arrData;
    }
}
