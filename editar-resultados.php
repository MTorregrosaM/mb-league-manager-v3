<?php require_once __DIR__ . "/config/auth.php"; ?>
<?php require_once __DIR__ . "/inc/puntuacion-fow.php"; ?>
<html lang="es" data-bs-theme="dark">
<head>
  <?php require_once ("cabecera.php"); ?>
</head>

<body>


<?php
  require_once ("model/class.php");
  require_once ("config/config.php");
  require_once ("controller/controller.php");
  

  try {

    /* variables */
    $oControllerEnfrentamiento = new controllerEnfrentamiento();
    $oControllerLiga = new controllerLiga();
    $oControllerJugador = new controllerJugador();

    $paginaActiva = "editar-resultados.php";
    $grid = "";
    $numPags = 1;
    $pagActual = 1;
    $numRegs = 10;
    $paginador = "";
    $txtAltaModBoton = "";
    $txtAltaModH3 = "";
    $comprobarBorrado = false;
    $mensajeBorrado = "";
    $comprobarAltaMod = false;
    $mensajeAltaMod = "";
    $comprobarMod = false;
    $mensajeMod = "";


    // variables POST
    $accionForm = (isset($_POST["accionForm"]))? $_POST["accionForm"] : 1;
    $fIdLiga = (isset( $_POST["fIdLiga"]))? $_POST["fIdLiga"] : (isset($_SESSION["fIdLiga"])? $_SESSION["fIdLiga"] : "");
    $fNumFase = (isset( $_POST["fNumFase"]))? $_POST["fNumFase"] : 0;
    $fNumRonda = (isset( $_POST["fNumRonda"]))? $_POST["fNumRonda"] : 1;
    $fIdJugador1 = (isset( $_POST["fIdJugador1"]))? $_POST["fIdJugador1"] : ((isset( $_POST["fIdJugador"]))? $_POST["fIdJugador"] : 0);
    $fIdJugador2 = (isset( $_POST["fIdJugador2"]))? $_POST["fIdJugador2"] : 0;  
    $fIdJugador = (isset( $_POST["fIdJugador"]))? $_POST["fIdJugador"] : 0;   
    $fIdJugador2Nick = (isset( $_POST["fIdJugador2Nick"]))? $_POST["fIdJugador2Nick"] : "";
    $fClaveCifrada = (isset( $_POST["fClaveCifrada"]))? $_POST["fClaveCifrada"] : "";
    $fFechaBatalla = (isset( $_POST["fFechaBatalla"]))? $_POST["fFechaBatalla"] : "";
    $fIdEnfrentamiento = (isset( $_POST["fIdEnfrentamiento"]))? $_POST["fIdEnfrentamiento"] : "";
    $fValPinturaJug1 = (isset( $_POST["fValPinturaJug1"]))? $_POST["fValPinturaJug1"] : 0;
    $fValPinturaJug2 = (isset( $_POST["fValPinturaJug2"]))? $_POST["fValPinturaJug2"] : 0;
    $fValDeportividadJug1 = (isset( $_POST["fValDeportividadJug1"]))? $_POST["fValDeportividadJug1"] : 1;
    $fValDeportividadJug2 = (isset( $_POST["fValDeportividadJug2"]))? $_POST["fValDeportividadJug2"] : 1;
    /*$fResultadoJugador1 = (isset( $_POST["fResultadoJugador1"]) && $_POST["fResultadoJugador1"] != '' && $_POST["fResultadoJugador1"] != null)? $_POST["fResultadoJugador1"] : 1; 
    $fResultadoJugador2 = (isset( $_POST["fResultadoJugador2"]) && $_POST["fResultadoJugador2"] != '' && $_POST["fResultadoJugador2"] != null)? $_POST["fResultadoJugador2"] : 6; */
    $fIdMisionSecJug11 = (isset( $_POST["fIdMisionSecJug11"]))? $_POST["fIdMisionSecJug11"] : "";
    $fIdMisionSecJug12 = (isset( $_POST["fIdMisionSecJug12"]))? $_POST["fIdMisionSecJug12"] : "";
    $fIdMisionSecJug13 = (isset( $_POST["fIdMisionSecJug13"]))? $_POST["fIdMisionSecJug13"] : "";
    $fIdMisionSecJug14 = (isset( $_POST["fIdMisionSecJug14"]))? $_POST["fIdMisionSecJug14"] : "";
    $fIdMisionSecJug21 = (isset( $_POST["fIdMisionSecJug21"]))? $_POST["fIdMisionSecJug21"] : "";
    $fIdMisionSecJug22 = (isset( $_POST["fIdMisionSecJug22"]))? $_POST["fIdMisionSecJug22"] : "";
    $fIdMisionSecJug23 = (isset( $_POST["fIdMisionSecJug23"]))? $_POST["fIdMisionSecJug23"] : "";
    $fIdMisionSecJug24 = (isset( $_POST["fIdMisionSecJug24"]))? $_POST["fIdMisionSecJug24"] : "";
    $fIndValidado = (isset( $_POST["fIndValidado"]))? $_POST["fIndValidado"] : 2;
    $fVictoriaSector = (isset( $_POST["fVictoriaSector"]))? $_POST["fVictoriaSector"] : 0; 
    $fIdJugVictoriaConcedida = (isset( $_POST["fIdJugVictoriaConcedida"]))? $_POST["fIdJugVictoriaConcedida"] : "";
    $fResultadoRadio = estadoResultadoFow((isset( $_POST["fResultadoRadio"]))? $_POST["fResultadoRadio"] : 1);
    $resultadoValido = true;
      
    /**********************************************************************************************/
    /**********************************************************************************************/
    /* SEGUN EL TIPO DE JUEGO, CAMBIAN LOS LÍMIETES 
      IDJUEGO    |  DESC  | MIN  |  MAX
      ---------------------------------------
      1      | FOW V3   | 1  |  6
      2      | FOW V4   | 1  |  8
      3      | WH40K    | 0  |  0
      4          | BOLT ACTION
    /**********************************************************************************************/
    
    if ($fIdLiga != 0) {
      $oLiga = $oControllerLiga->recuperarDatosLiga($fIdLiga);

      if ($oLiga !== null && (int) $oLiga->indActivo === 1) {
        // FLAMES OF WAR
        if($oLiga->idJuego <= 2 ){
        $maxResultado = 8;
        $maxResultadoSlider = $maxResultado; 
        $minResultado = 0;
      
        $fResultadoJugador1 = (isset( $_POST["fResultadoJugador1"]) && $_POST["fResultadoJugador1"] != '')? $_POST["fResultadoJugador1"] : 1; 
        $fResultadoJugador2 = (isset( $_POST["fResultadoJugador2"]) && $_POST["fResultadoJugador2"] != '')? $_POST["fResultadoJugador2"] : $maxResultado;         
      
      // GUILD BALL  
        } else  if($oLiga->idJuego == 5 ){
        $maxResultado = 12;
        $minResultado = 0;
        $maxResultadoSlider = $maxResultado; 
      
        $fResultadoJugador1 = (isset( $_POST["fResultadoJugador1"]) && $_POST["fResultadoJugador1"] != '')? $_POST["fResultadoJugador1"] : $minResultado; 
        $fResultadoJugador2 = (isset( $_POST["fResultadoJugador2"]) && $_POST["fResultadoJugador2"] != '')? $_POST["fResultadoJugador2"] : $maxResultado;      

      // RESTO DE JUEGOS
        } else {
          $maxResultado = 0; // no aplica a resto de juegos
          $minResultado = 0;
          $fResultadoJugador1 = $fResultadoRadio;
          $maxResultadoSlider = $maxResultado;
          if($fResultadoRadio == 3) {
            $fResultadoJugador2 = 0;
          }elseif($fResultadoRadio == 1){
            $fResultadoJugador2 = 1;
          }else{
            $fResultadoJugador2 = 3;
          }
        }
      } else {
        $fIdLiga = null;
      }
    }

  
    /**********************************************************************************************/
 
      // USUARIO
      $oControllerUsuario = new controllerUsuario();
    $ligasUsuario = $oControllerUsuario->recuperarLigasUsuario( $_SESSION["usuario"] );

      $pagActual = (isset($_POST["pagActual"]))? $_POST["pagActual"] : 1;
    $fIdEnfrentamientoReset = (isset($_POST["fIdEnfrentamientoReset"]))? $_POST["fIdEnfrentamientoReset"] : "";
    $fIdEnfrentamientoEditar = (isset($_POST["fIdEnfrentamientoEditar"]))? $_POST["fIdEnfrentamientoEditar"] : "";

    if ( $fIdJugador1 > 0 ){
      $oJugador1 = $oControllerJugador->recuperarDatosJugador( $fIdJugador1 );
      $oJugador2 = $oControllerJugador->recuperarDatosJugador( $fIdJugador2 );
    }

    // options para los select de los formularios
    // LIGAS
    $arrLigas =  $oControllerLiga->recuperarSelectLigas(null, false,  $ligasUsuario, false, true );
    $selectLigasSelected = ($fIdLiga != null ) ? $fIdLiga : 0;    
    $selectLigas = "<option value=''></option>\n";
    if (!empty($arrLigas) && is_array($arrLigas)   ){
      foreach ($arrLigas as $fila){
        $selectLigas .= "\n<option value=\"" . $fila[0] . "\" ". (($selectLigasSelected == $fila[0] && $selectLigasSelected > 0)? "selected" : "") ." >" .$fila[1]  . "</option>";
      }
    }

    if ($fIdLiga != null){

      // JUGADORES

      $arrJugadores =  $oControllerJugador->recuperarSelectJugadores( $fIdLiga, null, null, false ) ;

      $selectJugadoresSelected = ($fIdJugador1 != null ) ? $fIdJugador1 : 0;
      $selectJugadores = "";

      if (!empty($arrJugadores) && is_array($arrJugadores)) {
        foreach ($arrJugadores as $fila) {
            $selectJugadores .= "\n<option value=\"" . $fila[0] . "\" " . 
                (($selectJugadoresSelected == $fila[0] && $selectJugadoresSelected > 0) ? "selected" : "") . 
                " >" . $fila[1] . "</option>";
        }
    }
    
      // JUGADORES 2
      $arrJugadores2 =  $oControllerJugador->recuperarSelectJugadores( $fIdLiga, null, null, false ) ;

      $selectJugadoresSelected2 = ($fIdJugador2 != null ) ? $fIdJugador2 : 0;
      $selectJugadores2 = "";

      if (!empty($arrJugadores2) && is_array($arrJugadores2) >= 1 ){
        foreach ($arrJugadores2 as $fila){
          $selectJugadores2 .= "\n<option value=\"" . $fila[0] . "\" ". (($selectJugadoresSelected2 == $fila[0] && $selectJugadoresSelected2 > 0)? "selected" : "") ." >" .$fila[1]  . "</option>";
        }
      }


      // FASES
      $selectFases = "";
      $arrFases =  $oControllerLiga->recuperarSelectFases( $fIdLiga );
      $selectFasesSelected = ($fNumFase != null ) ? $fNumFase : 0;

      if (!empty($arrFases) && is_array($arrFases)  ){
        foreach ($arrFases as $fila){
          $selectFases .= "\n<option value=\"" . $fila[0] . "\" ". (($selectFasesSelected == $fila[0] && $selectFasesSelected > 0)? "selected" : "") ." >" .$fila[0]  . "</option>";
        }
      }

      // RONDAS
      $arrRondas =  $oControllerLiga->recuperarSelectRondas( $fIdLiga );

      $selectRondasSelected = ($fNumRonda != null ) ? $fNumRonda : 1;
      $selectRondas = ""; 
      if (!empty($arrRondas) && is_array($arrRondas)  ){
        foreach ($arrRondas as $fila){
          $selectRondas .= "\n<option id=\"" . $fila[0] . "\" value=\"" .  $fila[0] . "\" >" .$fila[0]  . "</option>";
        }
      }

      // SECTOR VICTORIA
      $selectVictoriaSector = "<option value='0' ". (($fVictoriaSector == 0 )? " selected " : "" ) ."></option>";
      $selectVictoriaSector .= "<option value='1' ". (($fVictoriaSector == 1 )? " selected " : "" ) .">Arnhem</option>";
      $selectVictoriaSector .= "<option value='2' ". (($fVictoriaSector == 2 )? " selected " : "" ) .">Gotenstellung</option>";
      $selectVictoriaSector .= "<option value='3' ". (($fVictoriaSector == 3 )? " selected " : "" ) .">Minsk</option>";


      


      /*    
        2. ALTA
        3. ELIMINAR
        4. MODIFICAR
      */

      /********************************/
      /* 3. ELIMINAR  */
      /* DEBE EJECUTARSE ANTES QUE EL BUSCADOR, PARA MOSTRAR LUEGO EL GRID ACTUALIZADO
      /********************************/
      if ( $accionForm == 3 ){
        $comprobarBorrado = $oControllerEnfrentamiento->resetearEnfrentamiento( $fIdEnfrentamiento );
    
        $mensajeBorrado .= "<div id=\"". (($comprobarBorrado)? "mensaje-ok" : "mensaje-error") ."\">". (($comprobarBorrado)? "Enfrentamiento reseteado correctamente." : "Se ha producido un error en su solicitud.") ."</div>";
      
      }

      /********************************/
      /* 1. BUSCADOR */
      /********************************/
      if ($accionForm != 2 && $accionForm != 4) {

        /********************************/
        /* PAGINADOR */
        /********************************/
        $numRegs = $oControllerEnfrentamiento->paginadorEnfrentamientos ($fIdLiga, $fIdJugador1, $fIndValidado, $fFechaBatalla);
        $numPags = ceil( $numRegs / 10) ;
        require_once("paginador.inc");


        /********************************/
        /* GRID DATOS */
        /********************************/
        /* SI SE ENVIA EL FORMULARIO DE BÚSQUEDA, MANDAMOS PARÁMETROS PARA FILTRAR */
		$arrEnfrentamientos = array();
        if (isset ($_POST["accionForm"]) && ($_POST["accionForm"] == 1 || $_POST["accionForm"] == 3)){    
          $arrEnfrentamientos = $oControllerEnfrentamiento->recuperarListadoEnfrentamientosCompleto ( $fIdLiga, $fIdJugador1,  $fFechaBatalla,  $fIndValidado, ($pagActual-1)); 

        }else{
          $arrEnfrentamientos = $oControllerEnfrentamiento->recuperarListadoEnfrentamientosCompleto( $fIdLiga, null, null,  NULL, 0);
        }

        $grid="";
        // comprobamos que haya datos 
        if (!empty($arrEnfrentamientos) >= 1){
 
          $grid  = "<table class=\"table-6\">\n
                <tr>
                <th>Fase </th>
                <th>Fecha</th>
                <th>Jugador1</th>
                <th>Jugador2</th>
                <th>Resultado</th>
                <th class=\"td-acciones\"></th>
                </tr>\n";

          foreach($arrEnfrentamientos as $fila){
            // gestion de correos
            $oJugador1 = $oControllerJugador->recuperarDatosJugador( $fila[4] );
            $oJugador2 = $oControllerJugador->recuperarDatosJugador( $fila[5]  );
          

            // misiones secundarias
            $arrMisionesSecJug1BD = $oControllerEnfrentamiento->recuperarMisionesSecJugador( $fila[0], $fila[4] );
            $arrMisionesSecJug2BD = $oControllerEnfrentamiento->recuperarMisionesSecJugador( $fila[0], $fila[5] );

            $fIdMisionSecJug11 = (isset($arrMisionesSecJug1BD[0]))? $arrMisionesSecJug1BD[0] : 0;
            $fIdMisionSecJug12 = (isset($arrMisionesSecJug1BD[1]))? $arrMisionesSecJug1BD[1] : 0;
            $fIdMisionSecJug13 = (isset($arrMisionesSecJug1BD[2]))? $arrMisionesSecJug1BD[2] : 0;
            $fIdMisionSecJug14 = (isset($arrMisionesSecJug1BD[3]))? $arrMisionesSecJug1BD[3] : 0;
            
            $fIdMisionSecJug21 = (isset($arrMisionesSecJug2BD[0]))? $arrMisionesSecJug2BD[0] : 0;
            $fIdMisionSecJug22 = (isset($arrMisionesSecJug2BD[1]))? $arrMisionesSecJug2BD[1] : 0;
            $fIdMisionSecJug23 = (isset($arrMisionesSecJug2BD[2]))? $arrMisionesSecJug2BD[2] : 0;
            $fIdMisionSecJug24 = (isset($arrMisionesSecJug2BD[3]))? $arrMisionesSecJug2BD[3] : 0;
          

            // SI HEMOS BUSCADO POR UN JUGADOR, LO PINTAMOS DE OTRO COLOR
            $classJugadorBuscado = ($fila[4] == $fIdJugador1) ? "class=\"jugadorSeleccionado\"" : "";
            $imgStar = "<img class=\"star-deportividad\" src=\"recursos/img/star.svg\" title=\"Deportividad\"/>";
            $imgFlag = "<img class=\"star-deportividad\" src=\"recursos/img/flag.svg\" title=\"Victoria concedida\"/>";


            $grid .="\n<tr><td>" . $fila[2] . " (" . $fila[3]  . ")</td><td>" . $fila[10] . "</td><td ". $classJugadorBuscado . ">" .  $oJugador1->nick ." (" ;

            // ESTRELLAS DEPORTIVIDAD
            if ($fila[12] > 0){
                for($i = 1; $i<= $fila[12]; $i++) { $grid .= $imgStar; }
            }else if($fila[14] > 0) {
              $grid .=  $imgFlag;   
            }else{
              $grid .= "<strong>?</strong>";
            }
            $grid .= ")</td>";

            // SI HEMOS BUSCADO POR UN JUGADOR, LO PINTAMOS DE OTRO COLOR
            $classJugadorBuscado = ($fila[5] == $fIdJugador1) ? "class=\"jugadorSeleccionado\"" : "";
            $imgStar = "<img class=\"star-deportividad\" src=\"recursos/img/star.svg\" title=\"Deportividad\"/>";
            $imgFlag = "<img class=\"star-deportividad\" src=\"recursos/img/flag.svg\" title=\"Victoria concedida\"/>";

            $grid .= "<td ". $classJugadorBuscado . ">" . $oJugador2->nick . " (";

            // ESTRELLAS DEPORTIVIDAD
            if ($fila[13] > 0){
                for($i = 1; $i<= $fila[13]; $i++) { $grid .= $imgStar; }
            }else if($fila[14] > 0) {
              $grid .= $imgFlag;  
            }else{
              $grid .= "<strong>?</strong>";
            }

            $grid .= ")</td>";
            $grid .= "<td class=\"align-center\">" .  $fila[6]. " - " . $fila[7] . "</td>";
            $grid .= "<td class=\"align-center td-acciones\">";

            
            if($fila[11] == 0) {
              $grid .= "<img src=\"recursos/img/ok.svg\"  alt=\"validar-resultado-".$fila[0]."\" class=\"btn-validar-resultado\"/ onClick=\"validarResultado(" . $fila[0] . "," . $fila[1] . ",'" . $fIdJugador1 . "','" .  $fFechaBatalla . "','" .$fIndValidado . "'," . $pagActual .");\">";
            }else{
              $icon = "";
               if ( ($fila[10] != null && $fila[8]  == 0) || ($fila[10] != null && $fila[9] == 0) || $fila[10] == null ){
                $icon = "icon_info_pend_jugador";
              }else{
                $icon =  "icon_info" ;
              }

              $grid .= " <form name=\"form-editar-".$fila[0]."\" id=\"form-editar-".$fila[0]."\" method=\"POST\" class=\"form-btn-acciones\">
                  <input type=\"hidden\" name=\"csrf_token\" value=\"".htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8')."\" />
                    <input type=\"hidden\" name=\"accionForm\" id=\"accionForm\" value=\"4\"/>
                    <input type=\"hidden\" name=\"fIdEnfrentamiento\" id=\"fIdEnfrentamiento\" value=\"". $fila[0] ."\" />
                    <input type=\"hidden\" name=\"fIdLiga\" id=\"fIdLiga\" value=\"".$fila[1]."\"/>
                    <input type=\"hidden\" name=\"fNumFase\" id=\"fNumFase\" value=\"". $fila[2] ."\" />
                    <input type=\"hidden\" name=\"fNumRonda\" id=\"fNumRonda\" value=\"". $fila[3] ."\" />
                    <input type=\"hidden\" name=\"fIdJugador1\" id=\"fIdJugador1\" value=\"". $fila[4] ."\" />
                    <input type=\"hidden\" name=\"fIdJugador2\" id=\"fIdJugador2\" value=\"". $fila[5] ."\" />
                    <input type=\"hidden\" name=\"fResultadoJugador1\" id=\"fResultadoJugador1\" value=\"". $fila[6] ."\" />
                    <input type=\"hidden\" name=\"fResultadoRadio\" id=\"fResultadoRadio\" value=\"". $fila[6] ."\" />                    
                    <input type=\"hidden\" name=\"fResultadoJugador2\" id=\"fResultadoJugador2\" value=\"". $fila[7] ."\" />
                    <input type=\"hidden\" name=\"fValPinturaJug1\" id=\"fValPinturaJug1\" value=\"". $fila[8] ."\" />
                    <input type=\"hidden\" name=\"fValPinturaJug2\" id=\"fValPinturaJug2\" value=\"". $fila[9] ."\" />
                    <input type=\"hidden\" name=\"fFechaBatalla\" id=\"fFechaBatalla\" value=\"". $fila[10] ."\" />
                    <input type=\"hidden\" name=\"fIndValidado\" id=\"fIndValidado\" value=\"". $fila[11] ."\" />
                    <input type=\"hidden\" name=\"fValDeportividadJug1\" id=\"fValDeportividadJug1\" value=\"". $fila[12] ."\" />
                    <input type=\"hidden\" name=\"fValDeportividadJug2\" id=\"fValDeportividadJug2\" value=\"". $fila[13] ."\" />
                    <input type=\"hidden\" name=\"fIdJugVictoriaConcedida\" id=\"fIdJugVictoriaConcedida\" value=\"". $fila[14] ."\" />
                    <input type=\"hidden\" name=\"fVictoriaSector\" id=\"fVictoriaSector\" value=\"". $fila[15] ."\" />
                    <input type=\"hidden\" name=\"fIdMisionSecJug11\" id=\"fIdMisionSecJug11\" value=\"". $fIdMisionSecJug11 ."\" />
                    <input type=\"hidden\" name=\"fIdMisionSecJug12\" id=\"fIdMisionSecJug12\" value=\"". $fIdMisionSecJug12 ."\" />
                    <input type=\"hidden\" name=\"fIdMisionSecJug13\" id=\"fIdMisionSecJug13\" value=\"". $fIdMisionSecJug13 ."\" />
                    <input type=\"hidden\" name=\"fIdMisionSecJug14\" id=\"fIdMisionSecJug14\" value=\"". $fIdMisionSecJug14 ."\" />
                    <input type=\"hidden\" name=\"fIdMisionSecJug21\" id=\"fIdMisionSecJug21\" value=\"". $fIdMisionSecJug21 ."\" />
                    <input type=\"hidden\" name=\"fIdMisionSecJug22\" id=\"fIdMisionSecJug22\" value=\"". $fIdMisionSecJug22 ."\" />
                    <input type=\"hidden\" name=\"fIdMisionSecJug23\" id=\"fIdMisionSecJug23\" value=\"". $fIdMisionSecJug23 ."\" />
                    <input type=\"hidden\" name=\"fIdMisionSecJug24\" id=\"fIdMisionSecJug24\" value=\"". $fIdMisionSecJug24 ."\" />

                    <img src=\"recursos/img/check.svg\" width=\"24\" height=\"24\" title=\"Editar o validar enfrentamiento\" alt=\"form-editar-".$fila[0]."\"  class=\"btn-editar-reg\"/>
                  </form>\n";
            }
            $grid .= "  <form name=\"form-borrar-".$fila[0]."\" id=\"form-borrar-".$fila[0]."\" method=\"POST\" class=\"form-btn-acciones\">
              <input type=\"hidden\" name=\"csrf_token\" value=\"".htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8')."\" />
                  <input type=\"hidden\" name=\"fIdEnfrentamientoReset\" id=\"fIdEnfrentamientoReset\" value=\"".$fila[0]."\"/>
                  <input type=\"hidden\" name=\"accionForm\" id=\"accionForm\" value=\"3\"/>
                  <input type=\"hidden\" name=\"pagActual\" id=\"pagActual\" value=\"". $pagActual ."\" />
                  <input type=\"hidden\" name=\"fIdEnfrentamiento\" id=\"fIdEnfrentamiento\" value=\"". $fila[0] ."\" />
                    <img src=\"recursos/img/trash.svg\" title=\"Eliminar enfrentamiento\" alt=\"form-borrar-".$fila[0]."\" class=\"btn-borrar\"/>                
                </form>";
            
          }
        
        $grid .= "</tr>\n</table>";
        }else{
          $grid  =  "<p>No hay resultados</p>";
        }

      /********************************/
      /* 3. EDITAR OBJETO */
      /********************************/      
      }else if ( $accionForm == 4  ){

        $txtAltaModBoton = "Modificar datos";
        $txtAltaModH3 = "Modificar enfrentamiento";

          
        if($oLiga->idJuego <= 2 ){ 
            $arrMisionesSec =  $oControllerEnfrentamiento->recuperarSelectMisionesSec( $fIdLiga );
            $selectMisSecJug11 = ($fIdMisionSecJug11 != null ) ? $fIdMisionSecJug11 : 0;
            $selectMisSecJug12 = ($fIdMisionSecJug12 != null ) ? $fIdMisionSecJug12 : 0;
            $selectMisSecJug13 = ($fIdMisionSecJug13 != null ) ? $fIdMisionSecJug13 : 0;
            $selectMisSecJug14 = ($fIdMisionSecJug14 != null ) ? $fIdMisionSecJug14 : 0;
            $selectMisSecJug21 = ($fIdMisionSecJug21 != null ) ? $fIdMisionSecJug21 : 0;
            $selectMisSecJug22 = ($fIdMisionSecJug22 != null ) ? $fIdMisionSecJug22 : 0;
            $selectMisSecJug23 = ($fIdMisionSecJug23 != null ) ? $fIdMisionSecJug23 : 0;
            $selectMisSecJug24 = ($fIdMisionSecJug24 != null ) ? $fIdMisionSecJug24 : 0;

            $selectMisionSecJug11 = "";
            $selectMisionSecJug12 = "";
            $selectMisionSecJug13 = "";
            $selectMisionSecJug14 = "";
            $selectMisionSecJug21 = "";
            $selectMisionSecJug22 = "";
            $selectMisionSecJug23 = "";
            $selectMisionSecJug24 = "";
            if (!empty($arrMisionesSec) && is_array($arrMisionesSec) ){
              foreach ($arrMisionesSec as $fila){
                $selectMisionSecJug11 .= "\n<option value=\"" . $fila[0] . "\" ". (($selectMisSecJug11 == $fila[0] && $selectMisSecJug11 > 0)? "selected" : "") ." >" .$fila[0]  . "</option>";
                $selectMisionSecJug12 .= "\n<option value=\"" . $fila[0] . "\" ". (($selectMisSecJug12 == $fila[0] && $selectMisSecJug12 > 0)? "selected" : "") ." >" .$fila[0]  . "</option>";
                $selectMisionSecJug13 .= "\n<option value=\"" . $fila[0] . "\" ". (($selectMisSecJug13 == $fila[0] && $selectMisSecJug13 > 0)? "selected" : "") ." >" .$fila[0]  . "</option>";
                $selectMisionSecJug14 .= "\n<option value=\"" . $fila[0] . "\" ". (($selectMisSecJug14 == $fila[0] && $selectMisSecJug14 > 0)? "selected" : "") ." >" .$fila[0]  . "</option>";
              }
            }
            if (!empty($arrMisionesSec) && is_array($arrMisionesSec) ){
              foreach ($arrMisionesSec as $fila){
                $selectMisionSecJug21 .= "\n<option value=\"" . $fila[0] . "\" ". (($selectMisSecJug21 == $fila[0] && $selectMisSecJug21 > 0)? "selected" : "") ." >" .$fila[0]  . "</option>";
                $selectMisionSecJug22 .= "\n<option value=\"" . $fila[0] . "\" ". (($selectMisSecJug22 == $fila[0] && $selectMisSecJug22 > 0)? "selected" : "") ." >" .$fila[0]  . "</option>";
                $selectMisionSecJug23 .= "\n<option value=\"" . $fila[0] . "\" ". (($selectMisSecJug23 == $fila[0] && $selectMisSecJug23 > 0)? "selected" : "") ." >" .$fila[0]  . "</option>";
                $selectMisionSecJug24 .= "\n<option value=\"" . $fila[0] . "\" ". (($selectMisSecJug24 == $fila[0] && $selectMisSecJug24 > 0)? "selected" : "") ." >" .$fila[0]  . "</option>";
              }
            }
        }




        if ($fIdEnfrentamientoEditar != null){
          
          // MISIONES SECUNDARIAS
          $arrMisionesSecJug1 =  array();
          $arrMisionesSecJug2 =  array();
          if($oLiga->idJuego <= 2 ){ 
              if ($selectMisSecJug11 > 0 ){
                array_push($arrMisionesSecJug1, $selectMisSecJug11);
              }
              if ($selectMisSecJug12 > 0 ){
                array_push($arrMisionesSecJug1, $selectMisSecJug12);
              }
              if ($selectMisSecJug13 > 0 ){
                array_push($arrMisionesSecJug1, $selectMisSecJug13);
              }
              if ($selectMisSecJug14 > 0 ){
                array_push($arrMisionesSecJug1, $selectMisSecJug14);
              } 

              if ($selectMisSecJug21 > 0 ){
                array_push($arrMisionesSecJug2, $selectMisSecJug21);
              }
              if ($selectMisSecJug22 > 0 ){
                array_push($arrMisionesSecJug2, $selectMisSecJug22);
              }
              if ($selectMisSecJug23 > 0 ){
                array_push($arrMisionesSecJug2, $selectMisSecJug23);
              }
              if ($selectMisSecJug24 > 0 ){
                array_push($arrMisionesSecJug2, $selectMisSecJug24);
              } 
         }
          if ($oLiga->idJuego <= 2) {
            $resultadoValido = validarPuntosFow($fResultadoJugador1, $fResultadoRadio)
              && validarPuntosFow($fResultadoJugador2, $fResultadoRadio === 3 ? 0 : ($fResultadoRadio === 0 ? 3 : 1));
          }
          if (!$resultadoValido) {
            $comprobarAltaMod = 2;
            $mensajeAltaMod .= '<div id="mensaje-error">Los puntos de victoria no son válidos para el resultado seleccionado.</div>';
          }
          if ($resultadoValido) {
            $comprobarAltaMod = $oControllerEnfrentamiento->modificarDatosEnfrentamiento( $fIdLiga, $fIdEnfrentamiento, $fIdJugador1, $fIdJugador2, $fResultadoJugador1, $fResultadoJugador2,
                      $fValPinturaJug1, $fValPinturaJug2, $fFechaBatalla, $fIndValidado, $arrMisionesSecJug1, $arrMisionesSecJug2, $fValDeportividadJug1, $fValDeportividadJug2, $fIdJugVictoriaConcedida, $fVictoriaSector );
          }

          /*  1. OK
            2. ERROR
            3. AVISO 
            4. ERROR DUPLICADO
          */ 
        
          $mensajeAltaMod .= "<div id=\"". ( ($comprobarAltaMod == 1)? "mensaje-ok" : (($comprobarAltaMod == 3)? "mensaje-aviso" : "mensaje-error")) ."\">". 
                  ( ($comprobarAltaMod == 1)? "Enfrentamiento modificado correctamente. <a href=\"". $paginaActiva  ."\">Volver</a>" : 
                    (($comprobarAltaMod == 2)? "Se ha producido un error en su solicitud." : "AVISO: debe modificar al menos un campo.") ) ."</div>";

          // actualizamos datos de enfrentamiento
          $oEnfrentamiento = $oControllerEnfrentamiento->recuperarEnfrentamiento ( $fIdEnfrentamiento );

          $fValPinturaJug1 = $oEnfrentamiento->valPinturaJug1;
          $fValPinturaJug2 =  $oEnfrentamiento->valPinturaJug2;
          $fValDeportividadJug1 =  $oEnfrentamiento->valDeportividadJug1;
          $fValDeportividadJug2 =  $oEnfrentamiento->valDeportividadJug2;
          $fResultadoJugador1 = $oEnfrentamiento->resultadoJugador1;
          $fResultadoJugador2 = $oEnfrentamiento->resultadoJugador2;
          $fResultadoRadio = resultadoRadioDesdePuntuacionFow($fResultadoJugador1, $fResultadoJugador2);
          $fIdJugVictoriaConcedida = $oEnfrentamiento->idJugVictoriaConcedida;
        }

      }
    }

  }catch(Throwable $e){
    $oLog = Log::getInstance();
    $oLog->trazaLog ($e, "editar-resultados.php");  
    return null;   
  }

