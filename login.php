<?php

	try {
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_set_cookie_params(array('httponly' => true, 'samesite' => 'Lax', 'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'));
			session_start();
		}
		if (empty($_SESSION['csrf_token'])) {
			$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
		}
		

		require_once ("model/class.php");
		require_once ("config/config.php");
		require_once ("controller/controller.php");

		/* variables */
		$oControllerUsuario = new controllerUsuario();

		$user = (isset( $_POST["user"]))? $_POST["user"] : null;
		$pass = (isset( $_POST["pass"]))? (string) $_POST["pass"] : null;
		
		$msjError = "";
		
		$idUsuario = 0;

		if(isset($_POST["validator"]) && $_POST["validator"] == "s"  ) {
			$tokenValido = isset($_POST['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string) $_POST['csrf_token']);
			$bloqueado = !empty($_SESSION['login_bloqueado_hasta']) && time() < $_SESSION['login_bloqueado_hasta'];
			$idUsuario = (!$tokenValido || $bloqueado) ? 0 : $oControllerUsuario->loginUsuario ($user, $pass);
		
			if( $idUsuario > 0 ){
				unset($_SESSION['login_intentos'], $_SESSION['login_bloqueado_hasta']);
				session_regenerate_id(true);
				$_SESSION["autorizado"]=1;
				$_SESSION["usuario"]=$idUsuario;
				$oControllerUsuario->registroUltimoAcceso( $idUsuario );
				$oUsuarioLogin = $oControllerUsuario->RecuperarDatosUsuario($idUsuario);
				$_SESSION["rol"]=$oUsuarioLogin->rol;
				header("Location: index.php");			
			}else{
				$_SESSION['login_intentos'] = (int) ($_SESSION['login_intentos'] ?? 0) + 1;
				if ($_SESSION['login_intentos'] >= 5) {
					$_SESSION['login_bloqueado_hasta'] = time() + 900;
				}
				$_SESSION["autorizado"]=0;
				$_SESSION["usuario"] = 0;
				$_SESSION["rol"] = "";
				$msjError = "<div id=\"box-error\" class=\"alert alert-danger\" role=\"alert\">Usuario incorrecto</div>";
			}
		}

	}catch(Exception $e){
		return null;	 
	}

?>

<html lang="es" data-bs-theme="dark">
<head>
	<!-- js -->
	<script type="text/javascript" src="recursos/js/jquery-1.11.2.min.js"></script>
	<script type="text/javascript" src="recursos/js/jquery.form-validator.min.js"></script>
	<script type="text/javascript" src="recursos/js/jquery-spanish.js"></script>
	<script type="text/javascript" src="recursos/js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="recursos/js/jquery.raty.js"></script>	
	<script type="text/javascript" src="recursos/js/moment.min.js"></script>
	<script type="text/javascript" src="recursos/js/fullcalendar.min.js"></script>
	<script type="text/javascript" src="recursos/js/jquery.qtip.min.js"></script>
	<script type="text/javascript" src="recursos/js/lang/es.js"></script>

	<!-- css  -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Lekton:wght@400;700&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="recursos/css/estilos.css" type="text/css" media="screen, projection"/>
	<link rel="stylesheet" type="text/css" href="recursos/js/css/jquery-ui.min.css" />
	<link rel="stylesheet" type="text/css" href="recursos/js/css/jquery.raty.css" />
	<link rel="stylesheet" type="text/css" href="recursos/css/style-drag.css" />

	<!-- calendario -->
	<link rel='stylesheet'  type="text/css" href="recursos/js/css/fullcalendar.css" />
	<link rel='stylesheet'  type="text/css" href="recursos/js/css/jquery.qtip.css" />
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
		
	<link rel="icon" href="http://modelbrush.com/wp-content/uploads/2014/12/favicon1-548ef461_site_icon-32x32.png" sizes="32x32" />
	<link rel="icon" href="http://modelbrush.com/wp-content/uploads/2014/12/favicon1-548ef461_site_icon-256x256.png" sizes="192x192" />

</head>

<body>
<div id="boxlogomodelbrush" aria-label="MB Leagues">
	<span class="login-brand-mark" aria-hidden="true"></span>
	<span class="login-brand-name">MB Leagues</span>
</div>
<div id="login">
	 
	 <?php printf($msjError); ?>
	 <fieldset>
        <form name="login" action="" method="post">
			<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>"/>
            <label for="user">Usuario: </label><input type="text" name="user" size="10" value=""/>
            <input type="hidden" name="validator" value="s"/>
            <label for="pass">Contrase&ntilde;a: </label><input type="password" name="pass" size="10" value=""/>
			<input type="submit" value="Entrar en el panel" class="button btn btn-primary w-100"/>
        </form>
    </fieldset>

</div>
<?php require_once "footer.php"; ?>
</body>
</html>