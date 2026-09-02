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
		$oControllerFase = new controllerFase();

		$paginaActiva = "gestion-fases.php";
		$grid = "";
		$txtAltaModBoton = "";
		$txtAltaModH3 = "";
		$numPags = 1;
		$pagActual = 1;
		$numRegs = 10;
		$paginador = "";
		$comprobarBorrado = false;
		$mensajeBorrado = "";
		$comprobarAltaMod = false;
		$mensajeAltaMod = "";
		$comprobarMod = false;
		$mensajeMod = "";


		// variables POST
		$accionForm = (isset($_POST["accionForm"]))? $_POST["accionForm"] : 1;
		$fIdLiga = (isset( $_POST["fIdLiga"]))? $_POST["fIdLiga"] : "";
		$fNumFase = (isset($_POST["fNumFase"]))? $_POST["fNumFase"] : "";
		$fNumRonda = (isset($_POST["fNumRonda"]))? $_POST["fNumRonda"] : "";
		$fFecIni = (isset($_POST["fFecIni"]))? $_POST["fFecIni"] : "";
		$fFecFin = (isset($_POST["fFecFin"]))? $_POST["fFecFin"] : "";
		$fClaveCifrada = (isset($_POST["fClaveCifrada"]))? $_POST["fClaveCifrada"] : "";

		$fClaveFaseBorrar = (isset($_POST["fClaveFaseBorrar"]))? $_POST["fClaveFaseBorrar"] : "";
		$fClaveFaseEditar = (isset($_POST["fClaveFaseEditar"]))? $_POST["fClaveFaseEditar"] : "";

		// options para los select de los formularios
		// LIGAS
		$arrFases =  $oControllerLiga->recuperarSelectLigas( null, false, ligasPermitidasUsuario() );
		$selectLigasSelected = ($fIdLiga != null ) ? $fIdLiga : 0;

		$selectLigas = "";
		if (is_array($arrFases) && count($arrFases) >= 1 ){
			foreach ($arrFases as $fila){
				$selectLigas .= "\n<option value=\"" . $fila[0] . "\" ". (($selectLigasSelected == $fila[0] && $selectLigasSelected > 0)? "selected" : "") ." >" .$fila[1]  . "</option>";
			}
		}

		/*
			2. ALTA
			3. ELIMINAR
			4. MODIFICAR
		*/

		/********************************/
		/* 1. BUSCADOR */
		/********************************/
		if ($accionForm != 2 && $accionForm != 4) {

			/********************************/
			/* PAGINADOR */
			/********************************/


			$numRegs = $oControllerFase->paginadorFases ($fIdLiga);
			$numPags = ceil( $numRegs / 10) ;
			require_once("paginador.inc");



			/********************************/
			/* GRID DATOS */
			/********************************/
			/* SI SE ENVIA EL FORMULARIO DE BÚSQUEDA, MANDAMOS PARÁMETROS PARA FILTRAR */
			if (isset ($_POST["accionForm"]) && ($_POST["accionForm"] == 1 || $_POST["accionForm"] == 3)){
				$arrFases = $oControllerFase->recuperarListadoFases ( $fIdLiga, ($pagActual-1));
			}else{
				$arrFases = $oControllerFase->recuperarListadoFases( $fIdLiga, 0);
			}


			// comprobamos que haya datos
			if (is_array($arrFases) && count($arrFases) >= 1){
				$grid  = "<table class=\"table-6\">\n
							<tr>
							<th>Fase</th>
							<th>Clave</th>
							<th>F. Ini</th>
							<th>F. Fin</th>
							<th class=\"td-acciones\"></th>
							</tr>\n";

				foreach($arrFases as $fila){
					$grid .="\n<tr><td>" . $fila[1] . "</td><td  class=\"align-center\">" .  $fila[4]  . "</td><td class=\"align-center\">" . $fila[2] . "</td><td class=\"align-center\">" .  $fila[3]. "</td>";
					$grid .= "<td class=\"align-center td-acciones\">";


					$grid .= " <form name=\"form-editar-".$fila[0].$fila[1]."\" id=\"form-editar-".$fila[0].$fila[1]."\" method=\"POST\" class=\"form-btn-acciones\">
								<input type=\"hidden\" name=\"accionForm\" id=\"accionForm\" value=\"4\"/>
								<input type=\"hidden\" name=\"fIdLiga\" id=\"fIdLiga\" value=\"".$fila[0]."\"/>
								<input type=\"hidden\" name=\"fNumFase\" id=\"fNumFase\" value=\"". $fila[1] ."\" />
								<input type=\"hidden\" name=\"fFecIni\" id=\"fFecIni\" value=\"". $fila[2] ."\" />
								<input type=\"hidden\" name=\"fFecFin\" id=\"fFecFin\" value=\"". $fila[3] ."\" />
								<input type=\"hidden\" name=\"fClaveCifrada\" id=\"fClaveCifrada\" value=\"". $fila[4] ."\" />
								<img src=\"recursos/img/tool.svg\" alt=\"form-editar-".$fila[0].$fila[1]."\"  class=\"btn-editar-reg\"/>
							</form>\n";
				}
				$grid .= "</tr>\n</table>";
			}else{
				$grid  =  "<p>No hay resultados</p>";
			}



		/********************************/
		/* 1. ALTA NUEVA */
		/********************************/
		}else if ( $accionForm == 2 ){
		  $txtAltaModBoton = "Dar de alta nueva fase";
		  $txtAltaModH3 = "Alta de nueva fase";

		  // grabamos nuevo registro en caso de que se haya enviado el formulario
		  if (count($_POST) > 2){
		    $comprobarAltaMod = $oControllerFase->altaFase( $fIdLiga, $fNumFase, $fFecIni, $fFecFin, $fClaveCifrada );

		    /*  1. OK
		      2. ERROR
		      3. AVISO
		      4. ERROR NUMERO DE FASE YA UTILIZADO
		    */

		    $mensajeAltaMod .= "<div id=\"". (($comprobarAltaMod == 1)? "mensaje-ok" : "mensaje-error") ."\">".
		              ( ($comprobarAltaMod == 1)? "Fase creada correctamente. " :
		                  (($comprobarAltaMod == 4)? "El número de fase ya está utilizado en la Liga, por favor, elige otro." :
		                 "Se ha producido un error en su solicitud.") ) ."</div>";
		  }


		/********************************/
		/* 3. EDITAR OBJETO */
		/********************************/
		}else if ( $accionForm == 4  ){
			$txtAltaModBoton = "Modificar datos de fase";
			$txtAltaModH3 = "Modificar fase";


			if ($fClaveFaseEditar != null){
				$comprobarAltaMod = $oControllerFase->modificarDatosFase( $fIdLiga, $fNumFase, $fFecIni, $fFecFin, $fClaveCifrada );


				/*  1. OK
					2. ERROR
					3. AVISO
					4. ERROR DUPLICADO
				*/

				$mensajeAltaMod .= "<div id=\"". ( ($comprobarAltaMod == 1)? "mensaje-ok" : (($comprobarAltaMod == 3)? "mensaje-aviso" : "mensaje-error")) ."\">".
								( ($comprobarAltaMod == 1)? "Fase modificada correctamente." :
								  (($comprobarAltaMod == 2)? "Se ha producido un error en su solicitud." : "AVISO: debe modificar al menos un campo.") ) ."</div>";


			}

		}


	}catch(Exception $e){
		$oLog = Log::getInstance();
		$oLog->trazaLog ($e, "gestion-fases.php");
		return null;
	}

