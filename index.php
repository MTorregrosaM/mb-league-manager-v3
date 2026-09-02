<?php
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

require_once "cabecera.php";
require_once "model/class.php";
require_once "config/config.php";
require_once "controller/controller.php";

function escaparIndex($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, "UTF-8");
}

function ordenarRanking($a, $b) {
    return ((float) ($b[1] ?? 0)) <=> ((float) ($a[1] ?? 0));
}

function ordenarRankingAscendente($a, $b) {
    return ((float) ($a[4] ?? 0)) <=> ((float) ($b[4] ?? 0));
}

function pintarRanking($filas, $columnas, $orden, $columnasDatos) {
    if (!is_array($filas) || count($filas) === 0) {
        echo '<tr><td colspan="' . (int) $columnas . '">No hay resultados</td></tr>';
        return;
    }
    if ($orden !== null) {
        usort($filas, $orden);
    }
    foreach (array_slice($filas, 0, 10) as $indice => $fila) {
        echo '<tr class="bg-' . (int) $indice . '">';
      foreach ($columnasDatos as $columna) {
        echo '<td>' . escaparIndex($fila[$columna] ?? '') . '</td>';
        }
        echo '</tr>';
    }
}

  function pintarRankingResultados($filas) {
    if (!is_array($filas) || count($filas) === 0) {
      echo '<tr><td colspan="4">No hay resultados</td></tr>';
      return;
    }
    foreach (array_slice($filas, 0, 10) as $indice => $fila) {
      $clasePosicion = $indice < 3 ? ' ranking-position-' . (int) ($indice + 1) : '';
      $idJugador = (int) ($fila[3] ?? 0);
      $nombreJugador = escaparIndex($fila[0] ?? '');
      $enlaceJugador = $idJugador > 0 ? '<a class="link-grid" href="detalle-jugador.php?fIdJugador=' . $idJugador . '">' . $nombreJugador . '</a>' : $nombreJugador;
      echo '<tr class="' . trim($clasePosicion) . '"><td><span class="ranking-position-badge">' . (int) ($indice + 1) . '</span></td><td>' . $enlaceJugador . '</td><td>' . escaparIndex($fila[1] ?? 0) . '</td><td>' . escaparIndex($fila[2] ?? 0) . '</td></tr>';
    }
  }

  function pintarDeportividad($filas, $idLiga) {
    if (!is_array($filas) || count($filas) === 0) {
      echo '<tr><td colspan="4">No hay resultados</td></tr>';
      return;
    }
    usort($filas, "ordenarRankingAscendente");
    foreach (array_slice($filas, 0, 10) as $fila) {
      echo '<tr><td>' . escaparIndex($fila[0] ?? '') . '</td>';
      echo '<td>' . escaparIndex($fila[1] ?? '') . '</td><td>' . escaparIndex($fila[5] ?? '') . '</td><td>' . escaparIndex($fila[2] ?? '') . '</td></tr>';
    }
  }

$oControllerEnfrentamiento = new controllerEnfrentamiento();
$oControllerLiga = new controllerLiga();
$oControllerUsuario = new controllerUsuario();
$ligasUsuario = $oControllerUsuario->recuperarLigasUsuario($_SESSION["usuario"] ?? "");
$arrLigas = $oControllerLiga->recuperarSelectLigas(null, true, null, true);
$arrLigas = is_array($arrLigas) ? $arrLigas : array();
$fIdLiga = isset($_POST["fIdLiga"]) ? (int) $_POST["fIdLiga"] : 0;
if ($fIdLiga <= 0 && count($arrLigas) > 0) {
    foreach ($arrLigas as $liga) {
        if ((int) ($liga[2] ?? 0) === 1) {
            $fIdLiga = (int) $liga[0];
            break;
        }
    }
    if ($fIdLiga <= 0) {
        $fIdLiga = (int) $arrLigas[0][0];
    }
}

