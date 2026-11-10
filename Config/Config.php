<?php 
	

	const BASE_URL = "http://localhost/pollo";

	//Zona horaria
	date_default_timezone_set('America/La_Paz');		

	//Datos de conexión a Base de Datos

	const DB_HOST = "localhost";
	const DB_NAME = "pollo";
	const DB_USER = "root";
	const DB_PASSWORD = "";
	const DB_CHARSET = "charset=utf8";

	//Deliminadores decimal y millar Ej. 24,1989.00
	const SPD = ".";
	const SPM = ",";

	//Simbolo de moneda
	const SMONEY = "Bs";

	
	//Seguridad de la sesión
	const MINUTOS_INACTIVIDAD = 60; // la sesión se cierra sola tras este tiempo sin uso
	const MAX_INTENTOS_LOGIN = 5;   // intentos fallidos permitidos...
	const MINUTOS_BLOQUEO = 2;     // ...dentro de estos minutos

	


 ?>