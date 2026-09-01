<?php

		// importamos librerias
		require_once("../controller/controller.php");
		/* cargamos clases de conexion a bd y log */
		require_once ("../config/db.class.php");
		require_once ("../model/jugador.class.php");

		$fIdLiga = (isset($_POST['fIdLiga'])? $_POST["fIdLiga"] : null);
		$fNumFase = (isset($_POST['fNumFase'])? $_POST["fNumFase"] : null);
		$fNumRonda = (isset($_POST['fNumRonda'])? $_POST["fNumRonda"] : null);
		$cruces = (isset($_POST['cruces'])? $_POST["cruces"] : null);

		$oControllerLiga = new controllerLiga();
		$oControllerEnfrentamiento = new controllerEnfrentamiento();
		$oControllerJugador = new controllerJugador();

		// primero borramos los enfrentamientos de la liga-fase-ronda
		$oControllerEnfrentamiento->borrarEnfrentamientosFaseRonda($fIdLiga, $fNumFase, $fNumRonda);



		// preparamos los cruces
		$arrCruces = array();
		$arrCruces = explode("#", $cruces);

		// luego registramos los nuevos enfrentamientos
		foreach($arrCruces as $cruce){
			$jugadores = explode("-", $cruce);
			
			// recuperamos los bandos de cada jugador
			$jug1 = $oControllerJugador->recuperarDatosJugador( $jugadores[0] );
			$jug2 = $oControllerJugador->recuperarDatosJugador( $jugadores[1] );

			$oControllerEnfrentamiento->altaEnfrentamiento( $fIdLiga, $fNumFase, $fNumRonda, $jugadores[0], $jugadores[1], $jug1->bando, $jug2->bando );
		}



		echo "<div id=\"mensaje-ok\">Cruces grabados correctamente.</div>";
		echo "<form action=\"gestion-enfrentamientos.php\" id=\"form-volver\" name=\"form-volver\" method=\"POST\"><input type=\"hidden\" id=\"fIdLiga\" name=\"fIdLiga\" value=\"".$fIdLiga."\"/>";
		echo "<input type=\"hidden\" id=\"fNumFase\" name=\"fNumFase\" value=\"".$fNumFase."\"/>";
		echo "<input type=\"hidden\" id=\"accionForm\" name=\"accionForm\" value=\"1\"/>";
		echo "<input type=\"hidden\" id=\"fNumRonda\" name=\"fNumRonda\" value=\"".$fNumRonda."\"/></form>";
		echo "<div id=\"div-volver\"><span class=\"btn-volver\" onClick=\"$('#form-volver').submit();\">Volver</span></div>";


?>