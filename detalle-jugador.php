<?php
require_once __DIR__ . "/model/class.php";
require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/controller/controller.php";

function escaparDetalle($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, "UTF-8");
}

function nombreJugadorDetalle($oJugador, $idJugador) {
    if ($oJugador === null) {
        return "Jugador #" . (int) $idJugador;
    }
    return trim((string) $oJugador->nick) !== "" ? $oJugador->nick : "Jugador #" . (int) $idJugador;
}

function enlaceListaDetalle($urlDocumento, $idLiga, $numFase) {
    if (filter_var($urlDocumento, FILTER_VALIDATE_URL)) {
        return $urlDocumento;
    }

    return "assets/docs/ligas/"
      . (int) $idLiga . "/"
        . (int) $numFase . "/"
        . rawurlencode(basename((string) $urlDocumento));
}

$idJugador = filter_input(INPUT_GET, "fIdJugador", FILTER_VALIDATE_INT);
if (!$idJugador || $idJugador < 1) {
    http_response_code(400);
    exit("Jugador no válido");
}

$oControllerJugador = new controllerJugador();
$oControllerResultado = new controllerResultado();
$oControllerLiga = new controllerLiga();
$oJugador = $oControllerJugador->recuperarDatosJugador((int) $idJugador);

if ($oJugador === null) {
    http_response_code(404);
    exit("Jugador no encontrado");
}

$idLiga = (int) $oJugador->idLiga;
$oLiga = $oControllerLiga->recuperarDatosLiga($idLiga);
$arrResultados = $oControllerResultado->recuperarDetalleResultadosJugador($idLiga, (int) $idJugador);
$arrResultados = is_array($arrResultados) ? $arrResultados : array();
$arrListas = $oControllerJugador->recuperarListadoListas((int) $idJugador, $idLiga);
$arrListas = is_array($arrListas) ? $arrListas : array();
$nombreJugador = nombreJugadorDetalle($oJugador, $idJugador);
$nombreLiga = $oLiga !== null ? (string) $oLiga->nombre : "";
$estadisticasJugador = array("victorias" => 0, "empates" => 0, "derrotas" => 0, "jugados" => 0);
$estadisticasFases = array();
foreach ($arrResultados as $resultado) {
  if ($resultado[10] === null || $resultado[10] === "") {
    continue;
  }

  $esJugador1 = (int) $resultado[4] === (int) $idJugador;
  $puntosJugador = (int) ($esJugador1 ? $resultado[6] : $resultado[7]);
  $puntosContrincante = (int) ($esJugador1 ? $resultado[7] : $resultado[6]);
  $fase = (int) $resultado[2];
  if (!isset($estadisticasFases[$fase])) {
    $estadisticasFases[$fase] = array("jugados" => 0, "victorias" => 0, "empates" => 0, "derrotas" => 0);
  }

  $estadisticasJugador["jugados"]++;
  $estadisticasFases[$fase]["jugados"]++;
  if ($puntosJugador > $puntosContrincante) {
    $estadisticasJugador["victorias"]++;
    $estadisticasFases[$fase]["victorias"]++;
  } elseif ($puntosJugador < $puntosContrincante) {
    $estadisticasJugador["derrotas"]++;
    $estadisticasFases[$fase]["derrotas"]++;
  } else {
    $estadisticasJugador["empates"]++;
    $estadisticasFases[$fase]["empates"]++;
  }
}
ksort($estadisticasFases);
?>
<!doctype html>
<html lang="es" data-bs-theme="dark">
<head>
  <?php require_once __DIR__ . "/cabecera.php"; ?>
  <title><?php echo escaparDetalle($nombreJugador); ?> - MB League</title>
