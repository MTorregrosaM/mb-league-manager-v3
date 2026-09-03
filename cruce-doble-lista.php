<?php
		require_once __DIR__ . "/config/auth.php";
		require_once __DIR__ . "/config/mailer.php";
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
		$oControllerResultado = new controllerResultado();
		$oControllerLiga = new controllerLiga();
		$oControllerJugador = new controllerJugador();

		$datosLigaFOW = $oControllerLiga->recuperarDatosLigaDobleLista();
		if (!is_array($datosLigaFOW) || count($datosLigaFOW) < 3) {
			$datosLigaFOW = array(0, "", 0);
		}
		$fIdLiga = $datosLigaFOW[0];
		$fNombreLiga = $datosLigaFOW[1];
		$fNumFase = $datosLigaFOW[2];
		$selectJugadores = "";
		$fNumRonda = (isset( $_POST["fNumRonda"]))? $_POST["fNumRonda"] : 0;
		$fIdJugador1 = (isset( $_POST["fIdJugador1"]))? $_POST["fIdJugador1"] : 0;
		$fIdJugador2 = (isset( $_POST["fIdJugador2"]))? $_POST["fIdJugador2"] : 0;		
		$fIdJugador2Nick = (isset( $_POST["fIdJugador2Nick"]))? $_POST["fIdJugador2Nick"] : "";
		$tockenEnvio =  (isset( $_POST["tockenEnvio"]))? $_POST["tockenEnvio"] : 0;
		$_SESSION['tockenEnvio'] = (!isset($_SESSION['tockenEnvio']) || $_SESSION['tockenEnvio'] == "") ? 0 : $_SESSION['tockenEnvio'];
		$_SESSION['fIdJugador2']= (!isset($_SESSION['fIdJugador2']) || $_SESSION['fIdJugador2'] == "") ? 0 : $_SESSION['fIdJugador2'];
		$mensaje = "";

		// options para los select de los formularios
		// JUGADORES
		$arrJugadores =  $oControllerJugador->recuperarSelectJugadores( $fIdLiga,  $fNumFase, null, true, true ) ;

		$selectJugadoresSelected = ($fIdJugador1 != null ) ? $fIdJugador1 : 0;
		$selectJugadores = "";

		if (!empty($arrJugadores) && is_array($arrJugadores) ){
			foreach ($arrJugadores as $fila){
				$selectJugadores .= "\n<option value=\"" . $fila[0] . "\" ". (($selectJugadoresSelected == $fila[0] && $selectJugadoresSelected > 0)? "selected" : "") ." >" .$fila[1]  . "</option>";
			}
		}


		// GESTIONAMOS EL ENVIO

		if ($fIdJugador2 != 0 && ($_SESSION['tockenEnvio']  <> 3 || $_SESSION['fIdJugador2']  != $fIdJugador2) ){
		//if ($fIdJugador2 != 0  ){

			

			// NUEVO 2016 - COMPROBAMOS SI YA SE HA GRABADO

			$arrBandosAsignados = $oControllerResultado->validarBandoAsignado( $fIdLiga, $fNumFase, $fIdJugador1, $fIdJugador2 ) ;

			$hayDosBandosAsignados = is_array($arrBandosAsignados) && count($arrBandosAsignados) >= 2;
			if (!$hayDosBandosAsignados || $arrBandosAsignados[0][1] == 'DOBLE' || $arrBandosAsignados[1][1] == 'DOBLE'){

				$arrBandos = array ( 0 => "ALIADO", 1 => "EJE");

				$bandoRandom = rand(0,1);
				$bandoJug1 = $arrBandos[$bandoRandom];
				$bandoJug2 = ($bandoRandom == 0)? $arrBandos[1] : $arrBandos[0];
				$banderaBandoJug1 = ($bandoRandom == 0)? "axis_icon_grande.png" : "allies_icon_grande.png";
				$banderaBandoJug2 = ($bandoRandom == 0)? "allies_icon_grande.png" : "axis_icon_grande.png";

				// NUEVO 2016 - GRABAMOS EN BD EL BANDO ASIGNADO
				$oControllerResultado->grabarBandoAleatorio( $fIdLiga, $fNumFase, $fIdJugador1, $fIdJugador2, $bandoJug1, $bandoJug2 ) ;

			}else{
			 	$banderaBandoJug1 = ($arrBandosAsignados[0][1] == 'EJE')? "axis_icon_grande.png" : "allies_icon_grande.png";
				$banderaBandoJug2 = ($arrBandosAsignados[1][1] == 'ALIADO')? "allies_icon_grande.png" : "axis_icon_grande.png";

				$fIdJugador1 = $arrBandosAsignados[0][0];
				$fIdJugador2 = $arrBandosAsignados[1][0]; 
			}

 

			$jug1 = $oControllerJugador->recuperarDatosJugador( $fIdJugador1 );
			$jug2 = $oControllerJugador->recuperarDatosJugador( $fIdJugador2 );

			$_SESSION['tockenEnvio'] = 3;
			$_SESSION['fIdJugador2'] = $fIdJugador2;
			$body = "<p>Hola,</p><p>Se han generado aleatoriamente los bandos del resultado de la " . $fNumFase . "&ordf; fase de la <i>" . htmlspecialchars($fNombreLiga, ENT_QUOTES, 'UTF-8') . "</i>.</p>";
			$body .= "<p>" . htmlspecialchars($jug1->nick, ENT_QUOTES, 'UTF-8') . ": " . htmlspecialchars($bandoJug1, ENT_QUOTES, 'UTF-8') . "</p>";
			$body .= "<p>" . htmlspecialchars($jug2->nick, ENT_QUOTES, 'UTF-8') . ": " . htmlspecialchars($bandoJug2, ENT_QUOTES, 'UTF-8') . "</p>";
			$body .= "<p>Cualquier duda, pod&eacute;is contactar con <a href=\"mailto:hola@modelbrush.com\">hola@modelbrush.com</a>.</p>";
			enviarCorreoSeguro(
				array($jug1->email, $jug2->email),
				"Liga FoW GTS - Asignacion de bandos aleatoria",
				$body
			);

			$mensaje = "<div id=\"mensaje-ok\">Asiganci&oacute;n aleatoria realizada correctamente. Os hemos enviado un correo con el bando de cada jugador.</div>";
		}else{
			$mensaje = "<div id=\"mensaje-error\">Ya se ha enviado por correo el bando de cada jugador, comprueba tu correo.</div>";
		}

	}catch(Exception $e){
		$oLog = Log::getInstance();
			   $oLog->trazaLog ($e, "gestion-resultados.php");
		return null;	 
	}

