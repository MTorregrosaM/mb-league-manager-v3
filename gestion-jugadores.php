<?php require_once __DIR__ . "/config/auth.php"; ?>
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
    $oControllerLiga = new controllerLiga();
    $oControllerJugador = new controllerJugador();

    $paginaActiva = "gestion-jugadores.php";
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
    $fIdJugador = (isset( $_POST["fIdJugador"]))? $_POST["fIdJugador"] : "";
    $fNick = (isset($_POST["fNick"]))? $_POST["fNick"] : "";
    $fNombre = (isset($_POST["fNombre"]))? $_POST["fNombre"] : "";
    $fApellido1 = (isset($_POST["fApellido1"]))? $_POST["fApellido1"] : "";
    $fApellido2 = (isset($_POST["fApellido2"]))? $_POST["fApellido2"] : "";
    $fBando = (isset($_POST["fBando"]))? $_POST["fBando"] : "";
    $fTelefono = (isset($_POST["fTelefono"]))? $_POST["fTelefono"] : "";
    $fEmail = (isset($_POST["fEmail"]))? $_POST["fEmail"] : "";
    $fPuntosPintura = (isset($_POST["fPuntosPintura"]))? (($_POST["fPuntosPintura"] >= 1)? 2 : 0) : 0;

    $pagActual = (isset($_POST["pagActual"]))? $_POST["pagActual"] : 1;
    $fIdJugadorBorrar = (isset($_POST["fIdJugadorBorrar"]))? $_POST["fIdJugadorBorrar"] : "";
    $fIdJugadorEditar = (isset($_POST["fIdJugadorEditar"]))? $_POST["fIdJugadorEditar"] : "";


    // USUARIO
    $oControllerUsuario = new controllerUsuario();
	$ligasUsuario = $oControllerUsuario->recuperarLigasUsuario( $_SESSION["usuario"] );

    // SELECT DE LIGAS
    $arrLigas =  $oControllerLiga->recuperarSelectLigas( null, false,  $ligasUsuario);
    $selectLigasSelected = ($fIdLiga != null ) ? $fIdLiga : 0;
    $selectLigas = "<option value=''></option>\n";

    if (is_array($arrLigas) && count($arrLigas) >= 1 ){
      foreach ($arrLigas as $fila){
        $selectLigas .= "\n<option value=\"" . $fila[0] . "\" ". (($selectLigasSelected == $fila[0] && $selectLigasSelected > 0)? "selected" : "") ." >" .$fila[1]  . "</option>";
      }
    }

    if ($fIdLiga != null){

	    // options para los select de los formularios
	    // SELECT DE FACCIONES
	    $oLiga = $oControllerLiga->recuperarDatosLiga($fIdLiga);
	    $selectFacciones = "";
	    $arrFacciones = $oControllerLiga->recuperarSelectFacciones($oLiga->idJuego);

      if (is_array($arrFacciones) && count($arrFacciones) >= 1 ){
	      foreach ($arrFacciones as $fila){
          $selectFacciones .= "\n<option value=\"" . $fila[0] . "\" ". (($fBando == $fila[0] && $fBando > 0)? "selected" : "") ." >" .$fila[1]  . "</option>";
	      }
	    }
	    /*
	        <option value="" <?php if($fBando == "") printf("selected"); ?>></option>
	          <option value="DOBLE"  <?php if($fBando == "DOBLE") printf("selected"); ?>>DOBLE</option>
	          <option value="EJE"  <?php if($fBando == "EJE") printf("selected"); ?>>EJE</option>
	          <option value="ALIADO"  <?php if($fBando == "ALIADO") printf("selected"); ?>>ALIADO</option>

	          */


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
	      $comprobarBorrado = $oControllerJugador->borrarJugador( $fIdJugadorBorrar );

	      $mensajeBorrado .= "<div id=\"". (($comprobarBorrado)? "mensaje-ok" : "mensaje-error") ."\">". (($comprobarBorrado)? "Jugador eliminado correctamente." : "Se ha producido un error en su solicitud.") ."</div>";

	    }

	    /********************************/
	    /* 1. BUSCADOR */
	    /********************************/
	    if ($accionForm != 2 && $accionForm != 4) {

	      /********************************/
	      /* PAGINADOR */
	      /********************************/
        $numRegs = $oControllerJugador->paginadorJugadores ( $fIdLiga, $fNick,  $fEmail, null );
	      $numPags = ceil( $numRegs / 10) ;
	      require_once("paginador.inc");



	      /********************************/
	      /* GRID DATOS */
	      /********************************/
        /* SI SE ENVIA EL FORMULARIO DE BÚSQUEDA, MANDAMOS PARÁMETROS PARA FILTRAR */
	      if (isset ($_POST["accionForm"]) && ($_POST["accionForm"] == 1 || $_POST["accionForm"] == 3)){
          $arrJugadores = $oControllerJugador->recuperarListadoJugadores ( $fIdLiga, $fNick,  $fEmail, null , ($pagActual-1));
	      }else{
	        $arrJugadores = $oControllerJugador->recuperarListadoJugadores( $fIdLiga, null, null, null, 0);
	      }


	      // comprobamos que haya datos
	      if ($arrJugadores != null && count($arrJugadores) >= 1){
          $grid  = "<div class=\"tabla-grid-wrap\">\n<table class=\"table-6\">\n
	              <tr>
	              <th>Nick</th>
	              <th>Nombre</th>
	              <th>Apellido</th>
	              <th>Email</th>
                <th>Teléfono</th>
	              <th class=\"td-acciones\"></th>
	              </tr>\n";

	        foreach($arrJugadores as $fila){
            $apellidos = trim($fila[4] . " " . $fila[5]);
            $grid .="\n<tr><td>" . $fila[2] . "</td><td>" .  $fila[3]  . "</td><td>" . $apellidos . "</td><td>" .  $fila[7]. "</td><td  class=\"align-center\">" .  $fila[6] . "</td>";
	          $grid .= "<td class=\"align-center td-acciones\">";


            $grid .= "  
              <form name=\"form-listas-".$fila[0]."\" id=\"form-listas-".$fila[0]."\" method=\"POST\" class=\"form-btn-acciones\" action=\"gestion-listas.php\">
	                <input type=\"hidden\" name=\"csrf_token\" value=\"".htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8')."\"/>
	                <input type=\"hidden\" name=\"fIdJugador\" id=\"fIdJugador\" value=\"".$fila[0]."\"/>
	                <input type=\"hidden\" name=\"fIdLiga\" id=\"fIdLiga\" value=\"".$fila[1]."\"/>
	                <input type=\"hidden\" name=\"accionForm\" id=\"accionForm\" value=\"1\"/>

                  <img src=\"assets/img/options.svg\" title=\"Gestionar listas de jugador\" alt=\"form-listas-".$fila[0]."\" class=\"btn-listas\"/>
	              </form>";

	          $grid .= "  <form name=\"form-borrar-".$fila[0]."\" id=\"form-borrar-".$fila[0]."\" method=\"POST\" class=\"form-btn-acciones\">
	                <input type=\"hidden\" name=\"fIdJugadorBorrar\" id=\"fIdJugadorBorrar\" value=\"".$fila[0]."\"/>
	                <input type=\"hidden\" name=\"accionForm\" id=\"accionForm\" value=\"3\"/>
	                <input type=\"hidden\" name=\"pagActual\" id=\"pagActual\" value=\"". $pagActual ."\" />
	                <img src=\"assets/img/trash.svg\" title=\"Eliminar jugador\" alt=\"form-borrar-".$fila[0]."\" class=\"btn-borrar\"/>
	              </form>";

	          $grid .= " <form name=\"form-editar-".$fila[0]."\" id=\"form-editar-".$fila[0]."\" method=\"POST\" class=\"form-btn-acciones\">
	                <input type=\"hidden\" name=\"accionForm\" id=\"accionForm\" value=\"4\"/>
	                <input type=\"hidden\" name=\"fIdJugador\" id=\"fIdJugador\" value=\"".$fila[0]."\"/>
	                <input type=\"hidden\" name=\"fIdLiga\" id=\"fIdLiga\" value=\"".$fila[1]."\"/>
	                <input type=\"hidden\" name=\"fNick\" id=\"fNick\" value=\"". $fila[2] ."\" />
	                <input type=\"hidden\" name=\"fNombre\" id=\"fNombre\" value=\"". $fila[3] ."\" />
	                <input type=\"hidden\" name=\"fApellido1\" id=\"fApellido1\" value=\"". $fila[4] ."\" />
	                <input type=\"hidden\" name=\"fApellido2\" id=\"fApellido2\" value=\"". $fila[5] ."\" />
                  <input type=\"hidden\" name=\"fTelefono\" id=\"fTelefono\" value=\"". $fila[6] ."\" />
                  <input type=\"hidden\" name=\"fEmail\" id=\"fEmail\" value=\"". $fila[7] ."\" />
                  <input type=\"hidden\" name=\"fBando\" id=\"fBando\" value=\"". $fila[8] ."\" />
                  <input type=\"hidden\" name=\"fPuntosPintura\" id=\"fPuntosPintura\" value=\"". $fila[9] ."\" />
	                <img src=\"assets/img/tool.svg\" title=\"Editar jugador\" alt=\"form-editar-".$fila[0]."\"  class=\"btn-editar-reg\"/>
	              </form>\n";
	        }
          $grid .= "</tr>\n</table>\n</div>";
	      }else{
	        $grid  =  "<p>No hay resultados</p>";
	      }



	    /********************************/
	    /* 1. ALTA NUEVA */
	    /********************************/
	    }else if ( $accionForm == 2 ){
	      $txtAltaModBoton = "Dar de alta nueva jugador";
	      $txtAltaModH3 = "Alta de nueva jugador";

	      // grabamos nuevo registro en caso de que se haya enviado el formulario
        if (isset($_POST["fNick"])){
          $comprobarAltaMod = $oControllerJugador->altaNuevoJugador( $fIdLiga, $fNick, $fNombre, $fApellido1, $fApellido2, $fTelefono, $fEmail, $fBando, $fPuntosPintura  );

	        if ($comprobarAltaMod == 1){

	        }
	        /*  1. OK
	          2. ERROR
	          3. AVISO
	        */

	        $mensajeAltaMod .= "<div id=\"". (($comprobarAltaMod == 1)? "mensaje-ok" : "mensaje-error") ."\">".
                    ( ($comprobarAltaMod == 1)? "Jugador creado correctamente. " :
                      (($comprobarAltaMod == 3)? "No se puede dar de alta: el nick, email, nombre + apellidos o teléfono ya se han utilizado." :
                     "Se ha producido un error en su solicitud."))  ."</div>";
	      }


	    /********************************/
	    /* 3. EDITAR OBJETO */
	    /********************************/
	    }else if ( $accionForm == 4  ){
	      $txtAltaModBoton = "Modificar datos de jugador";
	      $txtAltaModH3 = "Modificar jugador";


	      if ($fIdJugadorEditar != null){
          $comprobarAltaMod = $oControllerJugador->modificarDatosJugador( $fIdJugador, $fNick, $fNombre, $fApellido1, $fApellido2, $fTelefono, $fEmail, $fBando, $fPuntosPintura );


	        /*  1. OK
	          2. ERROR
	          3. AVISO
	          4. ERROR DUPLICADO
	        */

	        $mensajeAltaMod .= "<div id=\"". ( ($comprobarAltaMod == 1)? "mensaje-ok" : (($comprobarAltaMod == 3)? "mensaje-aviso" : "mensaje-error")) ."\">".
	                ( ($comprobarAltaMod == 1)? "Jugador modificado correctamente.  <a href=\"#\"  class=\"btnVolver\">Volver</a>" :
	                  (($comprobarAltaMod == 2)? "Se ha producido un error en su solicitud." : "AVISO: debe modificar al menos un campo.") ) ."</div>";


	      }

	    }
	}

  }catch(Exception $e){
    $oLog = Log::getInstance();
    $oLog->trazaLog ($e, "gestion-jugadores.php");
    return null;
  }

