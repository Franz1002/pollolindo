<?php

class Mysql extends Conexion
{
	private $conexion;
	private $strquery;
	private $arrValues;

	function __construct()
	{
		$this->conexion = new Conexion();
		$this->conexion = $this->conexion->conect();
	}

	//Insertar un registro
	public function insert(string $query, array $arrValues)
	{
		$this->strquery = $query;
		$this->arrVAlues = $arrValues;
		$insert = $this->conexion->prepare($this->strquery);
		$resInsert = $insert->execute($this->arrVAlues);
		if ($resInsert) {
			$lastInsert = $this->conexion->lastInsertId();
		} else {
			$lastInsert = 0;
		}
		return $lastInsert;
	}
	//Busca un registro
	public function select(string $query)
	{
		$this->strquery = $query;
		$result = $this->conexion->prepare($this->strquery);
		$result->execute();
		$data = $result->fetch(PDO::FETCH_ASSOC);
		return $data;
	}



	public function select1($query, $params = [])
	{
		$stmt = $this->conexion->prepare($query);  // Cambié $this->pdo por $this->conexion

		// Ejecutar la consulta pasando los parámetros correctamente
		$stmt->execute($params);

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function select2($sql)
	{
		// Ejecutar la consulta directamente en la base de datos
		$result = $this->conexion->query($sql); // Usando la conexión que ya tienes

		if ($result) {
			return $result->fetch_assoc(); // Retorna los resultados como un array asociativo
		} else {
			return null; // Si la consulta falla, retorna null
		}
	}





	//Devuelve todos los registros
	public function select_all(string $query)
	{
		$this->strquery = $query;
		$result = $this->conexion->prepare($this->strquery);
		$result->execute();
		$data = $result->fetchall(PDO::FETCH_ASSOC);
		return $data;
	}
	//Actualiza registros
	public function update(string $query, array $arrValues)
	{
		$this->strquery = $query;
		$this->arrVAlues = $arrValues;
		$update = $this->conexion->prepare($this->strquery);
		$resExecute = $update->execute($this->arrVAlues);
		return $resExecute;
	}
	//Eliminar un registros
	public function delete(string $query)
	{
		$this->strquery = $query;
		$result = $this->conexion->prepare($this->strquery);
		$del = $result->execute();
		return $del;
	}
}
