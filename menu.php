<?php

 	// USUARIO
	$estaLogado = (($_SESSION["autorizado"] ?? 0) == 1 && !empty($_SESSION["usuario"]));
	$rolUsuario = $estaLogado ? strtoupper(trim((string) ($_SESSION["rol"] ?? ''))) : '';
	$esAdministrador = $estaLogado && $rolUsuario === 'ADMIN';
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

<?php if ($estaLogado && ($rolUsuario == 'ADMIN' || $rolUsuario == 'USER' )) { ?>
<div id="logo">
	<img src="images/logo_mb.svg" alt="MB League">
</div>
<?php } ?>

<nav class="navbar navbar-expand-lg navbar-dark app-navbar mb-4">
	<div class="container-fluid px-0">
		<a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
			<span class="app-brand-mark" aria-hidden="true"></span>
			<span>MB League</span>
		</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavigation" aria-controls="mainNavigation" aria-expanded="false" aria-label="Abrir navegación">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="mainNavigation">
			<ul class="navbar-nav me-auto mb-2 mb-lg-0">
				<li class="nav-item"><a class="nav-link<?php printf($paginaActual == 'index.php' ? ' active' : ''); ?>" href="index.php">Competiciones</a></li>
				<?php if ($estaLogado) { ?>
					<?php if ($esAdministrador) { ?><li class="nav-item"><a class="nav-link<?php printf($paginaActual == 'gestion-usuarios.php' ? ' active' : ''); ?>" href="gestion-usuarios.php">Usuarios</a></li><?php } ?>
					<li class="nav-item"><a class="nav-link<?php printf(claseMenuActivo($paginaActual, $seccionesMenu['gestion-ligas.php']) ? ' active' : ''); ?>" href="gestion-ligas.php">Ligas</a></li>
					<li class="nav-item"><a class="nav-link<?php printf(claseMenuActivo($paginaActual, $seccionesMenu['gestion-jugadores.php']) ? ' active' : ''); ?>" href="gestion-jugadores.php">Jugadores</a></li>
					<li class="nav-item"><a class="nav-link<?php printf(claseMenuActivo($paginaActual, $seccionesMenu['gestion-enfrentamientos.php']) ? ' active' : ''); ?>" href="gestion-enfrentamientos.php">Cruces</a></li>
					<li class="nav-item"><a class="nav-link<?php printf(claseMenuActivo($paginaActual, $seccionesMenu['editar-resultados.php']) ? ' active' : ''); ?>" href="editar-resultados.php">Resultados</a></li>
					<li class="nav-item"><a class="nav-link<?php printf(claseMenuActivo($paginaActual, $seccionesMenu['cruce-doble-lista.php']) ? ' active' : ''); ?>" href="cruce-doble-lista.php">Doble lista</a></li>
				<?php } ?>
				<li class="nav-item"><a class="nav-link<?php printf(claseMenuActivo($paginaActual, $seccionesMenu['alta-resultados.php']) ? ' active' : ''); ?>" href="alta-resultados.php">Alta resultados</a></li>
			</ul>
			<a class="btn btn-outline-light btn-sm" href="<?php echo $estaLogado ? 'logout.php' : 'login.php'; ?>"><?php echo $estaLogado ? 'Salir' : 'Admin'; ?></a>
		</div>
	</div>
</nav>
	