</head>
<body>
<div id="contenedor-principal">
  <?php require_once "menu.php"; ?>
  <div class="detalle-jugador-cabecera">
    <h2 class="h2"><span><?php echo escaparDetalle($nombreJugador); ?></span></h2>
    <p class="detalle-jugador-liga"><?php echo escaparDetalle($nombreLiga); ?></p>
  </div>

  <?php if ($estadisticasJugador["jugados"] >= 2) { ?>
  <section class="detalle-jugador-graficos" aria-label="Resumen gráfico de enfrentamientos">
    <article class="detalle-jugador-grafico">
      <h3>Balance por fase</h3>
      <?php if (count($estadisticasFases) === 0) { ?>
        <p class="detalle-jugador-grafico-vacio">Sin enfrentamientos jugados.</p>
      <?php } else { ?>
        <div class="grafico-fases" role="img" aria-label="Victorias, empates y derrotas por fase">
          <?php foreach ($estadisticasFases as $fase => $estadisticasFase) {
              $jugadosFase = max(1, $estadisticasFase["jugados"]);
              $victoriasWidth = round($estadisticasFase["victorias"] * 100 / $jugadosFase, 2);
              $empatesWidth = round($estadisticasFase["empates"] * 100 / $jugadosFase, 2);
              $derrotasWidth = max(0, 100 - $victoriasWidth - $empatesWidth);
          ?>
            <div class="grafico-fase-fila">
              <span>Fase <?php echo (int) $fase; ?></span>
              <div class="grafico-fase-barra">
                <i class="grafico-victorias" style="width: <?php echo $victoriasWidth; ?>%"></i>
                <i class="grafico-empates" style="width: <?php echo $empatesWidth; ?>%"></i>
                <i class="grafico-derrotas" style="width: <?php echo $derrotasWidth; ?>%"></i>
              </div>
              <strong><?php echo (int) $estadisticasFase["jugados"]; ?></strong>
            </div>
          <?php } ?>
        </div>
        <div class="grafico-leyenda"><span><i class="grafico-victorias"></i>Victorias</span><span><i class="grafico-empates"></i>Empates</span><span><i class="grafico-derrotas"></i>Derrotas</span></div>
      <?php } ?>
    </article>

    <article class="detalle-jugador-grafico">
      <h3>Resultado global</h3>
      <?php $jugados = max(1, $estadisticasJugador["jugados"]); ?>
      <div class="grafico-dona-contenedor">
        <div class="grafico-dona" style="--victorias: <?php echo round($estadisticasJugador["victorias"] * 100 / $jugados, 2); ?>; --empates: <?php echo round($estadisticasJugador["empates"] * 100 / $jugados, 2); ?>;" role="img" aria-label="Resultado global de los enfrentamientos"></div>
        <div class="grafico-dona-total"><strong><?php echo (int) $estadisticasJugador["jugados"]; ?></strong><span>jugados</span></div>
      </div>
      <div class="grafico-resumen">
        <span><strong><?php echo (int) $estadisticasJugador["victorias"]; ?></strong>Victorias</span>
        <span><strong><?php echo (int) $estadisticasJugador["empates"]; ?></strong>Empates</span>
        <span><strong><?php echo (int) $estadisticasJugador["derrotas"]; ?></strong>Derrotas</span>
      </div>
    </article>
  </section>
  <?php } ?>

  <section class="detalle-jugador-bloque">
    <h3>Resultados</h3>
    <div class="detalle-jugador-tabla-wrap">
      <table class="detalle-jugador-tabla">
        <thead>
          <tr>
            <th>Fase</th>
            <th>Ronda</th>
            <th>Fecha</th>
            <th>Contrincante</th>
            <th>Resultado</th>
            <th>Estado</th>
            <th>Victoria concedida</th>
            <th>Victoria sector</th>
          </tr>
        </thead>
        <tbody>
        <?php if (count($arrResultados) === 0) { ?>
          <tr><td colspan="8">No hay resultados.</td></tr>
        <?php } else { ?>
          <?php foreach ($arrResultados as $resultado) {
              $esJugador1 = (int) $resultado[4] === (int) $idJugador;
              $idContrincante = $esJugador1 ? (int) $resultado[5] : (int) $resultado[4];
              $oContrincante = $oControllerJugador->recuperarDatosJugador($idContrincante);
              $resultadoJugador = $esJugador1 ? $resultado[6] : $resultado[7];
              $resultadoContrincante = $esJugador1 ? $resultado[7] : $resultado[6];
              $claseResultado = ((int) $resultadoJugador > (int) $resultadoContrincante) ? "resultado-victoria" : (((int) $resultadoJugador < (int) $resultadoContrincante) ? "resultado-derrota" : "");
              $victoriaConcedida = (int) $resultado[14] === (int) $idJugador ? "Sí" : "No";
              $victoriaSector = $resultado[15] === null || $resultado[15] === "" ? "-" : $resultado[15];
              $estado = (int) $resultado[11] === 1 ? "Validado" : "Pendiente";
          ?>
          <tr>
            <td><?php echo (int) $resultado[2]; ?></td>
            <td><?php echo (int) $resultado[3]; ?></td>
            <td><?php echo escaparDetalle($resultado[10]); ?></td>
            <td><?php echo escaparDetalle(nombreJugadorDetalle($oContrincante, $idContrincante)); ?></td>
            <td class="<?php echo $claseResultado; ?>"><span class="<?php echo $claseResultado; ?>"><?php echo escaparDetalle($resultadoJugador); ?></span>-<span class="<?php echo $claseResultado; ?>"><?php echo escaparDetalle($resultadoContrincante); ?></span></td>
            <td><?php echo $estado; ?></td>
            <td><?php echo $victoriaConcedida; ?></td>
            <td><?php echo escaparDetalle($victoriaSector); ?></td>
          </tr>
          <?php } ?>
        <?php } ?>
        </tbody>
      </table>
    </div>
  </section>

  <section class="detalle-jugador-bloque">
    <h3>Listas</h3>
    <div class="detalle-jugador-tabla-wrap">
      <table class="detalle-jugador-tabla detalle-jugador-listas">
        <thead>
          <tr><th>Fase</th><th>Bando</th><th>Fecha</th><th>Documento</th></tr>
        </thead>
        <tbody>
        <?php if (count($arrListas) === 0) { ?>
          <tr><td colspan="4">No hay listas disponibles.</td></tr>
        <?php } else { ?>
          <?php foreach ($arrListas as $lista) {
              $hrefLista = enlaceListaDetalle($lista[2], $idLiga, $lista[1]);
          ?>
          <tr>
            <td><?php echo (int) $lista[1]; ?></td>
            <td><?php echo escaparDetalle($lista[3]); ?></td>
            <td><?php echo escaparDetalle($lista[4]); ?></td>
            <td><a href="<?php echo escaparDetalle($hrefLista); ?>" target="_blank" rel="noopener" class="link-grid">Descargar</a></td>
          </tr>
          <?php } ?>
        <?php } ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
</body>
</html>
