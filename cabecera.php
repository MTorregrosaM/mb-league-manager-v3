<?php
    
    session_start();
    if (($_SESSION["autorizado"] ?? 0) != 1){
        echo "<script>window.top.location='login.php'</script>";
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
    <meta name="viewport" content="width=device-width" />
    <!-- js -->
    <script type="text/javascript" src="recursos/js/jquery-1.11.2.min.js"></script>
    <script type="text/javascript" src="recursos/js/jquery.form-validator.min.js"></script>
    <script type="text/javascript" src="recursos/js/jquery-spanish.js"></script>
    <script type="text/javascript" src="recursos/js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="recursos/js/jquery.raty.js"></script>   
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script type="text/javascript" src="recursos/js/fullcalendar.min.js"></script>
    <script type="text/javascript" src="recursos/js/jquery.qtip.min.js"></script>
    <script type="text/javascript" src="recursos/js/lang/es.js"></script>
    <script type="text/javascript" src="recursos/js/responsive-nav.js"></script>

    <!-- css  -->
    <link rel="stylesheet" href="recursos/css/estilos.css" type="text/css" media="screen, projection"/>
    <link rel="stylesheet" type="text/css" href="recursos/js/css/jquery-ui.min.css" />
    <link rel="stylesheet" type="text/css" href="recursos/js/css/jquery.raty.css" />
    <link rel="stylesheet" type="text/css" href="recursos/css/style-drag.css" />
    <link href="https://fonts.googleapis.com/css?family=Fjalla+One|Open+Sans|Oswald|PT+Serif" rel="stylesheet">


    <!-- calendario -->
    <link rel='stylesheet'  type="text/css" href="recursos/js/css/fullcalendar.css" />
    <link rel='stylesheet'  type="text/css" href="recursos/js/css/jquery.qtip.css" />
        
    <link rel="icon" href="http://modelbrush.com/wp-content/uploads/2014/12/favicon1-548ef461_site_icon-32x32.png" sizes="32x32" />
    <link rel="icon" href="http://modelbrush.com/wp-content/uploads/2014/12/favicon1-548ef461_site_icon-256x256.png" sizes="192x192" />

    <meta http-equiv="Content-type" content="text/html; charset=UTF-8" />