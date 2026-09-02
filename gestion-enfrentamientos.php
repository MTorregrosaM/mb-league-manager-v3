<?php
    require_once __DIR__ . "/config/auth.php";
?>
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
        $oControllerEnfrentamiento = new controllerEnfrentamiento();
        $oControllerFase = new controllerFase();

        $paginaActiva = "gestion-enfrentamientos.php";
        $grid = "";
        $comprobarBorrado = false;
        $mensajeBorrado = "";
        $comprobarAltaMod = false;
        $mensajeAltaMod = "";
        $comprobarMod = false;
        $mensajeMod = "";

        $fIdLiga = (isset( $_POST["fIdLiga"]))? $_POST["fIdLiga"] : (isset($_SESSION["fIdLiga"])? $_SESSION["fIdLiga"] : "");
        $accionForm = (isset( $_POST["accionForm"]))? $_POST["accionForm"] : "";
        $fNumFase = (isset( $_POST["fNumFase"]))? $_POST["fNumFase"] : 0;
        $fNumRonda = (isset( $_POST["fNumRonda"]))? $_POST["fNumRonda"] : 0;

        $selectLigas = "<option value=''></option>\n";
        $poolJugadores = "";
        $poolEnfrentamientos = "";
        $numVentanasEnfrentamiento = 0;
         
        // USUARIO
        $oControllerUsuario = new controllerUsuario();
        $ligasUsuario = $oControllerUsuario->recuperarLigasUsuario( $_SESSION["usuario"] ?? 0 );
    
        // options para los select de los formularios
        // LIGAS
        $arrLigas =  $oControllerLiga->recuperarSelectLigas(null, false,  $ligasUsuario );
        $selectLigasSelected = ($fIdLiga != null ) ? $fIdLiga : 0;

        if (is_array($arrLigas) && count($arrLigas) >= 1 ){
            foreach ($arrLigas as $fila){
                $selectLigas .= "\n<option value=\"" . $fila[0] . "\" ". (($selectLigasSelected == $fila[0] && $selectLigasSelected > 0)? "selected" : "") ." >" .$fila[1]  . "</option>";
            }
        }

        if ($fIdLiga != null){

            
            // options para los select de los formularios
            // FASES
            $selectFases = "";
            $arrFases =  $oControllerLiga->recuperarSelectFases( $fIdLiga );
            $selectFasesSelected = ($fNumFase != null ) ? $fNumFase : 0;

            if ($arrFases != null && count($arrFases) >= 1 ){
                foreach ($arrFases as $fila){
                    $selectFases .= "\n<option value=\"" . $fila[0] . "\" ". (($selectFasesSelected == $fila[0] && $selectFasesSelected > 0)? "selected" : "") ." >" .$fila[0]  . "</option>";
                }
            }

            // options para los select de los formularios
            // RONDAS
            $arrRondas =  $oControllerLiga->recuperarSelectRondas( $fIdLiga );
            $selectRondasSelected = ($fNumRonda != null ) ? $fNumRonda : 0;
            $selectRondas = "";

            if ($arrRondas != null && count($arrRondas) >= 1 ){
                foreach ($arrRondas as $fila){
                    $selectRondas .= "\n<option value=\"" . $fila[0] . "\" ". (($selectRondasSelected == $fila[0] && $selectRondasSelected > 0)? "selected" : "") ." >" .$fila[0]  . "</option>";
                }
            }   

            
 
            // pintamos jugadores para el pool del evento
			$arrJugadores = null;
            $arrJugadores = $oControllerJugador->recuperarListadoJugadores($fIdLiga, null, null, null, 0, 1000, true, $fNumFase, $fNumRonda);
			$numJugadores = 0;
 	
            if (!empty($arrJugadores) )  {// != null ){
              
                $numJugadores = count($arrJugadores);   
                foreach ($arrJugadores as $fila){
                    if ($fila[2] != 'zMercenario'){
                        $poolJugadores .= "<li class=\"sortable-item ". $fila[9] . "\" id=\"" . $fila[0] . "\">" . $fila[2] . "</li>\n";
                    }else{
                        $numJugadores --;
                    }
                }
            } 
            // calculamos el numero de ventanas de enfrentamiento
    
            $numVentanasEnfrentamiento = round($numJugadores/2);

   

            // primero sacamos un array con los cruces
			 $arrEnfrentamientos  = null;
            $arrEnfrentamientos = $oControllerEnfrentamiento->recuperarListadoEnfrentamientos($fIdLiga, $fNumFase, $fNumRonda);

            if ($arrEnfrentamientos != null && count($arrEnfrentamientos) >= 1 ){
                foreach ($arrEnfrentamientos as $fila){
                    $poolEnfrentamientos .= "<div class=\"column left\">\n<ul class=\"sortable-list ventana-enfrentamiento\">\n";
                    $poolEnfrentamientos .= "<li class=\"sortable-item ". $fila[3] . "\" id=\"" . $fila[1] . "\">" . $fila[2] . "</li>\n";
                    $poolEnfrentamientos .= "<li class=\"sortable-item ". $fila[6] . "\" id=\"" . $fila[4] . "\">" . $fila[5] . "</li>\n";
                    $poolEnfrentamientos .= "</ul>\n</div>\n";
                }
            }

            // terminamos de pintar los enfrentamientos disponibles
            for ($i=1;$i<= $numVentanasEnfrentamiento ; $i++){
                $poolEnfrentamientos .= "<div class=\"column left\">\n<ul class=\"sortable-list ventana-enfrentamiento\">\n</ul>\n</div>\n";
            }
        }

    }catch(Exception $e){
        $oLog = Log::getInstance();
        $oLog->trazaLog ($e, "gestion-ligas.php");  
        return null;     
    }

