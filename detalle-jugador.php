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

    return "recursos/docs/ligas/"
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
$oControllerEnfrentamiento = new controllerEnfrentamiento();
$oControllerLiga = new controllerLiga();
$oJugador = $oControllerJugador->recuperarDatosJugador((int) $idJugador);

if ($oJugador === null) {
    http_response_code(404);
    exit("Jugador no encontrado");
}

$idLiga = (int) $oJugador->idLiga;
$oLiga = $oControllerLiga->recuperarDatosLiga($idLiga);
$arrEnfrentamientos = $oControllerEnfrentamiento->recuperarDetalleEnfrentamientosJugador($idLiga, (int) $idJugador);
$arrEnfrentamientos = is_array($arrEnfrentamientos) ? $arrEnfrentamientos : array();
$arrListas = $oControllerJugador->recuperarListadoListas((int) $idJugador, $idLiga);
$arrListas = is_array($arrListas) ? $arrListas : array();
$nombreJugador = nombreJugadorDetalle($oJugador, $idJugador);
$nombreLiga = $oLiga !== null ? (string) $oLiga->nombre : "";
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

  <section class="detalle-jugador-bloque">
    <h3>Enfrentamientos</h3>
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
        <?php if (count($arrEnfrentamientos) === 0) { ?>
          <tr><td colspan="8">No hay enfrentamientos.</td></tr>
        <?php } else { ?>
          <?php foreach ($arrEnfrentamientos as $enfrentamiento) {
              $esJugador1 = (int) $enfrentamiento[4] === (int) $idJugador;
              $idContrincante = $esJugador1 ? (int) $enfrentamiento[5] : (int) $enfrentamiento[4];
              $oContrincante = $oControllerJugador->recuperarDatosJugador($idContrincante);
              $resultadoJugador = $esJugador1 ? $enfrentamiento[6] : $enfrentamiento[7];
              $resultadoContrincante = $esJugador1 ? $enfrentamiento[7] : $enfrentamiento[6];
              $victoriaConcedida = (int) $enfrentamiento[14] === (int) $idJugador ? "Sí" : "No";
              $victoriaSector = $enfrentamiento[15] === null || $enfrentamiento[15] === "" ? "-" : $enfrentamiento[15];
              $estado = (int) $enfrentamiento[11] === 1 ? "Validado" : "Pendiente";
          ?>
          <tr>
            <td><?php echo (int) $enfrentamiento[2]; ?></td>
            <td><?php echo (int) $enfrentamiento[3]; ?></td>
            <td><?php echo escaparDetalle($enfrentamiento[10]); ?></td>
            <td><?php echo escaparDetalle(nombreJugadorDetalle($oContrincante, $idContrincante)); ?></td>
            <td><?php echo escaparDetalle($resultadoJugador) . "-" . escaparDetalle($resultadoContrincante); ?></td>
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
