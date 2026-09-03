<?php
    require_once __DIR__ . "/config/error-handler.php";
    require_once __DIR__ . "/config/security.php";

    register_shutdown_function(function () {
        $ultimoError = error_get_last();
        $erroresFatales = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR);
        if (is_array($ultimoError) && in_array($ultimoError['type'], $erroresFatales, true)) {
            $oLog = Log::getInstance();
            $oLog->trazaLog(null, 'ERROR FATAL PHP: ' . $ultimoError['message'] . ' en ' . $ultimoError['file'] . ':' . $ultimoError['line']);
        }
        require_once __DIR__ . "/footer.php";
    });

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }


    if ( isset($_POST["fIdLiga"]) ) {
         $_SESSION["fIdLiga"] =  $_POST["fIdLiga"];
          $fIdLiga = $_POST["fIdLiga"];
        
    }else if( isset( $_SESSION["fIdLiga"]) ) {
         $fIdLiga = $_SESSION["fIdLiga"];
    }else{

         $fIdLiga = 0;
    }


?>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- js -->
    <script type="text/javascript" src="assets/js/jquery-1.11.2.min.js"></script>
    <script type="text/javascript" src="assets/js/jquery.form-validator.min.js"></script>
    <script type="text/javascript" src="assets/js/jquery-spanish.js"></script>
    <script type="text/javascript" src="assets/js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="assets/js/jquery.raty.js"></script>
    <script type="text/javascript" src="assets/js/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script type="text/javascript" src="assets/js/fullcalendar.min.js"></script>
    <script type="text/javascript" src="assets/js/jquery.qtip.min.js"></script>
    <script type="text/javascript" src="assets/js/lang/es.js"></script>
    <script type="text/javascript" src="assets/js/responsive-nav.js"></script>
    <script type="text/javascript" src="assets/js/menu-acciones.js?v=20260905"></script>
        <?php if (function_exists('csrfToken') || function_exists('csrfTokenPublico')): ?>
        <script>
            window.csrfToken = <?php echo json_encode(function_exists('csrfToken') ? csrfToken() : csrfTokenPublico(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
            $(function () {
                $('form[method="POST"], form[method="post"]').each(function () {
                    if (!this.querySelector('input[name="csrf_token"]')) {
                        $('<input>', { type: 'hidden', name: 'csrf_token', value: window.csrfToken }).appendTo(this);
                    }
                });
            });
            $.ajaxPrefilter(function (options, originalOptions, jqXHR) {
                if ((options.type || 'GET').toUpperCase() === 'POST') {
                    jqXHR.setRequestHeader('X-CSRF-Token', window.csrfToken);
                }
            });
        </script>
        <?php endif; ?>
    <script>window.responsiveNav = function () { return null; };</script>

    <!-- css  -->
    <link rel="stylesheet" href="assets/css/estilos.css?v=20260913" type="text/css" media="screen, projection"/>
    <link rel="stylesheet" type="text/css" href="assets/js/css/jquery-ui.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/js/css/jquery.raty.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/style-drag.css" />
    <link href="https://fonts.googleapis.com/css2?family=Lekton:wght@400;700&family=VT323&display=swap" rel="stylesheet">


    <!-- calendario -->
    <link rel='stylesheet'  type="text/css" href="assets/js/css/fullcalendar.css" />
    <link rel='stylesheet'  type="text/css" href="assets/js/css/jquery.qtip.css" />
        
    <link rel="icon" href="http://modelbrush.com/wp-content/uploads/2014/12/favicon1-548ef461_site_icon-32x32.png" sizes="32x32" />
    <link rel="icon" href="http://modelbrush.com/wp-content/uploads/2014/12/favicon1-548ef461_site_icon-256x256.png" sizes="192x192" />

    <meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>