?>








<script>
  $(function(){
    /*PAGINADOR - ASIGNAMOS NUM DE PÁGINA AL HIDDEN DEL FORMULARIO */
    $(".btn-paginador").click( function() {
      var pagActual = $(this).attr('id')
      $("#pagActual").attr("value", pagActual);
      $("#buscadorJugadores").submit();
    });

    // boton ALTA registro
    $("#btnAltaJugador").click( function() {
      $("#btnFormAltaJugador").submit();
    });

    // boton de volver
    $(".btnVolver").click( function() {
      $("#btnFormVolver").submit();
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

    // FORMULARIO EN CASO DE NO SELECCIONAR LIGA
    $(function(){

		$( "#fIdLiga" ).change(function() {
			$("#selectLiga").submit();
		});
	});

    // editar registro
    $(".btn-editar-reg").click( function() {
      var formularioEditar = $(this).attr('alt');
      $("#" + formularioEditar).submit();

    });

    // Gestionar listas jugador
    $(".btn-listas").click( function() {
      var formularioListas = $(this).attr('alt');
      $("#" + formularioListas).submit();

    });


  });
</script>


<div id="dialog-modal" title="Confirmaci&oacute;n" style="display: none;">&iquest;Est&aacute; seguro de realizar esta acci&oacute;n?</div>



<div id="contenedor-principal">
  <?php require_once("menu.php"); ?>
  <h2 class="h2"><span>Gesti&oacute;n de Jugadores</span></h2>
  <?php if ($fIdLiga == null) { ?>
			<p><b>No se ha seleccionado ninguna liga:</b></p>
			<div class="center"><form id="selectLiga" name="selectLiga" method="POST"><label for="fIdLiga" class="span-index">Liga  </label> <select name="fIdLiga" id="fIdLiga" class="select-index" ><?php printf($selectLigas); ?> </select></form></div>

 <?php }else{ ?>


  <?php /* MENSAJE ELIMINADO */ printf ($mensajeBorrado);   ?>



  <?php /* ALTA DE NUEVA  / MODIFICACION */
       if ($accionForm == 2 || $accionForm == 4) {


  ?>

  <div id="form">
    <h3><?php printf($txtAltaModH3);?></h3>
    <form name="altaModJugador" id="altaModJugador" method="POST" action="">

      <?php printf ($mensajeAltaMod);  ?>
      <input type="hidden" name="accionForm" id="accionForm" value="<?php printf($accionForm);?>"/>
      <?php if ($accionForm == 4){ ?> <input type="hidden" name="fIdJugadorEditar" id="fIdJugadorEditar" value="1"/>  <?php } ?>
      <input type="hidden" name="fIdJugador" id="fIdJugador" value="<?php printf($fIdJugador);?>"/>
      <p><label for="fIdLiga">Liga: </label> <select name="fIdLiga" id="fIdLiga"data-validation="required " ><?php printf($selectLigas); ?> </select></p>
      <p><label for="fNick">Nick: </label>  <input type="text" name="fNick" maxlength="35"  id="fNick" data-validation="required " value="<?php printf($fNick);?>"  ></p>
      <p><label for="fNombre">Nombre: </label>  <input type="text" name="fNombre" maxlength="35"  id="fNombre" value="<?php printf($fNombre);?>"  ></p>
      <p><label for="fApellido1">Primer apellido: </label>  <input type="text" name="fApellido1" maxlength="35"  id="fApellido1" value="<?php printf($fApellido1);?>"  ></p>
      <p><label for="fApellido2">Segundo apellido: </label>  <input type="text" name="fApellido2" maxlength="35"  id="fApellido2" value="<?php printf($fApellido2);?>"  ></p>
      <p><label for="fTelefono">Tel&eacute;fono: </label>  <input type="text" name="fTelefono" maxlength="35"  id="fTelefono" value="<?php printf($fTelefono);?>"  ></p>
      <p><label for="fEmail">E-mail: </label>  <input type="text" name="fEmail" maxlength="35"  id="fEmail" data-validation="required email " data-validation-error-msg="El email es obligatorio y debe tener un formato v&aacute;lido." value="<?php printf($fEmail);?>"  ></p>
      <p>
        <label for="fBando">Facción: </label>
        <select name="fBando" id="fBando" data-validation="required ">
            <?php printf($selectFacciones); ?>
          </select>
      </p>
      <p><span class="span-radio-button">Ejército pintado: </span>
        <input type="radio" name="fPuntosPintura" id="fPuntosPintura1" value="1" <?php printf(($fPuntosPintura == 2)? "checked" : "");?> class="radio-button"><label class="label-radio-button" for="fPuntosPintura1">S&iacute;</label>
          <input type="radio" name="fPuntosPintura" id="fPuntosPintura0" value="0" <?php printf(($fPuntosPintura == 0)? "checked" : "");?> class="radio-button"><label class="label-radio-button" for="fPuntosPintura">No</label>
      </p>
      <p><input type="submit" value="<?php printf($txtAltaModBoton);?>" id="formButton" class="submit-button"/></p>
    </form>
    <script>
      $.validate( {
          form : '#altaModJugador',
          decimalSeparator : ',',
          language : spanish,
          errorMessagePosition : 'top',
          errorMessageClass : 'form-error',
          validateOnBlur : false
        });
    </script>
  </div>

  <form name="btnFormVolver" id="btnFormVolver" method="POST" action="">
      <input type="hidden" name="fIdLiga" id="fIdLiga" value="<?php printf($fIdLiga);?>"/>
  </form>
                <input type="hidden" name="fIdLiga" id="fIdLiga" value="<?php printf($fila[1]); ?>"/>


  <?php
    /* BOTON VOLVER */
    printf("<div id=\"div-volver\"><a href=\"#\" class=\"btn-volver btnVolver\">Volver</a></div>");

    }else{


      /* BUSCADOR */ ?>
      <p>Desde este panel puede dar de alta, modificar o eliminar cualquier jugador de la aplicaci&oacute;n.</p>

      <div id="buscador">
        <form name="buscadorJugadores" id="buscadorJugadores" method="POST" action="">
          <input type="hidden" name="accionForm" id="accionForm" value="1"/>
          <input type="hidden" name="pagActual" id="pagActual" value="1" />
          <label for="fIdLiga">Liga: </label> <select name="fIdLiga" id="fIdLiga"data-validation="required " ><?php printf($selectLigas); ?> </select>
          <label for="fNick">Nick: </label>   <input type="text" name="fNick" id="fNick" value="<?php printf($fNick);?>"  />
          <label for="fEmail">E-mail: </label>   <input type="text" name="fEmail" id="fEmail" value="<?php printf($fEmail);?>" />
          <input type="submit" value="Buscar" id="formButton" class="submit-button"/>
        </form>
      </div>


      <div id="btn-alta">
        <form name="btnFormAltaJugador" id="btnFormAltaJugador" method="POST" action="">
          <input type="hidden" name="accionForm" id="accionForm" value="2"/>
          <input type="hidden" name="fIdLiga" id="fIdLiga" value="<?php printf($fIdLiga);?>"/>
          <a href="#" class="button" id="btnAltaJugador"> <img src="assets/img/new.svg" alt="Nuevo"/> Alta de nuevo jugador</a>
        </form>
      </div>


      <?php
        /* GRID DE CONTENIDO */
        printf ($grid);

        /* PAGINADOR */
        printf ($paginador);

      ?>

      <br/>
      <div id="div-volver"><a href="index.php" class="btn-volver">Volver</a></div>

  <?php } //END IF GRID
  	} // END IF VALIDACION LIGA SELECCIONADA
  ?>
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