?>
<div id="contenedor-principal">
	<?php require_once("menu.php"); ?>
	<div>
		<h2 class="h2"><span>Generar bando aleatorio para resultado con doble lista</span></h2>
	

			<div id="buscador">

			<?php if ($fIdJugador2 == 0) { ?>

				<form name="asignarBandos" id="asignarBandos" method="POST" action="" enctype="multipart/form-data">
					Selecciona tu nick y el jugador al que te enfrentas, y pulsa el bot&oacute;n <i>Generar bandos</i>:
					<p><label for="fIdLiga">Liga: </label> <select name="fIdLiga" id="fIdLiga" disabled ><option><?php printf($fNombreLiga); ?></option></select>

					<label for="fNumFase">Fase: </label> <span id="selectFases"><select name="fNumFase" id="fNumFase" disabled ><option><?php printf($fNumFase); ?></option></select></span>
					
					<label for="fIdJugador1">&iquest;Qui&eacute;n eres?: </label> <span id="selectJugadores"><select name="fIdJugador1" id="fIdJugador1" data-validation="required " ><option value=""></option><?php printf($selectJugadores); ?> </select></span>	

					<label for="fIdJugador2Nick">Tu contrincante: </label><span id="selectJugador2" name="selectJugador2"><input type="text" name="fIdJugador2Nick" id="fIdJugador2Nick" value="<?php printf($fIdJugador2Nick);?>" data-validation="required "  readonly /></span>
									
					<input type="submit" value="Generar bandos" id="formButton" class="submit-button"/> </p>
					</form>
					<script>
						$.validate( { 
						 		form : '#asignarBandos',
						 		decimalSeparator : ',',
						 		language : spanish,
						 		errorMessagePosition : 'top',
						 		validateOnBlur : false
						 	});	
					</script>
			<?php }else{ ?>
				<?php printf($mensaje);?> 
			<?php } ?>
			</div>
	</div>
	


<script>	

		$(function(){


				$("#fIdJugador1").change(function(){ 
					if ($('#fIdJugador1 option:selected').val() != null && $('#fIdJugador1 option:selected').val() != 0){
						actualizarSelectJugador2( <?php printf($fIdLiga); ?>, $('#fIdJugador1 option:selected').val(), <?php printf($fNumFase); ?>);
					}
					
				});
		}); 




	function actualizarSelectJugador2( fIdLiga, fIdJugador1, fNumFase)	{

	        var parametros = {
	                "fIdLiga" : fIdLiga,
	                "fIdJugador1" : fIdJugador1,
	                "fNumFase" : fNumFase,
	        };

	        $.ajax({
        			async: true,
	                data:  parametros,
	                url:   'ajax/ajax.jugador-contrincante-doble-lista.php',
	                type:  'post',
	              	beforeSend: function () {
	                        $("#selectJugador2").html("<div class=\"loading-select\"><img src=\"assets/img/loading.gif\" alt=\"Cargando...\" /></div>");
	                },
	                success:  function (response) {
	                        $("#selectJugador2").html(response);
							
	                }
	        });
		}
</script>
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
</script>