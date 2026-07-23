<?php


class Categorias extends Controllers
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

	public function categorias()
	{
		$this->esAdmin();
		//if (!empty($_SESSION['userData']['tipo'] == 1))
		// {
		$data['title'] = "Categorias";
		$data['page_functions_js'] = "functions_categorias.js";
		$this->views->getView($this, "categorias", $data);
	}


	public function getCategorias()
	{

		$arrData = $this->model->selectCategorias();

		// Recorre los datos para preparar las opciones y codificar correctamente
		for ($i = 0; $i < count($arrData); $i++) {
			// Construcción de botones
			$btnView = '<button class="btn btn-outline-info btn-rounded btn-icon" onClick="viewCategory(' . $arrData[$i]['id_categoria'] . ')" title="Ver"><i class="mdi mdi-eye"></i></button>';
			$btnEdit = '<button class="btn btn-outline-success btn-rounded btn-icon" onClick="editCategory(' . $arrData[$i]['id_categoria'] . ')" title="Editar"><i class="mdi mdi-pencil"></i></button>';
			$btnDelete = '<button class="btn btn-outline-danger btn-rounded btn-icon" onClick="deletCategory(' . $arrData[$i]['id_categoria'] . ')" title="Eliminar"><i class="mdi mdi-delete"></i></button>';

			// Agrega las opciones al array
			$arrData[$i]['options'] = '<div class="text-center">' . $btnView . ' ' . $btnEdit . ' ' . $btnDelete . '</div>';
		}

		// Envía los datos como JSON
		echo json_encode($arrData, JSON_UNESCAPED_UNICODE);

		die();
	}


	public function getSelectCategorias()
	{
		$htmlOptions = "<option value='' disabled selected>Seleccione una categoría</option>";  // Agregar una opción predeterminada.
		$arrData = $this->model->selectCategorias();  // Se asume que selectCategorias() obtiene las categorías desde la base de datos.

		if (count($arrData) > 0) {
			foreach ($arrData as $row) {
				if ($row['id_categoria'] > 0) {
					$htmlOptions .= '<option value="' . $row['id_categoria'] . '">' . $row['nombre_categoria'] . '</option>';
				}
			}
		}

		echo $htmlOptions;  // Envía el HTML de las opciones al frontend.
		die();  // Finaliza la ejecución del script.
	}

	public function getCategoria($id_categoria)
	{

		$idCategoria = intval($id_categoria);
		if ($id_categoria > 0) {
			$arrData = $this->model->getSelectCategoria($idCategoria);
			if (empty($arrData)) {
				$arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
			} else {
				$arrResponse = array('status' => true, 'data' => $arrData);
			}
			echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		}

		die();
	}

	public function registrarCategoria()
	{
		$this->esAdmin();
		$strNombre = convertirPalabrasAMayuscula($_POST['nombre']);
		$strDescripcion = $_POST['descripcion'];

		// Solo realizar el registro de la categoría (sin editar)
		$request_categoria = $this->model->registerCategoria($strNombre, $strDescripcion);

		if ($request_categoria > 0) {
			$arrResponse = array('status' => true, 'msg' => 'Categoría registrada correctamente.');
		} else if ($request_categoria == 0) {
			$arrResponse = array('status' => false, 'msg' => '¡Atención! Ya existe una categoría con ese nombre.');
		} else {
			$arrResponse = array('status' => false, 'msg' => 'Error al registrar la categoría.');
		}

		echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		die();
	}
	public function editCategoria()
	{
		$this->esAdmin();
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$idCategoria = intval($_POST['id_categoria']);
			$nombre = trim($_POST['nombre']);
			$descripcion = trim($_POST['descripcion']);

			// Validar que los campos no estén vacíos
			if ($idCategoria <= 0 || empty($nombre)) {
				$arrResponse = array("status" => false, "msg" => "Todos los campos son obligatorios.");
			} else {
				// Llamar al modelo para actualizar la categoría
				$update = $this->model->updateCategoria($idCategoria, $nombre, $descripcion);

				if ($update) {
					$arrResponse = array("status" => true, "msg" => "Categoría actualizada correctamente.");
				} else {
					$arrResponse = array("status" => false, "msg" => "Error al actualizar la categoría.");
				}
			}

			echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		}
		die();
	}



	public function deleteCategoria()
	{
		$this->esAdmin();
		if ($_POST) {
			$intIdCategoria = intval($_POST['idCategoria']);
			$requestDel = $this->model->deletCategoria($intIdCategoria);
			if ($requestDel) {
				$arrResponse = array('status' => true, 'msg' => 'Se ha eliminado la Categoría');
			} else {
				$arrResponse = array('status' => false, 'msg' => 'Error al eliminar la Categoría.');
			}
			echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		}
		die();
	}
}
