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
    $oControllerUsuario = new controllerUsuario();

    $paginaActiva = "gestion-usuario.php";
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


    $selectLigas = "<option value=''></option>\n";

    // variables POST
    $accionForm = (isset($_POST["accionForm"]))? $_POST["accionForm"] : 1;   
    $fIdUsuario = (isset( $_POST["fIdUsuario"]))? $_POST["fIdUsuario"] : "";
    $fNick = (isset($_POST["fNick"]))? $_POST["fNick"] : "";
    $fPass = (isset($_POST["fPass"]))? $_POST["fPass"] : "";
    $fRol = (isset($_POST["fRol"]))? $_POST["fRol"] : "";
    
      
    $pagActual = (isset($_POST["pagActual"]))? $_POST["pagActual"] : 1;
    $fIdUsuarioBorrar = (isset($_POST["fIdUsuarioBorrar"]))? $_POST["fIdUsuarioBorrar"] : "";
    $fIdUsuarioEditar = (isset($_POST["fIdUsuarioEditar"]))? $_POST["fIdUsuarioEditar"] : "";


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
      $comprobarBorrado = $oControllerUsuario->borrarUsuario( $fIdUsuarioBorrar );
  
      $mensajeBorrado .= "<div id=\"". (($comprobarBorrado)? "mensaje-ok" : "mensaje-error") ."\">". (($comprobarBorrado)? "Usuario eliminado correctamente." : "Se ha producido un error en su solicitud.") ."</div>";
    
    }

    /********************************/
    /* 1. BUSCADOR */
    /********************************/
    if ($accionForm != 2 && $accionForm != 4) {

      /********************************/
      /* PAGINADOR */
      /********************************/
      $numRegs = $oControllerUsuario->paginadorUsuarios (  $fNick );
      $numPags = ceil( $numRegs / 10) ;
      require_once("paginador.inc");

      

      /********************************/
      /* GRID DATOS */
      /********************************/
      /* SI SE ENVIA EL FORMULARIO DE BÚSQUEDA, MANDAMOS PARÁMETROS PARA FILTRAR */
      if (isset ($_POST["accionForm"]) && ($_POST["accionForm"] == 1 || $_POST["accionForm"] == 3)){    
        $arrUsuarios = $oControllerUsuario->recuperarListadoUsuarios (  $fNick, $fRol , ($pagActual-1));    
      }else{
        $arrUsuarios = $oControllerUsuario->recuperarListadoUsuarios(  null, null, 0);
      }
      

      // comprobamos que haya datos 
      if (is_array($arrUsuarios) && count($arrUsuarios) >= 1){
        $grid  = "<table class=\"table-4\">\n
              <tr>
              <th>Nick</th>
              <th>Rol</th>
              <th>Último acceso</th>
              <th class=\"td-acciones\"></th>
              </tr>\n";

        foreach($arrUsuarios as $fila){
          $grid .="\n<tr><td>" . $fila[1] . "</td><td>" .  $fila[3]  . "</td><td>" . $fila[4] . "</td>";
          $grid .= "<td class=\"align-center td-acciones\">";

          
          $grid .= "  <form name=\"form-ligas-".$fila[0]."\" id=\"form-ligas-".$fila[0]."\" method=\"POST\" class=\"form-btn-acciones\" action=\"gestion-ligas-usuario.php\">
                <input type=\"hidden\" name=\"fIdUsuario\" id=\"fIdUsuario\" value=\"".$fila[0]."\"/>
                <input type=\"hidden\" name=\"accionForm\" id=\"accionForm\" value=\"1\"/>

                <img src=\"recursos/img/icon_listas.png\" title=\"Ver ligas que gestiona el usuario\" alt=\"form-ligas-".$fila[0]."\" class=\"btn-ligas-usuario\"/>                
              </form>";
          
          $grid .= "  <form name=\"form-borrar-".$fila[0]."\" id=\"form-borrar-".$fila[0]."\" method=\"POST\" class=\"form-btn-acciones\">
                <input type=\"hidden\" name=\"fIdUsuarioBorrar\" id=\"fIdUsuarioBorrar\" value=\"".$fila[0]."\"/>
                <input type=\"hidden\" name=\"accionForm\" id=\"accionForm\" value=\"3\"/>
                <input type=\"hidden\" name=\"pagActual\" id=\"pagActual\" value=\"". $pagActual ."\" />
                <img src=\"recursos/img/icon_eliminar.png\" title=\"Eliminar usuario\" alt=\"form-borrar-".$fila[0]."\" class=\"btn-borrar\"/>                
              </form>";
          
          $grid .= " <form name=\"form-editar-".$fila[0]."\" id=\"form-editar-".$fila[0]."\" method=\"POST\" class=\"form-btn-acciones\">
                <input type=\"hidden\" name=\"accionForm\" id=\"accionForm\" value=\"4\"/>
                <input type=\"hidden\" name=\"fIdUsuario\" id=\"fIdUsuario\" value=\"".$fila[0]."\"/>
                <input type=\"hidden\" name=\"fNick\" id=\"fNick\" value=\"". $fila[1] ."\" />
                <input type=\"hidden\" name=\"fPass\" id=\"fPass\" value=\"". $fila[2] ."\" />
                <input type=\"hidden\" name=\"fRol\" id=\"fRol\" value=\"". $fila[3] ."\" />
               
                <img src=\"recursos/img/icon_editar.png\" title=\"Editar usuario\" alt=\"form-editar-".$fila[0]."\"  class=\"btn-editar-reg\"/>
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
      $txtAltaModBoton = "Dar de alta nuevo usuario";
      $txtAltaModH3 = "Alta de nuevo usuario";

      // grabamos nuevo registro en caso de que se haya enviado el formulario
      if (count($_POST) > 2){
        $comprobarAltaMod = $oControllerUsuario->altaNuevoUsuario( $fNick, $fPass, $fRol  );        

     
        /*  1. OK
          2. ERROR
          3. AVISO 
        */ 

        $mensajeAltaMod .= "<div id=\"". (($comprobarAltaMod == 1)? "mensaje-ok" : "mensaje-error") ."\">". 
                  ( ($comprobarAltaMod == 1)? "Usuario creado correctamente. <a href=\"#\"  class=\"btnVolver\">Volver</a>" : 
                     "Se ha producido un error en su solicitud.")  ."</div>";
      }


    /********************************/
    /* 3. EDITAR OBJETO */
    /********************************/      
    }else if ( $accionForm == 4  ){
      $txtAltaModBoton = "Modificar datos de usuario";
      $txtAltaModH3 = "Modificar usuario";


      if ($fIdUsuarioEditar != null){
        $comprobarAltaMod = $oControllerUsuario->modificarDatosUsuario( $fIdUsuario, $fNick, $fPass, $fRol );



        /*  1. OK
          2. ERROR
          3. AVISO 
          4. ERROR DUPLICADO
        */ 
      
        $mensajeAltaMod .= "<div id=\"". ( ($comprobarAltaMod == 1)? "mensaje-ok" : (($comprobarAltaMod == 3)? "mensaje-aviso" : "mensaje-error")) ."\">". 
                ( ($comprobarAltaMod == 1)? "Usuario modificado correctamente.  <a href=\"#\"  class=\"btnVolver\">Volver</a>" : 
                  (($comprobarAltaMod == 2)? "Se ha producido un error en su solicitud." : "AVISO: debe modificar al menos un campo.") ) ."</div>";


      }

    }


  }catch(Exception $e){
    $oLog = Log::getInstance();
    $oLog->trazaLog ($e, "gestion-usuarios.php");  
    return null;   
  }

?>








<script>
  $(function(){
    /*PAGINADOR - ASIGNAMOS NUM DE PÁGINA AL HIDDEN DEL FORMULARIO */
    $(".btn-paginador").click( function() {
      var pagActual = $(this).attr('id')
      $("#pagActual").attr("value", pagActual);
      $("#buscadorUsuarios").submit();
    }); 

    // boton ALTA registro
    $("#btnAltaCliente").click( function() {
      $("#btnFormAltaUsuario").submit();
    }); 

    // boton de volver
    $(".btnVolver").click( function() {
      $("#btnFormVolver").submit();
    }); 


    // borrar registro
    $(".btn-borrar").click( function() {  
      var formularioBorrar = $(this).attr('alt');
    
     
      $('#dialog-modal').dialog({
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
    $(".btn-ligas-usuario").click( function() {
      var formularioLigasUsuario = $(this).attr('alt');
      $("#" + formularioLigasUsuario).submit();   
    
    });

  });
</script>


<div id="dialog-modal" title="Confirmaci&oacute;n" style="display: none;">&iquest;Est&aacute; seguro de realizar esta acci&oacute;n?</div>



<div id="contenedor-principal">
  <?php require_once("menu.php"); ?>
  
  <h2 class="h2"><span>Gesti&oacute;n de Usuarios</span></h2>


  <?php /* MENSAJE ELIMINADO */ printf ($mensajeBorrado);   ?>



  <?php /* ALTA DE NUEVA  / MODIFICACION */ 
       if ($accionForm == 2 || $accionForm == 4) {

        
  ?>
  
  <div id="form">
    <h3><?php printf($txtAltaModH3);?></h3>
    <form name="btnAltaUsuario" id="btnAltaUsuario" method="POST" action="" >
    
      <?php printf ($mensajeAltaMod);  ?>
      <input type="hidden" name="accionForm" id="accionForm" value="<?php printf($accionForm);?>"/>
      <?php if ($accionForm == 4){ ?> <input type="hidden" name="fIdUsuarioEditar" id="fIdUsuarioEditar" value="1"/>  <?php } ?>
      <input type="hidden" name="fIdUsuario" id="fIdUsuario" value="<?php printf($fIdUsuario);?>"/>   
      <p><label for="fNick">Nick: </label>  <input type="text" name="fNick" maxlength="35"  id="fNick" data-validation="required " value="<?php printf($fNick);?>"  ></p>
      <p><label for="fPass">Contraseña: </label>  <input type="text" name="fPass" maxlength="35"  id="fPass" value="<?php printf($fPass);?>"  ></p>
      <p><label for="fRol">Rol: </label>  
       <select name="fRol" id="fRol">
            <option value=""></option>
            <option value="ADMIN" <?php printf( ( ($fRol == "ADMIN")? 'selected': '' ) ); ?>>ADMIN</option>
            <option value="USER" <?php printf( ( ($fRol == "USER")? 'selected': '' ) ); ?>>USER</option>
          </select> </p>     
      <p><input type="submit" value="<?php printf($txtAltaModBoton);?>" id="formButton" class="submit-button"/></p>
    </form>
  
  </div>

  <form name="btnFormVolver" id="btnFormVolver" method="POST" action="">
      <input type="hidden" name="fIdLiga" id="fIdLiga" value="<?php printf($fIdLiga);?>"/>
  </form>



  <?php 
    /* BOTON VOLVER */    
    printf("<div id=\"div-volver\"><a href=\"#\" class=\"btn-volver btnVolver\">Volver</a></div>"); 

    }else{ 


      /* BUSCADOR */ ?>
      <p>Desde este panel puede dar de alta, modificar o eliminar cualquier usuario de la aplicaci&oacute;n.</p>
      
      <div id="buscador">
        <form name="buscadorUsuarios" id="buscadorUsuarios" method="POST" action="">
          <input type="hidden" name="accionForm" id="accionForm" value="1"/>
          <input type="hidden" name="pagActual" id="pagActual" value="1" />
          <label for="fNick">Nick: </label>   <input type="text" name="fNick" id="fNick" value="<?php printf($fNick);?>"  />     
          <label for="fRol">Rol: </label>   
          <select name="fRol" id="fRol">
            <option value=""></option>
            <option value="ADMIN" <?php printf( ( ($fRol == "ADMIN")? 'selected': '' ) ); ?>>ADMIN</option>
            <option value="USER" <?php printf( ( ($fRol == "USER")? 'selected': '' ) ); ?>>USER</option>
          </select>        
          <input type="submit" value="Buscar" id="formButton" class="submit-button"/>
        </form>
      </div>


      <div id="btn-alta">
        <form name="btnFormAltaUsuario" id="btnFormAltaUsuario" method="POST" action="">
          <input type="hidden" name="accionForm" id="accionForm" value="2"/>
          <a href="#" class="button" id="btnAltaCliente"> <img src="recursos/img/icon_nuevo.png" alt="Nuevo"/> Alta de nuevo usuario</a>
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