function pintarSectores($filas) {
  if (!is_array($filas) || count($filas) === 0) {
    echo '<tr><td colspan="3">No hay resultados</td></tr>';
    return;
  }
  $sectores = array();
  foreach ($filas as $fila) {
    $clave = ($fila[0] ?? '') . '|' . ($fila[1] ?? '');
    $sectores[$clave][] = $fila;
  }
  foreach ($sectores as $grupo) {
    $primero = $grupo[0];
    $nombre = escaparIndex($primero[2] ?? '');
    $victorias = (int) ($primero[3] ?? 0);
    if (isset($grupo[1])) {
      $segundo = $grupo[1];
      $victorias2 = (int) ($segundo[3] ?? 0);
      $nombre2 = escaparIndex($segundo[2] ?? '');
      $nombre = $victorias === $victorias2 ? 'EMPATE (' . $victorias . '-' . $victorias2 . ')' : ($victorias > $victorias2 ? $nombre . ' (' . $victorias . '-' . $victorias2 . ')' : $nombre2 . ' (' . $victorias2 . '-' . $victorias . ')');
    } else {
      $nombre .= ' (' . $victorias . '-0)';
    }
    echo '<tr><td class="align-center">' . escaparIndex($primero[0] ?? '') . '</td><td class="align-left">' . escaparIndex($primero[1] ?? '') . '</td><td>' . $nombre . '</td></tr>';
  }
}
$oLiga = $fIdLiga > 0 ? $oControllerLiga->recuperarDatosLiga($fIdLiga) : null;
?>
<html lang="es" data-bs-theme="dark">
<head><title>Competiciones - MB League</title></head>
<body>
<div id="contenedor-principal">
  <?php require_once "menu.php"; ?>
  <h2 class="h2"><span>Competiciones</span></h2>
  <div class="center">
    <form id="selectLiga" name="selectLiga" method="POST">
      <label for="fIdLiga" class="span-index">Liga</label>
      <select name="fIdLiga" id="fIdLiga" class="select-index">
        <option value="">Seleccione una liga</option>
        <?php foreach ($arrLigas as $liga) { ?>
          <option value="<?php echo (int) $liga[0]; ?>" <?php echo $fIdLiga === (int) $liga[0] ? "selected" : ""; ?>><?php echo escaparIndex($liga[1]); ?></option>
        <?php } ?>
      </select>
    </form>
  </div>
  <?php if ($oLiga !== null) { ?>
    <section class="index-main-ranking">
      <h2>Ranking de resultados</h2>
      <table><tr><th>Nº</th><th>Nick</th><th>Partidas jugadas</th><th>Resultado</th></tr><?php pintarRankingResultados($oControllerEnfrentamiento->recuperarRankingResultados($fIdLiga)); ?></table>
    </section>
    <section class="index-ranking-grid">
      <div class="div-ranking-<?php echo in_array((int) $oLiga->idJuego, array(1, 2), true) ? "dch" : "izq"; ?>"><h2>Top Puntuación Pintura</h2><table><tr><th>Nick</th><th>Media puntos pintura (partidas disputadas)</th></tr><?php pintarRanking($oControllerEnfrentamiento->recuperarRankingPuntosPintura($fIdLiga), 2, "ordenarRanking", array(0, 2)); ?></table></div>
      <div class="div-ranking-<?php echo in_array((int) $oLiga->idJuego, array(1, 2), true) ? "izq" : "dch"; ?> ranking-deportividad"><h2>Top Deportividad</h2><table><tr><th>Nick</th><th>Puntos</th><th>Puntos</th><th>Partidas</th></tr><?php pintarDeportividad($oControllerEnfrentamiento->recuperarRankingPuntosDeportividad($fIdLiga), $fIdLiga); ?></table></div>
      <?php if (in_array((int) $oLiga->idJuego, array(1, 2), true)) { ?>
        <div class="div-ranking-dch"><h2>Campaña por sectores</h2><table><tr><th>Fase</th><th>Sector</th><th>Bando dominante</th></tr><?php pintarSectores($oControllerEnfrentamiento->recuperarRankingSectores($fIdLiga, $oLiga->idJuego)); ?></table></div>
      <?php } ?>
      <?php if (in_array((int) $oLiga->idJuego, array(1, 2), true)) { ?>
        <div class="div-ranking-izq"><h2>Top Misiones Secundarias</h2><table><tr><th>Nick</th><th>Medallas</th></tr><?php pintarRanking($oControllerEnfrentamiento->recuperarRankingPuntosMisionesSec($fIdLiga), 2, "ordenarRanking", array(0, 1)); ?></table></div>
      <?php } ?>
    </section>
  <?php } elseif (count($arrLigas) === 0) { ?>
    <p class="center">No hay competiciones disponibles.</p>
  <?php } ?>
</div>
<script>
  document.getElementById("fIdLiga").addEventListener("change", function () {
    document.getElementById("selectLiga").submit();
  });
</script>
</body>
</html>
