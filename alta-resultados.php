<?php 
  require_once __DIR__ . "/config/db.class.php";
  require_once __DIR__ . "/config/security.php";
  require_once __DIR__ . "/config/mailer.php";
  require_once __DIR__ . "/inc/puntuacion-fow.php";
  validarCsrfPublico();
?>
<html lang="es" data-bs-theme="dark">
<head>
<?php require_once "cabecera.php"; ?>

</head>

<body>


<?php
  require_once ("model/class.php");
  require_once ("config/config.php");
  require_once ("controller/controller.php");
  
  try {

    /* variables */
    $oControllerLiga = new controllerLiga();
    $oControllerJugador = new controllerJugador();
    $oControllerResultado = new controllerResultado();
    $oControllerFase = new controllerFase();

    $paginaActiva = "alta-resultados.php";
    $grid = "";
    $comprobarBorrado = false;
    $mensajeBorrado = "";
    $comprobarAlta = false;
    $mensajeAltaMod = "";
    $comprobarMod = false;
    $mensajeMod = "";
    $txtAltaModBoton = "";
    $txtAltaModH3 = "";
    $oLiga = null;
        

    $accionForm = (isset( $_POST["accionForm"]))? $_POST["accionForm"] : "";
    $fIdLiga = (isset( $_POST["fIdLiga"]))? $_POST["fIdLiga"] : 0;
    $fNumFase = (isset( $_POST["fNumFase"]))? $_POST["fNumFase"] : 0;
    $fNumRonda = (isset( $_POST["fNumRonda"]))? $_POST["fNumRonda"] : 1;
    $fIdJugador1 = (isset( $_POST["fIdJugador1"]))? $_POST["fIdJugador1"] : 0;
    $fIdJugador2 = (isset( $_POST["fIdJugador2"]))? $_POST["fIdJugador2"] : 0;    
    $fIdJugador2Nick = (isset( $_POST["fIdJugador2Nick"]))? $_POST["fIdJugador2Nick"] : "";
    $fClaveCifrada = (isset( $_POST["fClaveCifrada"]))? $_POST["fClaveCifrada"] : "";
    $fFechaBatalla = (isset( $_POST["fFechaBatalla"]))? $_POST["fFechaBatalla"] : "";
    $fIdResultado = (isset( $_POST["fIdResultado"]))? $_POST["fIdResultado"] : "";
    $fValPintura = (isset( $_POST["fValPintura"]))? $_POST["fValPintura"] : 1;
    $fValDeportividad = (isset( $_POST["fValDeportividad"]))? $_POST["fValDeportividad"] : 1;
    $fResultadoRadio = estadoResultadoFow((isset( $_POST["fResultadoRadio"]))? $_POST["fResultadoRadio"] : 1);
    $resultadoValido = true;

    /* FLAMES OF WAR */
    $fIdMisionSec1 = (isset( $_POST["fIdMisionSec1"]))? $_POST["fIdMisionSec1"] : "";
    $fIdMisionSec2 = (isset( $_POST["fIdMisionSec2"]))? $_POST["fIdMisionSec2"] : "";
    $fIdMisionSec3 = (isset( $_POST["fIdMisionSec3"]))? $_POST["fIdMisionSec3"] : "";
    $fIdMisionSec4 = (isset( $_POST["fIdMisionSec4"]))? $_POST["fIdMisionSec4"] : "";
    $fVictoriaSector = (isset( $_POST["fVictoriaSector"]))? $_POST["fVictoriaSector"] : 0; 
    $tockenEnvio =  (isset( $_POST["tockenEnvio"]))? $_POST["tockenEnvio"] : 0;
    $minResultado = 0;
    $idJuego= 0;
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
  
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $fIdLiga != 0) {
      exigirLigaActivaPublica($fIdLiga);
    }

    if ($fIdLiga != 0) {
      $oLiga = $oControllerLiga->recuperarDatosLiga($fIdLiga);
      
      // FLAMES OF WAR
      $idJuego = $oLiga->idJuego;
      if($oLiga->idJuego <= 2 ){
        $maxResultado = 8;
        $minResultado = 0;
      
        $fResultadoJugador1 = (isset( $_POST["fResultadoJugador1"]))? $_POST["fResultadoJugador1"] : $minResultado; 
        $fResultadoJugador2 = (isset( $_POST["fResultadoJugador2"]))? $_POST["fResultadoJugador2"] : $maxResultado;         

      // GUILD BALL  
      } else  if($oLiga->idJuego == 5 ){
        $maxResultado = 12;
        $minResultado = 0;
      
        $fResultadoJugador1 = (isset( $_POST["fResultadoJugador1"]))? $_POST["fResultadoJugador1"] : $minResultado; 
        $fResultadoJugador2 = (isset( $_POST["fResultadoJugador2"]))? $_POST["fResultadoJugador2"] : $minResultado;     

      // RESTO DE JUEGOS
      } else {
        $maxResultado = 0; // no aplica a resto de juegos
        $minResultado = 0;
        $fResultadoJugador1 = $fResultadoRadio;
        if($fResultadoRadio == 3) { 
          $fResultadoJugador2 = 0;
        }elseif($fResultadoRadio == 1){
          $fResultadoJugador2 = 1;
        }else{
          $fResultadoJugador2 = 3;
        }

      }
    }
  
    /**********************************************************************************************/

    $selectLigas = "";
    $poolJugadores = "";
    $poolResultados = "";
    $numVentanasResultado = 0;
    $oFase = null;
    
    // options para los select de los formularios
    // LIGAS
    $arrLigas =  $oControllerLiga->recuperarSelectLigas( null, false, null, true );

    $selectLigasSelected = ($fIdLiga != null ) ? $fIdLiga : 0;
    
    if (is_array($arrLigas) && count($arrLigas) >= 1 ){
      foreach ($arrLigas as $fila){
        $selectLigas .= "\n<option value=\"" . $fila[0] . "\" ". (($selectLigasSelected == $fila[0] && $selectLigasSelected > 0)? "selected" : "") ." >" .$fila[1]  . "</option>";
      }
    }

    
    // options para los select de los formularios
    // JUGADORES
    $arrJugadores =  $oControllerJugador->recuperarSelectJugadores( $fIdLiga,  $fNumFase, null, true ) ;

    $selectJugadoresSelected = ($fIdJugador1 != null ) ? $fIdJugador1 : 0;
    $selectJugadores = "";
   

    if (ISSET($arrJugadores) AND count($arrJugadores) >= 1 ){
      foreach ($arrJugadores as $fila){
        $selectJugadores .= "\n<option value=\"" . $fila[0] . "\" ". (($selectJugadoresSelected == $fila[0] && $selectJugadoresSelected > 0)? "selected" : "") ." >" .$fila[1]  . "</option>";
      }
    }


    // options para los select de los formularios
    // FASES
    $selectFases = "";
    $arrFases =  $oControllerLiga->recuperarSelectFases( $fIdLiga, true );
    $selectFasesSelected = ($fNumFase != null ) ? $fNumFase : 0;


    if ($arrFases != null && count($arrFases) >= 1 ){
      foreach ($arrFases as $fila){
        $selectFases .= "\n<option value=\"" . $fila[0] . "\" ". (($selectFasesSelected == $fila[0] && $selectFasesSelected > 0)? "selected" : "") ." >" .$fila[0]  . "</option>";
      }
    }
    
    // options para los select de los formularios
    // SECTORES
    $selectSectores = "";
    if ($fIdLiga != 0 ) {
      if ($oLiga->idJuego == 1){ // FOW V3
        $selectSectores = "<option value='1'>Sector 1 (Arnhem)</option>
                  <option value='2'>Sector 2 (Gotenstellung)</option>
                  <option value='3'>Sector 3 (Minsk)</option>";
      }else if($oLiga->idJuego == 2){ // FOW V4
        $selectSectores = "<option value='1'>Sector 1 (Dunkerque)</option>
                  <option value='2'>Sector 2 (Varsaw)</option>
                  <option value='3'>Sector 3 (Tunisia)</option>";     
      }
    }
    
  
    // options para los select de los formularios
    // RONDAS
    $arrRondas =  $oControllerLiga->recuperarSelectRondas( $fIdLiga, $fNumFase );
    $selectRondasSelected = ($fNumRonda != null ) ? $fNumRonda : 1;
    $selectRondas = "";
    $selectRondas = "<select id=\"fNumRonda\" name=\"fNumRonda\" disabled>";    
    if ($arrRondas != null && count($arrRondas) >= 1 ){
      foreach ($arrRondas as $fila){
        $selectRondas .= "\n<option id=\"" . $fila[0] . "\" value=\"" .  $fila[0] . "\" >" .$fila[0]  . "</option>";
      }
    }
    $selectRondas .= "</select>";

    // options para los select de los formularios
    // MISIONES SECUNDARIAS 1S
    $arrMisionesSec =  $oControllerResultado->recuperarSelectMisionesSec( $fIdLiga );
    $selectMisSec1 = ($fIdMisionSec1 != null ) ? $fIdMisionSec1 : 0;
    $selectMisSec2 = ($fIdMisionSec2 != null ) ? $fIdMisionSec2 : 0;
    $selectMisSec3 = ($fIdMisionSec3 != null ) ? $fIdMisionSec3 : 0;
    $selectMisSec4 = ($fIdMisionSec4 != null ) ? $fIdMisionSec4 : 0;
    $selectMisionSec1 = "";
    $selectMisionSec2 = "";
    $selectMisionSec3 = "";
    $selectMisionSec4 = "";
    if ($arrMisionesSec != null && count($arrMisionesSec) >= 1 ){
      foreach ($arrMisionesSec as $fila){
        $selectMisionSec1 .= "\n<option value=\"" . $fila[0] . "\" ". (($selectMisSec1 == $fila[0] && $selectMisSec1 > 0)? "selected" : "") ." >" .$fila[0]  . "</option>";
        $selectMisionSec2 .= "\n<option value=\"" . $fila[0] . "\" ". (($selectMisSec2 == $fila[0] && $selectMisSec2 > 0)? "selected" : "") ." >" .$fila[0]  . "</option>";
        $selectMisionSec3 .= "\n<option value=\"" . $fila[0] . "\" ". (($selectMisSec3 == $fila[0] && $selectMisSec3 > 0)? "selected" : "") ." >" .$fila[0]  . "</option>";
        $selectMisionSec4 .= "\n<option value=\"" . $fila[0] . "\" ". (($selectMisSec4 == $fila[0] && $selectMisSec4 > 0)? "selected" : "") ." >" .$fila[0]  . "</option>";
      }
    }
	  

	  
    // ******************* //
    // Clave cifrada //
    $auxClaveCorrecta = false;
	
    if ($accionForm == 5 || $accionForm == 1){
      $oFase = $oControllerFase->recuperarDatosFase($fIdLiga, $fNumFase);
       
      if ($fClaveCifrada == $oFase->claveCifrada){
        $auxClaveCorrecta = true;

      
      }
    }

    // ALTA DE NUEVO RESULTADO
    if ($accionForm == 1 && $tockenEnvio == 0 && $oLiga !== null && $oLiga->idJuego <= 2) {
      $resultadoValido = validarPuntosFow($fResultadoJugador1, $fResultadoRadio);
      if ($fResultadoRadio === 1) {
        $resultadoValido = $resultadoValido && validarPuntosFow($fResultadoJugador2, 1)
          && (int) $fResultadoJugador1 === (int) $fResultadoJugador2;
      }
      if (!$resultadoValido) {
        $mensajeAltaMod .= '<div id="mensaje-error">Los puntos de victoria no son válidos para el resultado seleccionado.</div>';
      }
    }

    if ($accionForm == 1 && $tockenEnvio == 0 && $resultadoValido) {
      $arrMisionesSec =  array();
      if ($selectMisSec1 > 0 ){
        array_push($arrMisionesSec, $selectMisSec1);
      }
      if ($selectMisSec2 > 0 ){
        array_push($arrMisionesSec, $selectMisSec2);
      }
      if ($selectMisSec3 > 0 ){
        array_push($arrMisionesSec, $selectMisSec3);
      }
      if ($selectMisSec4 > 0 ){
        array_push($arrMisionesSec, $selectMisSec4);
      } 

         
      $comprobarAlta = $oControllerResultado->altaResultadoResultado( $fIdLiga, $fIdResultado, $fIdJugador1, $fFechaBatalla, $fResultadoJugador1, $fResultadoJugador2, $fValPintura, $arrMisionesSec, $fValDeportividad, $fVictoriaSector, $fResultadoRadio );
      /*  1. OK
        2. ERROR
        3. AVISO 
      */ 

  
      $mensajeAltaMod .= "<div id=\"". (($comprobarAlta == 1)? "mensaje-ok" : "mensaje-error") ."\">".
                ( ($comprobarAlta == 1)? "Resultado grabado correctamente." : 
                   (($comprobarAlta == 3)? "Revisa todos los campos." : 
                    (($comprobarAlta == 4)? "Los resultados no coinciden con los registrados por el otro jugador. Ponte en contacto con los rangers." :"Se ha producido un error en su solicitud.") )) ."</div>";


      // gestion de correos
      try {
        $oJugador1 = $oControllerJugador->recuperarDatosJugador( $fIdJugador1 );
        $oJugador2 = $oControllerJugador->recuperarDatosJugador( $fIdJugador2 );

      // todo OK
      if ($comprobarAlta == 1 && $oJugador1 != null && $oJugador2 != null){
        $body = "<p>Hola, <br/><br/>El jugador <strong>" . $oJugador1->nick . "</strong> ha registrado a las " . Date('H:i') . " el resultado de la batalla contra <strong>" . $oJugador2->nick . "</strong> 
            con un resultado de <strong>" . $fResultadoJugador1 . " - " . $fResultadoJugador2 . "</strong>. Para validar el resultado es necesario que <u>ambos jugadores registren el resultado del mismo</u>.</p>";
        
        if ($oLiga->idJuego <= 2 ) {// SOLO FLAMES OF WAR 
          if (is_array($arrMisionesSec) && count($arrMisionesSec) >= 1){
            $body .= "<p>Las <strong>Misiones secundarias</strong> completas durante el resultado han sido: <ul>";
            // recuperamos los titulos de las misiones
            foreach($arrMisionesSec as $idMision){
              $oResultado = array();
              $oResultado = $oControllerResultado->recuperarMisionSec($idMision);
              $body .= "<li> " . $oResultado[0] . "(Medallas: " . $oResultado[2] . ")</li>";
            }
            $body .= "</ul></p>";
          }else{
            $body .= "<p>El jugador <strong>" . $oJugador1->nick . "</strong> no ha completado <strong>Misiones secundarias</strong>.</p>";
          }
        }
        $body .= "<p>Un saludo</p>";
        $body .= "<a href=\"http://www.modelbrush.com\"><img src=\"http://wiki.modelbrush.com/images/6/67/FIRMA-FOROS.jpg\" border=\"0\"/></a>";
        

      // resultados no coincidentes
      }else if ($comprobarAlta == 4 && $oJugador1 != null && $oJugador2 != null){
        $oResultado = $oControllerResultado->recuperarResultado($fIdResultado);
        $body = "<p>Hola, los resultados introducidos por ambos jugadores no coinciden, por favor, confirmadnos el resultado correcto de la batalla enviandonos un correo a 
              <a href='hola@modelbrush.com'>hola@modelbrush.com</a>. Los resultados que hemos recibido son: ";
        $body .= "<ul><li><strong>" .  $oJugador1->nick  . " vs " .  $oJugador2->nick  . "</strong>: " . $fResultadoJugador1 . " - " .  $fResultadoJugador2 . "</li>";
        $body .= "<li><strong>" .  $oJugador2->nick  . " vs " .  $oJugador1->nick  . "</strong> : " . $oResultado->resultadoJugador1 . " - " .  $oResultado->resultadoJugador2  . "</li></ul>";
        $body .= "<p>Un saludo</p>";
        $body .= "<a href=\"http://www.modelbrush.com\"><img src=\"http://wiki.modelbrush.com/images/6/67/FIRMA-FOROS.jpg\" border=\"0\"/></a>";
      }

      if (($comprobarAlta == 1 || $comprobarAlta == 4) && isset($body) && $oJugador1 != null && $oJugador2 != null) {
        enviarCorreoSeguro(
          array($oJugador2->email, $oJugador1->email),
          $oLiga->nombre . " - Registro resultados",
          $body
        );
      }
      } catch (Throwable $e) {
        $oLog = Log::getInstance();
        $oLog->trazaLog ($e, "notificacion resultado - alta-resultados.php");
      }


      // tocken para evitar duplicados
      $tockenEnvio = 1;

    }else if ($accionForm == 1 && (int) $tockenEnvio === 1){

      $mensajeAltaMod .= "<div id=\"mensaje-error\" class=\"alert alert-danger\" role=\"alert\">El resultado de tu batalla ya se ha enviado, no puedes volver a enviarlo. Si tienes alguna duda escr&iacute;benos a <a href='hola@modelbrush.com'>hola@modelbrush.com</a>.</div>";
      
    }
    
  }catch(Throwable $e){
    $oLog = Log::getInstance();
    $oLog->trazaLog ($e, "gestion-ligas.php");  
    $mensajeAltaMod = '<div id="mensaje-error" class="alert alert-danger" role="alert">Se ha guardado el resultado, pero no se pudo preparar la confirmaci&oacute;n.</div>';
  }

