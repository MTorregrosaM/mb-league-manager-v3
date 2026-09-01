<?php

	try {
		session_start();
		

		require_once ("model/class.php");
		require_once ("config/config.php");
		require_once ("controller/controller.php");

		/* variables */
		$oControllerUsuario = new controllerUsuario();

		$user = (isset( $_POST["user"]))? $_POST["user"] : null;
		$pass = (isset( $_POST["pass"]))? md5($_POST["pass"]) : null;
		
		$msjError = "";
		
		$idUsuario = 0;

		if(isset($_POST["validator"]) && $_POST["validator"] == "s"  ) {
			$idUsuario = $oControllerUsuario->loginUsuario ($user, $pass);
		
			if( $idUsuario > 0 ){
				session_regenerate_id(true);
				$_SESSION["autorizado"]=1;
				$_SESSION["usuario"]=$idUsuario;
				$oControllerUsuario->registroUltimoAcceso( $idUsuario );
				$oUsuarioLogin = $oControllerUsuario->RecuperarDatosUsuario($idUsuario);
				$_SESSION["rol"]=$oUsuarioLogin->rol;
				header("Location: index.php");			
			}else{
				$_SESSION["autorizado"]=0;
				$_SESSION["usuario"] = 0;
				$_SESSION["rol"] = "";
				$msjError = "<div id=\"box-error\">Usuario incorrecto</div>";
			}
		}

	}catch(Exception $e){
		return null;	 
	}

?>

<head>
	<!-- js -->
	<script type="text/javascript" src="recursos/js/jquery-1.11.2.min.js"></script>
	<script type="text/javascript" src="recursos/js/jquery.form-validator.min.js"></script>
	<script type="text/javascript" src="recursos/js/jquery-spanish.js"></script>
	<script type="text/javascript" src="recursos/js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="recursos/js/jquery.raty.js"></script>	
	<script type="text/javascript" src="recursos/js/fullcalendar.min.js"></script>
	<script type="text/javascript" src="recursos/js/jquery.qtip.min.js"></script>
	<script type="text/javascript" src="recursos/js/lang/es.js"></script>
	<script type="text/javascript" src="recursos/js/moment.min.js"></script>

	<!-- css  -->
	<link rel="stylesheet" href="recursos/css/estilos.css" type="text/css" media="screen, projection"/>
	<link rel="stylesheet" type="text/css" href="recursos/js/css/jquery-ui.min.css" />
	<link rel="stylesheet" type="text/css" href="recursos/js/css/jquery.raty.css" />
	<link rel="stylesheet" type="text/css" href="recursos/css/style-drag.css" />

	<!-- calendario -->
	<link rel='stylesheet'  type="text/css" href="recursos/js/css/fullcalendar.css" />
	<link rel='stylesheet'  type="text/css" href="recursos/js/css/jquery.qtip.css" />
		
	<link rel="icon" href="http://modelbrush.com/wp-content/uploads/2014/12/favicon1-548ef461_site_icon-32x32.png" sizes="32x32" />
	<link rel="icon" href="http://modelbrush.com/wp-content/uploads/2014/12/favicon1-548ef461_site_icon-256x256.png" sizes="192x192" />

</head>

<div id="boxlogomodelbrush"><img class="logomodelbrush" src="images/LOGO_LIGAS.png" alt="Ligas" width="485px"/></div>
<div id="login">
	 
	 <?php printf($msjError); ?>
	 <fieldset>
        <form name="login" action="" method="post">
            <label for="user">Usuario: </label><input type="text" name="user" size="10" value=""/>
            <input type="hidden" name="validator" value="s"/>
            <label for="pass">Contrase&ntilde;a: </label><input type="password" name="pass" size="10" value=""/>
            <input type="submit" value="Entrar en el panel" class="button"/>
        </form>
    </fieldset>

</div>