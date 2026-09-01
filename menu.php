<?php

 	// USUARIO
    $oControllerUsuario = new controllerUsuario();
	$oUsuario = $oControllerUsuario->recuperarDatosUsuario( $_SESSION["usuario"] );
	$paginaActual = basename($_SERVER['PHP_SELF']);
	$seccionesMenu = array(
		'gestion-ligas.php' => array('gestion-ligas.php', 'gestion-fases.php', 'gestion-ligas-usuario.php'),
		'gestion-jugadores.php' => array('gestion-jugadores.php', 'gestion-listas.php', 'gestion-listas2.php'),
		'gestion-enfrentamientos.php' => array('gestion-enfrentamientos.php'),
		'editar-resultados.php' => array('editar-resultados.php'),
		'alta-resultados.php' => array('alta-resultados.php', 'alta-resultados2025.php'),
		'cruce-doble-lista.php' => array('cruce-doble-lista.php')
	);

	function claseMenuActivo($paginaActual, $paginasSeccion) {
		return in_array($paginaActual, $paginasSeccion, true) ? ' menu-activo' : '';
	}

?>

<?php if ($oUsuario->rol == 'ADMIN' || $oUsuario->rol == 'USER' ) { ?>
<div id="logo">
	<img src="images/LOGO_LIGAS.png">
</div>
<?php } ?>

<nav class="nav-collapse">
	<ul>
		<li class="<?php printf($paginaActual == 'index.php' ? 'menu-activo' : ''); ?>"><a class="home" href="index.php"><img src="images/home.png" alt="Home" ></a></li>
		<?php if ($oUsuario->rol == 'ADMIN' ) { ?> <li class="<?php printf($paginaActual == 'gestion-usuarios.php' ? 'menu-activo' : ''); ?>"><a href="gestion-usuarios.php" class="btn-admin">Gesti&oacute;n de usuarios</a></li><?php } ?>
		<li class="<?php printf(claseMenuActivo($paginaActual, $seccionesMenu['gestion-ligas.php'])); ?>"><a href="gestion-ligas.php">Gesti&oacute;n de ligas</a></li>
		<li class="<?php printf(claseMenuActivo($paginaActual, $seccionesMenu['gestion-jugadores.php'])); ?>"><a href="gestion-jugadores.php">Gesti&oacute;n de jugadores</a></li>
		<li class="<?php printf(claseMenuActivo($paginaActual, $seccionesMenu['gestion-enfrentamientos.php'])); ?>"><a href="gestion-enfrentamientos.php">Generar cruces</a></li>
		<li class="<?php printf(claseMenuActivo($paginaActual, $seccionesMenu['editar-resultados.php'])); ?>"><a href="editar-resultados.php">Editar resultados</a></li>
		<li class="<?php printf(claseMenuActivo($paginaActual, $seccionesMenu['alta-resultados.php'])); ?>"><a href="alta-resultados.php"  class="btn-admin">Alta de resultados</a></li>
		<li class="<?php printf(claseMenuActivo($paginaActual, $seccionesMenu['cruce-doble-lista.php'])); ?>"><a href="cruce-doble-lista.php"  class="btn-admin">Doble lista</a></li>
		<li class="logout"><a href="logout.php">Salir</a></li>
	</ul>
</nav>
	<br/><br/>
	