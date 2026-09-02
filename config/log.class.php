<?php

/* Descripcion: POJO de log
 * Autor: Marcos Torregrosa
 * e-mail: hola@marcostorregrosa.com
 * Fecha: 16-04-2015
 * ---------------------
 * --Revisiones--
 * Objetivo: 
 * Autor:  | e-mail:| Fecha:
 * -- 
 */
class Log {

    private $ficheroLog;
    private $diasRetencion = 30;
	private $mensaje;
	private static $oLog = NULL;


	public function __construct( ) {
        $this->ficheroLog = getenv('MB_LOG_FILE') ?: sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'mb-league-manager.log';
    }

    // método que escribe en el log el error 
    public function trazaLog ($e = null, $texto = null){
 
	    	if ($e === null)
    	{
    		$this->mensaje = $texto;
    	}else{
       		$this->mensaje = $texto ." ## " . $e->getMessage()  ;
        }
	       
        $this->purgarEntradasAntiguas();

        $origen = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'CLI';
        $entrada = "## ERROR ## " . date("Y-m-d H:i:s") . " ## EXCEPCION: " . $this->mensaje . " ## ORIGEN: " . $origen . "\r\n";
        file_put_contents($this->ficheroLog, $entrada, FILE_APPEND | LOCK_EX);
    }

    private function purgarEntradasAntiguas()
    {
        if (!is_file($this->ficheroLog)) {
            return;
        }

        $contenido = file($this->ficheroLog, FILE_IGNORE_NEW_LINES);
        if ($contenido === false) {
            return;
        }

        $fechaLimite = time() - ($this->diasRetencion * 86400);
        $entradasVigentes = array();
        foreach ($contenido as $linea) {
            if (preg_match('/^## ERROR ## (\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}) ##/', $linea, $coincidencias)) {
                $fechaEntrada = strtotime($coincidencias[1]);
                if ($fechaEntrada !== false && $fechaEntrada < $fechaLimite) {
                    continue;
                }
            }
            $entradasVigentes[] = $linea;
        }

        file_put_contents($this->ficheroLog, count($entradasVigentes) > 0 ? implode("\r\n", $entradasVigentes) . "\r\n" : '', LOCK_EX);
    }

	public static function getInstance()
    {
        if (!self::$oLog)
        {
            self::$oLog = new Log;
        }
        return self::$oLog;
    }
}

?>