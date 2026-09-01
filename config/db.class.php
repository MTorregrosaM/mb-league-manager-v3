<?php

/* Descripcion: control de bd
 * Autor: Marcos Torregrosa
 * e-mail: hola@marcostorregrosa.com
 * Fecha: 16-04-2015
 * ---------------------
 * --Revisiones--
 * Objetivo: 
 * Autor:  | e-mail:| Fecha:
 * -- 
 */

require_once __DIR__ . "/log.class.php";

$dbCredentialsFile = __DIR__ . "/db.credentials.php";
if (!is_file($dbCredentialsFile)) {
	throw new RuntimeException('Falta config/db.credentials.php. Copia config/db.credentials-template.php y configura la conexión.');
}
$dbCredentials = require $dbCredentialsFile;


class conexBD {

	private $resultSet;
	private $conexion;
	
	private $servidor;
	private $usuario;
	private $contrasena;
	private $db;

	public function __construct( ) {
		global $dbCredentials;
		$this->servidor = $dbCredentials['servidor'];
		$this->usuario = $dbCredentials['usuario'];
		$this->contrasena = $dbCredentials['contrasena'];
		$this->db = $dbCredentials['db'];
	} 


    public function abrirConexion (){  

    	try {
		// conexion+
    
		$this->conexion = new mysqli($this->servidor,$this->usuario,$this->contrasena, $this->db);
		if ($this->conexion->connect_errno) {
			throw new RuntimeException('Ha fallado la conexión con el servidor: ' . $this->conexion->connect_error);
		}
		$this->conexion->set_charset('utf8');
		
		}catch(Exception $e){
			$oLog = Log::getInstance();
			$this->cerrarConexion();
			$oLog->trazaLog ($e, "ERROR DB");		 
		}

    }


	public function cerrarConexion (){
		if ($this->conexion instanceof mysqli) {
			$this->conexion->close();
			$this->conexion = null;
		}
	}


    public function ejecutarConsulta ( $query, $dml = null ){
    	try {

    		$this->abrirConexion();

			$this->resultSet = $this->conexion->query($query);
			if ($this->resultSet === false) {
				throw new RuntimeException($this->conexion->error);
			}
			 
			if ($dml == null){ // si no es operacion DML
					$rows = array();			
					
				if(isset($this->resultSet ) && $this->resultSet  != null){
						
						while($row = $this->resultSet->fetch_array())
						{
							$rows[] = $row;
						}
						$this->resultSet->close();

				}else{
					throw new ErrorException("La consulta SQL no devuelve datos (" .  $query  . ")");
				}
		
			}else{ // devolvemos num de filas afectadas por DML
				$affectedRows = $this->conexion->affected_rows;
				$this->cerrarConexion();
				return $affectedRows;
			}
			$this->cerrarConexion();
			return $rows;

		}catch(Exception $e){
			$oLog = Log::getInstance();
			$this->cerrarConexion();
			$oLog->trazaLog ($e, "db.class.php");	
			return null;	 
		}

    }



}

?>