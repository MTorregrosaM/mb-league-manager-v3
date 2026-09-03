<link rel='stylesheet' id='divi-fonts-css'  href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,700italic,800italic,400,300,700,800&#038;subset=latin,latin-ext' type='text/css' media='all' />
<link rel='stylesheet' id='divi-style-css'  href='http://modelbrush.com/wp-content/themes/modelbrush/style.css?ver=2.1.2' type='text/css' media='all' />



<?php

	$dbCredentials = require __DIR__ . '/config/db.credentials.php';
	$servidor = $dbCredentials['servidor'];
	$usuario = $dbCredentials['usuario'];
	$contrasena = $dbCredentials['contrasena'];
	$db = $dbCredentials['db'];

	// nos conectamos a la bd
	$link = mysqli_connect($servidor, $usuario, $contrasena, $db) or die('No se pudo conectar: ' . mysqli_connect_error());
	mysqli_set_charset($link, 'utf8');


    // 1. RECORREMOS LOS USUARIOS Y METEMOS UN NUEVO REGISTRO POR CADA PARTICIPACIÓN EN UNA TABLA TMP

    $query = "SELECT idJugador, nick FROM mb_jugadores";                
	$resultadoBD = mysqli_query($link, $query);
    printf("<div style=\"width: 450px !important\">");
    printf("<table class=\"listas\">\n");
	printf("<thead>\n
			<td class=\"td-sin-alinear\"><b>Nick</b></td>\n
			<td><b>Fase I</b></td>\n
			<td><b>Fase II</b></td>\n
			<td><b>Fase III</b></td></thead>\n
			<tbody>\n");
	while ($fila = mysqli_fetch_assoc($resultadoBD)) {


    	printf("<tr class=\"lista-jugador\">
				<td class=\"td-sin-alinear\">" . $fila["nick"] . "</td>");

    	// fase 1
    	$queryFase = "SELECT bando, urlDocumento, numFase FROM mb_listas where idJugador = " . $fila["idJugador"] . " AND numFase = 1";                
     	$resultadoBDFase = mysqli_query($link, $queryFase);
    	printf("<td>");
    	while ($filaFase = mysqli_fetch_assoc($resultadoBDFase)) {
    		$icon = ($filaFase["bando"] == 'ALIADO') ? "icon_aliados.png": "icon_eje.png";
			printf("<a href=\"http://www.modelbrush.com/mb-league/assets/docs/ligas/III Liga Flames of War/". $filaFase["numFase"]."/" . $filaFase["urlDocumento"] . "\" target=\"_blank\"><img title=\"Bajar lista en PDF\" src=\"http://www.modelbrush.com/mb-league/assets/img/" . $icon . "\" alt=\"\"></a>");
    	}
    	if($resultadoBDFase  == null){
			printf("-");
    	}
    	printf("</td>");

    	// fase 2
    	$queryFase = "SELECT bando, urlDocumento, numFase FROM mb_listas where idJugador = " . $fila["idJugador"] . " AND numFase = 2";                
    	$resultadoBDFase = mysqli_query($link, $queryFase);
    	printf("<td>");
    	while ($filaFase = mysqli_fetch_assoc($resultadoBDFase)) {
    		$icon = ($filaFase["bando"] == 'ALIADO') ? "icon_aliados.png": "icon_eje.png";
			printf("<a href=\"http://www.modelbrush.com/mb-league/assets/docs/ligas/III Liga Flames of War/". $filaFase["numFase"]."/" . $filaFase["urlDocumento"] . "\" target=\"_blank\"><img title=\"Bajar lista en PDF\" src=\"http://www.modelbrush.com/mb-league/assets/img/" . $icon . "\" alt=\"\"></a>");
    	}
    	if($resultadoBDFase  == null){
			printf("-");
    	}
    	printf("</td>");

    	// fase 3
    	$queryFase = "SELECT bando, urlDocumento, numFase FROM mb_listas where idJugador = " . $fila["idJugador"] . " AND numFase = 3";                
    	$resultadoBDFase = mysqli_query($link, $queryFase);
    	printf("<td>");
    	while ($filaFase = mysqli_fetch_assoc($resultadoBDFase)) {
    		$icon = ($filaFase["bando"] == 'ALIADO') ? "icon_aliados.png": "icon_eje.png";
			printf("<a href=\"http://www.modelbrush.com/mb-league/assets/docs/ligas/III Liga Flames of War/". $filaFase["numFase"]."/" . $filaFase["urlDocumento"] . "\" target=\"_blank\"><img title=\"Bajar lista en PDF\" src=\"http://www.modelbrush.com/mb-league/assets/img/" . $icon . "\" alt=\"\"></a>");
    	}
    	if($resultadoBDFase  == null){
			printf("-");
    	}
    	printf("</td>");

    	printf("</tr>");
	}
	printf("</tbody>\n
			</table>\n");

	printf("</div>");

            

?>
   




