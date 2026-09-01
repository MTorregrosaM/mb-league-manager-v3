<?php
	

		// importamos librerias
		require_once("../controller/controller.php");
		/* cargamos clases de conexion a bd y log */
		require_once ("../config/db.class.php");

		$fIdLiga = (isset($_POST['fIdLiga'])? $_POST["fIdLiga"] : null);
		$fNumRonda = (isset($_POST['fNumRonda'])? $_POST["fNumRonda"] : null);

		$oControllerLiga = new controllerLiga();

		// options para los select de los formularios
		// FASES
		$selectRondas = "<select name=\"fNumRonda\" id=\"fNumRonda\" data-validation=\"required\" > ";
		$arrRondas =  $oControllerLiga->recuperarSelectRondas( $fIdLiga );

		if (is_array($arrRondas) && count($arrRondas) >= 1 ){
			foreach ($arrRondas as $fila){
				$selectRondas .= "\n<option value=\"" . $fila[0] . " " .(($fNumRonda <> null && $fNumRonda == $fila[0])? "selected" : "" ). "\">" .$fila[0]  . "</option>";
			}
		}
		$selectRondas .= "</select>";

		echo $selectRondas;

?>