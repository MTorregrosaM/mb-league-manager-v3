<?php
    require_once __DIR__ . '/../config/db.class.php';
    require_once __DIR__ . '/../config/security.php';
    validarCsrfPublico();
    

        // importamos librerias
        require_once("../controller/controller.php");
        /* cargamos clases de conexion a bd y log */
        require_once ("../config/db.class.php");
        require_once ("../model/class.php");

        $fIdLiga = (isset($_POST['fIdLiga'])? $_POST["fIdLiga"] : null);
        $fNumRonda = (isset($_POST['fNumRonda'])? $_POST["fNumRonda"] : null);
        $fIdJugador1 = (isset($_POST['fIdJugador1'])? $_POST["fIdJugador1"] : null);
        $fNumFase = (isset($_POST['fNumFase'])? $_POST["fNumFase"] : null);
        exigirLigaActivaPublica($fIdLiga);
        //$fIdJuego = (isset($_POST['fIdJuego'])? $_POST["fIdJuego"] : 5);

        $oControllerJugador = new controllerJugador();
        $oControllerLiga = new controllerLiga();
    
        $oLiga = $oControllerLiga->recuperarDatosLiga($fIdLiga);
        // options para los select de los formularios
        // FASES
        if ($fIdJugador1 == null) {
            echo "\n<input type=\"text\" id=\"fIdJugador2Nick\" name=\"fIdJugador2Nick\" value=\"Jugador 2\" class=\"input-contrincante\" disabled/>";
        }else{
            
            $minResultado = 0;
            $maxResultado = 0;

            if($oLiga->idJuego <= 2) { // FLAMES OF WAR
                $minResultado = 1;
                $maxResultado = (($oLiga->idJuego == 1)? 6 : 8);
            }

            $arrJugadores =  $oControllerJugador->recuperarJugadorContrincante( $fIdLiga, $fNumRonda, $fIdJugador1, $fNumFase );
            
            if (is_array($arrJugadores) && count($arrJugadores) >= 1 ){
                // calculamos el contrincante
                $idJugador2 = ($arrJugadores[2] == $fIdJugador1)? $arrJugadores[1] : $arrJugadores[2]; 
                echo "\n<input type=\"hidden\" id=\"fIdEnfrentamiento\" name=\"fIdEnfrentamiento\" value=\"" . $arrJugadores[0] . "\" />";
                echo "\n<input type=\"hidden\" id=\"fIdJugador2\" name=\"fIdJugador2\" value=\"" . $idJugador2 . "\"  />";
                echo "\n<input type=\"text\" id=\"fIdJugador2Nick\" name=\"fIdJugador2Nick\" value=\"" . $arrJugadores[3] . "\" class=\"input-contrincante\" readonly/>";
                echo "\n<input type=\"hidden\" id=\"fResultadoJugador1Aux\" name=\"fResultadoJugador1Aux\" value=\"" . (($arrJugadores[4] != null)? $arrJugadores[4] : $minResultado) . "\"  />";
                echo "\n<input type=\"hidden\" id=\"fResultadoJugador2Aux\" name=\"fResultadoJugador2Aux\" value=\"" . (($arrJugadores[5] != null)? $arrJugadores[5] : $maxResultado) . "\"  />";
                echo "\n<input type=\"hidden\" id=\"fFechaBatallaAux\" name=\"fFechaBatallaAux\" value=\"" . $arrJugadores[6] . "\"  />";
            }else{
                echo "\n<input type=\"text\" id=\"fIdJugador2Nick\" name=\"fIdJugador2Nick\" value=\"&nbsp;\" class=\"input-contrincante\" disabled/>";
            }
        


        }


?>