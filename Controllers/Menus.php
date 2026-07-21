<?php
class Menus extends Controllers
{
	public $views;
	public $model;

	public function __construct()
	{

		parent::__construct();
		session_start();
		if (empty($_SESSION['login'])) {
			header('location: ' . base_url());
		}
	}

	public function menus()
	{
		//if (!empty($_SESSION['userData']['tipo'] == 1)) {

		$data['title'] = "Menu";
		$data['page_functions_js'] = "functions_menus.js";
		$this->views->getView($this, "menus", $data);
		//	} else {
		//		header('location: ' . base_url());
		//}
	}

	public function getMenus()
	{
		$arrData = $this->model->selectMenus();

		// Ruta base de las imágenes
		$baseImageUrl = base_url() . "/Assets/images/imgMenu/";

		for ($i = 0; $i < count($arrData); $i++) {
			// Agregar la URL completa de la imagen
			$arrData[$i]['img_menu'] = $baseImageUrl . $arrData[$i]['img_menu'];

			// Construcción de botones
			$btnView = '<button class="btn btn-outline-info btn-rounded btn-icon" onClick="viewMenu(' . $arrData[$i]['id_menu'] . ')" title="Ver"><i class="mdi mdi-eye"></i></button>';
			$btnEdit = '<button class="btn btn-outline-success btn-rounded btn-icon" onClick="editMenu(' . $arrData[$i]['id_menu'] . ')" title="Editar"><i class="mdi mdi-pencil"></i></button>';
			$btnDelete = '<button class="btn btn-outline-danger btn-rounded btn-icon" onClick="deletMenu(' . $arrData[$i]['id_menu'] . ')" title="Eliminar"><i class="mdi mdi-delete"></i></button>';

			// Agrega las opciones al array
			$arrData[$i]['options'] = '<div class="text-center">' . $btnView . ' ' . $btnEdit . ' ' . $btnDelete . '</div>';
		}

		echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
		die();
	}


	public function getMenu($id_menu)
	{
		$id_menu = intval($id_menu);
		if ($id_menu > 0) {
			$arrData = $this->model->selectMenuById($id_menu);
			if ($arrData) {
				$arrResponse = array("status" => true, "data" => $arrData);
			} else {
				$arrResponse = array("status" => false, "msg" => "Datos no encontrados");
			}
			echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		}
		die();
	}



	public function getProductosDisponibles()
	{

		$arrData = $this->model->selectProductosDisponibles();
		echo json_encode($arrData, JSON_UNESCAPED_UNICODE);

		die();
	}
	public function getProductosAgotados()
	{

		$arrData = $this->model->selectProductosAgotados();
		echo json_encode($arrData, JSON_UNESCAPED_UNICODE);

		die();
	}


	public function registrarMenu()
	{
		// Obtener los datos del formulario
		$strNombreMenu = $_POST['nombre_menu'];
		$floatPrecioMenu = floatval($_POST['precio_menu']);
		$intCategoriaId = intval($_POST['categoria_id']);
		$strDetalleMenu = $_POST['detalle_menu'];

		// Manejo de la imagen
		$img_menu = 'defect.png'; // Imagen predeterminada
		if (isset($_FILES['img_menu']) && $_FILES['img_menu']['error'] === UPLOAD_ERR_OK) {
			$nombreImagen = time() . '_' . $_FILES['img_menu']['name']; // Evitar nombres repetidos
			$rutaDestino = "C:/xampp/htdocs/pollo/Assets/images/imgMenu/" . $nombreImagen;

			// Mover el archivo a la carpeta de imágenes
			if (move_uploaded_file($_FILES['img_menu']['tmp_name'], $rutaDestino)) {
				$img_menu = $nombreImagen; // Solo actualiza si se movió correctamente
			}
		}

		// Llamar al modelo para registrar el menú
		$request_menu = $this->model->registerMenu(
			$strNombreMenu,
			$floatPrecioMenu,
			$intCategoriaId,
			$strDetalleMenu,
			$img_menu // Ahora contiene la imagen correcta
		);

		if ($request_menu > 0) {
			$arrResponse = array('status' => true, 'msg' => 'Menú registrado correctamente.');
		} else {
			$arrResponse = array('status' => false, 'msg' => '¡Atención! Hubo un error al registrar el menú.');
		}

		echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		die();
	}

	public function actualizarMenu()
	{
		if ($_POST) {
			$intIdMenu = intval($_POST['idMenu']);
			$strNombreMenu = $_POST['nombre_menu'];
			$floatPrecioMenu = floatval($_POST['precio_menu']);
			$intCategoriaId = intval($_POST['categoria_id']);
			$strDetalleMenu = $_POST['detalle_menu'];
			$strEstado = intval($_POST['estado']);

			// Verifica si se subió una nueva imagen
			if (isset($_FILES['img_menu']) && $_FILES['img_menu']['error'] === UPLOAD_ERR_OK) {
				$nombreImagen = time() . '_' . $_FILES['img_menu']['name'];
				$rutaDestino = "C:/xampp/htdocs/pollo/Assets/images/imgMenu/" . $nombreImagen;

				if (move_uploaded_file($_FILES['img_menu']['tmp_name'], $rutaDestino)) {
					$img_menu = $nombreImagen;
				} else {
					$img_menu = $_POST['imgActual']; // Si no se pudo subir, usa la actual
				}
			} else {
				$img_menu = $_POST['imgActual']; // No se subió imagen nueva
			}

			// Llamada al modelo para actualizar
			$request = $this->model->updateMenu(
				$intIdMenu,
				$strNombreMenu,
				$floatPrecioMenu,
				$intCategoriaId,
				$strDetalleMenu,
				$img_menu,
				$strEstado
			);

			if ($request) {
				$arrResponse = array('status' => true, 'msg' => 'Menú actualizado correctamente.');
			} else {
				$arrResponse = array('status' => false, 'msg' => 'No se pudo actualizar el menú.');
			}

			echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		}
		die();
	}


	public function deleteMenu()
	{
		if ($_POST) {
			$intIdMenu = intval($_POST['id_menu']);
			$requestDel = $this->model->deleteMenu($intIdMenu);
			if ($requestDel) {
				$arrResponse = array('status' => true, 'msg' => 'Menú eliminado correctamente.');
			} else {
				$arrResponse = array('status' => false, 'msg' => 'No se pudo eliminar el menú.');
			}
			echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		}
		die();
	}



	public function obtenerPrecioProducto($id_producto)
	{
		$idProducto = intval($id_producto);

		if ($idProducto > 0) {
			$arrData = $this->model->getSelectProductoPrecio($idProducto);

			if (empty($arrData)) {
				$arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
			} else {
				$arrResponse = array('status' => true, 'data' => $arrData);
			}
		} else {
			$arrResponse = array('status' => false, 'msg' => 'ID de producto no válido.');
		}

		echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		// Considera alternativas a die(), dependiendo de la estructura de tu aplicación.
		die();
	}
}