?>






<div id="contenedor-principal">
    <?php require_once("menu.php"); ?>
    <h2 class="h2"><span>Gesti&oacute;n de Enfrentamientos</span></h2>
    
    <?php if ($fIdLiga == null) { ?>
            <p><b>No se ha seleccionado ninguna liga:</b></p>
            <div class="center"><form id="selectLiga" name="selectLiga" method="POST"><label for="fIdLiga" class="span-index">Liga  </label> <select name="fIdLiga" id="fIdLiga" class="select-index" ><?php printf($selectLigas); ?> </select></form></div>

    <?php }else{ ?>
        <p>Gesti&oacute;n manual de enfrentamientos.<p>
        <div id="buscador">
            <form name="buscadorJugadores" id="buscadorJugadores" method="POST" action="">
                <input type="hidden" name="accionForm" id="accionForm" value="1"/>
                <label for="fIdLiga">Liga: </label> <select name="fIdLiga" id="fIdLiga" data-validation="required " ><?php printf($selectLigas); ?> </select>   
                <label for="fNumFase">Fase: </label> <span id="selectFases"><select name="fNumFase" id="fNumFase" data-validation="required" ><?php printf($selectFases); ?></select></span>
                <label for="fNumRonda">Ronda: </label> <span id="selectRondas"><select name="fNumRonda" id="fNumRonda" data-validation="required" ><?php printf($selectRondas); ?></select></span>
                <input type="submit" value="Seleccionar evento" id="formButton" class="submit-button"/> 

            </form>
        </div>

        <?php if($accionForm > 0){ ?>
            <div class="enfrentamientos-actions">
                <a href="#" class="btn-emparejar" id="btn-generar-emparejamientos">Generar emparejamientos</a>
                <a href="#" class="btn-grabar" id="btn-grabar">Grabar enfrentamientos</a>
            </div>
        <?php } ?>

        <?php if($accionForm > 0 && $fIdLiga != null){ ?>
            
            <!-- TABLA DRAG & DROP -->
            <div id="div-enfrentamientos">

                <div class="column left first">
                <p class="titulo-columna-drag">Pool de jugadores</p>
                    <ul class="sortable-list maestra">
                        <?php printf($poolJugadores); ?>
                    </ul>

                </div>

                <div class="matches-grid">
                    <?php printf($poolEnfrentamientos); ?>
                </div>



            <div class="clearer">&nbsp;</div>
        <?php } ?>
                
    <?php } ?>


