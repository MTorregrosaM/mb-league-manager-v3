<?php
	require_once __DIR__ . '/../config/auth.php';
	

		// importamos librerias
		require_once("../controller/controller.php");
		/* cargamos clases de conexion a bd y log */
		require_once ("../config/db.class.php");

		$fIdLiga = (isset($_POST['fIdLiga'])? $_POST["fIdLiga"] : null); 
		$fIdJugador1 = (isset($_POST['fIdJugador1'])? $_POST["fIdJugador1"] : null);
		$fNumFase = (isset($_POST['fNumFase'])? $_POST["fNumFase"] : null);
		$selectJugadores = "<select name=\"fIdJugador2\" id=\"fIdJugador2\" data-validation=\"required\" > ";
		$oControllerJugador = new controllerJugador();

		// options para los select de los formularios
		// FASES
		if ($fIdJugador1 == null) {
			echo "\n<input type=\"text\" id=\"fIdJugador2Nick\" name=\"fIdJugador2Nick\" value=\"Jugador 2\" style=\"background: red !important; color: white !important\" style=\"background: #EEE\" readonly/>";
		}else{

			$arrJugadores =  $oControllerJugador->recuperarJugadoresContrincante( $fIdLiga, $fIdJugador1, $fNumFase );

			if (is_array($arrJugadores) && count($arrJugadores) >= 1 ){

			
				foreach ($arrJugadores as $fila){
					$idJugador2 = ($fila[1] == $fIdJugador1)? $fila[0] : $fila[1]; 
					$selectJugadores .= "\n<option value=\"" . $idJugador2 . "\">Ronda " .  $fila[3] . " - " . $fila[2]   . "</option>";
					
				}
			}else{
				$selectJugadores .= "\n<option value=\"0\" selected>No hay enfrentamientos</option>";
			}
			$selectJugadores .= "</select>";

			echo $selectJugadores;



		}


?>