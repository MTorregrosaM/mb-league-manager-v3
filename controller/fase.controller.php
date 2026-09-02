<?php

/* Descripcion: Liga
 * Autor: Marcos Torregrosa
 * e-mail: hola@marcostorregrosa.com
 * Fecha: 13-05-2015
 * ---------------------
 * --Revisiones--
 * Objetivo: 
 * Autor:  | e-mail:| Fecha:
 * -- 
 */

class controllerFase {

    private $oConexBD;
    private $oFase;

    public function __construct( ) 
    { 
        $this->oConexBD = new ConexBD();

    } 

    /* GETTERS Y SETTERS */
    public function __get($property) {
        if (property_exists($this, $property)) {
          return $this->$property;
        }
    }

    public function __set($property, $value) {
        if (property_exists($this, $property)) {
          $this->$property = $value;
        }

        return $this;
    }



    

    /* MÉTODOS PÚBLICOS */


    /* datos de liga */
    public function recuperarDatosFase( $idLiga, $numFase ){
        try {
            
            $queryDB = "SELECT distinct idLiga, numFase, claveCifrada, fecIni, fecFin 
                        FROM mb_fases
                        WHERE 1=1 
                        AND idLiga = ". $idLiga . " 
                        AND numFase = ". $numFase;
            
            $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

            if ($resultadoBD != null){
                foreach ($resultadoBD as $fila) {
                    $this->oFase = new Fase ( $fila[0], $fila[1], $fila[2], $fila[3], $fila[4] ) ;
                    return  $this->oFase;   
                }
            }else{
                return null;
            }

        }catch(Exception $e){
            $oLog = Log::getInstance();
            $oLog->trazaLog ($e, "recuperarDatosFase - fase.controller.php");   
            return null;     
        }

    }



    /* Listado de empresas para los formularios de búsqueda */
    public function recuperarSelectFases( $fIdLiga, $faseActiva = null ){
        try{            
            $queryDB = "SELECT distinct numFase FROM mb_fases 
                        WHERE idLiga = " . $fIdLiga ;

            // GESTIONAMOS FILTROS
            if ( $faseActiva != null) { $queryDB .= " AND fecIni <= '" . Date('Y-m-d') . "' AND fecFin >= '" . Date('Y-m-d') . "'"; }

            $queryDB .= " ORDER BY 1";

            $arrResultados = array ();
            
            $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

            if ($resultadoBD != null){
                
                foreach ($resultadoBD as $fila) {
                    array_push($arrResultados, $fila[0]);
                }
                    
                return $arrResultados;  
            }else{
                return null;
            }
        }catch(Exception $e){
            $oLog = Log::getInstance();
            $oLog->trazaLog ($e, "recuperarSelectFases - liga.controller.php"); 
            return null;     
        }

    }
    /* método para recuperar el listado de registros. */
    public function recuperarListadoFases (  $fIdLiga = null, $numPag = 0 ) {
    
        try {
            // calculamos el num de pag

            $numPag =  ($numPag > 0)? ($numPag * 10) :  $numPag ;
    
            $queryDB = "SELECT  distinct idLiga, numFase, DATE_FORMAT(fecIni, '%d-%m-%Y' ), DATE_FORMAT(fecFin, '%d-%m-%Y' ), claveCifrada
                        FROM mb_fases
                        WHERE 1=1 ";
                        
            // GESTIONAMOS FILTROS
            if ( $fIdLiga != null) { $queryDB .= " AND idLiga = ". $fIdLiga;  }
            // ORDENAMOS
            $queryDB .= " ORDER BY numFase ASC ";

            // PAGINAMOS
            $queryDB .= " LIMIT " . $numPag . ", 10 ";

            $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

            $arrResultados = array ();

            if ($resultadoBD != null){
                foreach ($resultadoBD as $fila) {
                    $arrObjeto = array();

                    array_push($arrObjeto, $fila[0], $fila[1], $fila[2], $fila[3], $fila[4] ) ;

                    array_push($arrResultados, $arrObjeto);
                }
                return $arrResultados;  
            }else{
                return null;
            }

        }catch(Exception $e){
            $oLog = Log::getInstance();
            $oLog->trazaLog ($e, "recuperarListadoFases - fase.controller.php");    
            return null;     
        }
    }


