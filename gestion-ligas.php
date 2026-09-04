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

    /* Variables */
    $oControllerLiga = new controllerLiga();

    $paginaActiva = "gestion-ligas.php";
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

    // Variables POST
    $accionForm = $_POST["accionForm"] ?? 1;
    $fIdLiga = $_POST["fIdLiga"] ?? ID_LIGA;
    $fNombre = $_POST["fNombre"] ?? "";
    $fNumFases = $_POST["fNumFases"] ?? "";
    $fNumRondas = $_POST["fNumRondas"] ?? "";
    $fIdJuego = $_POST["fIdJuego"] ?? "";
    $fFecIni = $_POST["fFecIni"] ?? "";
    $fFecFin = $_POST["fFecFin"] ?? "";
    $fIndActivo = $_POST["fIndActivo"] ?? (($accionForm == 2) ? 1 : "");
    $pagActual = $_POST["pagActual"] ?? 1;
    $fIdLigaBorrar = $_POST["fIdLigaBorrar"] ?? "";
    $fIdLigaEditar = $_POST["fIdLigaEditar"] ?? "";

    // Usuario
    $oControllerUsuario = new controllerUsuario();
    $oUsuario = $oControllerUsuario->recuperarDatosUsuario($_SESSION["usuario"]);
    $ligasUsuario = $oControllerUsuario->recuperarLigasUsuario($_SESSION["usuario"]);

    // Select Juegos
    $selectJuegos = "";
    $arrJuegos = $oControllerLiga->recuperarSelectJuegos();

    if (is_array($arrJuegos) && count($arrJuegos) > 0) {
        foreach ($arrJuegos as $fila) {
            $selected = ($fIdJuego == $fila[0] && $fIdJuego > 0) ? "selected" : "";
            $selectJuegos .= "<option value=\"{$fila[0]}\" $selected>{$fila[1]}</option>" . PHP_EOL;
        }
    }

    /*
      2. ALTA
      3. ELIMINAR
      4. MODIFICAR
    */

    /********************************/
    /* 3. ELIMINAR  */
    /* DEBE EJECUTARSE ANTES QUE EL BUSCADOR, PARA MOSTRAR LUEGO EL GRID ACTUALIZADO
    /********************************/
    if ($accionForm == 3) {
      $idLigaBorrar = filter_var($fIdLigaBorrar, FILTER_VALIDATE_INT);
      $comprobarBorrado = $idLigaBorrar !== false && $idLigaBorrar > 0
        ? $oControllerLiga->borrarLiga($idLigaBorrar)
        : false;
        $mensajeBorrado = sprintf(
            '<div id="%s">%s</div>',
            $comprobarBorrado ? "mensaje-ok" : "mensaje-error",
            $comprobarBorrado ? "Liga eliminada correctamente." : "Se ha producido un error en su solicitud."
        );
    }

    /********************************/
    /* 1. BUSCADOR */
    /********************************/
    if ($accionForm != 2 && $accionForm != 4) {
        /********************************/
        /* PAGINADOR */
    /********************************/
        $numRegs = $oControllerLiga->paginadorLigas($fNombre, $fIndActivo, $fFecIni, $ligasUsuario);
        $numPags = max(1, (int) ceil($numRegs / 10));
        $pagActual = min(max(1, (int) $pagActual), $numPags);
        require_once("paginador.inc");

        /********************************/
        /* GRID DATOS */
    /********************************/
        // Si se envía el formulario de búsqueda, filtrar datos
        if ($_POST["accionForm"] ?? null) {
            $arrLigas = $oControllerLiga->recuperarListadoLigas($fNombre, $fIndActivo, $fFecIni, max(0, $pagActual - 1), $ligasUsuario);
        } else {
            $arrLigas = $oControllerLiga->recuperarListadoLigas(null, null, null, 0, $ligasUsuario);
        }

        // comprobamos que haya datos
      if (is_array($arrLigas) && count($arrLigas) >= 1){
        $grid  = "<div class=\"tabla-grid-wrap\">\n<table class=\"table-6\">\n
              <tr class=\"primerafilatabla\">
              <th>Nombre</th>
              <th>Juego</th>
              <th>Fases (Rondas)</th>
              <th>Activa</th>
              <th>Fecha Inicio</th>
              <th>Fecha Fin</th>
              <th class=\"td-acciones\"></th>
              </tr>\n";

        foreach($arrLigas as $fila){
          $grid .="\n<tr><td>" . $fila[1] . "</td><td>" . $oControllerLiga->recuperarDescJuego($fila[8]) . "</td><td  class=\"align-center\">" .  $fila[2]  . " ( ". $fila[3] .")</td><td class=\"align-center\">" . (($fila[4] == 1)? "SI" : "NO" ). "</td><td class=\"align-center\">" .  $fila[5]. "</td><td  class=\"align-center\">" .  $fila[6] . "</td>";
          $grid .= "<td class=\"align-center td-acciones\">";



          $grid .= "  <form name=\"form-fases-".$fila[0]."\" id=\"form-fases-".$fila[0]."\" method=\"POST\" class=\"form-btn-acciones\" action=\"gestion-fases.php\">
                <input type=\"hidden\" name=\"csrf_token\" value=\"".htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8')."\"/>
                <input type=\"hidden\" name=\"fIdLiga\" id=\"fIdLiga\" value=\"".$fila[0]."\"/>
                <input type=\"hidden\" name=\"accionForm\" id=\"accionForm\" value=\"1\"/>

                <img src=\"assets/img/cog.svg\" title=\"Ver fases de la liga\" alt=\"form-fases-".$fila[0]."\" class=\"btn-fases\"/>
              </form>";

              $grid .= "  <form name=\"form-borrar-".$fila[0]."\" id=\"form-borrar-".$fila[0]."\" method=\"POST\" action=\"gestion-ligas.php\" class=\"form-btn-acciones\">
                <input type=\"hidden\" name=\"csrf_token\" value=\"".htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8')."\"/>
                <input type=\"hidden\" name=\"fIdLigaBorrar\" id=\"fIdLigaBorrar\" value=\"".$fila[0]."\"/>
                <input type=\"hidden\" name=\"accionForm\" id=\"accionForm\" value=\"3\"/>
                <input type=\"hidden\" name=\"pagActual\" id=\"pagActual\" value=\"". $pagActual ."\" />
                <input type=\"hidden\" name=\"fNombre\" id=\"fNombre\" value=\"". $fNombre ."\" />
                <input type=\"hidden\" name=\"fNumFases\" id=\"fNumFases\" value=\"". $fNumFases ."\" />
                <input type=\"hidden\" name=\"fNumRondas\" id=\"fNumRondas\" value=\"". $fNumRondas ."\" />
                <img src=\"assets/img/trash.svg\" title=\"Eliminar liga\" alt=\"form-borrar-".$fila[0]."\" class=\"btn-borrar\"/>
              </form>";

          $grid .= " <form name=\"form-editar-".$fila[0]."\" id=\"form-editar-".$fila[0]."\" method=\"POST\" class=\"form-btn-acciones\">
                <input type=\"hidden\" name=\"accionForm\" id=\"accionForm\" value=\"4\"/>
                <input type=\"hidden\" name=\"fIdLiga\" id=\"fIdLiga\" value=\"".$fila[0]."\"/>
                <input type=\"hidden\" name=\"fNombre\" id=\"fNombre\" value=\"". $fila[1] ."\" />
                <input type=\"hidden\" name=\"fNumFases\" id=\"fNumFases\" value=\"". $fila[2] ."\" />
                <input type=\"hidden\" name=\"fNumRondas\" id=\"fNumRondas\" value=\"". $fila[3] ."\" />
                <input type=\"hidden\" name=\"fIndActivo\" id=\"fIndActivo\" value=\"". $fila[4] ."\" />
                <input type=\"hidden\" name=\"fFecIni\" id=\"fFecIni\" value=\"". $fila[5] ."\" />
                <input type=\"hidden\" name=\"fFecFin\" id=\"fFecFin\" value=\"". $fila[6] ."\" />
                <input type=\"hidden\" name=\"fIdJuego\" id=\"fIdJuego\" value=\"". $fila[8] ."\" />
                <img src=\"assets/img/tool.svg\" title=\"Editar liga\" alt=\"form-editar-".$fila[0]."\"  class=\"btn-editar-reg\"/>
                </form>\n";
              }
              $grid .= "</tr>\n</table>\n</div>";
        } else {
            $grid = "<p>No hay resultados</p>";
        }





    /********************************/
    /* 1. ALTA NUEVA */
    /********************************/
    }elseif (isset($_POST['accionForm']) && $_POST['accionForm'] == 2) {
        $txtAltaModBoton = "Dar de alta nueva liga";
        $txtAltaModH3 = "Alta de nueva liga";

        // Procesar alta nueva si se ha enviado el formulario
        if (!empty($_POST) and $fFecIni != "") {
            $comprobarAltaMod = $oControllerLiga->altaNuevaLiga(
                $fNombre, $fNumFases, $fNumRondas, $fFecIni,
                $fFecFin, $fIndActivo, $fIdJuego
            );

            if ($comprobarAltaMod == 1) {
                // Recuperamos el ID de la nueva liga
                $idNuevaLiga = $oControllerLiga->recuperarUltimaLiga($fNombre, $fFecIni);
                $oControllerLiga->asignarPermisoLigaUsuario($_SESSION["usuario"], $idNuevaLiga);

            }



            // Mensaje de resultado
            if ($comprobarAltaMod == 1) {
              $mensajeClase = "mensaje-ok";
              $mensajeTexto = "Liga creada correctamente.";
            } elseif ($comprobarAltaMod == 3) {
              $mensajeClase = "mensaje-error";
              $mensajeTexto = "Revisa las fechas de inicio y fin.";
            } elseif ($comprobarAltaMod == 5) {
              $mensajeClase = "mensaje-error";
              $mensajeTexto = "El número de fases y rondas debe ser un entero mayor que cero.";
            } elseif ($comprobarAltaMod == 4) {
              $mensajeClase = "mensaje-error";
              $mensajeTexto = "Ya existe una liga con ese nombre.";
            } else {
              $mensajeClase = "mensaje-error";
              $mensajeTexto = "Se ha producido un error en su solicitud.";
            }
            $mensajeAltaMod .= sprintf('<div id="%s">%s</div>', $mensajeClase, $mensajeTexto);
        }
    }


    /********************************/
    /* 3. EDITAR OBJETO */
    /********************************/
    else if ($accionForm == 4) {
        $txtAltaModBoton = "Modificar datos de liga";
        $txtAltaModH3 = "Modificar liga";

        $idLigaEditar = filter_var($fIdLiga, FILTER_VALIDATE_INT);
        if ($idLigaEditar !== false && $idLigaEditar > 0) {
            $comprobarAltaMod = $oControllerLiga->modificarDatosLiga(
          $idLigaEditar, $fNombre, $fNumFases, $fNumRondas, $fFecIni,
                $fFecFin, $fIndActivo, $fIdJuego
            );

            // Mensaje de resultado
            if ($comprobarAltaMod == 1) {
              $mensajeClase = "mensaje-ok";
              $mensajeTexto = "Liga modificada correctamente. <a href=\"{$paginaActiva}\">Volver</a>";
            } elseif ($comprobarAltaMod == 3) {
              $mensajeClase = "mensaje-aviso";
              $mensajeTexto = "AVISO: debe modificar al menos un campo.";
            } elseif ($comprobarAltaMod == 4) {
              $mensajeClase = "mensaje-error";
              $mensajeTexto = "Ya existe una liga con ese nombre.";
            } elseif ($comprobarAltaMod == 5) {
              $mensajeClase = "mensaje-error";
              $mensajeTexto = "El número de fases y rondas debe ser un entero mayor que cero.";
            } else {
              $mensajeClase = "mensaje-error";
              $mensajeTexto = "Se ha producido un error en su solicitud.";
            }
            $mensajeAltaMod .= sprintf('<div id="%s">%s</div>', $mensajeClase, $mensajeTexto);
        }
    }
} catch (Exception $e) {
    Log::getInstance()->trazaLog($e, "gestion-ligas.php");
    return null;
}

