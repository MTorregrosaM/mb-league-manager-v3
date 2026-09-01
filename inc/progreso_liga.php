<?php

$dbCredentials = require __DIR__ . '/../config/db.credentials.php';
$servidor = $dbCredentials['servidor'];
$usuario = $dbCredentials['usuario'];
$contrasena = $dbCredentials['contrasena'];
$db = $dbCredentials['db'];

$link = mysqli_connect($servidor, $usuario, $contrasena, $db) or die('No se pudo conectar: ' . mysqli_connect_error());
mysqli_set_charset($link, 'utf8');

 

$queryPorc = "select CASE WHEN count(fechaBatalla) = 0 THEN 0 ELSE round(count(fechaBatalla)*100 / ( (count(distinct idJugador)/2)*count(distinct t2.numFase)*count(distinct t2.numRonda)), 2) END as porc
 from mb_jugadores t1 
join mb_fases t2
on t1.idLiga = t2.idLiga
left join mb_enfrentamientos t3
on t1.idLiga = t3.idLiga and t2.numFase = t3.numFase and t2.numRonda = t3.numRonda and t1.idJugador = t3.idJugador1
where t1.idLiga = " . $idLiga;

$resultadoBDPorc = mysqli_query($link, $queryPorc);

    while ($filaPorc = mysqli_fetch_assoc($resultadoBDPorc)) {

        $fPorcentaje = ($filaPorc["porc"] == null )? "-" : $filaPorc["porc"];

        printf($fPorcentaje);

    }


?>