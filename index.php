<html>
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
		$oControllerUsuario = new controllerUsuario();
		
		$ligasUsuario = $oControllerUsuario->recuperarLigasUsuario( $_SESSION["usuario"] );
		
		

	  	// ordenamos por pintura
		function sortByOrder($a, $b) {
	       	      if($a[1] != null && $b[1] != null)
					return $b[1] - $a[1] ;
	    } 
	    // ordenamos por pintura
		function sortByOrderASC($a, $b) {
	       	return ($a[4] - $b[4])  ;
	    }     

	}catch(Exception $e){
		$oLog = Log::getInstance();
		$oLog->trazaLog ($e, "gestion-enfrentamientos.php");	
		return null;	 
	}


	// options para los select de los formularios
		// LIGAS

		$selectLigas = "<option value=''></option>\n";

	

		$arrLigas =  $oControllerLiga->recuperarSelectLigas( null, true, $ligasUsuario);
		$arrLigas = is_array($arrLigas) ? $arrLigas : array();
		$selectLigasSelected = ($fIdLiga != null ) ? $fIdLiga : 0;

			if (count($arrLigas) >= 1 ){
				foreach ($arrLigas as $fila){
					$selectLigas .= "\n<option value=\"" . $fila[0] . "\" ". (($selectLigasSelected == $fila[0] && $selectLigasSelected > 0)? "selected" : "") ." >" .$fila[1]  . "</option>";
				}
			}

?>

<div id="contenedor-principal">
	
	<?php require_once("menu.php"); ?>
	<div class="center"><form id="selectLiga" name="selectLiga" method="POST"><label for="fIdLiga" class="span-index">Liga  </label> <select name="fIdLiga" id="fIdLiga" class="select-index" ><?php printf($selectLigas); ?> </select></form></div>

