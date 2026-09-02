<?php
	require_once __DIR__ . '/../config/auth.php';


		// importamos librerias
		require_once("../controller/controller.php");
		/* cargamos clases de conexion a bd y log */
		require_once ("../config/db.class.php");
		require_once ("../model/class.php");

		$fIdEnfrentamiento = (isset( $_POST["fIdEnfrentamiento"]))? $_POST["fIdEnfrentamiento"] : null;
		$fIdLiga = (isset( $_POST["fIdLiga"]))? $_POST["fIdLiga"] : null;
		$fIdJugador1 = (isset( $_POST["fIdJugador1"]))? $_POST["fIdJugador1"] : 0;
		$fFechaBatalla = (isset( $_POST["fFechaBatalla"]))? $_POST["fFechaBatalla"] : null;
		$fIndValidado = (isset( $_POST["fIndValidado"]))? $_POST["fIndValidado"] : null;
     	$pagActual = (isset($_POST["pagActual"]))? $_POST["pagActual"] : 1;
		$csrfToken = htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8');

		$oControllerEnfrentamiento = new controllerEnfrentamiento();
		$oControllerLiga = new controllerLiga();
		$oControllerJugador = new controllerJugador();

		// actualizamos la marca de validacion
		$resultadoValidacion = $oControllerEnfrentamiento->validarResultado ( $fIdLiga, $fIdEnfrentamiento );
		if ($resultadoValidacion !== 1) {
			http_response_code(500);
			exit('No se pudo validar el enfrentamiento');
		}


		// generamos de nuevo el grid
		$arrEnfrentamientos = $oControllerEnfrentamiento->recuperarListadoEnfrentamientosCompleto ( $fIdLiga, $fIdJugador1, $fFechaBatalla, $fIndValidado, ($pagActual-1));

		$grid="";

			// comprobamos que haya datos
			if (is_array($arrEnfrentamientos) && count($arrEnfrentamientos) >= 1){
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

					$imgStar = "<img class=\"star-deportividad\" src=\"recursos/img/star.svg\" title=\"Deportividad\"/>";
					$imgFlag = "<img class=\"star-deportividad\" src=\"recursos/img/flag.svg\" title=\"Victoria concedida\"/>";

					$grid .="\n<tr><td>" . $fila[2] . " (" . $fila[3]  . ")</td><td>" . $fila[10] . "</td>
							<td>" .  $oJugador1->nick  . " (" ;

					// ESTRELLAS DEPORTIVIDAD
					if ($fila[12] > 0){
							for($i = 1; $i<= $fila[12]; $i++) { $grid .= $imgStar; }
					}else if($fila[14] > 0) {
						$grid .=  $imgFlag;
					}else{
						$grid .= "<strong>?</strong>";
					}
					$grid .= ")</td>";


					$grid .= "<td>" . $oJugador2->nick . " (";

					$imgStar = "<img class=\"star-deportividad\" src=\"recursos/img/star.svg\" title=\"Deportividad\"/>";
					$imgFlag = "<img class=\"star-deportividad\" src=\"recursos/img/flag.svg\" title=\"Victoria concedida\"/>";

					// ESTRELLAS DEPORTIVIDAD
					if ($fila[13] > 0){
							for($i = 1; $i<= $fila[13]; $i++) { $grid .= $imgStar; }
					}else if($fila[14] > 0) {
						$grid .= $imgFlag;
					}else{
						$grid .= "<strong>?</strong>";
					}

					$grid .= ")</td><td class=\"align-center\">" .  $fila[6]. " - " . $fila[7] . "</td>";


					$grid .= "<td class=\"align-center td-acciones\">";
					$grid .= "<form name=\"form-borrar-".$fila[0]."\" id=\"form-borrar-".$fila[0]."\" method=\"POST\" class=\"form-btn-acciones\">
						<input type=\"hidden\" name=\"csrf_token\" value=\"".$csrfToken."\"/>
						<input type=\"hidden\" name=\"csrf_token\" value=\"".$csrfToken."\"/>
						<input type=\"hidden\" name=\"fIdEnfrentamientoReset\" value=\"".$fila[0]."\"/>
						<input type=\"hidden\" name=\"accionForm\" value=\"3\"/>
						<input type=\"hidden\" name=\"pagActual\" value=\"".$pagActual."\" />
						<input type=\"hidden\" name=\"fIdEnfrentamiento\" value=\"".$fila[0]."\" />
						<img src=\"recursos/img/trash.svg\" title=\"Eliminar enfrentamiento\" alt=\"form-borrar-".$fila[0]."\" class=\"btn-borrar\"/>
					</form>";


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
									<input type=\"hidden\" name=\"csrf_token\" value=\"".$csrfToken."\"/>
									<input type=\"hidden\" name=\"csrf_token\" value=\"".$csrfToken."\"/>
									<input type=\"hidden\" name=\"accionForm\" id=\"accionForm\" value=\"4\"/>
									<input type=\"hidden\" name=\"fIdEnfrentamiento\" id=\"fIdEnfrentamiento\" value=\"". $fila[0] ."\" />
									<input type=\"hidden\" name=\"fIdLiga\" id=\"fIdLiga\" value=\"".$fila[1]."\"/>
									<input type=\"hidden\" name=\"fNumFase\" id=\"fNumFase\" value=\"". $fila[2] ."\" />
									<input type=\"hidden\" name=\"fNumRonda\" id=\"fNumRonda\" value=\"". $fila[3] ."\" />
									<input type=\"hidden\" name=\"fIdJugador1\" id=\"fIdJugador1\" value=\"". $fila[4] ."\" />
									<input type=\"hidden\" name=\"fIdJugador2\" id=\"fIdJugador2\" value=\"". $fila[5] ."\" />
									<input type=\"hidden\" name=\"fResultadoJugador1\" id=\"fResultadoJugador1\" value=\"". $fila[6] ."\" />
									<input type=\"hidden\" name=\"fResultadoJugador2\" id=\"fResultadoJugador2\" value=\"". $fila[7] ."\" />
									<input type=\"hidden\" name=\"fValPinturaJug1\" id=\"fValPinturaJug1\" value=\"". $fila[8] ."\" />
									<input type=\"hidden\" name=\"fValPinturaJug2\" id=\"fValPinturaJug2\" value=\"". $fila[9] ."\" />
									<input type=\"hidden\" name=\"fFechaBatalla\" id=\"fFechaBatalla\" value=\"". $fila[10] ."\" />
									<input type=\"hidden\" name=\"fIndValidado\" id=\"fIndValidado\" value=\"". $fila[11] ."\" />
									<input type=\"hidden\" name=\"fValDeportividadJug1\" id=\"fValDeportividadJug1\" value=\"". $fila[12] ."\" />
									<input type=\"hidden\" name=\"fValDeportividadJug2\" id=\"fValDeportividadJug2\" value=\"". $fila[13] ."\" />
									<input type=\"hidden\" name=\"fIdJugadorVictoriaConcedida\" id=\"fIdJugadorVictoriaConcedida\" value=\"". $fila[14] ."\" />
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
					if (false) {
					$grid .= "  <form style=\"display:none\" name=\"form-borrar-".$fila[0]."\" id=\"form-borrar-".$fila[0]."\" method=\"POST\" class=\"form-btn-acciones\">
								<input type=\"hidden\" name=\"fIdEnfrentamientoReset\" id=\"fIdEnfrentamientoReset\" value=\"".$fila[0]."\"/>
								<input type=\"hidden\" name=\"accionForm\" id=\"accionForm\" value=\"3\"/>
								<input type=\"hidden\" name=\"pagActual\" id=\"pagActual\" value=\"". $pagActual ."\" />
								<input type=\"hidden\" name=\"fIdEnfrentamiento\" id=\"fIdEnfrentamiento\" value=\"". $fila[0] ."\" />
								<img src=\"recursos/img/icon_reset.png\" title=\"Eliminar enfrentamiento\" alt=\"form-borrar-".$fila[0]."\" class=\"btn-borrar\"/>
							</form>";

					$grid .= " <form name=\"form-editar-".$fila[0]."\" id=\"form-editar-".$fila[0]."\" method=\"POST\" class=\"form-btn-acciones\">
								<input type=\"hidden\" name=\"accionForm\" id=\"accionForm\" value=\"4\"/>
								<input type=\"hidden\" name=\"fIdEnfrentamiento\" id=\"fIdEnfrentamiento\" value=\"". $fila[0] ."\" />
								<input type=\"hidden\" name=\"fIdLiga\" id=\"fIdLiga\" value=\"".$fila[1]."\"/>
								<input type=\"hidden\" name=\"fNumFase\" id=\"fNumFase\" value=\"". $fila[2] ."\" />
								<input type=\"hidden\" name=\"fNumRonda\" id=\"fNumRonda\" value=\"". $fila[3] ."\" />
								<input type=\"hidden\" name=\"fIdJugador1\" id=\"fIdJugador1\" value=\"". $fila[4] ."\" />
								<input type=\"hidden\" name=\"fIdJugador2\" id=\"fIdJugador2\" value=\"". $fila[5] ."\" />
								<input type=\"hidden\" name=\"fResultadoJugador1\" id=\"fResultadoJugador1\" value=\"". $fila[6] ."\" />
								<input type=\"hidden\" name=\"fResultadoJugador2\" id=\"fResultadoJugador2\" value=\"". $fila[7] ."\" />
								<input type=\"hidden\" name=\"fValPinturaJug1\" id=\"fValPinturaJug1\" value=\"". $fila[8] ."\" />
								<input type=\"hidden\" name=\"fValPinturaJug2\" id=\"fValPinturaJug2\" value=\"". $fila[9] ."\" />
								<input type=\"hidden\" name=\"fFechaBatalla\" id=\"fFechaBatalla\" value=\"". $fila[10] ."\" />
								<input type=\"hidden\" name=\"fIndValidado\" id=\"fIndValidado\" value=\"". $fila[11] ."\" />
								<input type=\"hidden\" name=\"fValDeportividadJug1\" id=\"fValDeportividadJug1\" value=\"". $fila[12] ."\" />
								<input type=\"hidden\" name=\"fValDeportividadJug2\" id=\"fValDeportividadJug2\" value=\"". $fila[13] ."\" />
								<input type=\"hidden\" name=\"fIdJugadorVictoriaConcedida\" id=\"fIdJugadorVictoriaConcedida\" value=\"". $fila[14] ."\" />
								<input type=\"hidden\" name=\"fIdMisionSecJug11\" id=\"fIdMisionSecJug11\" value=\"". $fIdMisionSecJug11 ."\" />
								<input type=\"hidden\" name=\"fIdMisionSecJug12\" id=\"fIdMisionSecJug12\" value=\"". $fIdMisionSecJug12 ."\" />
								<input type=\"hidden\" name=\"fIdMisionSecJug13\" id=\"fIdMisionSecJug13\" value=\"". $fIdMisionSecJug13 ."\" />
								<input type=\"hidden\" name=\"fIdMisionSecJug14\" id=\"fIdMisionSecJug14\" value=\"". $fIdMisionSecJug14 ."\" />
								<input type=\"hidden\" name=\"fIdMisionSecJug21\" id=\"fIdMisionSecJug21\" value=\"". $fIdMisionSecJug21 ."\" />
								<input type=\"hidden\" name=\"fIdMisionSecJug22\" id=\"fIdMisionSecJug22\" value=\"". $fIdMisionSecJug22 ."\" />
								<input type=\"hidden\" name=\"fIdMisionSecJug23\" id=\"fIdMisionSecJug23\" value=\"". $fIdMisionSecJug23 ."\" />
								<input type=\"hidden\" name=\"fIdMisionSecJug24\" id=\"fIdMisionSecJug24\" value=\"". $fIdMisionSecJug24 ."\" />
									<img src=\"recursos/img/check.svg\" title=\"Editar enfrentamiento\" alt=\"form-editar-".$fila[0]."\"  class=\"btn-editar-reg\"/>
							</form>\n";
					}
					if (false) {
					$grid .= "  <form name=\"form-borrar-".$fila[0]."\" id=\"form-borrar-".$fila[0]."\" method=\"POST\" class=\"form-btn-acciones\">
								<input type=\"hidden\" name=\"fIdEnfrentamientoReset\" value=\"".$fila[0]."\"/>
								<input type=\"hidden\" name=\"accionForm\" value=\"3\"/>
								<input type=\"hidden\" name=\"pagActual\" value=\"".$pagActual."\" />
								<img src=\"recursos/img/trash.svg\" title=\"Eliminar enfrentamiento\" alt=\"form-borrar-".$fila[0]."\" class=\"btn-borrar\"/>
							</form>";
					}
				}

			$grid .= "</tr>\n</table>";
			}else{
				$grid  =  "<p>No hay resultados</p>";
			}

			$grid = "<div id=\"mensaje-ok\">Enfrentamiento validado correctamente.</div>" . $grid;
			echo $grid;

?>





<script>
	$(function(){
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
	 	$(".btn-editar-reg").click( function() {
			var formularioEditar = $(this).attr('alt');
	 		$("#" + formularioEditar).submit();

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

	});




	// select de jugadores
	function validarResultado( fIdEnfrentamiento, fIdLiga, fIdJugador1, fFechaBatalla, fIndValidado, pagActual )	{

	        var parametros = {
	                "fIdEnfrentamiento" : fIdEnfrentamiento,
	                "fIdLiga" : fIdLiga,
	                "fIdJugador1" : fIdJugador1,
	                "fFechaBatalla" : fFechaBatalla,
	                "fIndValidado" : fIndValidado,
	                "pagActual" : pagActual,
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
	function actualizarSelectJugador( fIdLiga )	{

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