?>



<div id="contenedor-principal">
  <?php require_once("menu.php"); ?>
  <?php if (isset($oLiga) && $oLiga != null && !empty($oLiga->logo)) { ?>
    <div id="img-cabecera">
      <a href="alta-resultados.php">
        <img src="<?php printf("recursos/img/ligas/" . htmlspecialchars($oLiga->logo, ENT_QUOTES, 'UTF-8')); ?>" width="100%" />
      </a>
    </div>
  <?php } ?>

    <h2 class="h2"><span>Registrar resultado  <?php if(isset($oLiga) and $oLiga != null) { printf(" en " . $oLiga->nombre); } ?></span></h2>

  <?php if($fClaveCifrada == null || !$auxClaveCorrecta){ ?>
    <div id="buscador">

      <?php if(!$auxClaveCorrecta && $fClaveCifrada != null){ ?>
        <div id="mensaje-error" class="alert alert-danger" role="alert">&iexcl;&iexcl;ERROR!! CLAVE SECRETA INCORRECTA.</div>
      <?php } ?>

      <form name="validadorClave" id="validadorClave" method="POST" action="">
        <input type="hidden" name="accionForm" id="accionForm" value="5"/>
        <label for="fIdLiga">Liga: </label> <select name="fIdLiga" id="fIdLiga" data-validation="required " onchange="window.actualizarSelectFases(this.value, 1)" ><option></option><?php printf($selectLigas); ?> </select>
        <label for="fNumFase">Fase: </label> <span id="selectFases"><select name="fNumFase" id="fNumFase" data-validation="required" ><?php printf($selectFases); ?></select></span>
        <label for="fClaveCifrada">Clave cifrada: </label> <input type="password" name="fClaveCifrada" id="fClaveCifrada" class="input-corto-200" data-validation="required " value="<?php printf($fClaveCifrada);?>" />
        <input type="submit" value="Confirmar clave" id="formButton" class="submit-button btn btn-primary w-100"/>
      </form>
    </div>
    <script>
      $.validate( { 
          form : '#validadorClave',
          decimalSeparator : ',',
          language : spanish,
          errorMessagePosition : 'top',
          validateOnBlur : false
        }); 
    </script>

  <?php } ?>


  <?php if($auxClaveCorrecta){ ?>

    <div id="form">

        <form name="altaResultado" id="altaResultado" method="POST" action="" enctype="multipart/form-data">
        
          <p>Introduce los resultados del resultado:</p>
          
          <?php printf ($mensajeAltaMod);  ?>
          
          <input type="hidden" name="accionForm" id="accionForm" value="1"/>
          
          
          <?php if ($accionForm == 4){ ?> <input type="hidden" name="fIdResultadoEditar" id="fIdResultadoEditar" value="1"/>  <?php } ?>
        
          <input type="hidden" name="fClaveCifrada" id="fClaveCifrada"  value="<?php printf($fClaveCifrada);?>"  />
          <input type="hidden" name="fIdResultado" id="fIdResultado" value="<?php printf($fIdResultado);?>"/>
          <input type="hidden" name="fIdLiga" id="fIdLiga" value="<?php printf($fIdLiga);?>"/>
          <input type="hidden" name="fNumFase" id="fNumFase" value="<?php printf($fNumFase);?>"/>
          <input type="hidden" name="tockenEnvio" id="tockenEnvio" value="<?php printf($tockenEnvio);?>"/>
          <p><label for="fIdJugador1">&iquest;Qui&eacute;n eres?: </label> <span id="selectJugadores"><select name="fIdJugador1" id="fIdJugador1" data-validation="required " ><option value=""></option><?php printf($selectJugadores); ?> </select></span></p>
          
          <p><label for="fNumRonda">Ronda: </label> <span id="selectRondas"><?php printf($selectRondas); ?></span><br/></p>
          
          <p><label for="fIdJugador2Nick">Tu contrincante: </label><span id="selectJugador2" name="selectJugador2"><input type="text" name="fIdJugador2Nick" id="fIdJugador2Nick" value="<?php printf($fIdJugador2Nick);?>" class="input-contrincante" disabled /></span></p>
          
          <p><label for="fFechaBatalla">Fecha de batalla: </label>  
          <span class="fecha-batalla-control">
            <input type="text" class="fFechaBatallaForm" name="fFechaBatalla" id="fFechaBatalla" maxlength="10"
            value="<?php printf($fFechaBatalla);?>" data-validation="required date"
            data-validation-format="dd-mm-yyyy">
          </span></p>



     
          <div class="resultados">
            <p><label for="resultadoJugador1">Resultado:</label></p>

       
            <!-- RESULTADOS - DESLIZADOR. FLAMES OF WAR, GUILD BALL  -->
            <?php if ($oLiga->idJuego <= 2 || $oLiga->idJuego == 5) {?>
              <div class="resultados-izq">                          
                  <div class="resultados-radio">
                    <label>
                      <input type="radio" name="fResultadoRadio" value="3" <?php if ($fResultadoRadio == 3){ printf("checked"); } ?>/>
                      <img src="images/icono-victoria.png" alt="Victoria">
                    </label>
                    <label>
                      <input type="radio" name="fResultadoRadio" value="1" <?php if ($fResultadoRadio == 1){ printf("checked"); } ?>/>
                      <img src="images/icono-empate.png" alt="Empate">
                    </label>                    
                    <label>
                      <input id="fb3" type="radio" name="fResultadoRadio" value="0"  <?php if ($fResultadoRadio == 0){ printf("checked"); } ?>/>
                      <img src="images/icono-derrota.png" alt="Derrota">
                    </label>
                  </div>
                  <input class="input-resultado" type="text" id="fResultadoJugador1" name="fResultadoJugador1" value="<?php printf($fResultadoJugador1); ?>">
                <p id="resultadoJug1" > </p>
                  <div id="slider-resultado-1"></div>
                </div>

              
              <?php } ?>  
            </div>

          
          <!-- FLAMES OF WAR: SECTORES-->
          <?php if ($fIdLiga != 0 && $oLiga->idJuego <= 2) {  // SOLO FLAMES OF WAR?>
          <p class="p-sectores" id="p-sectores">
          <label for="fVictoriaSector">Asignar a sector:</label>  
              <span id="fVictoriaSectorSpan">
                <select name="fVictoriaSector" id="fVictoriaSector" data-validation="required">
                  <option value="0" selected></option>
                  <?php printf( $selectSectores ); ?>
                </select>
              </span> 
            </p>
          <?php } ?>


            <div class="contenedor-estrellas">
            <input type="hidden" name="fValPintura" id="fValPintura" value="<?php printf($fValPintura); ?>"/><label class="valoracion-label" for="estrellasPintura">Valora el nivel de pintura de tu contrincante:</label><div id="estrellasPintura" name="estrellasPintura"></div></div>


            <div class="contenedor-estrellas">
            <input type="hidden" name="fValDeportividad" id="fValDeportividad" value="<?php printf($fValDeportividad); ?>"/><label class="valoracion-label" for="estrellasDeportividad">Deportividad de tu contrincante:</label><div id="estrellasDeportividad" name="estrellasDeportividad"></div></div>
  
            <?php if ($fIdLiga == 0 ) {  // SOLO FLAMES OF WAR?>
           
            Misiones secundarias: <i>indica el n&uacute;mero de la carta que has completado que encontrar&aacute;s en la esquina derecha</i></p>
            <br/>
            <p class="p-misiones">
              <span id="fIdMisionSec1"><select name="fIdMisionSec1" id="fIdMisionSec1" data-validation="required " style="width: 100px !important" ><option value="0"></option><?php printf($selectMisionSec1); ?></select></span> 
              <span id="fIdMisionSec2"><select name="fIdMisionSec2" id="fIdMisionSec2" data-validation="required " style="width: 100px !important"><option value="0"></option><?php printf($selectMisionSec2); ?></select></span>  
              <span id="fIdMisionSec3"><select name="fIdMisionSec3" id="fIdMisionSec3" data-validation="required " style="width: 100px !important"><option value="0"></option><?php printf($selectMisionSec3); ?></select></span>  
              <span id="fIdMisionSec4"><select name="fIdMisionSec4" id="fIdMisionSec4" data-validation="required " style="width: 100px !important"><option value="0"></option><?php printf($selectMisionSec4); ?></select></span> 
            </p>
          <?php } ?>


          <p><input type="submit" value="Confirmar resultado" id="formButton" class="submit-button btn btn-primary w-100"/></p>
        </form>
        <script>
          $.validate( { 
              form : '#altaResultado',
              decimalSeparator : ',',
              language : spanish,
              errorMessagePosition : 'top',
              validateOnBlur : false
            }); 
        </script>
      <?php } ?>
    </div>


