<?php


     $dbCredentials = require __DIR__ . '/../config/db.credentials.php';
     $servidor = $dbCredentials['servidor'];
     $usuario = $dbCredentials['usuario'];
     $contrasena = $dbCredentials['contrasena'];
     $db = $dbCredentials['db'];
    // conexion
    try{
        $conexion = new mysqli($servidor,$usuario,$contrasena, $db) or die ('No se pudo conectar: ' . mysqli_connect_error());
        $conexion->set_charset('utf8');
    
    }catch(Exception $e){
         printf("ERROR DB");
    }

	$rutaLiga = "";
    $query = "SELECT nombre FROM mb_ligas WHERE idLiga = ". $idLiga;
    $resultadoBD = $conexion->	query( $query );
	while ($fila = mysqli_fetch_assoc($resultadoBD)) {
		$rutaLiga = $fila["nombre"];
	}

    $query = "SELECT idJugador, nick FROM mb_jugadores WHERE idLiga = ". $idLiga . " order by nick";
    $resultadoBD = $conexion->	query( $query );
    printf("<table class=\"listas\">\n");
    printf("<thead>\n<tr>
            <td class=\"td-sin-alinear\"><b>Nick</b></td>\n
            <td><b>Fase I</b></td>\n
            <td><b>Fase II</b></td>\n
            <td><b>Fase III</b></td></thead>\n</tr>
            <tbody>\n");

    while ($fila = mysqli_fetch_assoc($resultadoBD)) {

        printf("<tr class=\"lista-jugador\"><td class=\"td-sin-alinear\">" . str_replace("zMercenario","Mercenario",$fila["nick"]) . "</td>");
        
        /**************** fase 1 *****************/
        $queryFase = "SELECT bando, urlDocumento, numFase FROM mb_listas where idJugador = " . $fila["idJugador"] . " AND numFase = 1 ORDER BY bando";                
        $resultadoBDFase = $conexion->	query( $queryFase );
        printf("<td>");
        $aux = 0;
        while ($filaFase = mysqli_fetch_assoc($resultadoBDFase)) {
            $aux = 1;
            $icon = ($filaFase["bando"] == 'ALIADO') ? "icon_aliados.png": "icon_eje.png";
            printf("<a href=\"/mb-league/recursos/docs/ligas/" .$rutaLiga . "/". $filaFase["numFase"]."/" . $filaFase["urlDocumento"] . "\" target=\"_blank\"><img title=\"Bajar lista en PDF\" src=\"/mb-league/recursos/img/" . $icon . "\" width=\"17px\" alt=\"\"></a> ");
        }

        if( $aux == 0){
            printf("-");
        }

        printf("</td>");

        /**************** fase 2 *****************/
        $queryFase = "SELECT bando, urlDocumento, numFase FROM mb_listas where idJugador = " . $fila["idJugador"] . " AND numFase = 2 ORDER BY bando";                
        $resultadoBDFase = $conexion->	query( $queryFase );
        printf("<td>");
        $aux = 0;
        while ($filaFase = mysqli_fetch_assoc($resultadoBDFase)) {
            $aux = 1;
            $icon = ($filaFase["bando"] == 'ALIADO') ? "icon_aliados.png": "icon_eje.png";
             printf("<a href=\"/mb-league/recursos/docs/ligas/" .$rutaLiga . "/". $filaFase["numFase"]."/" . $filaFase["urlDocumento"] . "\" target=\"_blank\"><img title=\"Bajar lista en PDF\" src=\"/mb-league/recursos/img/" . $icon . "\" width=\"17px\" alt=\"\"></a> ");
        }
        if( $aux == 0){
            printf("-");
        }
        printf("</td>");

        /**************** fase 3 *****************/
        $queryFase = "SELECT bando, urlDocumento, numFase FROM mb_listas where idJugador = " . $fila["idJugador"] . " AND numFase = 3 ORDER BY bando";                
        $resultadoBDFase = $conexion->	query( $queryFase );
        printf("<td>");
        $aux = 0;
        while ($filaFase = mysqli_fetch_assoc($resultadoBDFase)) {
            $aux = 1;
            $icon = ($filaFase["bando"] == 'ALIADO') ? "icon_aliados.png": "icon_eje.png";
            printf("<a href=\"/mb-league/recursos/docs/ligas/" .$rutaLiga . "/". $filaFase["numFase"]."/" . $filaFase["urlDocumento"] . "\" target=\"_blank\"><img title=\"Bajar lista en PDF\" src=\"/mb-league/recursos/img/" . $icon . "\" width=\"17px\" alt=\"\"></a> ");
        }
     
        if( $aux == 0){
            printf("-");
        }
        printf("</td>");

        printf("</tr>");
    }
    printf("</tbody>\n
            </table>\n");

    printf("</div>");


?>