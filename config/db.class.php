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
	private $ultimoError = '';
	private $ultimoInsertId = 0;

	public function __construct( ) {
		global $dbCredentials;
		$this->servidor = $dbCredentials['servidor'];
		$this->usuario = $dbCredentials['usuario'];
		$this->contrasena = $dbCredentials['contrasena'];
		$this->db = $dbCredentials['db'];
	} 

	public function __destruct(){
		$this->cerrarConexion();
	}


    public function abrirConexion (){  

    	try {
		if ($this->conexion instanceof mysqli) {
			return;
		}
		// conexion+
    
		$this->conexion = new mysqli($this->servidor,$this->usuario,$this->contrasena, $this->db);
		if ($this->conexion->connect_errno) {
			throw new RuntimeException('Ha fallado la conexión con el servidor: ' . $this->conexion->connect_error);
		}
		$this->conexion->set_charset('utf8');
		
		}catch(Throwable $e){
			$oLog = Log::getInstance();
			$this->cerrarConexion();
			$oLog->trazaLog ($e, "ERROR DB");
			throw new RuntimeException('No se pudo abrir la conexión a la base de datos.', 0, $e);
		}

    }


	public function cerrarConexion (){
		if ($this->conexion instanceof mysqli) {
			$this->conexion->close();
			$this->conexion = null;
		}
	}

	public function escaparCadena($valor){
		$this->abrirConexion();
		$valorEscapado = $this->conexion->real_escape_string((string) $valor);
		$this->cerrarConexion();
		return $valorEscapado;
	}


    public function ejecutarConsulta ( $query, $dml = null ){
    	try {
			$this->ultimoError = '';
			$this->ultimoInsertId = 0;

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
				$this->ultimoInsertId = (int) $this->conexion->insert_id;
				return $affectedRows;
			}
			return $rows;

		}catch(Throwable $e){
			$this->ultimoError = $e->getMessage();
			$oLog = Log::getInstance();
			$this->cerrarConexion();
			$oLog->trazaLog ($e, "db.class.php");	
			return null;	 
		}

    }

	public function ejecutarConsultaPreparada($query, $tipos = '', $parametros = array(), $dml = null){
		try {
			$this->ultimoError = '';
			$this->ultimoInsertId = 0;
			$this->abrirConexion();
			$sentencia = $this->conexion->prepare($query);
			if ($sentencia === false) {
				throw new RuntimeException($this->conexion->error);
			}
			if ($tipos !== '') {
				$referencias = array($tipos);
				foreach ($parametros as $indice => $valor) {
					$referencias[] = &$parametros[$indice];
				}
				call_user_func_array(array($sentencia, 'bind_param'), $referencias);
			}
			if (!$sentencia->execute()) {
				throw new RuntimeException($sentencia->error);
			}
			if ($dml !== null) {
				$afectadas = $sentencia->affected_rows;
				$this->ultimoInsertId = (int) $sentencia->insert_id;
				$sentencia->close();
				return $afectadas;
			}
			$filas = array();
			if (method_exists($sentencia, 'get_result')) {
				$resultado = $sentencia->get_result();
				$filas = $resultado ? $resultado->fetch_all(MYSQLI_NUM) : array();
				if ($resultado) {
					$resultado->free();
				}
			} else {
				$metadatos = $sentencia->result_metadata();
				if ($metadatos !== false) {
					$valores = array();
					$referencias = array();
					while ($campo = $metadatos->fetch_field()) {
						$referencias[] = &$valores[$campo->name];
					}
					$metadatos->free();
					call_user_func_array(array($sentencia, 'bind_result'), $referencias);
					while ($sentencia->fetch()) {
						$filas[] = array_values($valores);
					}
				}
			}
			$sentencia->close();
			return $filas;
		} catch (Throwable $e) {
			$this->ultimoError = $e->getMessage();
			$oLog = Log::getInstance();
			$this->cerrarConexion();
			$oLog->trazaLog($e, "db.class.php");
			return null;
		}
	}

	public function obtenerUltimoError(){
		return $this->ultimoError;
	}

	public function obtenerUltimoInsertId(){
		return $this->ultimoInsertId;
	}



}

?>