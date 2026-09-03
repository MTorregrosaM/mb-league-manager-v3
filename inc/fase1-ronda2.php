<?php

$dbCredentials = require __DIR__ . '/../config/db.credentials.php';
$servidor = $dbCredentials['servidor'];
$usuario = $dbCredentials['usuario'];
$contrasena = $dbCredentials['contrasena'];
$db = $dbCredentials['db'];

    try {
    // conexion+
    
    $conexion = new mysqli($servidor,$usuario,$contrasena, $db) or die ('No se pudo conectar: ' . mysqli_connect_error());
    $conexion->set_charset('utf8');
    
    }catch(Exception $e){
         printf("ERROR DB");
    }


printf("<table class=\"fase1\">
        <thead>
        <tr class=\"headB-fases\">
        <td><b>F</b></td>
        <td><b>J1</b></td>
        <td><b>J2</b></td>
        <td><b>R</b></td>
        </tr>
        </thead>
        <tbody>"); 

$queryRonda = "SELECT idJugador1, t2.nick as nickJugador1, idJugador2, t3.nick as nickJugador2, resultadoJugador1, resultadoJugador2, fechaBatalla
FROM mb_enfrentamientos t1
RIGHT JOIN mb_jugadores t2 ON IdJugador1 = t2.idJugador
RIGHT JOIN mb_jugadores t3 ON IdJugador2 = t3.idJugador
WHERE t1.idLiga = " . $idLiga . " AND t1.numFase = 1 AND t1.numRonda = 2 ORDER BY idJugador2";

$resultadoBDRonda = $conexion-> query( $queryRonda );

    while ($filaRonda = mysqli_fetch_assoc($resultadoBDRonda)) {

        $fechaBatalla = ($filaRonda["fechaBatalla"] == null )? "-" : $filaRonda["fechaBatalla"];

        printf("<tr><td class=\"faseFuentePeq\">" . formatoFecha1 (false, $fechaBatalla) . "</td><td>" . $filaRonda["nickJugador1"] . "</td><td>" . $filaRonda["nickJugador2"] . "</td><td class=\"faseFuentePeq\">" . $filaRonda["resultadoJugador1"] . "-" . $filaRonda["resultadoJugador2"] . "</td></tr>");

    }
printf(" </tbody></table>");



?>