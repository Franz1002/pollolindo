<?php
class ClientesModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    // Obtener todos los clientes
    public function getClientes()
    {
        $sql = "SELECT * FROM cliente";
        $request = $this->select_all($sql);
        return $request;
    }

    // Obtener un cliente por nombre
    public function getClienteByNombre($nombre_cliente)
    {
        $query = $query = "SELECT id_cliente, nombre_cliente FROM cliente WHERE LOWER(nombre_cliente) = LOWER(:nombre_cliente) LIMIT 1";


        $params = [':nombre_cliente' => $nombre_cliente];
        $cliente = $this->select1($query, $params);

         return $cliente;
    }




    // Insertar un nuevo cliente
    public function insertarCliente(string $nombre_cliente)
    {
        // Consulta SQL con valores como placeholders (evita SQL Injection)
        $query = "INSERT INTO cliente (nombre_cliente) VALUES (?)";

        // Parámetros en un array correcto
        $arrData = array($nombre_cliente);

        // Ejecutamos la consulta e insertamos el cliente
        return $this->insert($query, $arrData);
    }
}
