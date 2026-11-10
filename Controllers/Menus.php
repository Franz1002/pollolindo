<?php
class Menus extends Controllers
{
	public function __construct()
	{
		parent::__construct();
	}

	public function menus()
	{
		$this->esAdmin();
		$data['title'] = "Menu";
		$data['page_functions_js'] = "functions_menus.js";
		$this->views->getView($this, "menus", $data);
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

	public function registrarMenu()
	{
		$this->esAdmin();
		if (!$_POST) {
			die();
		}

		$strNombreMenu = trim($_POST['nombre_menu'] ?? '');
		$floatPrecioMenu = floatval($_POST['precio_menu'] ?? 0);
		$intCategoriaId = intval($_POST['categoria_id'] ?? 0);
		$strDetalleMenu = trim($_POST['detalle_menu'] ?? '');

		if ($strNombreMenu === '' || $floatPrecioMenu <= 0 || $intCategoriaId <= 0) {
			$this->responder(false, 'Completa el nombre, un precio mayor a 0 y la categoría.');
		}

		// Imagen opcional: si se envió, tiene que ser válida
		$img_menu = 'defect.png';
		if ($this->seEnvioImagen()) {
			$img_menu = $this->guardarImagen($_FILES['img_menu']);
			if ($img_menu === null) {
				$this->responder(false, 'La imagen debe ser JPG, PNG o WEBP y pesar como máximo 2 MB.');
			}
		}

		$request_menu = $this->model->registerMenu($strNombreMenu, $floatPrecioMenu, $intCategoriaId, $strDetalleMenu, $img_menu);
		if ($request_menu > 0) {
			$this->responder(true, 'Menú registrado correctamente.');
		}
		$this->borrarImagen($img_menu);
		$this->responder(false, '¡Atención! Hubo un error al registrar el menú.');
	}

	public function actualizarMenu()
	{
		$this->esAdmin();
		if (!$_POST) {
			die();
		}

		$intIdMenu = intval($_POST['idMenu'] ?? 0);
		$strNombreMenu = trim($_POST['nombre_menu'] ?? '');
		$floatPrecioMenu = floatval($_POST['precio_menu'] ?? 0);
		$intCategoriaId = intval($_POST['categoria_id'] ?? 0);
		$strDetalleMenu = trim($_POST['detalle_menu'] ?? '');
		$intEstado = intval($_POST['estado'] ?? 1);

		// La imagen actual se toma de la base de datos, no del formulario
		$actual = $this->model->selectMenuById($intIdMenu);
		if (empty($actual)) {
			$this->responder(false, 'El producto no existe.');
		}
		if ($strNombreMenu === '' || $floatPrecioMenu <= 0 || $intCategoriaId <= 0) {
			$this->responder(false, 'Completa el nombre, un precio mayor a 0 y la categoría.');
		}

		$imgAnterior = $actual[0]['img_menu'];
		$img_menu = $imgAnterior;
		if ($this->seEnvioImagen()) {
			$img_menu = $this->guardarImagen($_FILES['img_menu']);
			if ($img_menu === null) {
				$this->responder(false, 'La imagen debe ser JPG, PNG o WEBP y pesar como máximo 2 MB.');
			}
		}

		$request = $this->model->updateMenu($intIdMenu, $strNombreMenu, $floatPrecioMenu, $intCategoriaId, $strDetalleMenu, $img_menu, $intEstado);
		if ($request) {
			// Si se cambió la imagen, la anterior ya no sirve
			if ($img_menu !== $imgAnterior) {
				$this->borrarImagen($imgAnterior);
			}
			$this->responder(true, 'Menú actualizado correctamente.');
		}
		if ($img_menu !== $imgAnterior) {
			$this->borrarImagen($img_menu);
		}
		$this->responder(false, 'No se pudo actualizar el menú.');
	}

	public function deleteMenu()
	{
		$this->esAdmin();
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

	private function seEnvioImagen()
	{
		return isset($_FILES['img_menu']) && $_FILES['img_menu']['error'] !== UPLOAD_ERR_NO_FILE;
	}

	// Guarda la imagen con un nombre aleatorio y devuelve ese nombre, o null si no es válida
	private function guardarImagen(array $archivo)
	{
		if ($archivo['error'] !== UPLOAD_ERR_OK || $archivo['size'] > 2 * 1024 * 1024) {
			return null;
		}

		// Se revisa el contenido real del archivo, no el nombre ni lo que dice el navegador
		$tiposPermitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
		$info = @getimagesize($archivo['tmp_name']);
		if ($info === false || !isset($tiposPermitidos[$info['mime']])) {
			return null;
		}
		$tipo = $info['mime'];

		$nombre = time() . '_' . bin2hex(random_bytes(6)) . '.' . $tiposPermitidos[$tipo];
		$destino = __DIR__ . '/../Assets/images/imgMenu/' . $nombre;
		return move_uploaded_file($archivo['tmp_name'], $destino) ? $nombre : null;
	}

	private function borrarImagen(string $nombre)
	{
		if ($nombre === '' || $nombre === 'defect.png') {
			return;
		}
		$ruta = __DIR__ . '/../Assets/images/imgMenu/' . basename($nombre);
		if (is_file($ruta)) {
			unlink($ruta);
		}
	}

	private function responder(bool $status, string $msg)
	{
		echo json_encode(['status' => $status, 'msg' => $msg], JSON_UNESCAPED_UNICODE);
		die();
	}
}