?>








<script>

  $(function(){
      

      <?php if ( $accionForm  < 2){ ?>
          /*PAGINADOR - ASIGNAMOS NUM DE PÁGINA AL HIDDEN DEL FORMULARIO */
          $(".btn-paginador").click( function() {
            var pagActual = $(this).attr('id')
            $("#pagActual").attr("value", pagActual);
            $("#buscadorligas").submit();
          }); 


          $("#fIdLiga").change(function(){ 
            if( $('#fIdLiga option:selected').val() > 0)
               actualizarSelectJugador( $('#fIdLiga option:selected').val());
          });

          // borrar registro
          $(".btn-borrar").click( function() {  
            var formularioBorrar = $(this).attr('alt');
          
           
            $('#dialog-modal').dialog({
              autoFocus: false,
                  buttons : {
                    "Confirmar" : function() {
                  $("#" + formularioBorrar).submit();
                    },
                    "Cancelar" : function() {
                      $(this).dialog("close");
                    }
                  }
              });
                   
            
          }); 

          // editar registro
          $(".btn-editar-reg").click( function(e) {
            e.preventDefault();
            var formulario = $(this).closest('form');
            if (formulario.length) {
              formulario.submit();
            }
          });


          // formulario en caso de no haber seleccionado una liga   
          $(function(){

            $( "#fIdLiga" ).change(function() {
              $("#selectLiga").submit();
            });
          });

    <?php } ?>



      /* calendario */
      $.datepicker.regional['es'] = {
          closeText: 'Cerrar',
          prevText: '<Ant',
          nextText: 'Sig>',
          currentText: 'Hoy',
          monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
          monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
          dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
          dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
          dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
          weekHeader: 'Sm',
          dateFormat: 'dd/mm/yy',
          firstDay: 1,
          isRTL: false,
          showMonthAfterYear: false,
          yearSuffix: ''
      };
      $.datepicker.setDefaults($.datepicker.regional['es']);

      /* fecha batalla */

      $( ".fFechaBatallaForm" ).datepicker({
        showOn: "both",
        buttonImage: "recursos/img/calendar.png",
        buttonImageOnly: true,
        buttonText: "Selecciona una fecha",
        dateFormat: 'dd-mm-yy',
        firstDay: 1 ,
        changeMonth: true,
        changeYear: true,
        yearRange: '1950:2035'
      });

      /* FORMULARIO DE ALTA */
      <?php if ( $accionForm  == 4){ ?>
                $('#estrellasPintura').raty({
                  score: <?php printf($fValPinturaJug1); ?>,
                  click: function(score, evt) {
                      $("#fValPinturaJug1").val(score);
                  }
                });

                $('#estrellasPintura2').raty({
                  score: <?php printf($fValPinturaJug2); ?>,
                  click: function(score, evt) {
                      $("#fValPinturaJug2").val(score);
                  }
                });


                $('#estrellasDeportividad1').raty({
                  score: <?php printf($fValDeportividadJug1); ?>,
                  number: 3,
                  click: function(score, evt) {
                      $("#fValDeportividadJug1").val(score);
                  }
                });


                $('#estrellasDeportividad2').raty({
                  score: <?php printf($fValDeportividadJug2); ?>,
                  number: 3,
                  click: function(score, evt) {
                      $("#fValDeportividadJug2").val(score);
                  }
                });

                  function maximoPuntosFowPorResultado(estado) {
                    return estado === 3 ? 5 : (estado === 0 ? 4 : 3);
                  }

                  function actualizarLimitesResultadoFow() {
                    var estado = parseInt($("input[name=fResultadoRadio]:checked").val(), 10);
                    var maximoJugador1 = maximoPuntosFowPorResultado(estado);
                    var maximoJugador2 = maximoPuntosFowPorResultado(estado === 3 ? 0 : (estado === 0 ? 3 : 1));
                    var minimoJugador1 = 0;
                    var minimoJugador2 = 0;
                    var puntosJugador1 = Math.max(minimoJugador1, Math.min(parseInt($("#fResultadoJugador1").val(), 10) || 0, maximoJugador1));
                    var puntosJugador2 = Math.max(minimoJugador2, Math.min(parseInt($("#fResultadoJugador2").val(), 10) || 0, maximoJugador2));
                    if (estado === 3) { puntosJugador1 = 5; puntosJugador2 = 4; }
                    if (estado === 0) { puntosJugador1 = 4; puntosJugador2 = 5; }
                    if (estado === 1) { puntosJugador2 = puntosJugador1; }
                    $("#slider-resultado-1").slider("option", "min", minimoJugador1).slider("option", "max", maximoJugador1).slider("option", "value", puntosJugador1);
                    $("#slider-resultado-2").slider("option", "min", minimoJugador2).slider("option", "max", maximoJugador2).slider("option", "value", puntosJugador2);
                    $("#fResultadoJugador1").val(puntosJugador1);
                    $("#fResultadoJugador2").val(puntosJugador2);
                  }

                  $( "#slider-resultado-1" ).slider({
                    range: "max",
                    min: 0,
                    max: maximoPuntosFowPorResultado(<?php echo $fResultadoRadio; ?>),
                    value: <?php printf($fResultadoJugador1);?>,
                    slide: function(event, ui) { $("#fResultadoJugador1").val(ui.value); }
                  });
                  $( "#slider-resultado-2" ).slider({
                    range: "max",
                    min: 0,
                    max: maximoPuntosFowPorResultado(<?php echo $fResultadoRadio == 3 ? 0 : ($fResultadoRadio == 0 ? 3 : 1); ?>),
                    value: <?php printf($fResultadoJugador2);?>,
                    slide: function(event, ui) { $("#fResultadoJugador2").val(ui.value); }
                  });
                  $("input[name=fResultadoRadio]").on("change", actualizarLimitesResultadoFow);
                  actualizarLimitesResultadoFow();
        <?php } ?>
  });





        // select de jugadores
        function validarResultado( fIdEnfrentamiento, fIdLiga, fIdJugador1, fFechaBatalla, fIndValidado, pagActual )  {

                var parametros = {
                        "fIdEnfrentamiento" : fIdEnfrentamiento,
                        "fIdLiga" : fIdLiga,
                        "fIdJugador1" : fIdJugador1,
                        "fFechaBatalla" : fFechaBatalla,
                        "fIndValidado" : fIndValidado,
                        "pagActual" : pagActual,
                        "csrf_token" : "<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>",
                };

                $.ajax({
                    async: true,
                        data:  parametros,
                        url:   'ajax/ajax.validar-resultado.php',
                        type:  'post',
                        beforeSend: function () {
                                $("#grid").html("<div style=\"text-align: center\"><img src=\"recursos/img/loader-blanco.gif\" alt=\"Cargando...\" /></div>");
                        },
                        success:  function (response) {
                                $("#grid").html(response);
                        }
                });
          }

        // select de jugadores
        function actualizarSelectJugador( fIdLiga ) {

                var parametros = {
                        "fIdLiga" : fIdLiga,
                };

                $.ajax({
                    async: true,
                        data:  parametros,
                        url:   'ajax/ajax.jugadores.php',
                        type:  'post',
                        beforeSend: function () {
                                $("#divSelectJugadores").html("<div class=\"loading-select\"><img src=\"recursos/img/loading.gif\" alt=\"Cargando...\" /></div>");
                        },
                        success:  function (response) {
                                $("#divSelectJugadores").html(response);
                        }
                });
          }       
