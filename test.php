<?php
require_once __DIR__ . "/config/auth.php";
exigirAdministrador();
require_once __DIR__ . "/model/class.php";
require_once __DIR__ . "/config/config.php";

$conexion = new conexBD();
$mensaje = "";
$error = "";
$numeroFases = 2;
$numeroRondas = 4;
$numeroJugadores = 8;

function escaparTest($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, "UTF-8");
}

function fechaTest($dias) {
    return date("Y-m-d", strtotime((int) $dias . " days"));
}

function insertarTest($conexion, $consulta, $tipos, $parametros) {
    return $conexion->ejecutarConsultaPreparada($consulta, $tipos, $parametros, 1);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idJuego = filter_input(INPUT_POST, "idJuego", FILTER_VALIDATE_INT);
    $numeroFases = filter_input(INPUT_POST, "numeroFases", FILTER_VALIDATE_INT);
    $numeroRondas = filter_input(INPUT_POST, "numeroRondas", FILTER_VALIDATE_INT);
    $numeroJugadores = filter_input(INPUT_POST, "numeroJugadores", FILTER_VALIDATE_INT);

    if (!$idJuego || $idJuego < 1 || !$numeroFases || $numeroFases < 1 || $numeroFases > 8 || !$numeroRondas || $numeroRondas < 1 || $numeroRondas > 12 || !$numeroJugadores || $numeroJugadores < 4 || $numeroJugadores > 20 || $numeroJugadores % 2 !== 0) {
        $error = "Selecciona valores válidos. Los jugadores deben ser un número par.";
    } else {
        $juegos = $conexion->ejecutarConsultaPreparada("SELECT idJuego, descJuego FROM mb_juegos WHERE idJuego = ? AND indActivo = 1", "i", array($idJuego));
        if (!is_array($juegos) || count($juegos) === 0) {
            $error = "El juego seleccionado no está disponible.";
        } else {
            $nombreJuego = (string) $juegos[0][1];
            $nombreLiga = "Test - " . $nombreJuego . " - " . date("YmdHis");
            $fechaInicio = date("Y-m-d");
            $fechaFin = fechaTest($numeroFases * $numeroRondas + 7);
            $ahora = date("Y-m-d H:i:s");

            $insertada = insertarTest($conexion, "INSERT INTO mb_ligas (nombre, numFases, numRondas, fecIni, fecFin, indActivo, idJuego, audAlta) VALUES (?, ?, ?, ?, ?, 1, ?, ?)", "siissis", array($nombreLiga, $numeroFases, $numeroRondas, $fechaInicio, $fechaFin, $idJuego, $ahora));
            $liga = $conexion->ejecutarConsultaPreparada("SELECT idLiga FROM mb_ligas WHERE nombre = ? ORDER BY idLiga DESC LIMIT 1", "s", array($nombreLiga));
            $idLiga = is_array($liga) && count($liga) > 0 ? (int) $liga[0][0] : 0;

            if ($insertada < 1 || $idLiga < 1) {
                $error = "No se pudo crear la liga de prueba.";
            } else {
                $jugadores = array();
                for ($indice = 1; $indice <= $numeroJugadores; $indice++) {
                    $nick = "Test_" . $idLiga . "_" . $indice;
                    $insertarJugador = insertarTest($conexion, "INSERT INTO mb_jugadores (idLiga, nick, nombre, bando, puntosPintura, audAlta) VALUES (?, ?, ?, ?, ?, ?)", "isssis", array($idLiga, $nick, "Jugador " . $indice, (string) (($indice % 2) + 1), random_int(1, 5), $ahora));
                    $jugador = $conexion->ejecutarConsultaPreparada("SELECT idJugador FROM mb_jugadores WHERE idLiga = ? AND nick = ? LIMIT 1", "is", array($idLiga, $nick));
                    if ($insertarJugador < 1 || !is_array($jugador) || count($jugador) === 0) {
                        $error = "No se pudieron crear todos los jugadores.";
                        break;
                    }
                    $jugadores[] = (int) $jugador[0][0];
                }

                if ($error === "") {
                    for ($fase = 1; $fase <= $numeroFases; $fase++) {
                        for ($ronda = 1; $ronda <= $numeroRondas; $ronda++) {
                            insertarTest($conexion, "INSERT INTO mb_fases (idLiga, numFase, numRonda, claveCifrada, fecIni, fecFin) VALUES (?, ?, ?, 'test', ?, ?)", "iiiss", array($idLiga, $fase, $ronda, $fechaInicio, $fechaFin));
                            $orden = $jugadores;
                            $desplazamiento = (($fase - 1) * $numeroRondas + $ronda - 1) % $numeroJugadores;
                            if ($desplazamiento > 0) {
                                $orden = array_merge(array_slice($orden, $desplazamiento), array_slice($orden, 0, $desplazamiento));
                            }
                            for ($partido = 0; $partido < $numeroJugadores; $partido += 2) {
                                $jugador1 = $orden[$partido];
                                $jugador2 = $orden[$partido + 1];
                                $estadoResultado = random_int(1, 100);
                                $entregado = $estadoResultado > 20;
                                $validado = $entregado && $estadoResultado > 55 ? 1 : ($entregado ? 0 : null);
                                $resultado1 = null;
                                $resultado2 = null;
                                $pintura1 = null;
                                $pintura2 = null;
                                $deportividad1 = null;
                                $deportividad2 = null;
                                $fechaResultado = null;
                                if ($entregado) {
                                    if ($idJuego <= 2) {
                                        $marcadores = array(array(1, 1), array(2, 2), array(3, 3), array(5, 4), array(4, 5));
                                        $marcador = $marcadores[random_int(0, count($marcadores) - 1)];
                                        $resultado1 = $marcador[0];
                                        $resultado2 = $marcador[1];
                                    } elseif ($idJuego == 5) {
                                        $resultado1 = random_int(0, 12);
                                        $resultado2 = random_int(0, 12);
                                    } else {
                                        $marcadores = array(array(0, 0), array(1, 0), array(0, 1), array(3, 0), array(0, 3));
                                        $marcador = $marcadores[random_int(0, count($marcadores) - 1)];
                                        $resultado1 = $marcador[0];
                                        $resultado2 = $marcador[1];
                                    }
                                    $pintura1 = random_int(1, 5);
                                    $pintura2 = random_int(1, 5);
                                    $deportividad1 = random_int(1, 3);
                                    $deportividad2 = random_int(1, 3);
                                    $fechaResultado = fechaTest(-($numeroFases * $numeroRondas) + (($fase - 1) * $numeroRondas + $ronda));
                                }
                                $insertarEnfrentamiento = insertarTest($conexion, "INSERT INTO mb_enfrentamientos (idLiga, numFase, numRonda, idJugador1, idJugador2, bandoJugador1, bandoJugador2, resultadoJugador1, resultadoJugador2, valPinturaJug1, valPinturaJug2, valDeportividadJug1, valDeportividadJug2, fechaBatalla, indValidado, audAlta) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", "iiiii" . "ss" . "iiiiii" . "sis", array($idLiga, $fase, $ronda, $jugador1, $jugador2, (string) (($partido % 2) + 1), (string) (((($partido + 1) % 2) + 1)), $resultado1, $resultado2, $pintura1, $pintura2, $deportividad1, $deportividad2, $fechaResultado, $validado, $ahora));
                                if ($insertarEnfrentamiento < 1) {
                                    $error = "No se pudieron crear todos los enfrentamientos.";
                                    break 3;
                                }
                            }
                        }
                    }
                    if ($error === "") {
                        $mensaje = "Liga creada correctamente: " . $nombreLiga . " (ID " . $idLiga . "). Contraseña de todas las fases: test.";
                    }
                }
            }
        }
    }
}