?>








<script>
	$(function(){
		/*PAGINADOR - ASIGNAMOS NUM DE PÁGINA AL HIDDEN DEL FORMULARIO */
		$(".btn-paginador").click( function() {
		 	var pagActual = $(this).attr('id')
			$("#pagActual").attr("value", pagActual);
			$("#buscadorFases").submit();

		});

		// editar registro
	 	$(".btn-editar-reg").click( function() {
			var formularioEditar = $(this).attr('alt');
	 		$("#" + formularioEditar).submit();

		});


	    // boton ALTA registro
	    $("#btnAltaCliente").click( function() {
	      $("#btnFormAltaFase").submit();
	    });

	 	/* calendario */
	 	$.datepicker.regional['es'] = {
	        closeText: 'Cerrar',
	        prevText: '<Ant',
	        nextText: 'Sig>',
	        currentText: 'Hoy',
	        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
	        monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
	        dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sáb'],
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
	      buttonImage: "recursos/img/calendar.png",
	      buttonImageOnly: true,
	      buttonText: "Selecciona una fecha",
	      dateFormat: 'dd-mm-yy',
	      firstDay: 1 ,
	      changeMonth: true,
	      changeYear: true,
		yearRange: '2020:2035'
	    });

	    $( ".fFecFinForm" ).datepicker({
	      showOn: "both",
	      buttonImage: "recursos/img/calendar.png",
	      buttonImageOnly: true,
	      buttonText: "Selecciona una fecha",
	      dateFormat: 'dd-mm-yy',
	      firstDay: 1 ,
	      changeMonth: true,
	      changeYear: true,
		yearRange: '2020:2035'
	    });
	});