</script>


<div id="dialog-modal" title="Confirmaci&oacute;n" style="display: none;">&iquest;Est&aacute; seguro de realizar esta acci&oacute;n?</div>



<div id="contenedor-principal">
  <?php require_once("menu.php"); ?>
  <h2 class="h2"><span>Editar resultados</span></h2>
  <?php if ($fIdLiga == null) { ?>
      <p><b>No se ha seleccionado ninguna liga:</b></p>
      <div class="center"><form id="selectLiga" name="selectLiga" method="POST"><label for="fIdLiga" class="span-index">Liga  </label> <select name="fIdLiga" id="fIdLiga" class="select-index" ><?php printf($selectLigas); ?> </select></form></div>

  <?php }else{ ?>

  <?php /* MENSAJE ELIMINADO */ printf ($mensajeBorrado);   ?>



  <?php /* ALTA DE NUEVA  / MODIFICACION */ 
       if ($accionForm == 4) {
  ?>
  
  <div id="form">
    <h3><?php printf($txtAltaModH3);?></h3>
    <form name="modEnfrentamiento" id="modEnfrentamiento" method="POST" action="" enctype="multipart/form-data">
      
      <br/>
      <?php printf ($mensajeAltaMod);  ?>
      <input type="hidden" name="accionForm" id="accionForm" value="<?php printf($accionForm);?>"/>
      <input type="hidden" name="fIdEnfrentamiento" id="fIdEnfrentamiento" value="<?php printf($fIdEnfrentamiento);?>"/>        
      <input type="hidden" name="fIdEnfrentamientoEditar" id="fIdEnfrentamientoEditar" value="1"/>
      <input type="hidden" name="fIdLiga" id="fIdLiga" value="<?php printf($fIdLiga);?>"/>
      <input type="hidden" name="fNumFase" id="fNumFase" value="<?php printf($fNumFase);?>"/>
      <input type="hidden" name="fNumRonda" id="fNumRonda" value="<?php printf($fNumRonda);?>"/>
      <input type="hidden" name="fIdJugador1" id="fIdJugador1" value="<?php printf($fIdJugador1);?>"/>
      <input type="hidden" name="fIdJugador2" id="fIdJugador2" value="<?php printf($fIdJugador2);?>"/>

      <p><label for="fIdLiga">Liga: </label> <span id="selectLigas"><select name="fIdLiga" id="fIdLiga" data-validation="required " disabled><?php printf($selectLigas); ?> </select></span></p>
      <p><label for="fNumFase">Fase:</label> <span id="selectFases"><select name="fNumFase" id="fNumFase" data-validation="required " disabled><?php printf($selectFases); ?> </select></span></p>
      <p><label for="fNumRonda">Ronda:</label> <span id="selectRondas"><select name="fNumRonda" id="fNumRonda" data-validation="required " disabled><?php printf($selectRondas); ?> </select></span></p>
      <p><label for="fFechaBatalla">Fecha batalla: </label>  
      <input type="text" class="fFechaBatallaForm" name="fFechaBatalla" id="fFechaBatalla" maxlength="10" 
        value="<?php printf($fFechaBatalla);?>" data-validation="required date" 
        data-validation-format="dd-mm-yyyy"></p>

      <p><label for="fIdJugador1">Jugador:</label> <span id="selectJugadores"><select name="fIdJugador1" id="fIdJugador1" data-validation="required " disabled><?php printf($selectJugadores); ?> </select></span></p>
      <p><label for="fIdJugador2">Contrincante:</label> <span id="selectJugadores"><select name="fIdJugador2" id="fIdJugador2" data-validation="required " disabled><?php printf($selectJugadores2); ?> </select></span></p>
      


      <div class="resultados">


          <!-- RESULTADOS - FLAMES OF WAR Y GUILD BALL -->
          <?php if ($oLiga->idJuego <= 2 || $oLiga->idJuego == 5) {?>
            <p><label for="resultadoJugador1">Resultado:</label></p>
            <?php if ($oLiga->idJuego <= 2) { ?>
              <div class="resultados-radio">
                <label><input type="radio" name="fResultadoRadio" value="3" <?php if ($fResultadoRadio == 3){ printf("checked"); } ?>/><img src="images/icono-victoria.png" alt="Victoria"></label>
                <label><input type="radio" name="fResultadoRadio" value="1" <?php if ($fResultadoRadio == 1){ printf("checked"); } ?>/><img src="images/icono-empate.png" alt="Empate"></label>
                <label><input type="radio" name="fResultadoRadio" value="0" <?php if ($fResultadoRadio == 0){ printf("checked"); } ?>/><img src="images/icono-derrota.png" alt="Derrota"></label>
              </div>
            <?php } ?>
            <div class="resultados-izq">                          
                <input class="input-resultado" type="text" id="fResultadoJugador1" name="fResultadoJugador1" value="<?php printf($fResultadoJugador1); ?>" >            
              <p id="enfrentamientoJug1" > </p>
                <div id="slider-resultado-1">
                </div>
              </div>

            <div class="resultados-dec">
                <input class="input-resultado" type="text" id="fResultadoJugador2" name="fResultadoJugador2" value="<?php printf($fResultadoJugador2); ?>" >
              <p id="enfrentamientoJug2" ><?php printf($fIdJugador2Nick);?></p>
                <div id="slider-resultado-2"></div>
              </div>
            <?php } ?>    


          <!-- RESULTADOS - RESTO DE JUEGOS -->
          <?php if ($oLiga->idJuego > 2 &&  $oLiga->idJuego != 5) {?>
            <p><label for="resultadoJugador1">Resultado <strong><?php echo $oJugador1->nick; ?></strong>:</label></p>
            <div class=" resultados-radio">                         
                  <label>
                    <input type="radio" name="fResultadoRadio" value="3" <?php if ($fResultadoRadio == 3){ printf("checked"); } ?>/>
                    <img src="images/icono-victoria.png">
                  </label>                
                  <label>
                    <input type="radio" name="fResultadoRadio" value="1" <?php if ($fResultadoRadio == 1){ printf("checked"); } ?>/>
                    <img src="images/icono-empate.png">
                  </label>                    
                  <label>
                    <input id="fb3" type="radio" name="fResultadoRadio" value="0"  <?php if ($fResultadoRadio == 0){ printf("checked"); } ?>/>
                    <img src="images/icono-derrota.png">
                  </label>
              </div>

          
          <?php } ?>  
        </div>

      <?PHP 
      // FLAMES OF WAR
      if($oLiga->idJuego <= 2 ){ ?>

        <p class="p-sectores" >
          <label for="fVictoriaSector">Asignar a sector:</label>  
          <span id="fVictoriaSectorSpan">
            <select name="fVictoriaSector" id="fVictoriaSector" data-validation="required">
              <?php printf($selectVictoriaSector); ?>
            </select>
          </span> 
        </p>

      <?php } ?>
      
      <div class="box-resultados">
        <p class="contenedor-estrellas">
        <input type="hidden" name="fValPinturaJug1" id="fValPinturaJug1" value="<?php printf($fValPinturaJug1); ?>"/>
        <label for="PinturaJugador1">Pintura <strong><?php echo $oJugador1->nick; ?></strong>:</label><div id="estrellasPintura" name="estrellasPintura"></div></p>
      </div>

      <div class="box-resultados">
        <p class="contenedor-estrellas">
        <input type="hidden" name="fValPinturaJug2" id="fValPinturaJug2" value="<?php printf($fValPinturaJug2); ?>"/>
        <label for="PinturaJugador2">Pintura <strong><?php echo $oJugador2->nick; ?></strong>:</label><div id="estrellasPintura2" name="estrellasPintura2"></div></p>
      </div>

      <div class="box-resultados">
        <p class="contenedor-estrellas">
        <input type="hidden" name="fValDeportividadJug1" id="fValDeportividadJug1" value="<?php printf($fValDeportividadJug1); ?>"/>
        <label for="DeportividadJugador1">Deportividad <strong><?php echo $oJugador1->nick; ?></strong>:</label><div id="estrellasDeportividad1" name="estrellasDeportividad1"></div></p>
      </div>

      <div class="box-resultados">
        <p class="contenedor-estrellas">
        <input type="hidden" name="fValDeportividadJug2" id="fValDeportividadJug2" value="<?php printf($fValDeportividadJug2); ?>"/>
        <label for="DeportividadJugador2">Deportividad <strong><?php echo $oJugador2->nick; ?></strong>:</label><div id="estrellasDeportividad2" name="estrellasDeportividad2"></div></p>
      </div>
    

      <p class="resultado-validado"><span class="span-radio-button">Resultado validado:</span><span class="resultado-validado-opciones">
        <input type="radio" name="fIndValidado" id="fIndValidado1" value="1" <?php printf(($fIndValidado == 1)? "checked" : "");?> class="radio-button"><label class="label-radio-button" for="fIndValidado1">S&iacute;</label>
        <input type="radio" name="fIndValidado" id="fIndValidado0" value="0" <?php printf(($fIndValidado == 0)? "checked" : "");?> class="radio-button"><label class="label-radio-button" for="fIndValidado0">No</label>
      </span></p>

      <p><span class="span-radio-button">Victoria concedida a: </span>
        <select name="fIdJugVictoriaConcedida" id="fIdJugVictoriaConcedida" data-validation="required " >
          <option value="0"></option>
          <option value="<?php echo $fIdJugador1 ?>" <?php echo (($fIdJugVictoriaConcedida == $fIdJugador1)? "selected" : ""); ?> ><?php echo $oJugador1->nick; ?></option>
          <option value="<?php echo $fIdJugador2 ?>" <?php echo (($fIdJugVictoriaConcedida == $fIdJugador2)? "selected" : ""); ?> ><?php echo $oJugador2->nick; ?></option>
        </select>       
      </p>  

      <?PHP 
      // FLAMES OF WAR
      if($oLiga->idJuego <= 2 ){ ?>
          <p class="p-misiones-titulo"><br/>
          Misiones secundarias <strong><?php echo $oJugador1->nick; ?></strong>:</p>
          <p class="p-misiones">
            <span id="fIdMisionSecJug11"><select name="fIdMisionSecJug11" id="fIdMisionSecJug11" data-validation="required " style="width: 100px !important" ><option value="0"></option><?php printf($selectMisionSecJug11); ?></select></span> 
            <span id="fIdMisionSecJug12"><select name="fIdMisionSecJug12" id="fIdMisionSecJug12" data-validation="required " style="width: 100px !important"><option value="0"></option><?php printf($selectMisionSecJug12); ?></select></span>  
            <span id="fIdMisionSecJug13"><select name="fIdMisionSecJug13" id="fIdMisionSecJug13" data-validation="required " style="width: 100px !important"><option value="0"></option><?php printf($selectMisionSecJug13); ?></select></span>  
            <span id="fIdMisionSecJug14"><select name="fIdMisionSecJug14" id="fIdMisionSecJug14" data-validation="required " style="width: 100px !important"><option value="0"></option><?php printf($selectMisionSecJug14); ?></select></span> 
          </p>


          <p class="p-misiones-titulo"><br/>
          Misiones secundarias <strong><?php echo $oJugador2->nick; ?></strong>:</p>
          <p class="p-misiones">
            <span id="fIdMisionSecJug21"><select name="fIdMisionSecJug21" id="fIdMisionSecJug21" data-validation="required " style="width: 100px !important" ><option value="0"></option><?php printf($selectMisionSecJug21); ?></select></span> 
            <span id="fIdMisionSecJug22"><select name="fIdMisionSecJug22" id="fIdMisionSecJug22" data-validation="required " style="width: 100px !important"><option value="0"></option><?php printf($selectMisionSecJug22); ?></select></span>  
            <span id="fIdMisionSecJug23"><select name="fIdMisionSecJug23" id="fIdMisionSecJug23" data-validation="required " style="width: 100px !important"><option value="0"></option><?php printf($selectMisionSecJug23); ?></select></span>  
            <span id="fIdMisionSecJug24"><select name="fIdMisionSecJug24" id="fIdMisionSecJug24" data-validation="required " style="width: 100px !important"><option value="0"></option><?php printf($selectMisionSecJug24); ?></select></span> 
          </p>

      <?php } ?>

      <p><input type="submit" value="<?php printf($txtAltaModBoton);?>" id="formButton" class="submit-button"/></p>
    </form>
    <script>
      $.validate( { 
          form : '#modEnfrentamiento',
          modules : 'file',
          decimalSeparator : ',',
          language : spanish,
          errorMessagePosition : 'top',
          validateOnBlur : false
        }); 
    </script>
  </div>



  <?php 
    /* BOTON VOLVER */    
    printf("<div id=\"div-volver\"><a href=\"$paginaActiva\" class=\"btn-volver\">Volver</a></div>"); 

    }else{ 


      /* BUSCADOR */ ?>

      <p>Desde este panel puede dar editar los resultados de los enfrentamientos.</p>

      <div id="buscador">
        <form name="buscadorligas" id="buscadorligas" method="POST" action="">
          <input type="hidden" name="accionForm" id="accionForm" value="1"/>
          <input type="hidden" name="pagActual" id="pagActual" value="1" />
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>" />
          <label for="fIdLiga">Liga:</label> <span id="selectLigas"><select name="fIdLiga" id="fIdLiga" data-validation="required " ><?php printf($selectLigas); ?> </select>
          <label for="fIdJugador1">Jugador:</label> <span id="divSelectJugadores" class="divSelectJugadores"><select name="fIdJugador1" id="fIdJugador1" data-validation="required " ><option value="0"></option><?php printf($selectJugadores); ?> </select></span>  
          <label for="fIndValidado">Validado: </label>   
          <select name="fIndValidado" id="fIndValidado">
            <option value="2" ></option>
            <option value="1" <?php printf( ( ($fIndValidado == 1)? 'selected': '' ) ); ?>>S&iacute;</option>
            <option value="0" <?php printf( ( ($fIndValidado == 0)? 'selected': '' ) ); ?>>No</option>
          </select>
        <label for="fFechaBatalla">Fecha: </label>   <input type="text" name="fFechaBatalla" id="fFechaBatalla" class="fFechaBatallaForm" value="<?php printf($fFechaBatalla);?>" class="input-fecha" />
          <input type="submit" value="Buscar" id="formButton" class="submit-button"/>
        </form>
      </div>


      <?php 
        /* GRID DE CONTENIDO */  
        printf ("<div id=\"grid\">" . $grid . "</div>");

        /* PAGINADOR */ 
        printf ($paginador);

      ?>

      <br/>
      <div id="div-volver"><a href="index.php" class="btn-volver">Volver</a></div>

  <?php } //END IF GRID

  } // END VALIDACION LIGA SELECCIONADA?>
</div>

<script>
  var navigation = responsiveNav(".nav-collapse", {
    animate: true,                    // Boolean: Use CSS3 transitions, true or false
    transition: 284,                  // Integer: Speed of the transition, in milliseconds
    label: "Menu",                    // String: Label for the navigation toggle
    insert: "after",                  // String: Insert the toggle before or after the navigation
    customToggle: "",                 // Selector: Specify the ID of a custom toggle
    closeOnNavClick: false,           // Boolean: Close the navigation when one of the links are clicked
    openPos: "relative",              // String: Position of the opened nav, relative or static
    navClass: "nav-collapse",         // String: Default CSS class. If changed, you need to edit the CSS too!
    navActiveClass: "js-nav-active",  // String: Class that is added to <html> element when nav is active
    jsClass: "js",                    // String: 'JS enabled' class which is added to <html> element
    init: function(){},               // Function: Init callback
    open: function(){},               // Function: Open callback
    close: function(){}               // Function: Close callback
  });
</script>