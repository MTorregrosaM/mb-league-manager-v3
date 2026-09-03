<?php
	require_once __DIR__ . '/../config/db.class.php';
	require_once __DIR__ . '/../config/security.php';
	validarCsrfPublico();
	

		// importamos librerias
		require_once("../controller/controller.php");
		/* cargamos clases de conexion a bd y log */
		require_once ("../config/db.class.php");

		$fIdLiga = (isset($_POST['fIdLiga'])? $_POST["fIdLiga"] : null);
		$fIdJugador = (isset($_POST['fIdJugador'])? $_POST["fIdJugador"] : null);
		$fNumFase = (isset($_POST['fNumFase'])? $_POST["fNumFase"] : null);
		exigirLigaActivaPublica($fIdLiga);

		$oControllerResultado = new controllerResultado();

		// options para los select de los formularios
		// RONDAS
		$arrRondas =  $oControllerResultado->recuperarRondasResultados( $fIdLiga, $fIdJugador, $fNumFase );
		$selectRondas = "";
		/*$aux = 1;
		if (is_array($arrRondas) && count($arrRondas) >= 1 ){
			while($aux <= 4){
				if(isset($arrRondas[$aux-1])){
					$numRonda = true; 
				}

				$selectRondas .= "\n<input type=\"radio\" id=\"" . $aux . "\" ". ((!$numRonda)? "disabled" : "") ." 
									value=\"" .  $aux  . "\" name=\"fNumRonda\"><label for=\"" . $aux  . "\">" . $aux  . "</label>";
				$aux++;
			}*/

		$selectRondas = "<select id=\"fNumRonda\" name=\"fNumRonda\">";	
		$aux = 1;	
		$RondaAux = 0;
		if ($arrRondas > 0){
			foreach ($arrRondas as $fila){
				$RondaAux = (($aux==1)? $aux : $RondaAux );
				$selectRondas .= "\n<option id=\"" . $fila[0] . "\"value=\"" .  $fila[0] . "\" " . (($aux==1)? " selected "  : "" ). ">" .$fila[0]  . "</option>";
				$aux++;
			}
		}else{
			$selectRondas .= "\n<option id=\"0\"value=\"0\" selected></option>";
		}
		$selectRondas .= "</select>";

		echo $selectRondas;


?>

<script>

	actualizarSelectJugador2Aux($('#fIdLiga').val(), $('#fNumRonda').val(),	 $('#fIdJugador1 option:selected').val(), <?php printf($fNumFase); ?>);
	
	$("#fNumRonda").change(function(){ 
		actualizarSelectJugador2Aux($('#fIdLiga').val(), $('#fNumRonda').val(), $('#fIdJugador1 option:selected').val(), <?php printf($fNumFase); ?>);
	});

	// select de jugadores
	function actualizarSelectJugador2Aux( fIdLiga, fNumRonda,  fIdJugador1, fNumFase)	{

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
	                        $("#selectJugador2").html("<div class=\"loading-select\"><img src=\"assets/img/loading.gif\" alt=\"Cargando...\" /></div>");
	                },
	                success:  function (response) {
	                        $("#selectJugador2").html(response);
							$("#resultadoJug2").text( $('#fIdJugador2Nick').val() );
							$("#fResultadoJugador1").val( $('#fResultadoJugador1Aux').val() );
			        		$("#slider-resultado-1").slider( "option", "value", $('#fResultadoJugador1Aux').val() );
							$("#fResultadoJugador2").val( $("#slider-resultado-1").slider("option", "max") + 1 - $('#fResultadoJugador1Aux').val() );
			        		$("#slider-resultado-2").slider( "option", "value", $("#slider-resultado-1").slider("option", "max") + 1 - $('#fResultadoJugador1Aux').val() );
							$("#fFechaBatalla").val( $('#fFechaBatallaAux').val() );
							$('#estrellasPintura').raty({ score: 1, click: function(score, evt) { $("#fValPintura").val(score);} });
							$('#fValPintura').val( 1 );
							if ($('#fResultadoJugador1Aux').val() >= 4){
			        	 		$("#fVictoriaSector option[value=0]").remove();
								$("#p-sectores").show();
							}else{
								$("#p-sectores").hide();
							}
	                      //  bindAjaxSelectChange();
	                }
	        });
		}
</script>