  <?php require_once __DIR__ . "/config/auth.php"; exigirAdministrador(); ?>
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
      $oControllerUsuario = new controllerUsuario();

      $paginaActiva = "gestion-ligas-usuario.php";
      $grid = "";
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
      $accionForm = (isset($_POST["accionForm"]))? $_POST["accionForm"] : 0;
      $fIdUsuario = (isset( $_POST["fIdUsuario"]))? $_POST["fIdUsuario"] : "";
      $fIdsLigasUsuario = (isset($_POST["fIdsLigasUsuario"]))? $_POST["fIdsLigasUsuario"] : "";      
		
	  // usuario		
      $oUsuarioForm =  $oControllerUsuario->recuperarDatosUsuario ($fIdUsuario);

      // CHECKBOXES DE LIGAS
      $arrLigas =  $oControllerUsuario->recuperarSelectLigasUsuario($fIdUsuario);



		// ACTUALIZAMOS PERMISOS
    if ( $accionForm == 1 && isset($_POST["guardarPermisos"]) ){
			$comprobarMod = $oControllerUsuario->modificarPermisosUsuario($fIdUsuario, $fIdsLigasUsuario);
      $mensajeAltaMod .= "<div id=\"" . (($comprobarMod == 1) ? "mensaje-ok" : "mensaje-error") . "\">" .
        (($comprobarMod == 1) ? "Permisos actualizados correctamente." : "Se ha producido un error al actualizar los permisos.") .
        "</div>";
      		$arrLigas =  $oControllerUsuario->recuperarSelectLigasUsuario($fIdUsuario);
			//echo $ligasUsuario;
		}
    $arrLigas = is_array($arrLigas) ? $arrLigas : array();



  ?>


  <div id="contenedor-principal">
    <?php require_once("menu.php"); ?>
    <h2 class="h2"><span>Gesti&oacute;n de permisos de Usuarios sobre Ligas</span></h2>
    
    <div id="form">
      <h3>Gestión de permisos</h3>
      <form name="altaModFase" id="altaModFase" method="POST" action="" >
      
        <?php printf ($mensajeAltaMod);  ?>   
		<input type="hidden" name="accionForm" id="accionForm" value="1"/>
    <input type="hidden" name="guardarPermisos" id="guardarPermisos" value="1"/>
        <input type="hidden" name="fIdUsuario" id="fIdUsuario" value="<?php printf($fIdUsuario);?>"/>
        
        <p>Seleccione qué Ligas puede gestionar el usuario <strong><?php echo $oUsuarioForm->nick; ?></strong></p>
        <div class="ligas-permisos-grid">
        <?php

              if (count($arrLigas) >= 1 ){
                foreach ($arrLigas as $fila){
                  printf("<label class=\"checkbox-liga\"><input type=\"checkbox\" name=\"fIdsLigasUsuario[]\" value=\"". $fila[0] . "\" class=\"checkbox-form\"");
                   if ((int) $fila[2] === 1) printf("checked ");
                  printf(">" . htmlspecialchars($fila[1], ENT_QUOTES, "UTF-8") . "</label>");
                }
              }

        ?>
        </div>
   
        
        <p><input type="submit" value="Actualizar permisos" id="formButton" class="submit-button"/></p>
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




        <br/>
        <div id="div-volver"><a href="gestion-usuarios.php" class="btn-volver">Volver</a></div>


  </div>

<?php


  }catch(Exception $e){
    $oLog = Log::getInstance();
    $oLog->trazaLog ($e, "gestion-fases.php");  
    return null;   
  }

?>




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

  </body>