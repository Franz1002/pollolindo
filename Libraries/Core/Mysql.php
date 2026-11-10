<?php

class Mysql extends Conexion
{
	private $conexion;

	function __construct()
	{
		$this->conexion = Conexion::obtener();
	}

	// Inserta un registro y devuelve su id
	public function insert(string $query, array $arrValues)
	{
		$stmt = $this->conexion->prepare($query);
		$ok = $stmt->execute($arrValues);
		return $ok ? $this->conexion->lastInsertId() : 0;
	}

	// Devuelve un solo registro (o false si no hay)
	public function select(string $query, array $arrValues = [])
	{
		$stmt = $this->conexion->prepare($query);
		$stmt->execute($arrValues);
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	// Devuelve todos los registros
	public function select_all(string $query, array $arrValues = [])
	{
		$stmt = $this->conexion->prepare($query);
		$stmt->execute($arrValues);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	// Igual que select_all; se mantiene porque varios modelos ya lo usan
	public function select1(string $query, array $arrValues = [])
	{
		return $this->select_all($query, $arrValues);
	}

	public function update(string $query, array $arrValues)
	{
		$stmt = $this->conexion->prepare($query);
		return $stmt->execute($arrValues);
	}

	public function delete(string $query, array $arrValues = [])
	{
		$stmt = $this->conexion->prepare($query);
		return $stmt->execute($arrValues);
	}
}