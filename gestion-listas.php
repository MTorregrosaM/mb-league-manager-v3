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

    $paginaActiva = "gestion-listas.php";
    $grid = "";
    $comprobarBorrado = false;
    $mensajeBorrado = "";
    $comprobarAltaMod = false;
    $mensajeAltaMod = "";
    $comprobarMod = false;
    $mensajeMod = "";
    $grid = "";

    $accionForm = (isset( $_POST["accionForm"]))? $_POST["accionForm"] : "";
    $fIdLiga = (int) (isset( $_POST["fIdLiga"]) ? $_POST["fIdLiga"] : 0);
    $fIdJugador = (int) (isset( $_POST["fIdJugador"]) ? $_POST["fIdJugador"] : 0);
    $fNumFase = (int) (isset( $_POST["fNumFase"]) ? $_POST["fNumFase"] : 0);
    $fBando = (isset( $_POST["fBando"]))? $_POST["fBando"] : "";
    $fUrlDocumentoAux = (isset( $_POST["fUrlDocumentoAux"]))? $_POST["fUrlDocumentoAux"] : "";
    $fUrlDocumento = $fUrlDocumentoAux;
    $fSubidaValida = true;
    $fArchivoSubido = isset($_FILES["fUrlDocumento"]) && $_FILES["fUrlDocumento"]["error"] !== UPLOAD_ERR_NO_FILE;

    if ($fIdJugador > 0) {
      $jugadorLiga = $oControllerJugador->recuperarDatosJugador($fIdJugador);
      if ($jugadorLiga !== null) {
        $fIdLiga = (int) $jugadorLiga->idLiga;
      }
    }

    if ($fArchivoSubido) {
      $archivo = $_FILES["fUrlDocumento"];
      $extensionesPermitidas = array("pdf", "doc", "docx", "xls", "xlsx");
      $extension = strtolower(pathinfo($archivo["name"], PATHINFO_EXTENSION));
      $tiposPermitidos = array(
        "pdf" => "application/pdf",
        "doc" => "application/msword",
        "docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        "xls" => "application/vnd.ms-excel",
        "xlsx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
      );
      $mime = (new finfo(FILEINFO_MIME_TYPE))->file($archivo["tmp_name"]);
      $fSubidaValida = $archivo["error"] === UPLOAD_ERR_OK
        && $archivo["size"] <= 5 * 1024 * 1024
        && in_array($extension, $extensionesPermitidas, true)
        && isset($tiposPermitidos[$extension])
        && $mime === $tiposPermitidos[$extension];
      if ($fSubidaValida) {
        $fUrlDocumento = "lista-" . $fIdLiga . "-" . $fNumFase . "-" . $fIdJugador . "-" . bin2hex(random_bytes(8)) . "." . $extension;
      } else {
        $fUrlDocumentoAux = null;
        $fUrlDocumento = null;
      }
    }
    $fFechaDocumento = (isset( $_POST["fFechaDocumento"]))? $_POST["fFechaDocumento"] : "";
    $fRutaDoc = (isset( $_POST["fRutaDoc"]))? $_POST["fRutaDoc"] : "";
    $fIdListaEditar = (isset( $_POST["fIdListaEditar"]))? $_POST["fIdListaEditar"] : "";

    $selectLigas = "<option value=''></option>\n";
    $selectJugadores = "";

    $oLiga = $oControllerLiga -> recuperarDatosLiga( $fIdLiga );
    $oJugador = $oControllerJugador -> recuperarDatosJugador( $fIdJugador );
    $rutaDocumentosLiga = __DIR__ . "/assets/docs/ligas/" . $fIdLiga;

    // options para los select de los formularios
    // LIGAS
    $arrListas =  $oControllerLiga->recuperarSelectLigas( null, false, ligasPermitidasUsuario() );
    $selectLigasSelected = ($fIdLiga != null ) ? $fIdLiga : 0;

    if (is_array($arrListas) && count($arrListas) >= 1 ){
      foreach ($arrListas as $fila){
        $selectLigas .= "\n<option value=\"" . $fila[0] . "\" ". (($selectLigasSelected == $fila[0] && $selectLigasSelected > 0)? "selected" : "") ." >" .$fila[1]  . "</option>";
      }
    }


    // options para los select de los formularios
    // FASES
    $selectFases = "";
    $arrFases =  $oControllerLiga->recuperarSelectFases( $fIdLiga );
    $selectFasesSelected = ($fNumFase != null ) ? $fNumFase : 0;

    if (is_array($arrFases) && count($arrFases) >= 1 ){
      $selectFases .= "\n<option value=\"\"></option>";
      foreach ($arrFases as $fila){
        $selectFases .= "\n<option value=\"" . $fila[0] . "\" ". (($selectFasesSelected == $fila[0] && $selectFasesSelected > 0)? "selected" : "") ." >" .$fila[0]  . "</option>";
      }
    }


    // options para los select de los formularios
    // JUGADORES
    $arrJugadores =  $oControllerJugador->recuperarSelectJugadores( $fIdLiga, $fNumFase ) ;
    $selectJugadoresSelected = ($fIdJugador != null ) ? $fIdJugador : 0;
    $selectJugadores = "";

    if (is_array($arrJugadores) && count($arrJugadores) >= 1 ){
      foreach ($arrJugadores as $fila){
        $selectJugadores .= "\n<option value=\"" . $fila[0] . "\" ". (($selectJugadoresSelected == $fila[0] && $selectJugadoresSelected > 0)? "selected" : "") ." >" .$fila[1]  . "</option>";
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
    if ( $accionForm == 3 ){

      $comprobarBorrado = $oControllerJugador->borrarLista( $fIdLiga, $fIdJugador, $fNumFase, $fBando, $fRutaDoc );

      $mensajeBorrado .= "<div id=\"". (($comprobarBorrado)? "mensaje-ok" : "mensaje-error") ."\">". (($comprobarBorrado)? "Lista eliminada correctamente." : "Se ha producido un error en su solicitud.") ."</div>";
      $fBando = null;
      $fNumFase = null;
    }

    /********************************/
    /* 1. BUSCADOR */

    if ($accionForm != 2 && $accionForm != 4) {


      /********************************/
      /* GRID DATOS */
      /********************************/
      /* SI SE ENVIA EL FORMULARIO DE BÚSQUEDA, MANDAMOS PARÁMETROS PARA FILTRAR */
      $arrListasJugador = array();
      if (isset ($_POST["accionForm"]) && ($_POST["accionForm"] == 1 || $_POST["accionForm"] == 3)){
        $arrListasJugador = $oControllerJugador->recuperarListadoListas ( $fIdJugador, $fIdLiga, $fNumFase, $fBando );
      }


      // comprobamos que haya datos
      if ($arrListasJugador != null && count($arrListasJugador) >= 1){
        $grid  = "<div class=\"tabla-grid-wrap\">\n<table class=\"table-4\">\n
              <tr>
              <th>Fase</th>
              <th>Bando</th>
              <th>Documento</th>
              <th>Fecha</th>
              <th class=\"td-acciones\"></th>
              </tr>\n";

        foreach($arrListasJugador as $fila){
          // primero calculamos la ruta del directorio
          $fDocUrl = $fila[2];
          $fDocAux = $fila[2];

          if (strpos($fila[2] , "http") === false && strpos($fila[2] , "https") === false && strpos($fila[2] , "wwww") === false){
            //$fDocAux  = getcwd()."/assets/docs/ligas/".$oLiga->nombre."/".$fila[1]."/".$fila[2];
            //$fDocAux  =$_SERVER['HTTP_HOST'] ."/mb-league/assets/docs/ligas/".$oLiga->nombre."/".$fila[1]."/".$fila[2];
            $fDocUrl = "/assets/docs/ligas/" . $fIdLiga . "/" . $fila[1] . "/" . rawurlencode(basename($fila[2]));
            $fDocAux = $rutaDocumentosLiga . "/" . $fila[1] . "/" . basename($fila[2]);
          }

          $grid .="\n<tr><td>" . $fila[1] . "</td><td>" . $fila[3] . "</td><td  class=\"align-center\"><a target=\"blank\" href=\"" .  $fDocUrl  . "\" class=\"link-grid\">Descargar</a></td><td class=\"align-center\">" . $fila[4] . "</td>";
          $grid .= "<td class=\"align-center td-acciones\">";


          $grid .= "  <form name=\"form-borrar-".$fila[1].$fila[0].$fila[3]."\" id=\"form-borrar-".$fila[1].$fila[0].$fila[3]."\" method=\"POST\" class=\"form-btn-acciones\">
                <input type=\"hidden\" name=\"accionForm\" id=\"accionForm\" value=\"3\"/>
                <input type=\"hidden\" name=\"fIdLiga\" id=\"fIdLiga\" value=\"". $fIdLiga ."\" />
                <input type=\"hidden\" name=\"fIdJugador\" id=\"fIdJugador\" value=\"". $fIdJugador ."\" />
                <input type=\"hidden\" name=\"fNumFase\" id=\"fNumFase\" value=\"". $fila[1] ."\" />
                <input type=\"hidden\" name=\"fBando\" id=\"fBando\" value=\"". $fila[3]."\" />
                <input type=\"hidden\" name=\"fRutaDoc\" id=\"fRutaDoc\" value=\"". $fDocAux ."\" />
                <img src=\"assets/img/trash.svg\" title=\"Eliminar lista\" alt=\"form-borrar-".$fila[1].$fila[0].$fila[3]."\" class=\"btn-borrar\"/>
              </form>";

          $grid .= " <form name=\"form-editar-".$fila[0]."\" id=\"form-editar-".$fila[0]."\" method=\"POST\" class=\"form-btn-acciones\">
                <input type=\"hidden\" name=\"accionForm\" id=\"accionForm\" value=\"4\"/>
                <input type=\"hidden\" name=\"fIdLiga\" id=\"fIdLiga\" value=\"". $fIdLiga ."\" />
                <input type=\"hidden\" name=\"fIdJugador\" id=\"fIdJugador\" value=\"". $fIdJugador ."\" />
                <input type=\"hidden\" name=\"fNumFase\" id=\"fNumFase\" value=\"". $fila[1] ."\" />
                <input type=\"hidden\" name=\"fBando\" id=\"fBando\" value=\"". $fila[3]."\" />
                <input type=\"hidden\" name=\"fUrlDocumentoAux\" id=\"fUrlDocumentoAux\" value=\"". $fila[2] ."\" />
                <img src=\"assets/img/cog.svg\" title=\"Editar lista\" alt=\"form-editar-".$fila[0]."\"  class=\"btn-editar-reg\"/>
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
      $txtAltaModBoton = "Subir nueva lista";
      $txtAltaModH3 = "Alta de nueva lista";

      // grabamos nuevo registro en caso de que se haya enviado el formulario
      if ($fUrlDocumentoAux != null ){
        if (!$fArchivoSubido && strpos($fUrlDocumento , "http") === false && strpos($fUrlDocumento , "https") === false && strpos($fUrlDocumento , "wwww") === false){
          $fUrlDocumento = "lista-".$fIdLiga.$fNumFase.$fIdJugador.Date("is")."-".$fBando.substr($fUrlDocumento,strpos($fUrlDocumento, "."));
        }
        $comprobarAltaMod = $oControllerJugador->altaNuevaLista( $fIdJugador, $fIdLiga, $fNumFase, $fUrlDocumento, $fBando  );

        if ($comprobarAltaMod == 1){

          // subimos el fichero de la imagen si todo ha ido ok
          if ($fSubidaValida && strpos($fUrlDocumento , "http") === false && strpos($fUrlDocumento , "https") === false && strpos($fUrlDocumento , "wwww") === false){

            if (!is_dir($rutaDocumentosLiga) && !mkdir($rutaDocumentosLiga, 0775, true)){
              printf("Error creando directorio");
            }
            if (!is_dir($rutaDocumentosLiga . "/" . $fNumFase) && !mkdir($rutaDocumentosLiga . "/" . $fNumFase, 0775, true)){
              printf("Error creando directorio");
            }
            $rutaDestino = $rutaDocumentosLiga . "/" . $fNumFase . "/" . basename($fUrlDocumento);
            move_uploaded_file($_FILES['fUrlDocumento']['tmp_name'], $rutaDestino);
          }
        }
        /*
          1. OK
          2. ERROR
          3. AVISO
        */

        $mensajeAltaMod .= "<div id=\"". (($comprobarAltaMod == 1)? "mensaje-ok" : "mensaje-error") ."\">".
                  ( ($comprobarAltaMod == 1)? "Lista subida correctamente." :
                    (($comprobarAltaMod == 3)? "El jugador seleccionado ya tiene registrada una lista para el bando indicado." : "Se ha producido un error en su solicitud.") )."</div>";

      }


    /********************************/
    /* 3. EDITAR OBJETO */
    /********************************/
    }else if ( $accionForm == 4  ){
      $txtAltaModBoton = "Modificar lista";
      $txtAltaModH3 = "Modificar lista";


      if ($fIdListaEditar != null){
        if (!$fArchivoSubido && strpos($fUrlDocumento , "http") === false && strpos($fUrlDocumento , "https") === false && strpos($fUrlDocumento , "wwww") === false){
          $fUrlDocumento = "lista-".$fIdLiga.$fNumFase.$fIdJugador.Date("is")."-".$fBando.substr($fUrlDocumento,strpos($fUrlDocumento, "."));
        }
        $comprobarAltaMod = $oControllerJugador->modificarLista( $fIdJugador, $fIdLiga, $fNumFase, $fBando, $fUrlDocumento, $rutaDocumentosLiga . "/" . $fNumFase . "/"  );


        // subimos el fichero de la imagen si todo ha ido ok
        if ($comprobarAltaMod == 1 && $fUrlDocumento != null){
          // subimos el fichero de la imagen si todo ha ido ok
          if ($fSubidaValida && strpos($fUrlDocumento , "http") === false && strpos($fUrlDocumento , "https") === false && strpos($fUrlDocumento , "wwww") === false && isset($_FILES['fUrlDocumento']['name'])){

            if (!is_dir($rutaDocumentosLiga) && !mkdir($rutaDocumentosLiga, 0775, true)){
              printf("Error creando directorio");
            }
            if (!is_dir($rutaDocumentosLiga . "/" . $fNumFase) && !mkdir($rutaDocumentosLiga . "/" . $fNumFase, 0775, true)){
              printf("Error creando directorio");
            }
            $rutaDestino = $rutaDocumentosLiga . "/" . $fNumFase . "/" . basename($fUrlDocumento);
            move_uploaded_file($_FILES['fUrlDocumento']['tmp_name'], $rutaDestino);
          }
        }

        /*  1. OK
          2. ERROR
          3. AVISO
          4. ERROR DUPLICADO
        */

        $mensajeAltaMod .= "<div id=\"". ( ($comprobarAltaMod == 1)? "mensaje-ok" : (($comprobarAltaMod == 3)? "mensaje-aviso" : "mensaje-error")) ."\">".
                ( ($comprobarAltaMod == 1)? "Lista modificada correctamente. <a href=\"". $paginaActiva  ."\">Volver</a>" :
                  (($comprobarAltaMod == 2)? "Se ha producido un error en su solicitud." : "AVISO: debe modificar al menos un campo.") ) ."</div>";


      }

    }


  }catch(Exception $e){
    $oLog = Log::getInstance();
    $oLog->trazaLog ($e, "gestion-ligas.php");
    return null;
  }

?>



<script>



  /* ajax */
  $(function(){

    //  $("#fIdLiga").change(function(){ alert(9), actualizarSelectFases( $('#fIdLiga option:selected').val());
    $("#fIdLiga").change(function(){
       actualizarSelectFases( $('#fIdLiga option:selected').val(), <?php printf(($fNumFase == null) ? "0" : $fNumFase); ?>);
       actualizarSelectJugadores( $('#fIdLiga option:selected').val(), $('#fNumFase option:selected').val(),   <?php printf($fIdJugador); ?>);
    });


    // boton ALTA registro
    $("#btnAltaLista").click( function() {
      $("#btnFormAltaLista").submit();
    });

    $("#fUrlDocumento").change( function(){
      $("#fUrlDocumentoAux").val($("#fUrlDocumento").val().replace("C:\\fakepath\\" ,""));
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

  });

  // select de fases
  function actualizarSelectFases( fIdLiga, fNumFase ) {

          var parametros = {
                  "fIdLiga" : fIdLiga,
                  "fNumFase" : fNumFase,
          };

          $.ajax({
              async: true,
                  data:  parametros,
                  url:   'ajax/ajax.fases.php',
                  type:  'post',
                  beforeSend: function () {
                          $("#selectFases").html("<div class=\"loading-select\"><img src=\"assets/img/loading.gif\" alt=\"Cargando...\" /></div>");
                  },
                  success:  function (response) {
                          $("#selectFases").html(response);
                        //  bindAjaxSelectChange();
                  }
          });
  }


  // select de jugadores
  function actualizarSelectJugadores( fIdLiga, fNumFase,  fIdJugador) {

          var parametros = {
                  "fIdLiga" : fIdLiga,
                  "fNumFase" : fNumFase,
                  "fIdJugador" : fIdJugador,
          };

          $.ajax({
              async: true,
                  data:  parametros,
                  url:   'ajax/ajax.jugadores.php',
                  type:  'post',
                  beforeSend: function () {
                          $("#selectJugadores").html("<div class=\"loading-select\"><img src=\"assets/img/loading.gif\" alt=\"Cargando...\" /></div>");
                  },
                  success:  function (response) {
                          $("#selectJugadores").html(response);
                        //  bindAjaxSelectChange();
                  }
          });
  }

</script>





<div id="dialog-modal" title="Confirmaci&oacute;n" style="display: none;">&iquest;Est&aacute; seguro de realizar esta acci&oacute;n?</div>


<div id="contenedor-principal">
  <?php require_once("menu.php"); ?>
  <h2 class="h2"><span>Gesti&oacute;n de listas</span></h2>
  <?php /* ALTA DE NUEVA  / MODIFICACION */
       if ($accionForm == 2 || $accionForm == 4) {
  ?>

  <div id="form">
    <h3><?php printf($txtAltaModH3);?></h3>
    <form name="altaModLista" id="altaModLista" method="POST" action="" enctype="multipart/form-data">
      <?php printf ($mensajeAltaMod);  ?>
      <input type="hidden" name="accionForm" id="accionForm" value="<?php printf($accionForm);?>"/>
      <?php if ($accionForm == 4){ ?> <input type="hidden" name="fIdListaEditar" id="fIdListaEditar" value="1"/>  <?php } ?>
      <input type="hidden" name="fIdLiga" id="fIdLiga" value="<?php printf($fIdLiga);?>"/>
      <input type="hidden" name="fIdJugador" id="fIdJugador" value="<?php printf($fIdJugador);?>"/>
      <input type="hidden" name="fBando" id="fBando" value="<?php printf($fBando);?>"/>
      <input type="hidden" name="fNumFase" id="fNumFase" value="<?php printf($fNumFase);?>"/>
      <p><label for="fLiga">Liga: </label>  <input type="text" name="fLiga" id="fLiga" value="<?php printf( $oLiga->nombre );?>" disabled></p>
      <p><label for="fNombre">Jugador: </label>  <input type="text" name="fNombre" id="fNombre" value="<?php printf( $oJugador->nick . " - " . $oJugador->nombre . " " . $oJugador->apellido1);?>" disabled ></p>
      <p><label for="fNumFase">Fase: </label><select name="fNumFase" id="fNumFase" data-validation="required" <?php if($accionForm == 4) printf("disabled"); ?>><?php printf($selectFases); ?></select></p>
      <p>
        <label for="fBando">Bando: </label>
        <select name="fBando" id="fBando" data-validation="required " <?php if($accionForm == 4) printf("disabled"); ?>>
          <option value="ALIADO"  <?php if($fBando == "ALIADO") printf("selected"); ?>>ALIADO</option>
          <option value="EJE"  <?php if($fBando == "EJE") printf("selected"); ?>>EJE</option>
          </select>
      </p>
      <p><label for="fUrlDocumento">Documento: </label>   <input type="file" name="fUrlDocumento" id="fUrlDocumento" data-validation="mime size" data-validation-allowing="pdf, doc, docx, xls, xlsx" data-validation-max-size="5000kb"
                            data-validation-error-msg-size="El documento debe debe ser un PDF, XLS o DOC"
                            /></p>
      <p><label for="fUrlDocumentoAux">Url documento: </label>  <input type="text" name="fUrlDocumentoAux" maxlength="35"  id="fUrlDocumentoAux" value="<?php printf($fUrlDocumento);?>"   data-validation="required"></p>
      <?php if (strlen($fUrlDocumento) > 0 && $accionForm == 4 ) { ?>
          <p><label for=""> </label><div class="config-foto"><a href="/assets/docs/ligas/<?php echo (int) $fIdLiga . "/" . (int) $fNumFase . "/" . rawurlencode(basename($fUrlDocumento)); ?>" target="_blank"><img src="assets/img/icon-pdf.png" alt="Lista de ejército"/></a></div></p>
      <?php } ?>
      <p><input type="submit" value="<?php printf($txtAltaModBoton);?>" id="formButton" class="submit-button"/></p>
    </form>
    <script>
      $.validate( {
          form : '#altaModLista',
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
    echo "<form action=\"$paginaActiva\" id=\"form-volver\" name=\"form-volver\" method=\"POST\"><input type=\"hidden\" id=\"fIdLiga\" name=\"fIdLiga\" value=\"".$fIdLiga."\"/>";
    echo "<input type=\"hidden\" id=\"fNumFase\" name=\"fNumFase\" value=\"".$fNumFase."\"/>";
    echo "<input type=\"hidden\" id=\"accionForm\" name=\"accionForm\" value=\"1\"/>";
    echo "<input type=\"hidden\" id=\"fIdJugador\" name=\"fIdJugador\" value=\"".$fIdJugador."\"/>";
    echo "<input type=\"hidden\" id=\"fIdLiga\" name=\"fIdLiga\" value=\"".$fIdLiga."\"/></form>";
    echo "<div id=\"div-volver\"><span class=\"btn-volver\" onClick=\"$('#form-volver').submit();\">Volver</span></div>";


    }else{

    /* BUSCADOR */ ?>

    <div id="buscador">
      <form name="buscadorJugadores" id="buscadorJugadores" method="POST" action="">
        <input type="hidden" name="accionForm" id="accionForm" value="1"/>
        <label for="fIdLiga">Liga: </label> <select name="fIdLiga" id="fIdLiga" data-validation="required " ><?php printf($selectLigas); ?> </select>
        <label for="fNumFase">Fase: </label> <span id="selectFases"><select name="fNumFase" id="fNumFase" data-validation="required" ><?php printf($selectFases); ?></select></span>
        <label for="fIdJugador">Jugador: </label> <span id="selectJugadores"><select name="fIdJugador" id="fIdJugador" data-validation="required " ><?php printf($selectJugadores); ?> </select></span>
        <label for="fBando">Bando: </label>
        <select name="fBando" id="fBando" data-validation="required ">
          <option value=""  <?php if($fBando != "ALIADO" && $fBando != "EJE") printf("selected"); ?>></option>
          <option value="ALIADO"  <?php if($fBando == "ALIADO") printf("selected"); ?>>ALIADO</option>
          <option value="EJE"  <?php if($fBando == "EJE") printf("selected"); ?>>EJE</option>
          </select>
        <input type="submit" value="Buscar listas" id="formButton" class="submit-button"/>

      </form>
    </div>

    <div id="btn-alta">
      <form name="btnFormAltaLista" id="btnFormAltaLista" method="POST" action="">
        <input type="hidden" name="accionForm" id="accionForm" value="2"/>
        <input type="hidden" name="fIdJugador" id="fIdJugador" value="<?php printf($fIdJugador);?>"/>
        <input type="hidden" name="fIdLiga" id="fIdLiga" value="<?php printf($fIdLiga);?>"/>
        <input type="hidden" name="fNumFase" id="fNumFase" value="<?php printf("%s", $fNumFase ?? "");?>"/>
        <a href="#" class="button" id="btnAltaLista"> <img src="assets/img/new.svg" alt="Nuevo"/>Alta de nueva lista</a>
      </form>
    </div>

      <?php
        /* GRID DE CONTENIDO */
        printf ($grid);

        /* PAGINADOR */


      ?>



    <br/>
    <div id="div-volver"><a href="gestion-jugadores.php" class="btn-volver">Volver</a></div>
  <?php } //END IF GRID?>
  <br/>





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