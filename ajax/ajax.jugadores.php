<?php
	require_once __DIR__ . '/../config/auth.php';
	

		// importamos librerias
		require_once("../controller/controller.php");
		/* cargamos clases de conexion a bd y log */
		require_once ("../config/db.class.php");

		$fIdLiga = (isset($_POST['fIdLiga'])? $_POST["fIdLiga"] : null);
		$fNumFase = (isset($_POST['fNumFase'])? $_POST["fNumFase"] : null);
		$fNumRonda = (isset($_POST['fNumRonda'])? $_POST["fNumRonda"] : null);
		$fIdJugador = (isset($_POST['fIdJugador'])? $_POST["fIdJugador"] : null);
		$validarGrabResultado = (isset($_POST['validarGrabResultado'])? $_POST["validarGrabResultado"] : null);

		$oControllerLiga = new controllerLiga();
		$oControllerJugador = new controllerJugador();

		// options para los select de los formularios
		// FASES
		$selectJugadores = "<select name=\"fIdJugador\" id=\"fIdJugador\" data-validation=\"required\" > ";

		if($validarGrabResultado){
			$arrJugadores =  $oControllerJugador->recuperarSelectJugadores( $fIdLiga, null, $fNumRonda, true);
		}else{
			$arrJugadores =  $oControllerJugador->recuperarSelectJugadores( $fIdLiga, null, $fNumRonda, false);
		}
		if (is_array($arrJugadores) && count($arrJugadores) >= 1 ){
			$selectJugadores .= "\n<option value=\"0\"></option>";
			foreach ($arrJugadores as $fila){
				$selectJugadores .= "\n<option value=\"" . $fila[0] . "\" " .(($fIdJugador <> null && $fIdJugador == $fila[0])? "selected" : "" ). "\">" .$fila[1]  . "</option>";
			}
		}else{
			$selectJugadores .= "\n<option value=\"0\" selected>No hay resultados</option>";
		}
		$selectJugadores .= "</select>";

		echo $selectJugadores;



?>