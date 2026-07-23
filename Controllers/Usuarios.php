<?php

class Usuarios extends Controllers
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
	public function usuarios()
	{
		$this->esAdmin();
		$data['title'] = "Usuarios";
		$data['page_functions_js'] = "functions_usuarios.js";
		$this->views->getView($this, "usuarios", $data);
		//} else {
		//header('location: ' . base_url());
		//}
	}

	public function getSelectCargos()
	{
		$this->esAdmin();
		$htmlOptions = "<option value='' disabled selected>Seleccione un cargo</option>";  // Agregar una opción predeterminada.
		$arrData = $this->model->selectCargos();
		if (count($arrData) > 0) {
			foreach ($arrData as $row) {
				if ($row['id_tipo'] > 0) {
					$htmlOptions .= '<option value="' . $row['id_tipo'] . '">' . $row['nombre_tipo'] . '</option>';
				}
			}
		}
		echo $htmlOptions;
		die();
	}


	public function getUsuarioId()
	{
		echo json_encode(['usuario_id' => isset($_SESSION['idUsuario']) ? $_SESSION['idUsuario'] : null]);
	}

	public function getUsuario($id)
	{
		$this->esAdmin();
		//header('Content-Type: application/json; charset=UTF-8');
		$idUsuario = intval($id);
		if ($id > 0) {
			$arrData = $this->model->getSelectUsuario($idUsuario);
			if (empty($arrData)) {
				$arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
			} else {
				$arrResponse = array('status' => true, 'data' => $arrData);
			}
			echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		}

		die();
	}
	public function getUsuarios()
	{
		$this->esAdmin();
		$arrData = $this->model->selectUsuarios(); // Obtener los usuarios

		// Recorremos los usuarios y modificamos los datos
		// Modifica este bloque dentro de tu método getUsuarios
		for ($i = 0; $i < count($arrData); $i++) {
			// Estado
			if ($arrData[$i]['estado'] == 1) {
				$arrData[$i]['estado'] = '<span class="badge badge-outline-success">Activo</span>';
			} else {
				$arrData[$i]['estado'] = '<span class="badge badge-outline-danger">Inactivo</span>';
			}

			// Tipo de usuario: ahora se extrae el nombre según el id_tipo
			$arrData[$i]['tipo'] = $this->model->selectCargosTipo($arrData[$i]['tipo']);
			// Asegurémonos de que tipo solo contenga el nombre del tipo
			$arrData[$i]['tipo'] = $arrData[$i]['tipo']['nombre_tipo'];  // Aquí extraemos el nombre_tipo

			// Botones de acción
			$btnView = '<button class="btn btn-outline-info btn-rounded btn-icon btnViewUsuario" onClick="viewUser(' . $arrData[$i]['id'] . ')" title="Ver"><i class="mdi mdi-eye"></i></button>';
			$btnEdit = '<button class="btn btn-outline-success btn-rounded btn-icon btnEditUsuario" onClick="editUser(' . $arrData[$i]['id'] . ')" title="Editar"><i class="mdi mdi-pencil"></i></button>';
			$btnDelete = '<button class="btn btn-outline-danger btn-rounded btn-icon btnDelUsuario" onClick="deletUser(' . $arrData[$i]['id'] . ')" title="Eliminar"><i class="mdi mdi-delete"></i></button>';

			// Opciones
			$arrData[$i]['options'] = '<div class="text-center">' . $btnView . ' ' . $btnEdit . ' ' . $btnDelete . '</div>';
		}

		// Enviar la respuesta en formato JSON
		echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
		die();
	}




	public function registrarUsuario()
	{
		$this->esAdmin();
		$strNombres = convertirPalabrasAMayuscula($_POST['nombres']);
		$strApellidos = convertirPalabrasAMayuscula($_POST['apellidos']);
		$strEmail = $_POST['email'];
		$strPassword = password_hash($_POST['password'], PASSWORD_BCRYPT);
		$strCedula = $_POST['ci'];
		$intCelular = intval($_POST['celular']);
		$intTipoUsuario = $_POST['cargo'];
		$intEstado = $_POST['estado'];

		// Llamamos a la función para registrar el usuario
		$request_usuario = $this->model->registerUsuario(
			$strNombres,
			$strApellidos,
			$strEmail,
			$strPassword,
			$strCedula,
			$intCelular,
			$intTipoUsuario,
			$intEstado
		);

		if ($request_usuario > 0) {
			$arrResponse = array('status' => true, 'msg' => 'Usuario registrado correctamente.');
		} else {
			$arrResponse = array('status' => false, 'msg' => '¡Atención! El usuario ya existe o hubo un error al registrar.');
		}

		echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		die();
	}


	public function deleteUsuario()
	{
		$this->esAdmin();
		if ($_POST) {
			$intIdUsuario = intval($_POST['idUsuario']);
			$requestDel = $this->model->deletUsuario($intIdUsuario);
			if ($requestDel) {
				$arrResponse = array('status' => true, 'msg' => 'Se ha eliminado');
			} else {
				$arrResponse = array('status' => false, 'msg' => 'Error al eliminar');
			}
			echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		}
		die();
	}
}