    /* método para MODIFICAR los datos de un registro. Previamente debemos comprobar cuáles han cambiado */
    public function altaFase( $fIdLiga, $fNumFase, $fFecIni, $fFecFin, $fClaveCifrada ){
        
        try {

            if (filter_var($fIdLiga, FILTER_VALIDATE_INT) === false || (int) $fIdLiga < 1 ||
                filter_var($fNumFase, FILTER_VALIDATE_INT) === false || (int) $fNumFase < 1 ||
                trim((string) $fFecIni) === '' || trim((string) $fFecFin) === '') {
                return 2;
            }

            $fechaInicio = $this->formatoFecha(true, $fFecIni);
            $fechaFin = $this->formatoFecha(true, $fFecFin);
            if ($fechaInicio > $fechaFin) {
                return 5;
            }

            $queryCalendarioLiga = "SELECT DATE_FORMAT(fecIni, '%Y-%m-%d'), DATE_FORMAT(fecFin, '%Y-%m-%d')
                                    FROM mb_ligas WHERE idLiga = " . (int) $fIdLiga;
            $calendarioLiga = $this->oConexBD->ejecutarConsulta($queryCalendarioLiga);
            if ($calendarioLiga == null || empty($calendarioLiga[0][0]) || empty($calendarioLiga[0][1]) ||
                $fechaInicio < $calendarioLiga[0][0] || $fechaFin > $calendarioLiga[0][1]) {
                return 6;
            }
        
            // COMPROBAMOS SI YA HAY UNA FASE CON EL NÚMERO ASIGNADO
            $queryBD = "SELECT count(distinct numFase) as CONTADOR FROM mb_fases
                        WHERE idLiga = " . (int) $fIdLiga . "
                        AND CAST(numFase AS UNSIGNED) = " . (int) $fNumFase;

            $resultadoBD = $this->oConexBD->ejecutarConsulta($queryBD);

            if ($resultadoBD != null){
                foreach ($resultadoBD as $fila) {
                      if  ($fila[0] > 0 ){
                        return 4; /// ERROR, NUMERO DE FASE YA UTILIZADO
                      } 

                }
            }

            $querySolapamiento = "SELECT count(distinct numFase) FROM mb_fases
                                  WHERE idLiga = " . (int) $fIdLiga . "
                                  AND fecIni <= '" . $fechaFin . "'
                                  AND fecFin >= '" . $fechaInicio . "'";
            $resultadoSolapamiento = $this->oConexBD->ejecutarConsulta($querySolapamiento);
            if ($resultadoSolapamiento != null && (int) $resultadoSolapamiento[0][0] > 0) {
                return 5;
            }



            // RECUPERAMOS EL NUMERO DE RONDAS DE LA LIGA
            $queryNumRondas = "SELECT numRondas from mb_ligas WHERE idLiga = " . $fIdLiga;

            $resultadoBD = $this->oConexBD->ejecutarConsulta($queryNumRondas);
            $numRondas = 0;
            if ($resultadoBD != null){
                foreach ($resultadoBD as $fila) {
                    $numRondas = $fila[0];
                }
            }

            if ($numRondas < 1) {
                return 2;
            }

            // insertar una fila por cada ronda
            $rondasInsertadas = 0;
            for ($i = 1; $i<= $numRondas; $i++ ){
               
                $queryDBFases = "INSERT INTO mb_fases( idLiga, numFase, numRonda, fecIni, fecFin, claveCifrada )
                      VALUES (" . $fIdLiga . ", " . $fNumFase . ", " . $i . ",'" . $this->formatoFecha(true, $fFecIni) . "', '" . $this->formatoFecha(true, $fFecFin) . "', '" . $fClaveCifrada . "')";                             

                $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDBFases, 1);
                if ($resultadoBD >= 1) {
                    $rondasInsertadas++;
                }
            }

                        
            if ($rondasInsertadas == $numRondas){
                return 1;   
            }else{
                return 2;
            }
            
        }catch(Exception $e){
            $oLog = Log::getInstance();
            $oLog->trazaLog ($e, "altaFase - fase.controller.php");   
            return null;     
        }
 
    }


    /* método para MODIFICAR los datos de un registro. Previamente debemos comprobar cuáles han cambiado */
    public function modificarDatosFase( $fIdLiga, $fNumFase, $fFecIni, $fFecFin, $fClaveCifrada ){
        
        try {

            if (filter_var($fIdLiga, FILTER_VALIDATE_INT) === false || (int) $fIdLiga < 1 ||
                filter_var($fNumFase, FILTER_VALIDATE_INT) === false || (int) $fNumFase < 1 ||
                trim((string) $fFecIni) === '' || trim((string) $fFecFin) === '') {
                return 2;
            }

            $fechaInicio = $this->formatoFecha(true, $fFecIni);
            $fechaFin = $this->formatoFecha(true, $fFecFin);
            if ($fechaInicio > $fechaFin) {
                return 3;
            }

            $queryCalendarioLiga = "SELECT DATE_FORMAT(fecIni, '%Y-%m-%d'), DATE_FORMAT(fecFin, '%Y-%m-%d')
                                    FROM mb_ligas WHERE idLiga = " . (int) $fIdLiga;
            $calendarioLiga = $this->oConexBD->ejecutarConsulta($queryCalendarioLiga);
            if ($calendarioLiga == null || empty($calendarioLiga[0][0]) || empty($calendarioLiga[0][1]) ||
                $fechaInicio < $calendarioLiga[0][0] || $fechaFin > $calendarioLiga[0][1]) {
                return 6;
            }

            $querySolapamiento = "SELECT count(distinct numFase) FROM mb_fases
                                  WHERE idLiga = " . (int) $fIdLiga . "
                                  AND numFase <> " . (int) $fNumFase . "
                                  AND fecIni <= '" . $fechaFin . "'
                                  AND fecFin >= '" . $fechaInicio . "'";
            $resultadoSolapamiento = $this->oConexBD->ejecutarConsulta($querySolapamiento);
            if ($resultadoSolapamiento != null && (int) $resultadoSolapamiento[0][0] > 0) {
                return 4;
            }


            $queryDB = "UPDATE mb_fases SET 
                        fecIni = '". $this->formatoFecha(true, $fFecIni) ."'
                        ,fecFin = '". $this->formatoFecha(true, $fFecFin) ."' 
                        , claveCifrada = '". $fClaveCifrada . "'
                        WHERE idLiga = " . $fIdLiga."
                        AND numFase = " . $fNumFase;
        

            $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);
                        


            $queryDB = "UPDATE mb_fases SET 
                        fecIni = '". $this->formatoFecha(true, $fFecFin) ."'
                        WHERE idLiga = " . $fIdLiga."
                        AND numFase = " . ($fNumFase+1);
        

            $resultadoBD2 = $this->oConexBD->ejecutarConsulta($queryDB, 1);
                        
            if ($resultadoBD >= 1){
                return 1;   
            }else{
                return 2;
            }
            
        }catch(Exception $e){
            $oLog = Log::getInstance();
            $oLog->trazaLog ($e, "modificarDatosFase - fase.controller.php");   
            return null;     
        }
 
    }





    /* método para PAGINADOR de empresas */
    public function paginadorFases (  $fIdLiga = null ) {
        
        try {

            $queryDB = "SELECT count(distinct numFase)
                        FROM mb_fases
                        WHERE 1=1 ";
                        
            // GESTIONAMOS FILTROS
            if (  $fIdLiga != null) { $queryDB .= " AND idLiga = " .  $fIdLiga;  }                      

            $resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

            if ($resultadoBD != null){
                foreach ($resultadoBD as $fila) {
                    $resultado = $fila[0];
                }
                return $resultado;  
            }else{
                return null;
            }

        }catch(Exception $e){
            $oLog = Log::getInstance();
            $oLog->trazaLog ($e, "paginadorFases - fase.controller.php");   
            return null;     
        }
    }


    /* método para cambiar el formato de fecha de la BD al UTC español */
    public function formatoFecha ( $utcUsa = true, $fFecha = null ){

        if ($utcUsa){
            $user_tz = 'America/Los_Angeles';
            $format = "Y-m-d";
        }else{
            $user_tz = 'Europe/Madrid';
            $format = "d-m-Y";
        }

        $schedule_date = new DateTime($fFecha, new DateTimeZone($user_tz) );
        $schedule_date->setTimeZone(new DateTimeZone('UTC'));
        return $schedule_date->format($format);
    }



}



?>