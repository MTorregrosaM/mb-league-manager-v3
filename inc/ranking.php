    <?php

     $dbCredentials = require __DIR__ . '/../config/db.credentials.php';
     $servidor = $dbCredentials['servidor'];
     $usuario = $dbCredentials['usuario'];
     $contrasena = $dbCredentials['contrasena'];
     $db = $dbCredentials['db'];


    //query("SET NAMES 'utf8'");
    try {
    // conexion+
    
    $conexion = new mysqli($servidor,$usuario,$contrasena, $db) or die ('No se pudo conectar: ' . mysqli_connect_error());
    $conexion->set_charset('utf8');
    
    }catch(Exception $e){
         printf("ERROR DB");
    }



        function sortByOrder($a, $b) {
            if($a[5] == $b[5]){ // comprobamos puntosTotales
                if($a[4] == $b[4]){ // comprobamos puntosVictoria
                    if($a[2] == $b[2]){ // victorias
                        return strcmp($a[0],$b[0]);
                    }else{
                        return ($a[2] > $b[2]) ? -1 : 1;
                    }       
                }else{
                    return ($a[4] > $b[4]) ? -1 : 1;
                }
            }else{
                return ($a[5] > $b[5]) ? -1 : 1;
            }

        }   


        function sortByOrderGB($a, $b) {
           /* if($a[8] == $b[8]){ // comprobamos puntosTotales
                if($a[6] == $b[6]){ // puntos a favor
                    if($a[7] == $b[7]){ // puntos en contra
                          if($a[3] == $b[3]){ // victorias
                                return strcmp($a[0],$b[0]);
                            }else{
                                return ($a[3] > $b[3]) ? -1 : 1;
                            }  
                    }else{
                        return ($a[7] < $b[7]) ? -1 : 1;
                    }       
                }else{
                    return ($a[6] > $b[6]) ? -1 : 1;
                }
            }else{
                return ($a[8] > $b[8]) ? -1 : 1;
            }*/
            if($a[8] == $b[8]){ // comprobamos puntosTotales
                if($a[6]-$a[7] == $b[6]-$b[7]){ // puntos a favor
                   // if($a[7] == $b[7]){ // puntos en contra
                          if($a[3] == $b[3]){ // victorias
                                return strcmp($a[0],$b[0]);
                            }else{
                                return ($a[3] > $b[3]) ? -1 : 1;
                            }  
                   /* }else{
                        return ($a[7] < $b[7]) ? -1 : 1;
                    }    */   
                }else{
                    return ($a[6]-$a[7] > $b[6]-$b[7]) ? -1 : 1;
                }
            }else{
                return ($a[8] > $b[8]) ? -1 : 1;
            }

        }   

        // recuperamos el tipo de juego de la liga
        /*
            
            1 Flames of War v3 
            2 Flames of War V4
            3 Warhammer 40000
            4 Bolt Action
            5 Guild Ball

        */
        $idJuego = 0;
        $query = "SELECT idJuego FROM mb_ligas WHERE idLiga = " . $idLiga;
        $resultadoBD = $conexion->	query	( $query );
        while ($fila = mysqli_fetch_assoc ($resultadoBD)) {
          $idJuego = $fila["idJuego"]; 
        }
       



        /**********************************/
        /*******  ranking de liga *********/
        /**********************************/

        $query = "SELECT distinct idJugador, nick, T2.descFaccion, T2.imgFaccion FROM mb_jugadores T1 INNER JOIN mb_facciones T2 on T1.bando = T2.idFaccion AND idLiga = " . $idLiga . " and nick !=  'zMercenario' order by idJugador";                
        $resultadoBD = $conexion->	query	( $query );



        $arrRanking = array ();
        
        /**********************************/
        /******* FLAMES OF WAR V3 *********/
        /**********************************/
        if($idJuego == 1)  {

            printf("<table class=\"clasificacion\">\n");
            printf("<thead><tr class=\"head-clasif\"><td class=\"td-sin-alinear\"><b>Nick</b></td><td><b>J</b></td><td><b>G</b></td><td><b>P</b></td><td><b>PV</b></td><td><b>PT</b></td></thead></tr><tbody>");

            while ($fila = mysqli_fetch_assoc ($resultadoBD)) {
                 //$cont++;

                   $query1 = "SELECT 
                            SUM(CASE WHEN fechaBatalla > 0 THEN 1 ELSE 0 END) as numPartidas,
                            sum(CASE WHEN idJugador1 = " . $fila["idJugador"] . " AND resultadoJugador1 > resultadoJugador2 AND resultadoJugador1 > 3 THEN 1 ELSE 0 END) + 
                            sum(CASE WHEN idJugador2 = " . $fila["idJugador"] . " AND resultadoJugador2 > resultadoJugador1 AND resultadoJugador2 > 3 THEN 1 ELSE 0 END) as partidasVictoria,
                         
                            sum(CASE WHEN idJugador1 = " . $fila["idJugador"] . " AND resultadoJugador1 < resultadoJugador2  THEN 1 ELSE 0 END) + 
                            sum(CASE WHEN idJugador2 = " . $fila["idJugador"] . " AND resultadoJugador2 < resultadoJugador1  THEN 1 ELSE 0 END) +
                            sum(CASE WHEN idJugador1 = " . $fila["idJugador"] . " AND resultadoJugador2 < 3 and resultadoJugador1 < 3 THEN 1 ELSE 0 END) +
                            sum(CASE WHEN idJugador2 = " . $fila["idJugador"] . " AND resultadoJugador1 < 3 and resultadoJugador2 < 3 THEN 1 ELSE 0 END) as partidasDerrota,
                            sum(CASE WHEN idJugador1 = " . $fila["idJugador"] . " THEN IFNULL(resultadoJugador1,0) ELSE 0 END) + sum(CASE WHEN idJugador2 = " . $fila["idJugador"] . " THEN IFNULL(resultadoJugador2,0) ELSE 0 END) as puntosVictoria,
                            sum(CASE WHEN idJugador1 = " . $fila["idJugador"] . " AND resultadoJugador1 > resultadoJugador2 AND resultadoJugador1 > 3 THEN 3 ELSE 0 END) + 
                            sum(CASE WHEN idJugador2 = " . $fila["idJugador"] . " AND resultadoJugador2 > resultadoJugador1 AND resultadoJugador2 > 3 THEN 3 ELSE 0 END)   as puntosTotales,
                            sum(indValidado) AS indValidado
                            FROM mb_enfrentamientos T1
                           
                            WHERE (IdJugador1 = " . $fila["idJugador"] . " or idJugador2 = " . $fila["idJugador"] . ")
                             
                            and idLiga = " .$idLiga;              

                $resultadoBD1 = $conexion->	query	( $query1 );

                // nuevo 19-06-2016 puntos por pintura
                $queryPintura = "SELECT sum(puntosPintura) as puntosPintura
                            FROM mb_jugadores                   
                            WHERE IdJugador = " . $fila["idJugador"];                 
                $resultadoBDPintura = $conexion->	query	( $queryPintura );
                while ($filaPintura = mysqli_fetch_assoc ($resultadoBDPintura)) {
                    $puntosPintura = $filaPintura["puntosPintura"];
                }  

            
                while ($fila1 = mysqli_fetch_assoc ($resultadoBD1)) {
                    $arrRankingRow = array ();
                    $nickNuevo = ($puntosPintura == 0)? $fila["nick"] : $fila["nick"] . " <img src=\"/mb-league/assets/img/icono-flamesofwar-pintura.png\" alt=\"Ejército pintado: +2 puntos\"  title=\"Ejército pintado: +2 puntos\" >";
                    array_push($arrRankingRow, $nickNuevo, $fila1["numPartidas"], $fila1["partidasVictoria"], $fila1["partidasDerrota"], $fila1["puntosVictoria"], $fila1["puntosTotales"]+$puntosPintura, $fila1["indValidado"]);
                    array_push($arrRanking, $arrRankingRow);   
                }
            }  

            usort($arrRanking, "sortByOrder");

            $numPos = 1;
            foreach ($arrRanking as $valor) {

                $valor[1] = (strlen($valor[1]) < 1)? "-" : $valor[1];
                $valor[2] = ($valor[2] == null)? "-" : $valor[2];
                $valor[3] = ($valor[3] == "")? "-" : $valor[3];
                $valor[4] = ($valor[4] == "")? "-" : $valor[4];
                $valor[5] = ($valor[5] == "")? "0" : $valor[5];


                $sinValidarIni = ( $valor[6] != $valor[1] && $valor[1] > 0 )? "<i>" : "" ;
                $sinValidarFin = ( $valor[6] != $valor[1] && $valor[1] > 0 )? "</i>" : "" ;
                printf("<tr class=\"lista-jugador ranking-" . $numPos . "\">");
                printf("<td class=\"td-sin-alinear\">" . $sinValidarIni . " " . $valor[0] . " " . $sinValidarFin . "</td><td>" . $valor[1] . "</td><td>" . $valor[2] . "</td><td>" . $valor[3] . "</td><td>" . $valor[4] . "</td><td>" . $valor[5] . "</td></tr>");

                $numPos++;
            }   
            printf("</tbody>\n</table>\n");

        /**********************************/
        /******* FLAMES OF WAR V4 *********/
        /**********************************/
        } elseif($idJuego == 2)  {

            printf("<table class=\"clasificacion\">\n");
            printf("<thead><tr class=\"head-clasif\"><td class=\"td-sin-alinear\"><b>Nick</b></td><td><b>J</b></td><td><b>G</b></td><td><b>P</b></td><td><b>PV</b></td><td><b>PT</b></td></thead></tr><tbody>");



                while ($fila = mysqli_fetch_assoc ($resultadoBD)) {
                 //$cont++;

                  $query1 = "SELECT 
                            SUM(CASE WHEN fechaBatalla > 0 THEN 1 ELSE 0 END) as numPartidas,
                            sum(CASE WHEN idJugador1 = " . $fila["idJugador"] . " AND resultadoJugador1 > resultadoJugador2 AND resultadoJugador1 > 4 THEN 1 ELSE 0 END) + 
                            sum(CASE WHEN idJugador2 = " . $fila["idJugador"] . " AND resultadoJugador2 > resultadoJugador1 AND resultadoJugador2 > 4 THEN 1 ELSE 0 END) as partidasVictoria,
                         
                            sum(CASE WHEN idJugador1 = " . $fila["idJugador"] . " AND resultadoJugador1 < resultadoJugador2 THEN 1 ELSE 0 END) + 
                            sum(CASE WHEN idJugador2 = " . $fila["idJugador"] . " AND resultadoJugador2 < resultadoJugador1 THEN 1 ELSE 0 END) +
                            sum(CASE WHEN idJugador1 = " . $fila["idJugador"] . " AND resultadoJugador2 < 4 and resultadoJugador1 < 4 THEN 1 ELSE 0 END) +
                            sum(CASE WHEN idJugador2 = " . $fila["idJugador"] . " AND resultadoJugador1 < 4 and resultadoJugador2 < 4 THEN 1 ELSE 0 END) as partidasDerrota,
                            sum(CASE WHEN idJugador1 = " . $fila["idJugador"] . " THEN IFNULL(resultadoJugador1,0) ELSE 0 END) + sum(CASE WHEN idJugador2 = " . $fila["idJugador"] . " THEN IFNULL(resultadoJugador2,0) ELSE 0 END) as puntosVictoria,
                            sum(CASE WHEN idJugador1 = " . $fila["idJugador"] . " AND resultadoJugador1 > resultadoJugador2 AND resultadoJugador1 > 4 THEN 3 ELSE 0 END) + 
                            sum(CASE WHEN idJugador2 = " . $fila["idJugador"] . " AND resultadoJugador2 > resultadoJugador1 AND resultadoJugador2 > 4 THEN 3 ELSE 0 END)   as puntosTotales,
                            sum(indValidado) AS indValidado
                            FROM mb_enfrentamientos T1
                           
                            WHERE (IdJugador1 = " . $fila["idJugador"] . " or idJugador2 = " . $fila["idJugador"] . ")
                             
                            and idLiga = " .$idLiga;              

                $resultadoBD1 = $conexion->	query	( $query1 );

                // nuevo 19-06-2016 puntos por pintura
                $queryPintura = "SELECT sum(puntosPintura) as puntosPintura
                            FROM mb_jugadores                   
                            WHERE IdJugador = " . $fila["idJugador"];                 
                $resultadoBDPintura = $conexion->	query	( $queryPintura );
                while ($filaPintura = mysqli_fetch_assoc ($resultadoBDPintura)) {
                    $puntosPintura = $filaPintura["puntosPintura"];
                }  

            
                while ($fila1 = mysqli_fetch_assoc ($resultadoBD1)) {
                    $arrRankingRow = array ();
                    $nickNuevo = ($puntosPintura == 0)? $fila["nick"] : $fila["nick"] . " <img src=\"/mb-league/assets/img/icono-flamesofwar-pintura.png\" alt=\"Ejército pintado: +2 puntos\"  title=\"Ejército pintado: +2 puntos\" >";
                    array_push($arrRankingRow, $nickNuevo, $fila1["numPartidas"], $fila1["partidasVictoria"], $fila1["partidasDerrota"], $fila1["puntosVictoria"], $fila1["puntosTotales"]+$puntosPintura, $fila1["indValidado"]);
                    array_push($arrRanking, $arrRankingRow);   
                }
            }  

            usort($arrRanking, "sortByOrder");

            $numPos = 1;
            foreach ($arrRanking as $valor) {

                $valor[1] = (strlen($valor[1]) < 1)? "-" : $valor[1];
                $valor[2] = ($valor[2] == null)? "-" : $valor[2];
                $valor[3] = ($valor[3] == "")? "-" : $valor[3];
                $valor[4] = ($valor[4] == "")? "-" : $valor[4];
                $valor[5] = ($valor[5] == "")? "0" : $valor[5];


                $sinValidarIni = ( $valor[6] != $valor[1] && $valor[1] > 0 )? "<i>" : "" ;
                $sinValidarFin = ( $valor[6] != $valor[1] && $valor[1] > 0 )? "</i>" : "" ;
                printf("<tr class=\"lista-jugador ranking-" . $numPos . "\">");
                printf("<td class=\"td-sin-alinear\">" . $sinValidarIni . " " . $valor[0] . " " . $sinValidarFin . "</td><td>" . $valor[1] . "</td><td>" . $valor[2] . "</td><td>" . $valor[3] . "</td><td>" . $valor[4] . "</td><td>" . $valor[5] . "</td></tr>");

                $numPos++;
            }   
            printf("</tbody>\n</table>\n");

        /**********************************/
        /******* GUILD BALL *********/
        /**********************************/
        } elseif($idJuego == 5)  {

            printf("<table class=\"clasificacion\">\n");
            printf("<thead><tr class=\"head-clasif\"><td class=\"td-sin-alinear\"><b>Nick</b></td><TD></td><td><b title=\"Partidas jugadas\">J</b></td> <td><b title=\"Partidas ganadas\">G</b></td> <td><b title=\"Partidas empatadas\">E</b></td>  <td><b title=\"Partidas perdidas\">P</b></td>  <td><b title=\"Diferencia de puntos\">DP</b></td>   <td><b title=\"Puntos de victoria\">PV</b></td></thead></tr><tbody>");



                while ($fila = mysqli_fetch_assoc ($resultadoBD)) {
                 //$cont++;

                  $query1 = "SELECT 
                            SUM(CASE WHEN fechaBatalla > 0 THEN 1 ELSE 0 END) as numPartidas,
                           sum(CASE WHEN idJugador1 = " . $fila["idJugador"] . " AND resultadoJugador1 > resultadoJugador2 THEN 1 ELSE 0 END) + 
                            sum(CASE WHEN idJugador2 = " . $fila["idJugador"] . " AND resultadoJugador2 > resultadoJugador1  THEN 1 ELSE 0 END) as partidasVictoria,  

                            sum(CASE WHEN resultadoJugador1 = 1 AND resultadoJugador1 = 1 THEN 1 ELSE 0 END)as partidasEmpate,

                            sum(CASE WHEN idJugador1 = " . $fila["idJugador"] . " AND resultadoJugador1 < resultadoJugador2 THEN 1 ELSE 0 END) + 
                            sum(CASE WHEN idJugador2 = " . $fila["idJugador"] . " AND resultadoJugador2 < resultadoJugador1  THEN 1 ELSE 0 END) as partidasDerrota,

                            sum(CASE WHEN idJugador1 = " . $fila["idJugador"] . " THEN resultadoJugador1 ELSE 0 END) + 
                            sum(CASE WHEN idJugador2 = " . $fila["idJugador"] . " THEN resultadoJugador2 ELSE 0 END)  as puntosAFavor,

                            sum(CASE WHEN idJugador1 = " . $fila["idJugador"] . " THEN resultadoJugador2 ELSE 0 END) + 
                            sum(CASE WHEN idJugador2 = " . $fila["idJugador"] . " THEN resultadoJugador1 ELSE 0 END)  as puntosEnContra,

                            sum(CASE WHEN idJugador1 = " . $fila["idJugador"] . " AND resultadoJugador1 > resultadoJugador2 THEN 2 ELSE 0 END) + 
                            sum(CASE WHEN idJugador2 = " . $fila["idJugador"] . " AND resultadoJugador2 > resultadoJugador1 THEN 2 ELSE 0 END)  + 
                            sum(CASE WHEN resultadoJugador2 = resultadoJugador1 THEN 1 ELSE 0 END) as puntosTotales,

                            sum(indValidado) AS indValidado
                            FROM mb_enfrentamientos T1
                           
                            WHERE (IdJugador1 = " . $fila["idJugador"] . " or idJugador2 = " . $fila["idJugador"] . ")
                             
                            and idLiga = " .$idLiga;              

                $resultadoBD1 = $conexion->	query	( $query1 );

                // nuevo 19-06-2016 puntos por pintura
                $queryPintura = "SELECT sum(puntosPintura) as puntosPintura
                            FROM mb_jugadores                   
                            WHERE IdJugador = " . $fila["idJugador"];                 
                $resultadoBDPintura = $conexion->	query	( $queryPintura );
                while ($filaPintura = mysqli_fetch_assoc ($resultadoBDPintura)) {
                    $puntosPintura = $filaPintura["puntosPintura"];
                }  

            
                while ($fila1 = mysqli_fetch_assoc ($resultadoBD1)) {
                    $arrRankingRow = array ();
                    $nickNuevo = ($puntosPintura == 0)? $fila["nick"] : $fila["nick"] . " <img src=\"/mb-league/assets/img/icono-flamesofwar-pintura.png\" alt=\"Ejército pintado: +2 puntos\"  title=\"Ejército pintado: +2 puntos\" >";
                    $iconoFaccion = "<img src=\"/mb-league/assets/img/" . $fila["imgFaccion"]  . "\" alt=\"" . $fila["descFaccion"] . "\" title=\"" . $fila["descFaccion"] . "\" width=\"20px\"/>";
                    array_push($arrRankingRow, $nickNuevo, $iconoFaccion, $fila1["numPartidas"], $fila1["partidasVictoria"], $fila1["partidasEmpate"], $fila1["partidasDerrota"], $fila1["puntosAFavor"], $fila1["puntosEnContra"], $fila1["puntosTotales"]+$puntosPintura, $fila1["indValidado"]);
                    array_push($arrRanking, $arrRankingRow);   
                }
            }  

            usort($arrRanking, "sortByOrderGB");

            $numPos = 1;
            foreach ($arrRanking as $valor) {

                //$valor[1] = (strlen($valor[1]) < 1)? "-" : $valor[1]; //  icono
                $valor[2] = ($valor[2] == null)? "-" : $valor[2];
                $valor[3] = ($valor[3] == "")? "-" : $valor[3];
                $valor[4] = ($valor[4] == "")? "-" : $valor[4];
                $valor[5] = ($valor[5] == "")? "0" : $valor[5];
                $valor[6] = ($valor[6] == "")? "0" : $valor[6];
                $valor[7] = ($valor[7] == "")? "0" : $valor[7];
                $valor[8] = ($valor[8] == "")? "0" : $valor[8];
                $valor[9] = ($valor[9] == "")? null : $valor[9];


                $sinValidarIni = ( $valor[9] != $valor[1] && $valor[1] > 0 )? "<i>" : "" ;
                $sinValidarFin = ( $valor[9] != $valor[1] && $valor[1] > 0 )? "</i>" : "" ;
                printf("<tr class=\"lista-jugador ranking-" . $numPos . "\">");
                printf("<td class=\"td-sin-alinear\">" . $sinValidarIni . " " . $valor[0] . " " . $sinValidarFin . "</td><td>" . $valor[1] . "</td><td>" . $valor[2] . "</td><td>" . $valor[3] . "</td><td>" . $valor[4] . "</td><td>" . $valor[5] . "</td><td>" . $valor[6] . "-" . $valor[7] . "</td><td>" . $valor[8] . "</td></tr>");

                $numPos++;
            }  
            printf("</tbody>\n</table>\n");


        /**********************************/
        /******* RESTO *********/
        /**********************************/
        }else{

            printf("<table class=\"clasificacion\">\n");
            printf("<thead><tr class=\"head-clasif\"><td class=\"td-sin-alinear\"><b>Nick</b></td><td><b>J</b></td><td><b>G</b></td><td><b>E</b></td><td><b>P</b></td><td><b>PT</b></td></thead></tr><tbody>");


                while ($fila = mysqli_fetch_assoc ($resultadoBD)) {
                 //$cont++;

                $query1 = "SELECT 
                            SUM(CASE WHEN fechaBatalla > 0 THEN 1 ELSE 0 END) as numPartidas,
                            sum(CASE WHEN idJugador1 = " . $fila["idJugador"] . " AND resultadoJugador1 > resultadoJugador2 THEN 1 ELSE 0 END) + 
                            sum(CASE WHEN idJugador2 = " . $fila["idJugador"] . " AND resultadoJugador2 > resultadoJugador1  THEN 1 ELSE 0 END) as partidasVictoria,                     
                            sum(CASE WHEN resultadoJugador1 = 1 AND resultadoJugador1 = 1 THEN 1 ELSE 0 END)as partidasEmpate,
                            sum(CASE WHEN idJugador1 = " . $fila["idJugador"] . " AND resultadoJugador1 < resultadoJugador2 THEN 1 ELSE 0 END) + 
                            sum(CASE WHEN idJugador2 = " . $fila["idJugador"] . " AND resultadoJugador2 < resultadoJugador1  THEN 1 ELSE 0 END) as partidasDerrota,
                            sum(CASE WHEN idJugador1 = " . $fila["idJugador"] . " THEN resultadoJugador1 ELSE 0 END) + 
                            sum(CASE WHEN idJugador2 = " . $fila["idJugador"] . " THEN resultadoJugador2 ELSE 0 END )   as puntosTotales,
                            sum(indValidado) AS indValidado
                            FROM mb_enfrentamientos T1
                           
                            WHERE (IdJugador1 = " . $fila["idJugador"] . " or idJugador2 = " . $fila["idJugador"] . ")
                             
                            and idLiga = " .$idLiga;              

                $resultadoBD1 = $conexion->	query	( $query1 );

                // nuevo 19-06-2016 puntos por pintura
                $queryPintura = "SELECT sum(puntosPintura) as puntosPintura
                            FROM mb_jugadores                   
                            WHERE IdJugador = " . $fila["idJugador"];                 
                $resultadoBDPintura = $conexion->	query	( $queryPintura );
                while ($filaPintura = mysqli_fetch_assoc ($resultadoBDPintura)) {
                    $puntosPintura = $filaPintura["puntosPintura"];
                }  

            
                while ($fila1 = mysqli_fetch_assoc ($resultadoBD1)) {
                    $arrRankingRow = array ();
                    $nickNuevo = ($puntosPintura == 0)? $fila["nick"] : $fila["nick"] . " <img src=\"/mb-league/assets/img/icono-flamesofwar-pintura.png\" alt=\"Ejército pintado: +2 puntos\"  title=\"Ejército pintado: +2 puntos\" >";
                    array_push($arrRankingRow, $nickNuevo, $fila1["numPartidas"], $fila1["partidasVictoria"], $fila1["partidasEmpate"], $fila1["partidasDerrota"], $fila1["puntosTotales"]+$puntosPintura, $fila1["indValidado"]);
                    array_push($arrRanking, $arrRankingRow);   
                }
            }  

            usort($arrRanking, "sortByOrder");

            $numPos = 1;
            foreach ($arrRanking as $valor) {

                $valor[1] = (strlen($valor[1]) < 1)? "-" : $valor[1];
                $valor[2] = ($valor[2] == null)? "-" : $valor[2];
                $valor[3] = ($valor[3] == "")? "-" : $valor[3];
                $valor[4] = ($valor[4] == "")? "-" : $valor[4];
                $valor[5] = ($valor[5] == "")? "0" : $valor[5];



                $sinValidarIni = ( $valor[6] != $valor[1] && $valor[1] > 0 )? "<i>" : "" ;
                $sinValidarFin = ( $valor[6] != $valor[1] && $valor[1] > 0 )? "</i>" : "" ;
                printf("<tr class=\"lista-jugador ranking-" . $numPos . "\">");
                printf("<td class=\"td-sin-alinear\">" . $sinValidarIni . " " . $valor[0] . " " . $sinValidarFin . "</td><td>" . $valor[1] . "</td><td>" . $valor[2] . "</td><td>" . $valor[3] . "</td><td>" . $valor[4] . "</td><td>" . $valor[5] . "</td></tr>");

                $numPos++;
            }   
            printf("</tbody>\n</table>\n");
        }


    ?>