<script>  

    $(function(){

      $('#estrellasPintura').raty({
        score: <?php printf($fValPintura); ?>,
        click: function(score, evt) {
            $("#fValPintura").val(score);
        }
      });


      $('#estrellasDeportividad').raty({
        score: <?php printf($fValDeportividad); ?>,
        number: 3,
        click: function(score, evt) {
            $("#fValDeportividad").val(score);
        }
      });

      <?php if($fClaveCifrada != null && $auxClaveCorrecta == true){ ?>


                $("#resultadoJug1").text( $('#fIdJugador1 option:selected').text());
                
                $("#fIdJugador1").change(function(){ 
                  $("#p-sectores").hide();
                  if ($('#fIdJugador1 option:selected').val() != null && $('#fIdJugador1 option:selected').val() != 0){
                    actualizarRadioRondas (<?php printf($fIdLiga); ?> , $('#fIdJugador1 option:selected').val(), <?php printf($fNumFase); ?>  );
                    if ( $('#fIdJugador1 option:selected').val() > 0){
                      $("#resultadoJug1").text( $('#fIdJugador1 option:selected').text());
                    }else{
                      $("#resultadoJug1").text( "Jugador 1");
                    }
                  }
                  
                });

                 $("#fNumRonda").change(function(){ 
                  $("#p-sectores").hide();
                  actualizarSelectJugador2(<?php printf($fIdLiga); ?> , $('input[name=fNumRonda]:checked').val(),  $('#fIdJugador1 option:selected').val(), <?php printf($fNumFase); ?>, <?php printf($idJuego); ?>);
                });


                
                // datepicker de fecha
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

                
                // slider de resultados
                  function maximoPuntosFowPorResultado(estado) {
                    return estado === 3 ? 5 : (estado === 0 ? 4 : 3);
                  }

                  function actualizarResultadoFow() {
                    var estado = parseInt($("input[name=fResultadoRadio]:checked").val(), 10);
                    var maximo = maximoPuntosFowPorResultado(estado);
                    var puntos = Math.max(1, Math.min(parseInt($("#fResultadoJugador1").val(), 10) || 1, maximo));
                    if (estado === 3) { puntos = 5; }
                    if (estado === 0) { puntos = 4; }
                    $("#slider-resultado-1").slider("option", "max", maximo);
                    $("#slider-resultado-1").slider("option", "value", puntos);
                    $("#fResultadoJugador1").val(puntos);
                    $("#fResultadoJugador2").val(estado === 1 ? puntos : (estado === 3 ? 4 : 5));
                  }

                  $( "#slider-resultado-1" ).slider({
                    range: "max",
                    min: 1,
                    max: maximoPuntosFowPorResultado(<?php echo $fResultadoRadio; ?>),
                    value: <?php printf($fResultadoJugador1);?>,
                    slide: function( event, ui ) {
                      $("#fResultadoJugador1" ).val( ui.value );
                      actualizarResultadoFow();

                      if(  ui.value >= 3){
                        $("#fVictoriaSector option[value=0]").remove();
                        $("#p-sectores").show();
                        
                      }else{
                        $("#p-sectores").hide();
                      }
                    }
                  });

                  $( "#fResultadoJugador1" ).val( $( "#slider-resultado-1" ).slider( "value" ) );

                  $("input[name=fResultadoRadio]").on("change", actualizarResultadoFow);
                  actualizarResultadoFow();

      <?php } ?>
    }); 

    // select de fases
    window.actualizarSelectFases = function( fIdLiga, fNumFase ) {

            var parametros = {
                    "fIdLiga" : fIdLiga,
                    "fNumFase" : fNumFase,
                    "faseActiva" : 1,
            };

            $.ajax({
                async: true,
                    data:  parametros,
                    url:   'ajax/ajax.fases.php',
                    type:  'post',
                    beforeSend: function () {
                            $("#selectFases").html("<span class=\"loading-select\"><img src=\"recursos/img/loading.gif\" alt=\"Cargando...\" /></span>");
                    },
                    success:  function (response) {
                            $("#selectFases").html(response);
                          //  bindAjaxSelectChange();
                        },
                        error: function () {
                          $("#selectFases").html('<select name="fNumFase" id="fNumFase" data-validation="required"><option value="">No se pudieron cargar las fases</option></select>');
                    }
            });
    };

    // select de jugadores
    function actualizarRadioRondas( fIdLiga,  fIdJugador, fNumFase ) {

          var parametros = {
                  "fIdLiga" : fIdLiga,
                  "fIdJugador" : fIdJugador,
                  "fNumFase" : fNumFase,
          };
          $.ajax({
              async: true,
                  data:  parametros,
                  url:   'ajax/ajax.rondas-resultado.php',
                  type:  'post',
                  beforeSend: function () {
                          $("#selectRondas").html("<div class=\"loading-select\"><img src=\"recursos/img/loading.gif\" alt=\"Cargando...\" /></div>");
                  },
                  success:  function (response) {
                          $("#selectRondas").html(response);
                        //  bindAjaxSelectChange();
                  }
          });
    }


    // select de jugadores
    function actualizarSelectJugador2( fIdLiga, fNumRonda,  fIdJugador1, fNumFase)  {

          var parametros = {
                  "fIdLiga" : fIdLiga,
                  "fNumRonda" : fNumRonda,
                  "fIdJugador1" : fIdJugador1,
                  "fNumFase" : fNumFase,
          };

          $.ajax({
              async: true,
                  data:  parametros,
                  url:   'ajax/ajax.jugador-contrincante.php',
                  type:  'post',
                  beforeSend: function () {
                          $("#selectJugador2").html("<div class=\"loading-select\"><img src=\"recursos/img/loading.gif\" alt=\"Cargando...\" /></div>");
                  },
                  success:  function (response) {
                          $("#selectJugador2").html(response);
                          $("#resultadoJug2").text( $('#fIdJugador2Nick').val() );
                          $("#fResultadoJugador1").val( $('#fResultadoJugador1Aux').val() );
                          $("#slider-resultado-1").slider( "option", "value", $('#fResultadoJugador1Aux').val() );
                          actualizarResultadoFow();
                          $("#fFechaBatalla").val( $('#fFechaBatallaAux').val() );
                          $('#estrellasPintura').raty({ score: 1, click: function(score, evt) { $("#fValPintura").val(score);} });
                          $('#estrellasDeportividad').raty({ score: 1, click: function(score, evt) { $("#fValDeportividad").val(score);} });
                          $('#fValPintura').val( 1 );
                          $('#fValDeportividad').val( 1 );
                        //  bindAjaxSelectChange();
                  }
          });
    }

  
</script>

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