</script>


<div id="contenedor-principal">
	<?php require_once("menu.php"); ?>
	<h2 class="h2"><span>Gesti&oacute;n de Ligas</span></h2>


	<?php /* MENSAJE ELIMINADO */ printf ($mensajeBorrado);   ?>



	<?php /* ALTA DE NUEVA  / MODIFICACION */
		   if ($accionForm == 2 || $accionForm == 4) {


	?>

	<div id="form">
		<h3><?php printf($txtAltaModH3);?></h3>
		<form name="altaModFase" id="altaModFase" method="POST" action="" enctype="multipart/form-data">

			<?php printf ($mensajeAltaMod);  ?>
			<input type="hidden" name="accionForm" id="accionForm" value="<?php printf($accionForm);?>"/>
			<?php if ($accionForm == 4){ ?> <input type="hidden" name="fClaveFaseEditar" id="fClaveFaseEditar" value="1"/>  <?php } ?>
			<input type="hidden" name="fIdLiga" id="fIdLiga" value="<?php printf($fIdLiga);?>"/>
			<input type="hidden" name="fNumFase" id="fNumFase" value="<?php printf($fNumFase);?>"/>
			<p><label for="fNumFase">Fase: </label>  <input type="text" name="fNumFase" <?php if( $accionForm == 4){ printf("disabled"); } ?>  id="fNumFase" value="<?php printf($fNumFase);?>" class="no-border spinnerFases"></p>
			<p><label for="fClaveCifrada">Clave cifrada: </label>  <input type="text" name="fClaveCifrada" maxlength="35"  id="fClaveCifrada" value="<?php printf($fClaveCifrada);?>"  ></p>
			<p><label for="fFecIni">Fecha Inicio: </label>
				<input type="text" class="fFecIniForm" name="fFecIni" id="fFecIni" maxlength="10"
				value="<?php printf($fFecIni);?>" data-validation="required date"
				data-validation-format="dd-mm-yyyy"></p>
			<p><label for="fFecFin">Fecha Fin: </label>
				<input type="text" class="fFecFinForm" name="fFecFin" id="fFecFin" maxlength="10"
				value="<?php printf($fFecFin);?>" data-validation="required date"
				data-validation-format="dd-mm-yyyy"></p>

			<p><input type="submit" value="<?php printf($txtAltaModBoton);?>" id="formButton" class="submit-button"/></p>
		</form>
		<script>
			$.validate( {
			 		form : '#altaModFase',
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
		echo "<input type=\"hidden\" id=\"fIdLiga\" name=\"fIdLiga\" value=\"".$fIdLiga."\"/></form>";
		echo "<div id=\"div-volver\"><span class=\"btn-volver\" onClick=\"$('#form-volver').submit();\">Volver</span></div>";

		}else{


			/* BUSCADOR */ ?>
			<p >Desde este panel puede dar de alta, modificar o eliminar cualquier liga de la aplicaci&oacute;n.</p>

			<div id="buscador">
				<form name="buscadorFases" id="buscadorFases" method="POST" action="">
					<input type="hidden" name="accionForm" id="accionForm" value="1"/>
					<input type="hidden" name="pagActual" id="pagActual" value="1" />
					<label for="fIdLiga">Liga: </label> <select name="fIdLiga" id="fIdLiga" data-validation="required " ><?php printf($selectLigas); ?> </select>
					<input type="submit" value="Buscar" id="formButton" class="submit-button"/>
				</form>
			</div>



	      <div id="btn-alta">
	        <form name="btnFormAltaFase" id="btnFormAltaFase" method="POST" action="">
	          <input type="hidden" name="accionForm" id="accionForm" value="2"/>
	       	  <input type="hidden" id="fIdLiga" name="fIdLiga" value="<?php printf($fIdLiga); ?>"/>
	          <a href="#" class="button" id="btnAltaCliente"> <img src="recursos/img/icon_nuevo.png" alt="Nuevo"/> Alta de nueva fase</a>
	        </form>
	      </div>

			<?php
				/* GRID DE CONTENIDO */
				printf ($grid);

				/* PAGINADOR */
				printf ($paginador);

			?>

			<br/>
			<div id="div-volver"><a href="gestion-ligas.php" class="btn-volver">Volver</a></div>

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