<script>

    // Get items
    function getItems()
    {
        var columns = [];
        var aux = 0;
        $('#div-enfrentamientos ul.ventana-enfrentamiento').each(function(){
            columns.push($(this).sortable('toArray').join('-'));            
            if ($(this).sortable('toArray').length != 2){           
                aux= 1;
            }
        });

        if(aux == 1){
            return null;
        }else{
            return columns.join('#');
        }
    }

    function generarEmparejamientosAleatorios()
    {
        var jugadores = [];
        $('#div-enfrentamientos .sortable-list').each(function(){
            $(this).children('li').each(function(){
                jugadores.push(this);
            });
        });

        if (jugadores.length % 2 !== 0){
            alert("No se puede generar el emparejamiento porque hay un numero impar de jugadores.");
            return;
        }

        for (var i = jugadores.length - 1; i > 0; i--){
            var posicionAleatoria = Math.floor(Math.random() * (i + 1));
            var jugador = jugadores[i];
            jugadores[i] = jugadores[posicionAleatoria];
            jugadores[posicionAleatoria] = jugador;
        }

        $('#div-enfrentamientos ul.ventana-enfrentamiento').empty();
        $('#div-enfrentamientos ul.ventana-enfrentamiento').each(function(indice){
            $(this).append(jugadores[indice * 2], jugadores[indice * 2 + 1]);
        });
    }

    $(document).ready(function(){

        // Example 1.3: Sortable and connectable lists with visual helper
        $('#div-enfrentamientos .sortable-list').sortable({
            connectWith: '#div-enfrentamientos .sortable-list',
            placeholder: 'placeholder',
            opacity: 0.8,
        });
    });

    
    /* ajax */
    $(function(){

        //  $("#fIdLiga").change(function(){ alert(9), actualizarSelectFases( $('#fIdLiga option:selected').val());
        $("#fIdLiga").change(function(){ 
             actualizarSelectFases( $('#fIdLiga option:selected').val(), <?php printf($fNumFase); ?>);
             actualizarSelectRondas( $('#fIdLiga option:selected').val(), <?php printf($fNumRonda); ?>);
        });

        $("#btn-generar-emparejamientos").click(function(evento){
            evento.preventDefault();
            generarEmparejamientosAleatorios();
        });

        $("#btn-grabar").click(function(){
            if (getItems() == null){
                alert("Todos los enfrentamientos deben tener dos jugadores.");  
        /*  }else if (getNumItems() != 2 ){//<?php printf($numVentanasEnfrentamiento); ?>){
                alert("Debes configurar todos los enfrentamientos.");*/
            }else{
                grabarEnfrentamientos($('#fIdLiga option:selected').val(), getItems(), <?php printf($numVentanasEnfrentamiento); ?>, $('#fNumFase option:selected').val(), $('#fNumRonda option:selected').val());
            }
        });

    }); 


    $(function(){

        $( "#fIdLiga" ).change(function() {
            $("#selectLiga").submit();
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
                            $("#selectFases").html("<span class=\"loading-select\"><img src=\"recursos/img/loading.gif\" alt=\"Cargando...\" /></span>");
                    },
                    success:  function (response) {
                            $("#selectFases").html(response);
                          //  bindAjaxSelectChange();
                    }
            });
    }

    // select de rondas
    function actualizarSelectRondas( fIdLiga, fNumRonda )   {

            var parametros = {
                    "fIdLiga" : fIdLiga,
                    "fNumRonda" : fNumRonda,
            };

            $.ajax({
                    async: true,
                    data:  parametros,
                    url:   'ajax/ajax.rondas.php',
                    type:  'post',
                    beforeSend: function () {
                            $("#selectRondas").html("<span class=\"loading-select\"><img src=\"recursos/img/loading.gif\" alt=\"Cargando...\" /></span>");
                    },
                    success:  function (response) {
                            $("#selectRondas").html(response);
                          //  bindAjaxSelectChange();
                    }
            });
    }

    // grabar datos
    function grabarEnfrentamientos( fIdLiga, cruces, numVentanasEnfrentamiento, fNumFase, fNumRonda  )  {


            var parametros = {
                    "fIdLiga" : fIdLiga,
                    "cruces" : cruces,
                    "numVentanasEnfrentamiento" : numVentanasEnfrentamiento,
                    "fNumFase" : fNumFase,
                    "fNumRonda" : fNumRonda,
            };

            $.ajax({
                    async: true,
                    data:  parametros,
                    url:   'ajax/ajax.cruces.php',
                    type:  'post',
                    beforeSend: function () {
                            $("#div-enfrentamientos").html("<span class=\"loading-select\"><img src=\"recursos/img/loading.gif\" alt=\"Cargando...\" /></span>");
                    },
                    success:  function (response) {
                            $("#div-enfrentamientos").html(response);
                          //  bindAjaxSelectChange();
                    }
            });
    }
</script>




    <br/>
    <div id="div-volver"><a href="index.php" class="btn-volver">Volver</a></div>


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