$juegos = $conexion->ejecutarConsulta("SELECT idJuego, descJuego FROM mb_juegos WHERE indActivo = 1 ORDER BY descJuego");
$juegos = is_array($juegos) ? $juegos : array();
?>
<!doctype html>
<html lang="es" data-bs-theme="dark">
<head>
  <?php require_once __DIR__ . "/cabecera.php"; ?>
  <title>Test - MB League</title>
</head>
<body>
<div id="contenedor-principal">
  <?php require_once __DIR__ . "/menu.php"; ?>
  <h2 class="h2"><span>Generador de datos de prueba</span></h2>
    <section class="ranking-card test-generador">
    <p>Genera una liga completa con fases, rondas, jugadores y resultados.</p>
    <?php if ($mensaje !== "") { ?><div id="mensaje-ok"><?php echo escaparTest($mensaje); ?></div><?php } ?>
    <?php if ($error !== "") { ?><div id="mensaje-error"><?php echo escaparTest($error); ?></div><?php } ?>
    <form id="form" method="post">
      <p><label for="idJuego">Juego</label>
        <select id="idJuego" name="idJuego" required>
          <option value="">Selecciona un juego</option>
          <?php foreach ($juegos as $juego) { ?><option value="<?php echo (int) $juego[0]; ?>"><?php echo escaparTest($juego[1]); ?></option><?php } ?>
        </select>
      </p>
      <p><label for="numeroFases">Número de fases</label><input id="numeroFases" name="numeroFases" type="number" min="1" max="8" value="<?php echo (int) $numeroFases; ?>" required></p>
      <p><label for="numeroRondas">Rondas por fase</label><input id="numeroRondas" name="numeroRondas" type="number" min="1" max="12" value="<?php echo (int) $numeroRondas; ?>" required></p>
      <p><label for="numeroJugadores">Jugadores</label><input id="numeroJugadores" name="numeroJugadores" type="number" min="4" max="20" step="2" value="<?php echo (int) $numeroJugadores; ?>" required></p>
      <button type="submit" class="button">Generar liga de prueba</button>
    </form>
  </section>
</div>
</body>
</html>
