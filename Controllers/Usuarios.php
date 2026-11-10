<?php

class Usuarios extends Controllers
{
	public $views;
	public $model;

	public function __construct()
	{
		parent::__construct();
	}

	public function usuarios()
	{
		$this->esAdmin();
		$data['title'] = "Usuarios";
		$data['page_functions_js'] = "functions_usuarios.js";
		$this->views->getView($this, "usuarios", $data);
	}

	public function getSelectCargos()
	{
		$this->esAdmin();
		$htmlOptions = "<option value='' disabled selected>Seleccione un cargo</option>";
		$arrData = $this->model->selectCargos();
		foreach ($arrData as $row) {
			$htmlOptions .= '<option value="' . intval($row['id_tipo']) . '">' . htmlspecialchars($row['nombre_tipo']) . '</option>';
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
		$idUsuario = intval($id);
		if ($idUsuario > 0) {
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
		$arrData = $this->model->selectUsuarios();

		for ($i = 0; $i < count($arrData); $i++) {
			if ($arrData[$i]['estado'] == 1) {
				$arrData[$i]['estado'] = '<span class="badge badge-outline-success">Activo</span>';
			} else {
				$arrData[$i]['estado'] = '<span class="badge badge-outline-danger">Inactivo</span>';
			}

			$cargo = $this->model->selectCargosTipo($arrData[$i]['tipo']);
			$arrData[$i]['tipo'] = $cargo['nombre_tipo'] ?? '';

			$id = intval($arrData[$i]['id']);
			$btnView = '<button class="btn btn-outline-info btn-rounded btn-icon" onClick="viewUser(' . $id . ')" title="Ver"><i class="mdi mdi-eye"></i></button>';
			$btnEdit = '<button class="btn btn-outline-success btn-rounded btn-icon" onClick="editUser(' . $id . ')" title="Editar"><i class="mdi mdi-pencil"></i></button>';
			$btnDelete = '<button class="btn btn-outline-danger btn-rounded btn-icon" onClick="deletUser(' . $id . ')" title="Eliminar"><i class="mdi mdi-delete"></i></button>';
			$arrData[$i]['options'] = '<div class="text-center">' . $btnView . ' ' . $btnEdit . ' ' . $btnDelete . '</div>';
		}

		echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
		die();
	}

	// Registra un usuario nuevo o actualiza uno existente (si llega idUsuario)
	public function registrarUsuario()
	{
		$this->esAdmin();
		if (!$_POST) {
			die();
		}

		$idUsuario = intval($_POST['idUsuario'] ?? 0);
		$strNombres = trim($_POST['nombres'] ?? '');
		$strApellidos = trim($_POST['apellidos'] ?? '');
		$strEmail = trim($_POST['email'] ?? '');
		$strPassword = $_POST['password'] ?? '';
		$strCedula = trim($_POST['ci'] ?? '');
		$intCelular = intval($_POST['celular'] ?? 0);
		$intTipo = intval($_POST['cargo'] ?? 0);
		$intEstado = intval($_POST['estado'] ?? 1);

		$error = '';
		if ($strNombres === '' || $strApellidos === '' || $strCedula === '' || $strEmail === '' || $intTipo <= 0) {
			$error = 'Completa todos los campos obligatorios.';
		} elseif (!filter_var($strEmail, FILTER_VALIDATE_EMAIL)) {
			$error = 'El correo electrónico no es válido.';
		} elseif ($idUsuario === 0 && strlen($strPassword) < 6) {
			$error = 'La contraseña debe tener al menos 6 caracteres.';
		} elseif ($idUsuario > 0 && $strPassword !== '' && strlen($strPassword) < 6) {
			$error = 'La nueva contraseña debe tener al menos 6 caracteres.';
		} elseif ($idUsuario > 0 && $idUsuario == $_SESSION['idUsuario']['id'] && ($intTipo != 1 || $intEstado != 1)) {
			$error = 'No puedes quitarte el rol de administrador ni desactivar tu propio usuario.';
		}

		if ($error !== '') {
			echo json_encode(['status' => false, 'msg' => $error], JSON_UNESCAPED_UNICODE);
			die();
		}

		$strNombres = convertirPalabrasAMayuscula($strNombres);
		$strApellidos = convertirPalabrasAMayuscula($strApellidos);
		$hash = $strPassword !== '' ? password_hash($strPassword, PASSWORD_BCRYPT) : null;

		if ($idUsuario === 0) {
			$request = $this->model->registerUsuario($strNombres, $strApellidos, $strEmail, $hash, $strCedula, $intCelular, $intTipo, $intEstado);
			$msgOk = 'Usuario registrado correctamente.';
		} else {
			$request = $this->model->editUsuario($idUsuario, $strNombres, $strApellidos, $strEmail, $hash, $strCedula, $intCelular, $intTipo, $intEstado);
			$msgOk = 'Usuario actualizado correctamente.';
		}

		if ($request) {
			$arrResponse = array('status' => true, 'msg' => $msgOk);
		} else {
			$arrResponse = array('status' => false, 'msg' => 'Ya existe otro usuario con esa cédula o ese correo.');
		}
		echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		die();
	}

	public function deleteUsuario()
	{
		$this->esAdmin();
		if ($_POST) {
			$intIdUsuario = intval($_POST['idUsuario']);
			if ($intIdUsuario == $_SESSION['idUsuario']['id']) {
				$arrResponse = array('status' => false, 'msg' => 'No puedes dar de baja tu propio usuario.');
			} elseif ($this->model->deletUsuario($intIdUsuario)) {
				$arrResponse = array('status' => true, 'msg' => 'Usuario dado de baja. Ya no podrá entrar al sistema.');
			} else {
				$arrResponse = array('status' => false, 'msg' => 'No se pudo dar de baja al usuario.');
			}
			echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		}
		die();
	}
}
