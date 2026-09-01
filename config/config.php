<?php 


	// visualizar errores. 0 NO. 1 SI
	ini_set('display_errors', 1);

	define('DISABLE_CACHE', true);

	/* cargamos clases de conexion a bd y log */
	require_once __DIR__ . "/log.class.php";
	require_once __DIR__ . "/db.class.php";
	//require_once ($_SERVER['DOCUMENT_ROOT'] .  "motor.correo.php");

	/* personalizamos el tratamiento de errores */

	function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    	throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
	}
	set_error_handler("exception_error_handler");

	date_default_timezone_set('UTC');

	define("ID_LIGA", 1);
?>