<?php if (isset($fIdLiga) && $fIdLiga != null && $fIdLiga > 0) { 
	// sacamos el tipo de juego para ver qué paneles mostrar

	$oLiga = $oControllerLiga->recuperarDatosLiga ( $fIdLiga );
	if ($oLiga !== null) {
		$idJuegoLiga = $oLiga->idJuego;
	} else {
		$idJuegoLiga = null; 
	}
?>

	<?php if ($idJuegoLiga == 1 || $idJuegoLiga == 2) { ?>
		<div class="div-ranking-izq">
			<h2>Top Misiones Secundarias</h2>

			<?php 
				printf("<table>\n
									<tr>
									<th>Nick </th>
									<th>Medallas</th>
									</tr>\n");

				$arrRanking = $oControllerEnfrentamiento->recuperarRankingPuntosMisionesSec(  $fIdLiga );
				if (!empty($arrRanking) && !is_array($arrRanking) ){
					usort($arrRanking, "sortByOrder");
					$top = 0;
					foreach($arrRanking as $fila ) {
						printf("<tr class=\"bg-" . $top . "\"><td>" . $fila[0] . "</td><td class=\"align-center\">" . $fila[1] . "</td></tr>");
						$top++;
						if ($top == 10) break;
					}
				}else{
					printf("<tr><td colspan=\"2\">No hay resultados</td></tr>");
				}

				printf("</table>");
			?>
		</div>
	
	<?php } ?>

	<?php if ($idJuegoLiga == 1 || $idJuegoLiga == 2) { ?>
		<div class="div-ranking-dch">
	<?php }else{ ?>
		<div class="div-ranking-izq">
	<?php }?>
		<h2>Top Puntuaci&oacute;n Pintura</h2>

		<?php 
			printf("<table>\n
								<tr>
								<th>Nick </th>
								<th>Media puntos pintura (partidas disputadas)</th>
								</tr>\n");

			$arrRanking = $oControllerEnfrentamiento->recuperarRankingPuntosPintura( $fIdLiga );
			if ($arrRanking != null && count($arrRanking) > 0){
				usort($arrRanking, "sortByOrder");
				$top = 0;
				foreach($arrRanking as $fila ) {
					printf("<tr class=\"bg-" . $top . "\"><td>" . $fila[0] . "</td><td class=\"align-center\">" . $fila[2] . "</td></tr>");
					$top++;
					if ($top == 10) break;
				}
			}else{
				printf("<tr><td colspan=\"2\">No hay resultados</td></tr>");
			}

			printf("</table>");
		?>
	</div>

	<?php if ($idJuegoLiga == 1 || $idJuegoLiga == 2) { ?>
		<div class="div-ranking-izq">
		<br/><br/>
	<?php }else{ ?>
		<div class="div-ranking-dch">
	<?php }?>
		<h2>Top Deportividad</h2>

		<?php 
			printf("<table>\n
								<tr>
								<th>Nick </th>
								<th>Deportividad</th>
								<th>Total puntos</th>
								<th>Partidas validadas</th>
								</tr>\n");

			$arrRanking = $oControllerEnfrentamiento->recuperarRankingPuntosDeportividad(  $fIdLiga );
			if ($arrRanking != null && count($arrRanking) > 0){
				usort($arrRanking, "sortByOrderASC");
				$top = 0;
				foreach($arrRanking as $fila ) {
					printf("<tr><td><form method=\"POST\" action=\"editar-resultados.php\" id=\"form-dep-" . $fila[3] . "\" class=\"form-deportividad\">
						<input type=\"hidden\" name=\"fIdJugador1\" id=\"fIdJugador1\" value=\"" . $fila[3] . "\"/>
						<input type=\"hidden\" name=\"fIdLiga\" id=\"fIdLiga\" value=\"" . $fIdLiga . "\"/>
						<input type=\"hidden\" name=\"accionForm\" id=\"accionForm\" value=\"1\"/>
						<a href=\"#\" id=\"form-deportividad-" . $fila[3] . "\" name=\"form-dep-" . $fila[3] . "\">" . $fila[0] . " <img src=\"recursos/img/icon-link-depor.png\"/></a></form></td>
						<td>" . $fila[1] . " </td><td>" . $fila[5] . "</td><td>" . $fila[2] . " </td></tr>");
					$top++;
					if ($top == 10) break;
				}
			}else{
				printf("<tr><td colspan=\"4\">No hay resultados</td></tr>");
			}

			printf("</table>");
		?>
	</div>

	<?php if ($idJuegoLiga == 1 || $idJuegoLiga == 2) { ?>
	<div class="div-ranking-dch">
		<br/><br/>
		<h2>Campaña por sectores</h2>

		<?php 
			printf("<table>\n
								<tr>
								<th>Fase </th>
								<th>Sector</th>
								<th>Bando dominante</th>
								</tr>\n");

			$arrRanking = $oControllerEnfrentamiento->recuperarRankingSectores( $fIdLiga, $oLiga->idJuego );
			if ($arrRanking != null && count($arrRanking) > 1){

				FOR ($i = 0; $i < count($arrRanking); $i++) {

				
				
					/* sector 1 - ARNHEM */
					
					if ( ($arrRanking[$i][1] == 'SECTOR 1' && $arrRanking[$i+1][1] == 'SECTOR 1') || ($arrRanking[$i][1] == 'SECTOR 1' && array_key_exists($i+1, $arrRanking) && $arrRanking[$i+1][1] != 'SECTOR 1' ) ){
						$bando = $arrRanking[$i][2];
						$victoriasBando = $arrRanking[$i][3];


						if ($arrRanking[$i+1][1] == 'SECTOR 1'){
							$bando2 = $arrRanking[$i+1][2];
							$victoriasBando2 = $arrRanking[$i+1][3];

							$bando = ($victoriasBando = $victoriasBando2)? "EMPATE" . " (" . $victoriasBando . "-" .$victoriasBando2 . ")" : 
							(($victoriasBando > $victoriasBando2)? 
									$bando . " (" . $victoriasBando . "-" .$victoriasBando2 . ")" : $bando2. " (" . $victoriasBando2 . "-" .$victoriasBando . ")");
							$i++;
						}else{
							$bando = $bando . " (" . $victoriasBando . "-0)" ;
						}

						printf("<tr><td class=\"align-center\">" . $arrRanking[$i][0] . "</td><td class=\"align-left\">" . $arrRanking[$i][1] . "</td><td>" . $bando . "</td></tr>");
						
						// incrementamos el índice en 1
						//$i++;
						
					}else if ($arrRanking[$i][1] == 'SECTOR 1' && array_key_exists($i-1, $arrRanking) && $arrRanking[$i-1][1] != 'SECTOR 1'){

						$bando = $arrRanking[$i ][2];
						$victoriasBando = $arrRanking[$i][3];

						$bando = $bando . " (" . $victoriasBando . "-0)" ;
						printf("<tr><td class=\"align-center\">" . $arrRanking[$i][0] . "</td><td class=\"align-left\">" . $arrRanking[$i][1] . "</td><td>" . $bando . "</td></tr>");
						
					}
					


					/* sector 2 - GOTENSTELLUNG */
					if ($arrRanking[$i][1] == 'SECTOR 2' && array_key_exists($i+1, $arrRanking)  && $arrRanking[$i+1][1] == 'SECTOR 2' ){
						$bando = $arrRanking[$i ][2];
						$victoriasBando = $arrRanking[$i][3];

						

						if ($arrRanking[$i+1][1] == 'SECTOR 2'){
							$bando2 = $arrRanking[$i+1][2];
							$victoriasBando2 = $arrRanking[$i+1][3];

							$bando = ($victoriasBando = $victoriasBando2)? "EMPATE" . " (" . $victoriasBando . "-" .$victoriasBando2 . ")" : 
								(($victoriasBando > $victoriasBando2)? 
									$bando . " (" . $victoriasBando . "-" .$victoriasBando2 . ")" : $bando2. " (" . $victoriasBando2 . "-" .$victoriasBando . ")");
						}else{
							$bando = $bando . " (" . $victoriasBando . "-0)" ;
						}

						printf("<tr><td class=\"align-center\">" . $arrRanking[$i][0] . "</td><td class=\"align-left\">" . $arrRanking[$i][1] . "</td><td>" . $bando . "</td></tr>");
					}else if ($arrRanking[$i][1] == 'SECTOR 2' && $arrRanking[$i-1][1] != 'SECTOR 2'){
						$bando = $arrRanking[$i ][2];
						$victoriasBando = $arrRanking[$i][3];

						$bando = $bando . " (" . $victoriasBando . "-0)" ;
						printf("<tr><td class=\"align-center\">" . $arrRanking[$i][0] . "</td><td class=\"align-left\">" . $arrRanking[$i][1] . "</td><td>" . $bando . "</td></tr>");
					}


					/* sector 3 - MINSK */
					if ($arrRanking[$i][1] == 'SECTOR 3' && array_key_exists($i+1, $arrRanking)  && $arrRanking[$i+1][1] == 'SECTOR 3' ){
						$bando = $arrRanking[$i ][2];
						$victoriasBando = $arrRanking[$i][3];

						

						if ($arrRanking[$i+1][1] == 'SECTOR 3'){
							$bando2 = $arrRanking[$i+1][2];
							$victoriasBando2 = $arrRanking[$i+1][3];

							$bando = ($victoriasBando = $victoriasBando2)? "EMPATE" . " (" . $victoriasBando . "-" .$victoriasBando2 . ")" : 
								(($victoriasBando > $victoriasBando2)? 
									$bando . " (" . $victoriasBando . "-" .$victoriasBando2 . ")" : $bando2. " (" . $victoriasBando2 . "-" .$victoriasBando . ")");
						}else{
							$bando = $bando . " (" . $victoriasBando . "-0)" ;
						}

						printf("<tr><td class=\"align-center\">" . $arrRanking[$i][0] . "</td><td class=\"align-left\">" . $arrRanking[$i][1] . "</td><td>" . $bando . "</td></tr>");


					}else if ($arrRanking[$i][1] == 'SECTOR 3' && $arrRanking[$i-1][1] != 'SECTOR 3'){
						$bando = $arrRanking[$i ][2];
						$victoriasBando = $arrRanking[$i][3];

						$bando = $bando . " (" . $victoriasBando . "-0)" ;
						printf("<tr><td class=\"align-center\">" . $arrRanking[$i][0] . "</td><td class=\"align-left\">" . $arrRanking[$i][1] . "</td><td>" . $bando . "</td></tr>");
					}


					if (COUNT($arrRanking) > $i+1 && $arrRanking[$i+1][0] != $arrRanking[$i][0] ){
						printf("<tr ><td colspan=\"4\" class=\"barra-horizontal\"></td></tr>");
					}

			
			}
			}else{
				printf("<tr><td colspan=\"3\">No hay resultados</td></tr>");
			}

			printf("</table>");
		?>
	</div>

	<?php }
	} ?>

</div>
<!-- Script para el menú responsive -->
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

	$(function(){
		$(document).on('click', 'a[id^=form-deportividad]', function() { 
			var Keyword = $(this).attr('name');
			$("#" + Keyword).submit();
		});

		$( "#fIdLiga" ).change(function() {
			$("#selectLiga").submit();
		});
	});
</script>
</body>
</html>