<?php
	

		// importamos librerias
		require_once("../controller/controller.php");
		/* cargamos clases de conexion a bd y log */
		require_once ("../config/db.class.php");

		$fIdLiga = (isset($_POST['fIdLiga'])? $_POST["fIdLiga"] : null);
		$fNumFase = (isset($_POST['fNumFase'])? $_POST["fNumFase"] : null);
		$faseActiva = (isset($_POST['faseActiva'])? $_POST["faseActiva"] : null);

		$oControllerLiga = new controllerLiga();

		// options para los select de los formularios
		// FASES
		$selectFases = "<select name=\"fNumFase\" id=\"fNumFase\" data-validation=\"required\" > ";
		$arrFases =  $oControllerLiga->recuperarSelectFases( $fIdLiga, $faseActiva );

		if (is_array($arrFases) && count($arrFases) >= 1 ){
			foreach ($arrFases as $fila){
				$selectFases .= "\n<option value=\"" . $fila[0] . "\">" .$fila[0]  . "</option>";
			}
		}
		$selectFases .= "</select>";

		echo $selectFases;

?>