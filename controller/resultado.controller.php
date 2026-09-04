<?php
require_once __DIR__ . '/../inc/puntuacion-fow.php';

/* Descripcion: Entrenador
 * Autor: Marcos Torregrosa
 * e-mail: hola@marcostorregrosa.com
 * Fecha: 13-05-2015
 * ---------------------
 * --Revisiones--
 * Objetivo: 
 * Autor:  | e-mail:| Fecha:
 * -- 
 */

class controllerResultado {

	private $oConexBD;
	private $oResultado;

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

  	/* método para ALTA NUEVA */
  	public function altaResultado( $fIdLiga, $fNumFase, $fNumRonda, $fIdJugador1, $fIdJugador2, $fBandoJug1, $fBandoJug2 ){

		try {

			// insertamos el nuevo registro
			$queryDB = "INSERT INTO mb_enfrentamientos ( IdLiga, numFase, numRonda, idJugador1, idJugador2, bandoJugador1, bandoJugador2 )
						VALUES (" . $fIdLiga . ", " . $fNumFase . ", " . $fNumRonda . "," .$fIdJugador1 . " ," . $fIdJugador2 . ", '" . $fBandoJug1 . "', '" . $fBandoJug2. "')";
			
			$resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);

			
			if ($resultadoBD >= 1){
				return 1;				
			}else{
				return 2;
			} 

		}catch(Exception $e){
			$oLog = Log::getInstance();
			$oLog->trazaLog ($e, "altaResultado - liga.controller.php");	
			return null;	 
		}
  	}


  	/* método para RESULTADO */
	public function altaResultadoResultado( $fIdLiga, $fIdResultado, $fIdJugador, $fFechaBatalla, $fResultadoJugador1, $fResultadoJugador2, $fValPintura, $arrMisionesSec, $fValDeportividad, $fVictoriaSector, $fResultadoRadio = null ){

		try {
			//$fValDeportividad = 0;



			// validamos que recibimos todos los datos
			if ( $fIdResultado == null || $fFechaBatalla == null || $fIdJugador == null || $fResultadoJugador1 == null ||  $fValPintura == null ) {
			/*	echo  $fResultadoJugador2;
				echo 'fIdResultado: ' . $fIdResultado . '<br>fFechaBatalla: ' . $fFechaBatalla . '<br>fIdJugador: ' . $fIdJugador . '<br>fResultadoJugador1: ' . $fResultadoJugador1 . '<br>fResultadoJugador2: ' . $fResultadoJugador2 . '<br>fValPintura: ' . $fValPintura;

				if( $fIdResultado == null ) echo 'fIdResultado';
				if( $fFechaBatalla == null ) echo 'fFechaBatalla';
				if( $fIdJugador == null ) echo 'fIdJugador';
				if( $fResultadoJugador1 == null ) echo 'fResultadoJugador1';
				if( $fResultadoJugador2 == null ) echo 'fResultadoJugador2';
				if( $fValPintura == null ) echo 'fValPintura';*/

				return 3;
			}
			$errorDatos  = false;
			$errorPrincipal  = false;

			// validamos si ya se han actualizado los datos del resultado
			$oResultado = $this->recuperarResultado($fIdResultado);
			$tipoJuegoLiga = $this->oConexBD->ejecutarConsulta("SELECT idJuego FROM mb_ligas WHERE idLiga = " . (int) $fIdLiga);
			if (!empty($tipoJuegoLiga) && (int) $tipoJuegoLiga[0][0] <= 2
				&& !validarMarcadorFow($fResultadoJugador1, $fResultadoJugador2)) {
				return 3;
			}
	
			if ($oResultado->indValidado == null &&  $oResultado->resultadoJugador1 == null && $oResultado->resultadoJugador2 == null) {
				if (!empty($tipoJuegoLiga) && (int) $tipoJuegoLiga[0][0] <= 2) {
					if ($fResultadoRadio !== null && (int) $fResultadoRadio === 3) {
						$marcadorFow = array(5, 4);
					} elseif ($fResultadoRadio !== null && (int) $fResultadoRadio === 0) {
						$marcadorFow = array(4, 5);
					} else {
						$marcadorFow = calcularMarcadorFow($fResultadoJugador1, $fResultadoJugador2);
					}
					if ($marcadorFow === null) {
						return 3;
					}
					$fResultadoJugador1 = $marcadorFow[0];
					$fResultadoJugador2 = $marcadorFow[1];
				}

				// actualizamos los resultados de los resultados
				$queryDB = "UPDATE mb_enfrentamientos SET
							fechaBatalla = '" . $this->formatoFecha(true,$fFechaBatalla) . "' 
							,indValidado = 0
							,audAlta = '" .  Date('Y-m-d H:i:s')  . "' 
							WHERE idEnfrentamiento = " . $fIdResultado;
				
				$resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);

				// actualizamos los resultados de los resultados
				$queryDB = "UPDATE mb_enfrentamientos SET
							resultadoJugador1 = " . $fResultadoJugador1 . " 
							,resultadoJugador2 = " . $fResultadoJugador2 . "
							WHERE idEnfrentamiento = " . $fIdResultado . "
							AND idJugador1 = " . $fIdJugador;				
				$resultadoBD1 = $this->oConexBD->ejecutarConsulta($queryDB, 1);

				// actualizamos los resultados de los resultados
				$queryDB = "UPDATE mb_enfrentamientos SET
							resultadoJugador1 = " . $fResultadoJugador2 . " 
							,resultadoJugador2 = " . $fResultadoJugador1 . "
							WHERE idEnfrentamiento = " . $fIdResultado . "
							AND idJugador2 = " . $fIdJugador;				
				$resultadoBD2 = $this->oConexBD->ejecutarConsulta($queryDB, 1);

				if ($resultadoBD < 1 && $resultadoBD1 < 1 && $resultadoBD2){
					$errorPrincipal = true;
				}

			}else{
			
				if ( $oResultado->idJugador1 == $fIdJugador &&  ($oResultado->resultadoJugador1 != $fResultadoJugador1 || ($oResultado->resultadoJugador2 != $fResultadoJugador2))) { 
					$errorDatos = true;
				}

				
				if ( $oResultado->idJugador2 == $fIdJugador &&  ($oResultado->resultadoJugador2 != $fResultadoJugador1 || ($oResultado->resultadoJugador1 != $fResultadoJugador2 ))) {
					$errorDatos = true;
				}

				if ($errorDatos) {
					return 4;
				}
			}

			// actualizamos la pintura del jugador 
			$queryDB = "UPDATE mb_enfrentamientos SET
						valPinturaJug2 = '" . $fValPintura . "' 
						WHERE idEnfrentamiento = " . $fIdResultado . " 
						AND idJugador1 = " . $fIdJugador;
			$resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);

			$queryDB = "UPDATE mb_enfrentamientos SET
						valPinturaJug1 = '" . $fValPintura . "' 
						WHERE idEnfrentamiento = " . $fIdResultado . " 
						AND idJugador2 = " . $fIdJugador;
			$resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);

			// actualizamos la deportividad del jugador 
			$queryDB = "UPDATE mb_enfrentamientos SET
						valDeportividadJug2 = '" . $fValDeportividad . "' 
						WHERE idEnfrentamiento = " . $fIdResultado . " 
						AND idJugador1 = " . $fIdJugador;
			$resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);

			$queryDB = "UPDATE mb_enfrentamientos SET
						valDeportividadJug1 = '" . $fValDeportividad . "' 
						WHERE idEnfrentamiento = " . $fIdResultado . " 
						AND idJugador2 = " . $fIdJugador;
			$resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);

			if($fVictoriaSector != 0){
				// actualizamos la pintura del jugador 
				$queryDB = "UPDATE mb_enfrentamientos SET
						victoriaSector = '" . $fVictoriaSector . "' 
						WHERE idEnfrentamiento = " . $fIdResultado;
				$resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);
			}
			
			// registramos las misiones secundarias
			if (is_array($arrMisionesSec) && count($arrMisionesSec) > 0 ) {

				foreach ( $arrMisionesSec as $idMision ){

					// comprobamos que no exista
					$queryDB = "SELECT count(1) as contador
						FROM mb_enfren_misiones_sec 
						WHERE idEnfrentamiento = " . $fIdResultado ." 
						AND idJugador1 = " . $fIdJugador ."						
						AND idMisionSecundaria = " . $idMision ;

					$resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

					if ($resultadoBD[0][0] == 0){
						 $queryDB = "INSERT INTO mb_enfren_misiones_sec (idEnfrentamiento, idJugador1, idMisionSecundaria)
									VALUES (" . $fIdResultado . ", " . $fIdJugador . ", " . $idMision . ")";
						
						$resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);		
					}		
				}
			}
			
			if (!$errorPrincipal){
				return 1;				
			}else{
				return 2;
			} 

		}catch(Exception $e){
			$oLog = Log::getInstance();
			$oLog->trazaLog ($e, "altaResultadoResultado - liga.controller.php");	
			return null;	 
		}
  	}



  	/* método para RESULTADO */
  	public function altaResultadoResultadoResumido( $fIdLiga, $fIdJugador1, $fIdJugador2, $fFechaBatalla, $fResultadoJugador1, $fResultadoJugador2, $fValPintura,  $fValDeportividad, $fNumFase ){

		try {
			//$fValDeportividad = 0;



			// validamos que recibimos todos los datos
			if ( $fIdResultado == null || $fFechaBatalla == null || $fIdJugador1 == null || $fIdJugador2 == null || $fResultadoJugador1 == null ||  $fResultadoJugador2 == null || $fValPintura == null ) {

				return 3;
			}
			$errorDatos  = false;
			$errorPrincipal  = false;

	
			if ($oResultado->indValidado == null &&  $oResultado->resultadoJugador1 == null && $oResultado->resultadoJugador2 == null) {

				
				// insertamos el nuevo registro
				$queryDB = "INSERT INTO mb_enfrentamientos ( IdLiga, numFase, numRonda, idJugador1, idJugador2, bandoJugador1, bandoJugador2, resultadoJugador1, resultadoJugador2, valPintura )
							VALUES (" . $fIdLiga . ", " . $fNumFase . ", 0," .$fIdJugador1 . " ," . $fIdJugador2 . ", '" . $fBandoJug1 . "', '" . $fBandoJug2. "')";
				
				$resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);

					if ($resultadoBD < 1 && $resultadoBD1 < 1 && $resultadoBD2){
						$errorPrincipal = true;
					}

			}

				
			if ($errorDatos ){
				return 4;
			}else if (!$errorPrincipal){
				return 1;				
			}else{
				return 2;
			} 

		}catch(Exception $e){
			$oLog = Log::getInstance();
			$oLog->trazaLog ($e, "altaResultadoResultadoResumido - liga.controller.php");	
			return null;	 
		}
  	}



  	/* método para MODIFICAR los datos de un registro. Previamente debemos comprobar cuáles han cambiado */
  	public function modificarDatosResultado( $fIdLiga, $fIdResultado, $fIdJugador1, $fIdJugador2, $fResultadoJugador1, $fResultadoJugador2, $fValPinturaJug1, $fValPinturaJug2, $fFechaBatalla, $fIndValidado, 
  				$arrMisionesSecJug1, $arrMisionesSecJug2, $fValDeportividadJug1, $fValDeportividadJug2, $fIdJugVictoriaConcedida, $fVictoriaSector ){
  		
		try {
		
			// comprobamos los campos que han cambiado
			$this->oResultado = $this->recuperarResultado ( $fIdResultado );
			$tipoJuegoLiga = $this->oConexBD->ejecutarConsulta("SELECT idJuego FROM mb_ligas WHERE idLiga = " . (int) $fIdLiga);
			if (!empty($tipoJuegoLiga) && (int) $tipoJuegoLiga[0][0] <= 2
				&& !validarMarcadorFow($fResultadoJugador1, $fResultadoJugador2)) {
				return 2;
			}
			

	  		$queryDB = "UPDATE mb_enfrentamientos SET ";
		  	$aux = 0;

			
			// SI HAY VICTORIA CONCEDIDA NO HAY MISIONES SECUNDARIAS, NI PINTURA NI DEPORTIVIDAD

			if ($fIdJugVictoriaConcedida > 0){

					$resultadoMax = 6;
					$tipoJuegoLigaBD = $this->oConexBD->ejecutarConsulta("SELECT idJuego FROM mb_ligas WHERE idLiga = " . $fIdLiga );

					if ($tipoJuegoLigaBD != null){

						foreach ($tipoJuegoLigaBD as $fila) {
							if($fila[0] == 1){// FOW V3
								$resultadoMax = 6;
							} elseif($fila[0] == 2){// FOW V4
								$resultadoMax = 8;
							}

						}
					}
				$fFechaBatalla = substr($fFechaBatalla,6,4) . "-" .substr($fFechaBatalla, 3,2)."-".substr($fFechaBatalla,0,2);

				$queryDB .= "resultadoJugador1 = CASE WHEN idJugador1 =  " . $fIdJugVictoriaConcedida . " THEN ". $resultadoMax . " ELSE 1 END, ";
				$queryDB .= "resultadoJugador2 = CASE WHEN idJugador2 =  " . $fIdJugVictoriaConcedida . " THEN ". $resultadoMax . " ELSE 1 END, ";
				$queryDB .= "idJugVictoriaConcedida = " . $fIdJugVictoriaConcedida . ", valPinturaJug1 = 0, valPinturaJug2 = 0, valDeportividadJug1 = 0, valDeportividadJug2 = 0, fechaBatalla = '" . $fFechaBatalla . "', audAlta = '" .  Date('Y-m-d H:i:s')  . "' ";

				$queryDBDelete = "DELETE from mb_enfren_misiones_sec 
							WHERE idEnfrentamiento = " . $fIdResultado;
				
				$resultadoBDDelete = $this->oConexBD->ejecutarConsulta($queryDBDelete, 1);

				if ($fIndValidado != $this->oResultado->indValidado){

		  			$queryDB .= ($aux > 0)? " , " : "";
		  			$queryDB .= " , indValidado = ". $fIndValidado ." ";
		  			$aux = 1;
		  		}

		  	  	$queryDB .= " WHERE idEnfrentamiento = " . $fIdResultado;
		  		$aux = 1;

			}else{

		  		if ($fResultadoJugador1 != $this->oResultado->resultadoJugador1){
		  			$queryDB .= ($aux > 0)? " , " : "";
		  			$queryDB .= " resultadoJugador1 = ". $fResultadoJugador1 ." ";
		  			$aux = 1;
		  		}
		  		if ($fResultadoJugador1 != $this->oResultado->resultadoJugador1){

		  			$queryDB .= ($aux > 0)? " , " : "";
		  			$queryDB .= " resultadoJugador2 = ". $fResultadoJugador2 ." ";
		  			$aux = 1;
		  		}
		  		if ($fValPinturaJug1 != $this->oResultado->valPinturaJug1){

		  			$queryDB .= ($aux > 0)? " , " : "";
		  			$queryDB .= " valPinturaJug1 = ". $fValPinturaJug1 ." ";
		  			$aux = 1;
		  		}
		  		if ($fValPinturaJug2 != $this->oResultado->valPinturaJug2){

		  			$queryDB .= ($aux > 0)? " , " : "";
		  			$queryDB .= " valPinturaJug2 = ". $fValPinturaJug2 ." ";
		  			$aux = 1;
		  		}
		  		if ($fValDeportividadJug1 != $this->oResultado->valDeportividadJug1){

		  			$queryDB .= ($aux > 0)? " , " : "";
		  			$queryDB .= " valDeportividadJug1 = ". $fValDeportividadJug1 ." ";
		  			$aux = 1;
		  		}
		  		if ($fValDeportividadJug2 != $this->oResultado->valDeportividadJug2){

		  			$queryDB .= ($aux > 0)? " , " : "";
		  			$queryDB .= " valDeportividadJug2 = ". $fValDeportividadJug2 ." ";
		  			$aux = 1;
		  		}
		  		if ($fVictoriaSector!= $this->oResultado->victoriaSector){

		  			$queryDB .= ($aux > 0)? " , " : "";
		  			$queryDB .= " victoriaSector = ". $fVictoriaSector ." ";
		  			$aux = 1;
		  		}
		  		// partimos la fecha a formato USA
		  		$fFechaBatalla = substr($fFechaBatalla,6,4) . "-" .substr($fFechaBatalla, 3,2)."-".substr($fFechaBatalla,0,2);
		  		if ($fFechaBatalla != $this->oResultado->fechaBatalla){
		  			$queryDB .= ($aux > 0)? " , " : "";
		  			$queryDB .= " fechaBatalla = '". $fFechaBatalla  ."' ";
		  			$aux = 1;
		  		}
		  		if ($fIndValidado != $this->oResultado->indValidado){

		  			$queryDB .= ($aux > 0)? " , " : "";
		  			$queryDB .= " indValidado = ". $fIndValidado ." ";
		  			$aux = 1;
		  		}
		  		if ($fIdJugVictoriaConcedida != $this->oResultado->idJugVictoriaConcedida){

		  			$queryDB .= ($aux > 0)? " , " : "";
		  			$queryDB .= " idJugVictoriaConcedida = ". $fIdJugVictoriaConcedida ." , valDeportividadJug1 = 0, valDeportividadJug2 = 0";
		  			$aux = 1;
		  		}
		  	 	$queryDB .= " WHERE idEnfrentamiento = " . $fIdResultado;


				// MISIONES SECUNDARIAS
				$auxMis = 0;
		  		$arrMisionesJug1 = $this->recuperarMisionesSecJugador( $fIdResultado, $fIdJugador1 );
		  		$arrMisionesJug2 = $this->recuperarMisionesSecJugador( $fIdResultado, $fIdJugador2 );
		  		if (is_array($arrMisionesJug1) && is_array($arrMisionesSecJug1)) {
					if (is_array($arrMisionesJug1) && is_array($arrMisionesSecJug1) && count($arrMisionesJug1) != count($arrMisionesSecJug1)) {
						$auxMis = 1;
					}
				}

				$queryDBDelete = "DELETE from mb_enfren_misiones_sec 
							WHERE idEnfrentamiento = " . $fIdResultado;
				
				$resultadoBDDelete = $this->oConexBD->ejecutarConsulta($queryDBDelete, 1);

				// METEMOS LAS NUEVAS MISIONES SECUNDARIAS
				// JUGADOR 1
					if (is_array($arrMisionesSecJug1) && count($arrMisionesSecJug1) > 0 ) {
					$auxMis = 1;
					foreach ( $arrMisionesSecJug1 as $idMision ){
							if ($idMision > 0){
								$queryDBMis1 = "INSERT INTO mb_enfren_misiones_sec (idEnfrentamiento, idJugador1, idMisionSecundaria)
											VALUES (" . $fIdResultado . ", " . $fIdJugador1 . ", " . $idMision . ")";
								$resultadoBDMisSec1 = $this->oConexBD->ejecutarConsulta($queryDBMis1, 1);
							}
									
					}
				}

				// JUGADOR 2
					if (is_array($arrMisionesSecJug2) && count($arrMisionesSecJug2) > 0 ) {
					$auxMis = 1;
					foreach ( $arrMisionesSecJug2 as $idMision ){
							if ($idMision > 0){
							$queryDBMis2 = "INSERT INTO mb_enfren_misiones_sec (idEnfrentamiento, idJugador1, idMisionSecundaria)
										VALUES (" . $fIdResultado . ", " . $fIdJugador2 . ", " . $idMision . ")";
								$resultadoBDMisSec2 = $this->oConexBD->ejecutarConsulta($queryDBMis2, 1);
							}		
					}
				}

			} // fin comprobacion partida concedida
		
		

			if ($aux == 1){ 
				$resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);

				if ($resultadoBD >= 1){
					return 1;	
				}else{
					return 2;
				}
			}else if ($auxMis == 1){				
				
					return 1;
			}else{
				return 3;
			}

		}catch(Exception $e){
			$oLog = Log::getInstance();
			$oLog->trazaLog ($e, "modificarDatosResultado - jugador.controller.php");	
			return null;	 
		}
 
  	}



  	/* método para borrar todos los resultados */
   	public function borrarResultadosFaseRonda( $fIdLiga, $fNumFase, $fNumRonda  ){

		try {

			$idLiga = (int) $fIdLiga;
			$numFase = (int) $fNumFase;
			$numRonda = (int) $fNumRonda;
			$queryDB = "DELETE FROM mb_enfren_misiones_sec
						WHERE idEnfrentamiento IN (
							SELECT idEnfrentamiento FROM mb_enfrentamientos
							WHERE idLiga = " . $idLiga . "
							AND numFase = " . $numFase . "
							AND numRonda = " . $numRonda . ")";
			$this->oConexBD->ejecutarConsulta($queryDB, 1);

			$queryDB = "DELETE FROM mb_enfrentamientos
						WHERE idLiga = " . $idLiga . "
						AND numFase = " . $numFase . "
						AND numRonda = " . $numRonda;
			
			$resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);

			
			if ($resultadoBD >= 1){
				return 1;				
			}else{
				return 2;
			} 

		}catch(Exception $e){
			$oLog = Log::getInstance();
			$oLog->trazaLog ($e, "borrarResultadosFaseRonda - liga.controller.php");	
			return null;	 
		}
  	}  	


	/* método para recuperar todos los resultados de un jugador. */
	public function recuperarDetalleResultadosJugador($fIdLiga, $fIdJugador) {
		return $this->recuperarListadoResultadosCompleto($fIdLiga, $fIdJugador, null, null, 0, null);
	}


  	/* método para recuperar el listado de registros. */
  	public function recuperarListadoResultados ( $fIdLiga, $fNumFase, $fNumRonda) {

		try {

	  		 $queryDB = "SELECT t1.idEnfrentamiento, idJugador1, t2.nick, t2.bando, idJugador2, t3.nick, t3.bando
						FROM mb_enfrentamientos t1
						join mb_jugadores t2 
						on t1.idJugador1 = t2.idJugador
						join mb_jugadores t3
						on t1.idJugador2 = t3.idJugador
						WHERE t1.idLiga = " . $fIdLiga ." 
						AND t1.numFase = " . $fNumFase ."						
						AND t1.numRonda = " . $fNumRonda ;

			// ORDENAMOS
	  		$queryDB .= " ORDER BY idEnfrentamiento ASC ";


			$resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

			$arrResultados = array ();

			if ($resultadoBD != null){

				foreach ($resultadoBD as $fila) {
					$arrObjeto = array();

					array_push($arrObjeto, $fila[0], $fila[1], $fila[2], $fila[3], $fila[4], $fila[5], $fila[6]  ) ;

					array_push($arrResultados, $arrObjeto);

				}
				return $arrResultados;	
			}else{
				return null;
			}

		}catch(Exception $e){
			$oLog = Log::getInstance();
			$oLog->trazaLog ($e, "recuperarListadoResultados - resultado.controller.php");	
			return null;	 
		}
  	}


  	/* método para recuperar el listado de registros. */
  	public function recuperarListadoResultadosCompleto ( $fIdLiga = NULL, $fIdJugador1 = NULL, $fFechaBatalla = NULL, $fIndValidado  = NULL, $numPag = 0, $numLim = 10) {

		try {
 
			// calculamos el num de pag
			$numPag =  ($numPag > 0)? ($numPag * 10) :  $numPag ;

 			$queryDB = "SELECT distinct idEnfrentamiento, idLiga, numFase, numRonda, idJugador1, idJugador2, resultadoJugador1, resultadoJugador2, IFNULL(valPinturaJug1,0), IFNULL(valPinturaJug2,0), fechaBatalla, indValidado, 
 						 IFNULL(valDeportividadJug1,0), IFNULL(valDeportividadJug2,0), idJugVictoriaConcedida, victoriaSector
						FROM mb_enfrentamientos 
						WHERE 1 = 1 ";
			// where
			if ( $fIdLiga != null && $fIdLiga > 0) { $queryDB .= " AND idLiga = " . $fIdLiga ;  }
	  		if ( $fIdJugador1 != null  && $fIdJugador1 > 0) { $queryDB .= " AND ( idJugador1 = " . $fIdJugador1 . " OR idJugador2 = " . $fIdJugador1  . " ) ";  }		
	  		if ( $fIndValidado != null && $fIndValidado < 2) { $queryDB .= " AND indValidado = " . $fIndValidado ;  }	
	  		if ( $fFechaBatalla != null  && $fFechaBatalla > 0) { $queryDB .= " AND fechaBatalla = " . $this->formatoFecha(true, $fFechaBatalla) ;  }	

			// ORDENAMOS
	  	    $queryDB .= " ORDER BY fechaBatalla desc, indValidado asc   ";

			// PAGINAMOS
			if ($numLim !== null) {
				$queryDB .= " LIMIT " . $numPag . ", ".  $numLim . "	";
			}

			$resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

			$arrResultados = array ();

			if ($resultadoBD != null){

				foreach ($resultadoBD as $fila) {
					$arrObjeto = array();
					$fFechaBatallaAux = ($fila[10]!= null) ? substr($fila[10],8,2) . "-" .substr($fila[10], 5,2). "-" . substr($fila[10],0,4) : "";
					array_push($arrObjeto, $fila[0], $fila[1], $fila[2], $fila[3], $fila[4], $fila[5], $fila[6], $fila[7], $fila[8], $fila[9], $fFechaBatallaAux, $fila[11], $fila[12], $fila[13], $fila[14], $fila[15]       ) ;

					array_push($arrResultados, $arrObjeto);

				}
				return $arrResultados;	
			}else{
				return null;
			}

		}catch(Exception $e){
			$oLog = Log::getInstance();
			$oLog->trazaLog ($e, "recuperarListadoResultadosCompleto - resultado.controller.php");	
			return null;	 
		}
  	}


  	/* método para recuperar el listado de registros. */
  	public function paginadorResultados ($fIdLiga = NULL, $fIdJugador1 = NULL,  $fIndValidado = NULL, $fFechaBatalla = NULL) {

		try {

	  		$queryDB = "SELECT count( distinct idEnfrentamiento)
						FROM mb_enfrentamientos 
						WHERE 1 = 1 ";
			// where
			if ( $fIdLiga != null && $fIdLiga > 0) { $queryDB .= " AND idLiga = " . $fIdLiga ;  }
	  		if ( $fIdJugador1 != null  && $fIdJugador1 > 0) { $queryDB .= " AND ( idJugador1 = " . $fIdJugador1 . " OR idJugador2 = " . $fIdJugador1  . " ) ";  }		
	  		if ( $fIndValidado != null && $fIndValidado < 2) { $queryDB .= " AND indValidado = " . $fIndValidado ;  }	
	  		if ( $fFechaBatalla != null  && $fFechaBatalla > 0) { $queryDB .= " AND fechaBatalla = " . $this->formatoFecha(true, $fFechaBatalla) ;  }							
						

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
			$oLog->trazaLog ($e, "paginadorResultados - resultado.controller.php");	
			return null;	 
		}
  	}

  	/* método para recuperar el listado de registros. */
  	public function recuperarResultado ( $fIdResultado ) {

		try {

	  		$queryDB = "SELECT idLiga, numFase, numRonda, idJugador1, idJugador2, resultadoJugador1, resultadoJugador2, valPinturaJug1, valPinturaJug2, idJugVictoriaConcedida, fechaBatalla, 
	  							indValidado, valDeportividadJug1, valDeportividadJug2, bandoJugador1, bandoJugador2, victoriaSector
						FROM mb_enfrentamientos 
						WHERE idEnfrentamiento = " . $fIdResultado;


			$resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

			if ($resultadoBD != null){

				foreach ($resultadoBD as $fila) {
					
					$this->oResultado = new Resultado( $fIdResultado, $fila[0], $fila[1], $fila[2], $fila[3], $fila[4], $fila[5], $fila[6], $fila[7], $fila[8], $fila[9], $fila[10], $fila[11] , $fila[12], $fila[13], $fila[14], $fila[15] , $fila[16] ) ;

					return 	$this->oResultado;	
				
				}
			}else{
				return null;
			}

		}catch(Exception $e){
			$oLog = Log::getInstance();
			$oLog->trazaLog ($e, "recuperarResultado - resultado.controller.php");	
			return null;	 
		}
  	}



  	/* validar resultado  */
  	public function validarResultado( $fIdLiga, $fIdResultado  ){

		try {

			// insertamos el nuevo registro
			$queryDB = "UPDATE mb_enfrentamientos SET
						indValidado = 1
						WHERE idLiga = " . $fIdLiga . " 
						AND idEnfrentamiento = " . $fIdResultado;
			
			$resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);

			
			if ($resultadoBD >= 1){
				return 1;				
			}else{
				return 2;
			} 

		}catch(Exception $e){
			$oLog = Log::getInstance();
			$oLog->trazaLog ($e, "validarResultado - liga.controller.php");	
			return null;	 
		}
  	}




  	/* resetear resultado  */
  	public function resetearResultado( $fIdResultado  ){

		try {
			$fIdResultado = (int) $fIdResultado;
			if ($fIdResultado <= 0) {
				return 2;
			}

			$this->oConexBD->ejecutarConsulta(
				"DELETE FROM mb_enfren_misiones_sec WHERE idEnfrentamiento = " . $fIdResultado,
				1
			);

			$queryDB = "UPDATE mb_enfrentamientos SET
						resultadoJugador1 = NULL
						,resultadoJugador2 = NULL
						,valPintura = NULL
						,valPinturaJug1 = NULL
						,valPinturaJug2 = NULL
						,valDeportividadJug1 = NULL
						,valDeportividadJug2 = NULL
						,fechaBatalla = NULL
						,indValidado = NULL
						,idJugVictoriaConcedida = NULL
						,victoriaSector = NULL
						,audAlta = NULL
						WHERE idEnfrentamiento = " . $fIdResultado;
			
			$resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);

			$estadoReset = $this->oConexBD->ejecutarConsulta(
				"SELECT resultadoJugador1, resultadoJugador2, valPinturaJug1,
						valPinturaJug2, valDeportividadJug1, valDeportividadJug2,
						fechaBatalla, indValidado
				 FROM mb_enfrentamientos WHERE idEnfrentamiento = " . $fIdResultado
			);

			
			if ($resultadoBD !== null
				&& isset($estadoReset[0])
				&& count(array_filter($estadoReset[0], function ($valor) {
					return $valor !== null;
				})) === 0){
				return 1;				
			}else{
				return 2;
			} 

		}catch(Exception $e){
			$oLog = Log::getInstance();
			$oLog->trazaLog ($e, "resetearResultado - liga.controller.php");	
			return null;	 
		}
  	}



	public function resetearValidacion( $fIdResultado  ){

		try {
			$fIdResultado = (int) $fIdResultado;
			if ($fIdResultado <= 0) {
				return 2;
			}

			$queryDB = "UPDATE mb_enfrentamientos SET
						indValidado = 0
						WHERE idEnfrentamiento = " . $fIdResultado;

			$resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);
			$estadoReset = $this->oConexBD->ejecutarConsulta(
				"SELECT indValidado FROM mb_enfrentamientos WHERE idEnfrentamiento = " . $fIdResultado
			);

			if ($resultadoBD !== null
				&& isset($estadoReset[0][0]) && (int) $estadoReset[0][0] === 0){
				return 1;
			}

			return 2;
		}catch(Exception $e){
			$oLog = Log::getInstance();
			$oLog->trazaLog ($e, "resetearValidacion - liga.controller.php");
			return null;
		}
	}



  	/* método para recuperar el listado de registros. */
  	public function recuperarMisionSec ( $fIdMisionSecundaria ) {

		try {

	  		$queryDB = "SELECT tituloMisionSecundaria, txtMisionSecundaria, numMedallas
						FROM mb_misiones_secundarias 
						WHERE idMisionSecundaria = " . $fIdMisionSecundaria;


			$resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

			$arrResultados = array ();

			if ($resultadoBD != null){

				foreach ($resultadoBD as $fila) {

					array_push($arrResultados, $fila[0], $fila[1] , $fila[2] ) ;

				}
				return $arrResultados;	
			}else{
				return null;
			}

		}catch(Exception $e){
			$oLog = Log::getInstance();
			$oLog->trazaLog ($e, "recuperarMisionSec - resultado.controller.php");	
			return null;	 
		}
  	}


  	/* método para recuperar el listado de registros. */
  	public function recuperarSelectMisionesSec ( $fIdLiga ) {

		try {

	  		$queryDB = "SELECT idMisionSecundaria, txtMisionSecundaria FROM mb_misiones_secundarias
						WHERE idLiga = " . $fIdLiga . " ORDER BY idMisionSecundaria ASC ";


			$resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

			$arrResultados = array ();

			if ($resultadoBD != null){

				foreach ($resultadoBD as $fila) {
					$arrObjeto = array();

					array_push($arrObjeto, $fila[0], $fila[1] ) ;

					array_push($arrResultados, $arrObjeto);

				}
				return $arrResultados;	
			}else{
				return null;
			}

		}catch(Exception $e){
			$oLog = Log::getInstance();
			$oLog->trazaLog ($e, "recuperarSelectMisionesSec - resultado.controller.php");	
			return null;	 
		}
  	}




  	/* método para recuperar el listado de registros. */
  	public function recuperarMisionesSecJugador( $fIdResultado, $fIdJugador1 ) {

		try {

	  		$queryDB = "SELECT idMisionSecundaria
	  					FROM mb_enfren_misiones_sec
						where idJugador1 = " . $fIdJugador1 . "
						and idEnfrentamiento = " . $fIdResultado . " ORDER BY 1";


			$resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

			$arrResultados = array ();

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
			$oLog->trazaLog ($e, "recuperarMisionesSecJugador - resultado.controller.php");	
			return null;	 
		}
  	}




  	/* método para recuperar el listado de registros. */
  	public function recuperarRondasResultados( $fIdLiga, $fIdJugador, $fNumFase ) {

		try {

	  		$queryDB = "select DISTINCT numRonda from mb_enfrentamientos
						where idLiga = " . $fIdLiga . "
						and numFase = " . $fNumFase . " 
						and ((idJugador1 = " . $fIdJugador . " and valPinturaJug2 IS NULL)
						or (idJugador2 = " . $fIdJugador . " and valPinturaJug1 IS NULL))
						 ORDER BY 1";


			$resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

			$arrResultados = array ();

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
			$oLog->trazaLog ($e, "recuperarRondasResultados - resultado.controller.php");	
			return null;	 
		}
  	}




  	/* Ranking puntos pintura. */
	   public function recuperarRankingResultados($fIdLiga) {

			try {
				$queryDB = "SELECT j.idJugador AS idJugador, j.nick,
						SUM(CASE WHEN e.fechaBatalla IS NOT NULL AND e.fechaBatalla > 0 THEN 1 ELSE 0 END) AS partidasJugadas,
						SUM(CASE WHEN e.idJugador1 = j.idJugador THEN IFNULL(e.resultadoJugador1, 0) ELSE IFNULL(e.resultadoJugador2, 0) END) AS resultado
						,SUM(CASE WHEN (e.idJugador1 = j.idJugador AND e.resultadoJugador1 > e.resultadoJugador2)
							OR (e.idJugador2 = j.idJugador AND e.resultadoJugador2 > e.resultadoJugador1) THEN 1 ELSE 0 END) AS victorias
					FROM mb_jugadores j
					LEFT JOIN mb_enfrentamientos e
						ON (e.idJugador1 = j.idJugador OR e.idJugador2 = j.idJugador)
						AND e.idLiga = " . (int) $fIdLiga . "
					WHERE j.idLiga = " . (int) $fIdLiga . "
						AND j.nick != 'zMercenario'
					GROUP BY j.idJugador, j.nick
					ORDER BY victorias DESC, resultado DESC, partidasJugadas DESC, j.nick";

				$resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);
				$arrResultados = array();

				if ($resultadoBD != null) {
					foreach ($resultadoBD as $fila) {
						$arrResultados[] = array($fila["nick"], $fila["partidasJugadas"], $fila["resultado"], $fila["idJugador"], $fila["victorias"]);
					}
				}

				return $arrResultados;

			}catch(Exception $e){
				$oLog = Log::getInstance();
				$oLog->trazaLog ($e, "recuperarRankingResultados - resultado.controller.php");
				return null;
			}
		}



	   /* Ranking puntos pintura. */
  	public function recuperarRankingPuntosPintura(  $fIdLiga ) {

		try {
			$queryDB = "SELECT j.nick,
							ROUND(IFNULL(SUM(CASE
								WHEN e.idJugador1 = j.idJugador THEN e.valPinturaJug1
								WHEN e.idJugador2 = j.idJugador THEN e.valPinturaJug2
								ELSE 0
							END) / NULLIF(COUNT(e.idEnfrentamiento), 0), 0), 2) AS puntosNumericos,
							REPLACE(CAST(ROUND(IFNULL(SUM(CASE
								WHEN e.idJugador1 = j.idJugador THEN e.valPinturaJug1
								WHEN e.idJugador2 = j.idJugador THEN e.valPinturaJug2
								ELSE 0
							END) / NULLIF(COUNT(e.idEnfrentamiento), 0), 0), 2) AS CHAR), '.', ',') AS puntosPintura
						FROM mb_jugadores j
						LEFT JOIN mb_enfrentamientos e
							ON e.idLiga = j.idLiga
							AND (e.idJugador1 = j.idJugador OR e.idJugador2 = j.idJugador)
							AND e.idJugVictoriaConcedida = 0
						WHERE j.idLiga = ?
						AND j.nick <> 'zMercenario'
						GROUP BY j.idJugador, j.nick
						ORDER BY j.idJugador";

			$resultadoBD = $this->oConexBD->ejecutarConsultaPreparada($queryDB, 'i', array((int) $fIdLiga));
			$arrResultados = array();

			if (is_array($resultadoBD)) {
				foreach ($resultadoBD as $fila) {
					$arrResultados[] = array(
						$fila[0],
						(float) ($fila[1] ?? 0),
						(string) ($fila[2] ?? '0')
					);
				}
			}

			return $arrResultados;

		}catch(Exception $e){
			$oLog = Log::getInstance();
			$oLog->trazaLog ($e, "recuperarRankingPuntosPintura - resultado.controller.php");	
			return null;	 
		}
  	}

    


  	/* Ranking puntos pintura. */
  	public function recuperarRankingPuntosMisionesSec(  $fIdLiga ) {

		try {

	  		$queryDB = "SELECT t3.nick, IFNULL(sum(numMedallas),0) as puntosMisionesSec
						FROM mb_enfren_misiones_sec t1 
						join mb_misiones_secundarias t2 
						on t1.idMisionSecundaria = t2.idMisionSecundaria
						right join mb_jugadores t3 
						on t1.idJugador1 = t3.idJugador
						and t3.idLiga = t2.idLiga
						where t3.idLiga =  " . $fIdLiga . "
						and nick != 'zMercenario'
						group by t3.idJugador, t3.nick
						ORDER BY t3.idJugador";  

			$resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

			$arrResultados = array ();

			if ($resultadoBD != null){

				foreach ($resultadoBD as $fila) {
				    $arrRankingRow = array ();

				    array_push($arrRankingRow, $fila[0], $fila[1]);
				    array_push($arrResultados, $arrRankingRow);

				}
					
				return $arrResultados;	

			}else{
				return null;
			}

		}catch(Exception $e){
			$oLog = Log::getInstance();
			$oLog->trazaLog ($e, "recuperarRankingPuntosMisionesSec - resultado.controller.php");	
			return null;	 
		}
  	}


    

  	/* Ranking puntos deportividad . */
  	public function recuperarRankingPuntosDeportividad(  $fIdLiga ) {

		try {
				$queryDB = "SELECT
						T1.idJugador,
						T1.nick,
						COUNT(T2.idEnfrentamiento) AS numPartidas,
						IFNULL(SUM(CASE
							WHEN T2.idJugador1 = T1.idJugador THEN T2.valDeportividadJug1
							ELSE T2.valDeportividadJug2
						END), 0) AS PuntosTotalesDeportividad,
						IFNULL(ROUND(SUM(CASE
							WHEN T2.idJugador1 = T1.idJugador THEN T2.valDeportividadJug1
							ELSE T2.valDeportividadJug2
						END) / NULLIF(COUNT(T2.idEnfrentamiento), 0), 2), 0) AS PuntosDeportividad
					FROM mb_jugadores T1
					LEFT JOIN mb_enfrentamientos T2
						ON (T2.idJugador1 = T1.idJugador OR T2.idJugador2 = T1.idJugador)
						AND T2.idLiga = " . (int) $fIdLiga . "
						AND T2.indValidado = 1
						AND T2.idJugVictoriaConcedida = 0
					WHERE T1.idLiga = " . (int) $fIdLiga . "
						AND T1.nick != 'zMercenario'
					GROUP BY T1.idJugador, T1.nick
					ORDER BY T1.idJugador";

			$resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

			$arrResultados = array ();

			if ($resultadoBD != null){

				foreach ($resultadoBD as $fila) {
					$arrRankingRow = array();
					array_push($arrRankingRow, $fila["nick"], str_replace('.', ',', (string) $fila["PuntosDeportividad"]), $fila["numPartidas"], $fila["idJugador"], $fila["PuntosDeportividad"], $fila["PuntosTotalesDeportividad"]);
					array_push($arrResultados, $arrRankingRow);
				}
    			//usort($arrResultados, "sortByOrder");
				return $arrResultados;	
			}else{
				return null;
			}

		}catch(Exception $e){
			$oLog = Log::getInstance();
			$oLog->trazaLog ($e, "recuperarRankingPuntosDeportividad - resultado.controller.php");	
			return null;	 
		}
  	}

  	/* método para cambiar el formato de fecha de la BD al UTC español */
  	public function formatoFecha ( $utcUsa = true, $fFecha = null ){

  		if ($fFecha != null) {
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
		}else{
			return null;
		}
	}


	// GRABAMOS BANDO ALEATORIO
	public function grabarBandoAleatorio ( $fIdLiga, $fNumFase, $idJugador1, $idJugador2, $bandoJug1, $bandoJug2  ){
		try {

				// 1. BUSCAMOS EL RESULTADO POR LOS JUGADORES INVOLUCRADOS
				 $queryDB = "SELECT idEnfrentamiento, idJugador1, idJugador2 FROM mb_enfrentamientos
							where idLiga = " . $fIdLiga . " and numFase = " . $fNumFase . " 
							and (  (idJugador2 = " . $idJugador2 . " and idJugador1 = " . $idJugador1 . ") or  (idJugador2 = " . $idJugador1 . " and idJugador1 = " . $idJugador2 . ") )";  

				$resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);


				if ($resultadoBD != null){

					foreach ($resultadoBD as $fila) {
						$idEnfrentamiento = $fila[0];
						$idJugEnf1 = $fila[1];
						$idJugEnf2 = $fila[2];
					}

				}

				// calculamos los id jugadores con los recibidos
				if ($idJugEnf1  == $idJugador2) {
					$bandoJugAux = $bandoJug1;
					$bandoJug1 = $bandoJug2;
					$bandoJug2 = $bandoJugAux;
				}

				

				// insertamos el nuevo registro
				$queryDB = "UPDATE mb_enfrentamientos SET
							bandoJugador1 = '" . $bandoJug1 . "',
							bandoJugador2 = '" . $bandoJug2 . "'
							WHERE idLiga = " . $fIdLiga . " 
							AND idEnfrentamiento = " . $idEnfrentamiento;
				
				$resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB, 1);

				

				

			}catch(Exception $e){
				$oLog = Log::getInstance();
				$oLog->trazaLog ($e, "validarResultado - liga.controller.php");	
				return null;	 
			}
	}



	// consultamos BANDO ALEATORIO
	public function validarBandoAsignado( $fIdLiga, $fNumFase, $idJugador1, $idJugador2 ){
		try {


				// 1. BUSCAMOS EL RESULTADO POR LOS JUGADORES INVOLUCRADOS
				$queryDB = "SELECT idJugador1, bandoJugador1, idJugador2, bandoJugador2 FROM mb_enfrentamientos
							where idLiga = " . $fIdLiga . " and numFase = " . $fNumFase . " 
							and (  (idJugador2 = " . $idJugador2 . " and idJugador1 = " . $idJugador1 . ") or  (idJugador2 = " . $idJugador1 . " and idJugador1 = " . $idJugador2 . ") )";  

				$resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);


				$bandosJugadores = array ();

				if ($resultadoBD != null){

					foreach ($resultadoBD as $fila) { 
					 
					 	if($fila["bandoJugador1"] == '' and $fila["bandoJugador2"]== '') 
					 		return $bandosJugadores ;

						$bandoJugador1 = array ();
					    array_push($bandoJugador1, $fila["idJugador1"], $fila["bandoJugador1"]);

						$bandoJugador2 = array ();
					    array_push($bandoJugador2, $fila["idJugador2"], $fila["bandoJugador2"]); 

						array_push($bandosJugadores, $bandoJugador1, $bandoJugador2 );

					}

				}

				

				return $bandosJugadores;


				

			}catch(Exception $e){
				$oLog = Log::getInstance();
				$oLog->trazaLog ($e, "validarBandoAsignado - liga.controller.php");	
				return null;	 
			}
	}



	// grabamos bando en los resultados
	public function grabarBandoResultados ( $fIdLiga, $fNumFase ){
			try {

				// 1. BUSCAMOS EL RESULTADO POR LOS JUGADORES INVOLUCRADOS
				 $queryDB = "SELECT idJugador, bando FROM mb_jugadores
							where idLiga = " . $fIdLiga . " and numFase = " . $fNumFase . " AND bando <> 'DOBLE'";  

				$resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);


				if ($resultadoBD != null){

					foreach ($resultadoBD as $fila) {

							$queryDBUpdate = "UPDATE mb_enfrentamientos SET
										bandoJugador1 = '" .$fila["bando"] . "'
										WHERE idLiga = " . $fIdLiga . " 
										AND idJugador1 = " . $fila["idJugador"];
							
							$resultadoBDUpdate= $this->oConexBD->ejecutarConsulta($queryDBUpdate, 1);

							$queryDBUpdate = "UPDATE mb_enfrentamientos SET
										bandoJugador2 = '" .$fila["bando"] . "'
										WHERE idLiga = " . $fIdLiga . " 
										AND idJugador2 = " . $fila["idJugador"];
							
							$resultadoBDUpdate= $this->oConexBD->ejecutarConsulta($queryDBUpdate, 1);

					}

				}

			
			
				

				

			}catch(Exception $e){
				$oLog = Log::getInstance();
				$oLog->trazaLog ($e, "grabarBandoResultados - liga.controller.php");	
				return null;	 
			}
	}




  	/* Ranking puntos pintura. */
  	public function recuperarRankingSectores(  $fIdLiga, $idJuego ) {

		try {

			// flames of war V3
			if ($idJuego == 1) {

	  			$queryDB = "SELECT numFase, sector, descFaccion as bandoJugadorGanador, count(1) as victorias FROM 
						(
							SELECT numFase, idEnfrentamiento, 
								case 
								when resultadoJugador1 > resultadoJugador2 and resultadoJugador1 >3 then idJugador1 
								 when resultadoJugador2 > resultadoJugador1 and resultadoJugador2 >3 then idJugador2 
								 else 0
								end as JugadorGanador,
								CASE  WHEN  bandoJugador1 = 3 AND  bandoJugador2 = 3 THEN  1
										WHEN resultadoJugador1 > resultadoJugador2 and resultadoJugador1 >3 and bandoJugador1 = 3 and bandoJugador2 = 2 then 1 
										 WHEN resultadoJugador1 > resultadoJugador2 and resultadoJugador1 >3 and bandoJugador1 = 3 and bandoJugador2 = 1 then 2 
										 WHEN resultadoJugador1 > resultadoJugador2 and resultadoJugador1 >3 and bandoJugador1 != 3 then bandoJugador1 
										 WHEN resultadoJugador2 > resultadoJugador1 and resultadoJugador2 >3 and bandoJugador2 = 3 and bandoJugador1 = 2 then 1
										 WHEN resultadoJugador2 > resultadoJugador1 and resultadoJugador2 >3 and bandoJugador2 = 3 and bandoJugador1 = 1 then 2
										 WHEN resultadoJugador2 > resultadoJugador1 and resultadoJugador2 >3 and resultadoJugador1 != 3 then bandoJugador2 
										
										 end as bandoJugadorGanador
								 , 
								 case when victoriaSector = 1 then 'SECTOR 1' 
								 WHEN victoriaSector = 2 then 'SECTOR 2'
								 WHEN victoriaSector = 3 then 'SECTOR 3'
								 end as Sector
							FROM mb_enfrentamientos A
							where idLiga = " . $fIdLiga . " 
							and resultadoJugador1 is not null and resultadoJugador2 is not null 
							and victoriaSector > 0 

						) A 
						JOIN mb_facciones B on A.bandoJugadorGanador = B.idFaccion and B.idJuego = ". $idJuego . "
						group by  numFase, bandoJugadorGanador, sector
						order by numFase, sector, bandoJugadorGanador";

				// flames of war V4
				}elseif($idJuego == 2){

			  		 $queryDB = "SELECT numFase, sector, descFaccion as bandoJugadorGanador, count(1) as victorias FROM 
								(
									SELECT idLiga, numFase, idEnfrentamiento, 
										case 
										when resultadoJugador1 > resultadoJugador2 and resultadoJugador1 >3 then idJugador1 
										 when resultadoJugador2 > resultadoJugador1 and resultadoJugador2 >3 then idJugador2 
										 else 0
										end as JugadorGanador,
										CASE  WHEN  bandoJugador1 = 6 AND  bandoJugador2 = 6 THEN  3
												WHEN resultadoJugador1 > resultadoJugador2 and resultadoJugador1 >3 and bandoJugador1 = 6  and bandoJugador2 = 5 then 4
												 WHEN resultadoJugador1 > resultadoJugador2 and resultadoJugador1 >3 and bandoJugador1 = 6  and bandoJugador2 = 4 then 5
												 WHEN resultadoJugador1 > resultadoJugador2 and resultadoJugador1 >3 and bandoJugador1 != 6  then CASE WHEN bandoJugador1 = 5 THEN 5 ELSE 4 END
												 WHEN resultadoJugador2 > resultadoJugador1 and resultadoJugador2 >3 and bandoJugador2 = 6  and bandoJugador1 = 5 then 4
												 WHEN resultadoJugador2 > resultadoJugador1 and resultadoJugador2 >3 and bandoJugador2 = 6  and bandoJugador1 = 4 then 5
												 WHEN resultadoJugador2 > resultadoJugador1 and resultadoJugador2 >3 and resultadoJugador1 != 6  then  CASE WHEN bandoJugador2 =5 THEN 5 ELSE 4 END
												 end as bandoJugadorGanador, 
										 case when victoriaSector = 1 then 'SECTOR 1' 
										 WHEN victoriaSector = 2 then 'SECTOR 2'
										 WHEN victoriaSector = 3 then 'SECTOR 3'
										 end as Sector
									FROM mb_enfrentamientos A
									where idLiga =  " . $fIdLiga . " 
									and resultadoJugador1 is not null and resultadoJugador2 is not null 
									and victoriaSector > 0 

								) A
								JOIN mb_facciones B on A.bandoJugadorGanador = B.idFaccion and B.idJuego = ". $idJuego . "
								group by  numFase, bandoJugadorGanador, sector
								order by numFase, sector, bandoJugadorGanador";  
				}
			$resultadoBD = $this->oConexBD->ejecutarConsulta($queryDB);

			$arrResultados = array ();

			if ($resultadoBD != null){

				foreach ($resultadoBD as $fila) {
				    $arrRankingRow = array ();

				    array_push($arrRankingRow, $fila[0], $fila[1], $fila[2], $fila[3]);
				    array_push($arrResultados, $arrRankingRow);

				}
					
				return $arrResultados;	

			}else{
				return null;
			}

		}catch(Exception $e){
			$oLog = Log::getInstance();
			$oLog->trazaLog ($e, "recuperarRankingSectores - resultado.controller.php");	
			return null;	 
		}
  	}


}



?>