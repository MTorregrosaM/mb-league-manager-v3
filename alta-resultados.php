<?php 
    session_start();
?>
<head>
<meta name="viewport" content="width=device-width" />
  <!-- js -->
  <script type="text/javascript" src="recursos/js/jquery-1.11.2.min.js"></script>
  <script type="text/javascript" src="recursos/js/jquery.form-validator.min.js"></script>
  <script type="text/javascript" src="recursos/js/jquery-spanish.js"></script>
  <script type="text/javascript" src="recursos/js/jquery-ui.min.js"></script>
  <script type="text/javascript" src="recursos/js/jquery.raty.js"></script>
  <script type="text/javascript" src="recursos/js/responsive-nav.js"></script>  

  <!-- css  -->
  <link rel="stylesheet" href="recursos/css/estilos.css" type="text/css" media="screen, projection"/>
  <link rel="stylesheet" type="text/css" href="recursos/js/css/jquery-ui.min.css" />
  <link rel="stylesheet" type="text/css" href="recursos/js/css/jquery.raty.css" />

  <link href="https://fonts.googleapis.com/css?family=Fjalla+One|Open+Sans|Oswald|PT+Serif" rel="stylesheet">

    
  <link rel="icon" href="http://modelbrush.com/wp-content/uploads/2014/12/favicon1-548ef461_site_icon-32x32.png" sizes="32x32" />
  <link rel="icon" href="http://modelbrush.com/wp-content/uploads/2014/12/favicon1-548ef461_site_icon-256x256.png" sizes="192x192" />

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
    $oControllerEnfrentamiento = new controllerEnfrentamiento();
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
    $fIdEnfrentamiento = (isset( $_POST["fIdEnfrentamiento"]))? $_POST["fIdEnfrentamiento"] : "";
    $fValPintura = (isset( $_POST["fValPintura"]))? $_POST["fValPintura"] : 1;
    $fValDeportividad = (isset( $_POST["fValDeportividad"]))? $_POST["fValDeportividad"] : 1;
    $fResultadoRadio = (isset( $_POST["fResultadoRadio"]))? $_POST["fResultadoRadio"] : 3;

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
  
    if ($fIdLiga != 0) {
      $oLiga = $oControllerLiga->recuperarDatosLiga($fIdLiga);
      
      // FLAMES OF WAR
      $idJuego = $oLiga->idJuego;
      if($oLiga->idJuego <= 2 ){
        $maxResultado = (($oLiga->idJuego == 1)? 6 : 8);
        $minResultado = 1;
      
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
    $poolEnfrentamientos = "";
    $numVentanasEnfrentamiento = 0;
    $oFase = null;
    
    // options para los select de los formularios
    // LIGAS
    $arrLigas =  $oControllerLiga->recuperarSelectLigas( );

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
    $arrMisionesSec =  $oControllerEnfrentamiento->recuperarSelectMisionesSec( $fIdLiga );
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

    // ALTA DE NUEVO ENFRENTAMIENTO 
    if ($accionForm == 1 && $tockenEnvio == 0 ) {
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

         
      $comprobarAlta = $oControllerEnfrentamiento->altaResultadoEnfrentamiento( $fIdLiga, $fIdEnfrentamiento, $fIdJugador1, $fFechaBatalla, $fResultadoJugador1, $fResultadoJugador2, $fValPintura, $arrMisionesSec, $fValDeportividad, $fVictoriaSector );
      /*  1. OK
        2. ERROR
        3. AVISO 
      */ 

  
      $mensajeAltaMod .= "<div id=\"". (($comprobarAlta == 1)? "mensaje-ok" : "mensaje-error") ."\">". 
                ( ($comprobarAlta == 1)? "Resultado grabado correctamente." : 
                   (($comprobarAlta == 3)? "Revisa todos los campos." : 
                    (($comprobarAlta == 4)? "Los resultados no coinciden con los registrados por el otro jugador. Ponte en contacto con los rangers." :"Se ha producido un error en su solicitud.") )) ."</div>";


      // gestion de correos
      $oJugador1 = $oControllerJugador->recuperarDatosJugador( $fIdJugador1 );
      $oJugador2 = $oControllerJugador->recuperarDatosJugador( $fIdJugador2 );

      // todo OK
      if ($comprobarAlta == 1){
        $body = "<p>Hola, <br/><br/>El jugador <strong>" . $oJugador1->nick . "</strong> ha registrado a las " . Date('H:i') . " el resultado de la batalla contra <strong>" . $oJugador2->nick . "</strong> 
            con un resultado de <strong>" . $fResultadoJugador1 . " - " . $fResultadoJugador2 . "</strong>. Para validar el enfrentamiento es necesario que <u>ambos jugadores registren el resultado del mismo</u>.</p>";
        
        if ($oLiga->idJuego <= 2 ) {// SOLO FLAMES OF WAR 
          if (is_array($arrMisionesSec) && count($arrMisionesSec) >= 1){
            $body .= "<p>Las <strong>Misiones secundarias</strong> completas durante el enfrentamiento han sido: <ul>";
            // recuperamos los titulos de las misiones
            foreach($arrMisionesSec as $idMision){
              $oEnfrentamiento = array();
              $oEnfrentamiento = $oControllerEnfrentamiento->recuperarMisionSec($idMision);
              $body .= "<li> " . $oEnfrentamiento[0] . "(Medallas: " . $oEnfrentamiento[2] . ")</li>";
            }
            $body .= "</ul></p>";
          }else{
            $body .= "<p>El jugador <strong>" . $oJugador1->nick . "</strong> no ha completado <strong>Misiones secundarias</strong>.</p>";
          }
        }
        $body .= "<p>Un saludo</p>";
        $body .= "<a href=\"http://www.modelbrush.com\"><img src=\"http://wiki.modelbrush.com/images/6/67/FIRMA-FOROS.jpg\" border=\"0\"/></a>";
        

      // resultados no coincidentes
      }else if ($comprobarAlta == 4){
        $oEnfrentamiento = $oControllerEnfrentamiento->recuperarEnfrentamiento($fIdEnfrentamiento);
        $body = "<p>Hola, los resultados introducidos por ambos jugadores no coinciden, por favor, confirmadnos el resultado correcto de la batalla enviandonos un correo a 
              <a href='hola@modelbrush.com'>hola@modelbrush.com</a>. Los resultados que hemos recibido son: ";
        $body .= "<ul><li><strong>" .  $oJugador1->nick  . " vs " .  $oJugador2->nick  . "</strong>: " . $fResultadoJugador1 . " - " .  $fResultadoJugador2 . "</li>";
        $body .= "<li><strong>" .  $oJugador2->nick  . " vs " .  $oJugador1->nick  . "</strong> : " . $oEnfrentamiento->resultadoJugador1 . " - " .  $oEnfrentamiento->resultadoJugador2  . "</li></ul>";
        $body .= "<p>Un saludo</p>";
        $body .= "<a href=\"http://www.modelbrush.com\"><img src=\"http://wiki.modelbrush.com/images/6/67/FIRMA-FOROS.jpg\" border=\"0\"/></a>";
      }


      $mail_header = "MIME-Version: 1.0\r\n";
      $mail_header .= "Content-type: text/html; charset=utf-8\r\n";
      $mail_header .= "From: Rangers Modelbrush <no-reply@modelbrush.com>\r\n";
      $mail_header .= "Reply-To: Rangers Modelbrush <hola@modelbrush.com>\r\n";
      $mail_header .= "Bcc: hola@modelbrush.com\r\n";
      $mail_destino = $oJugador2->email.",".$oJugador1->email;      
      $mail_titulo =  $oLiga->nombre . " - Registro resultados";

      mail($mail_destino, $mail_titulo, $body, $mail_header);

      // tocken para evitar duplicados
      $tockenEnvio = 1;

    }else if ($accionForm == 1 && $tockenEnvio = 1){

      $mensajeAltaMod .= "<div id=\"mensaje-error\">El resultado de tu batalla ya se ha enviado, no puedes volver a enviarlo. Si tienes alguna duda escr&iacute;benos a <a href='hola@modelbrush.com'>hola@modelbrush.com</a>.</div>";
      
    }
    
  }catch(Exception $e){
    $oLog = Log::getInstance();
    $oLog->trazaLog ($e, "gestion-ligas.php");  
    return null;   
  }

?>



<div id="contenedor-principal">
  <?php require_once("menu.php"); ?>
  <div id="img-cabecera">
    <a href="alta-resultados.php">
        <img src="<?php if(isset($oLiga) and $oLiga != null) { printf("recursos/img/ligas/". $oLiga->logo); }else{ printf("images/logo_ligas_2018.jpg"); } ?>"  width="100%" />     
    </a>
  </div>

    <h2 class="h2"><span>Registrar enfrentamiento  <?php if(isset($oLiga) and $oLiga != null) { printf(" en " . $oLiga->nombre); } ?></span></h2> 

  <?php if($fClaveCifrada == null || !$auxClaveCorrecta){ ?>
    <div id="buscador">

      <?php if(!$auxClaveCorrecta && $fClaveCifrada != null){ ?>
        <div id="mensaje-error">&iexcl;&iexcl;ERROR!! CLAVE SECRETA INCORRECTA.</div>
      <?php } ?> 

      <form name="validadorClave" id="validadorClave" method="POST" action="">
        <input type="hidden" name="accionForm" id="accionForm" value="5"/>
        <label for="fIdLiga">Liga: </label> <select name="fIdLiga" id="fIdLiga" data-validation="required " ><option></option><?php printf($selectLigas); ?> </select>  
        <label for="fNumFase">Fase: </label> <span id="selectFases"><select name="fNumFase" id="fNumFase" data-validation="required" ><?php printf($selectFases); ?></select></span>
        <label for="fClaveCifrada">Clave cifrada: </label>   <input type="password" name="fClaveCifrada" id="fClaveCifrada" data-validation="required "  value="<?php printf($fClaveCifrada);?>"  />  
        <input type="submit" value="Confirmar clave" id="formButton" class="submit-button"/> 
      </form>
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
        
          <p>Introduce los resultados del enfrentamiento:</p>
          
          <?php printf ($mensajeAltaMod);  ?>
          
          <input type="hidden" name="accionForm" id="accionForm" value="1"/>
          
          
          <?php if ($accionForm == 4){ ?> <input type="hidden" name="fIdEnfrentamientoEditar" id="fIdEnfrentamientoEditar" value="1"/>  <?php } ?>
        
          <input type="hidden" name="fClaveCifrada" id="fClaveCifrada"  value="<?php printf($fClaveCifrada);?>"  />
          <input type="hidden" name="fIdEnfrentamiento" id="fIdEnfrentamiento" value="<?php printf($fIdEnfrentamiento);?>"/>
          <input type="hidden" name="fIdLiga" id="fIdLiga" value="<?php printf($fIdLiga);?>"/>
          <input type="hidden" name="fNumFase" id="fNumFase" value="<?php printf($fNumFase);?>"/>
          <input type="hidden" name="tockenEnvio" id="tockenEnvio" value="<?php printf($tockenEnvio);?>"/>
          <p><label for="fIdJugador1">&iquest;Qui&eacute;n eres?: </label> <span id="selectJugadores"><select name="fIdJugador1" id="fIdJugador1" data-validation="required " ><option value=""></option><?php printf($selectJugadores); ?> </select></span></p>
          
          <p><label for="fNumRonda">Ronda: </label> <span id="selectRondas"><?php printf($selectRondas); ?></span><br/></p>
          
          <p><label for="fIdJugador2Nick">Tu contrincante: </label><span id="selectJugador2" name="selectJugador2"><input type="text" name="fIdJugador2Nick" id="fIdJugador2Nick" value="<?php printf($fIdJugador2Nick);?>"  disabled /></span></p>
          
          <p><label for="fFechaBatalla">Fecha de batalla: </label>  
          <input type="text" class="fFechaBatallaForm" name="fFechaBatalla" id="fFechaBatalla" maxlength="10" 
          value="<?php printf($fFechaBatalla);?>" data-validation="required date" 
          data-validation-format="dd-mm-yyyy"></p>



     
          <div class="resultados">
            <p><label for="resultadoJugador1">Resultado:</label></p>

       
            <!-- RESULTADOS - DESLIZADOR. FLAMES OF WAR, GUILD BALL  -->
            <?php if ($oLiga->idJuego <= 2 || $oLiga->idJuego == 5) {?>
              <div class="resultados-izq">                          
                  <input class="input-resultado" type="text" id="fResultadoJugador1" name="fResultadoJugador1" value="<?php printf($fResultadoJugador1); ?>" >            
                <p id="enfrentamientoJug1" > </p>
                  <div id="slider-resultado-1" style=" width: 300px; background: #cd5700">
                  </div>
                </div>

              <div class="resultados-dec">
                  <input class="input-resultado" type="text" id="fResultadoJugador2" name="fResultadoJugador2" value="<?php printf($fResultadoJugador2); ?>" >
                <p id="enfrentamientoJug2" ><?php printf($fIdJugador2Nick);?></p>
                  <div id="slider-resultado-2" style="width: 300px; background: #cd5700;" ></div>
                </div>
              <?php } ?>    


            <!-- RESULTADOS - ICONOS VICTORIA, EMPATE, DERROTA. WARHAMMER 40K, BOLT ACTION -->
            <?php if ($oLiga->idJuego > 2 && $oLiga->idJuego <> 5)  {?>
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


            <p class="contenedor-estrellas">
            <input type="hidden" name="fValPintura" id="fValPintura" value="<?php printf($fValPintura); ?>"/>
            <label for="PinturaJugador1">Valora el nivel de pintura de tu contrincante:</label><div id="estrellasPintura" name="estrellasPintura"></div></p>


            <p class="contenedor-estrellas">
            <input type="hidden" name="fValDeportividad" id="fValDeportividad" value="<?php printf($fValDeportividad); ?>"/>
            <label for="DeportividadJugador1">Deportividad de tu contrincante:</label><div id="estrellasDeportividad" name="estrellasDeportividad"></div></p>
  
            <?php if ($fIdLiga != 0 && $oLiga->idJuego <= 2) {  // SOLO FLAMES OF WAR?>
            <p><br/>
            Misiones secundarias: <i>indica el n&uacute;mero de la carta que has completado que encontrar&aacute;s en la esquina derecha</i></p>
            <p class="p-misiones">
              <span id="fIdMisionSec1"><select name="fIdMisionSec1" id="fIdMisionSec1" data-validation="required " style="width: 100px !important" ><option value="0"></option><?php printf($selectMisionSec1); ?></select></span> 
              <span id="fIdMisionSec2"><select name="fIdMisionSec2" id="fIdMisionSec2" data-validation="required " style="width: 100px !important"><option value="0"></option><?php printf($selectMisionSec2); ?></select></span>  
              <span id="fIdMisionSec3"><select name="fIdMisionSec3" id="fIdMisionSec3" data-validation="required " style="width: 100px !important"><option value="0"></option><?php printf($selectMisionSec3); ?></select></span>  
              <span id="fIdMisionSec4"><select name="fIdMisionSec4" id="fIdMisionSec4" data-validation="required " style="width: 100px !important"><option value="0"></option><?php printf($selectMisionSec4); ?></select></span> 
            </p>
          <?php } ?>


          <p><input type="submit" value="Confirmar resultado" id="formButton" class="submit-button"/></p>
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

      $("#fIdLiga").change(function(){ 
        
        if( $('#fIdLiga option:selected').val() > 0)
         actualizarSelectFases( $('#fIdLiga option:selected').val(), <?php printf("1.".$fNumFase); ?>);
      });


      <?php if($fClaveCifrada != null && $auxClaveCorrecta == true){ ?>


                $("#enfrentamientoJug1").text( $('#fIdJugador1 option:selected').text());
                
                $("#fIdJugador1").change(function(){ 
                  $("#p-sectores").hide();
                  if ($('#fIdJugador1 option:selected').val() != null && $('#fIdJugador1 option:selected').val() != 0){
                    actualizarRadioRondas (<?php printf($fIdLiga); ?> , $('#fIdJugador1 option:selected').val(), <?php printf($fNumFase); ?>  );
                    if ( $('#fIdJugador1 option:selected').val() > 0){
                      $("#enfrentamientoJug1").text( $('#fIdJugador1 option:selected').text());
                    }else{
                      $("#enfrentamientoJug1").text( "Jugador 1");
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
                  $( "#slider-resultado-1" ).slider({
                    range: "max",
                    min: <?php echo $minResultado; ?>,
                    max: <?php echo $maxResultado; ?>,
                    value: <?php printf($fResultadoJugador1);?>,
                    slide: function( event, ui ) {
                      $("#fResultadoJugador1" ).val( ui.value );

                      <?php  
                        // SOLO EN FLAMES OF WAR LOS SLIDER SE REPARTEN 7 PUNTOS
                        if($oLiga->idJuego <= 2 ){ ?>
                            $("#fResultadoJugador2" ).val( <?php echo $maxResultado+1; ?>-ui.value );
                            $("#slider-resultado-2").slider( "option", "value", <?php echo $maxResultado+1; ?>-ui.value );
                      <?php } ?>

                      if(  ui.value >= <?php echo $maxResultado-2; ?>){
                        $("#fVictoriaSector option[value=0]").remove();
                        $("#p-sectores").show();
                        
                      }else{
                        $("#p-sectores").hide();
                      }
                    }
                  });

                  $( "#fResultadoJugador1" ).val( $( "#slider-resultado-1" ).slider( "value" ) );

                  $( "#slider-resultado-2" ).slider({
                    range: "max",
                    min: <?php echo $minResultado; ?>,
                    max: <?php echo $maxResultado; ?>,
                    value: <?php printf($fResultadoJugador2);?>,
                    slide: function( event, ui ) {
                      $("#fResultadoJugador2" ).val( ui.value );
                      /*$("#fResultadoJugador1" ).val( <?php echo $maxResultado+1; ?>-ui.value );
                      $("#slider-resultado-1").slider( "option", "value",<?php echo $maxResultado+1; ?>-ui.value );*/
                    }
                  });
                  $("#fResultadoJugador2" ).val( $( "#slider-resultado-2" ).slider( "value" ) );

      <?php } ?>
    }); 

    // select de fases
    function actualizarSelectFases( fIdLiga, fNumFase ) {

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
                    }
            });
    }


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
                  url:   'ajax/ajax.rondas-enfrentamiento.php',
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
                          $("#enfrentamientoJug2").text( $('#fIdJugador2Nick').val() );
                          $("#fResultadoJugador1").val( $('#fResultadoJugador1Aux').val() );
                          $("#slider-resultado-1").slider( "option", "value", $('#fResultadoJugador1Aux').val() );
                          $("#fResultadoJugador2").val( $('#fResultadoJugador2Aux').val() );
                          $("#slider-resultado-2").slider( "option", "value", $('#fResultadoJugador2Aux').val() );
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