?>








<script>
  $(function(){
    /*PAGINADOR - ASIGNAMOS NUM DE PÁGINA AL HIDDEN DEL FORMULARIO */
    $(".btn-paginador").click( function() {
      var pagActual = $(this).attr('id')
      $("#pagActual").attr("value", pagActual);
      $("#buscadorligas").submit();
    });

    // boton ALTA registro
    $("#btnAltaCliente").click( function() {
      $("#btnFormAltaLiga").submit();
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
    $(".btn-editar-reg").click( function() {
      var formularioEditar = $(this).attr('alt');
      $("#" + formularioEditar).submit();

    });

    // Gestionar fases jugador
    $(".btn-fases").click( function() {
      var formularioFases = $(this).attr('alt');
      $("#" + formularioFases).submit();

    });

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


      $( ".fFecIniForm" ).datepicker({
        showOn: "both",
        buttonImage: "assets/img/calendar.svg",
        buttonImageOnly: true,
        buttonText: "Selecciona una fecha",
        dateFormat: 'dd-mm-yy',
        firstDay: 1 ,
        changeMonth: true,
        changeYear: true,
        yearRange: '1950:2035'
      });

      $( ".fFecFinForm" ).datepicker({
        showOn: "both",
        buttonImage: "assets/img/calendar.svg",
        buttonImageOnly: true,
        buttonText: "Selecciona una fecha",
        dateFormat: 'dd-mm-yy',
        firstDay: 1 ,
        changeMonth: true,
        changeYear: true,
        yearRange: '1950:2035'
      });

     $( ".spinnerFases" ).spinner();
     $( ".spinnerRondas" ).spinner();
  });
</script>


<div id="dialog-modal" title="Confirmaci&oacute;n" style="display: none;">&iquest;Est&aacute; seguro de realizar esta acci&oacute;n?</div>



<div id="contenedor-principal">
  <?php require_once("menu.php"); ?>

  <h2 class="h2"><span>Gesti&oacute;n de Ligas</span></h2>

  <?php /* MENSAJE ELIMINADO */ printf ($mensajeBorrado);   ?>



  <?php /* ALTA DE NUEVA  / MODIFICACION */
       if ($accionForm == 2 || $accionForm == 4) {


  ?>

  <div id="form">
    <h3><?php printf($txtAltaModH3);?></h3>
    <form name="altaModLiga" id="altaModLiga" method="POST" action="" autocomplete="new-password">

      <?php printf ($mensajeAltaMod);  ?>
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>" />
      <input type="hidden" name="accionForm" id="accionForm" value="<?php printf($accionForm);?>"/>
      <?php if ($accionForm == 4){ ?>
      <input type="hidden" name="fIdLigaEditar" id="fIdLigaEditar" value="<?php printf($fIdLiga);?>"/>
      <input type="hidden" name="fIdLiga" id="fIdLiga" value="<?php printf($fIdLiga);?>"/>
      <?php } ?>
      <p><label for="fNombre">Nombre: </label>  <input type="text" name="fNombre" maxlength="35"  id="fNombre" data-validation="required " value="<?php printf($fNombre);?>"  ></p>
      <p><label for="fIdJuego">Juego: </label>  <select name="fIdJuego"  id="fIdJuego" ><option></option><?php printf($selectJuegos); ?></select></p>
      <p><label for="fNumFases">N&uacute;mero fases: </label>  <input type="number" name="fNumFases" min="1" step="1" autocomplete="new-password" data-validation="required number"  id="fNumFases" value="<?php printf($fNumFases);?>" class="no-border spinnerFases"></p>
      <p><label for="fNumRondas">Rondas por fase: </label>  <input type="number" name="fNumRondas" min="1" step="1" autocomplete="new-password" data-validation="required number"  id="fNumRondas" value="<?php printf($fNumRondas);?>" class="no-border spinnerRondas" ></p>
      <p><label for="fFecIni">Fecha Inicio: </label>
        <input type="text" class="fFecIniForm" name="fFecIni" id="fFecIni" maxlength="10"
        value="<?php printf($fFecIni);?>" data-validation="required date"
        data-validation-format="dd-mm-yyyy"></p>
      <p><label for="fFecFin">Fecha Fin: </label>
        <input type="text" class="fFecFinForm" name="fFecFin" id="fFecFin" maxlength="10"
        value="<?php printf($fFecFin);?>" data-validation="required date"
        data-validation-format="dd-mm-yyyy"></p>
      <p><span class="span-radio-button">Activa: </span>
        <input type="radio" name="fIndActivo" id="fIndActivo1" value="1" <?php printf(($fIndActivo == 1)? "checked" : "");?> class="radio-button"><label class="label-radio-button" for="fIndActivo1">S&iacute;</label>
          <input type="radio" name="fIndActivo" id="fIndActivo0" value="0" <?php printf(($fIndActivo == 0)? "checked" : "");?> class="radio-button"><label class="label-radio-button" for="fIndActivo0">No</label>
      </p>
      <p><input type="submit" value="<?php printf($txtAltaModBoton);?>" id="formButton" class="submit-button"/></p>
    </form>
    <script>
      $.validate( {
          form : '#altaModLiga',
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

      <p>Desde este panel puede dar de alta, modificar o eliminar cualquier liga de la aplicaci&oacute;n.</p>

      <div id="buscador">
        <form name="buscadorligas" id="buscadorligas" method="POST" action="">
          <input type="hidden" name="accionForm" id="accionForm" value="1"/>
          <input type="hidden" name="pagActual" id="pagActual" value="1" />
          <label for="fNombre">Nombre: </label>   <input type="text" name="fNombre" id="fNombre" value="<?php printf($fNombre);?>" />
          <label for="fIndActivo">Activo: </label>
          <select name="fIndActivo" id="fIndActivo">
            <option value=""></option>
            <option value="1" <?php printf( ( ($fIndActivo == 1)? 'selected': '' ) ); ?>>S&iacute;</option>
            <option value="0" <?php printf( ( ($fIndActivo == 0)? 'selected': '' ) ); ?>>No</option>
          </select>
        <label for="fFecIni">Fecha: </label>   <input type="text" name="fFecIni" id="fFecIni" class="fFecIniForm" value="<?php printf($fFecIni);?>" class="input-fecha" />
          <input type="submit" value="Buscar" id="formButton" class="submit-button"/>
        </form>
      </div>

      <div id="btn-alta">
        <?php if($oUsuario->rol == 'ADMIN') { ?>
        <a href="gestion-juegos.php" class="button"> <img src="assets/img/new.svg" alt="Nuevo"/> Gesti&oacute;n de juegos</a>
        <?php } ?>
        <form name="btnFormAltaLiga" id="btnFormAltaLiga" method="POST" action="">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>" />
          <input type="hidden" name="accionForm" id="accionForm" value="2"/>
          <a href="#" class="button" id="btnAltaCliente"> <img src="assets/img/new.svg" alt="Nuevo"/> Alta de nueva liga</a>
        </form>
      </div>


      <?php
        /* GRID DE CONTENIDO */
        $grid = str_replace('Ver fases de la liga', 'Gestionar fases', $grid);
        printf ($grid);

        /* PAGINADOR */
        printf ($paginador);

      ?>

      <br/>
      <div id="div-volver"><a href="index.php" class="btn-volver">Volver</a></div>

  <?php } //END IF GRID?>
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