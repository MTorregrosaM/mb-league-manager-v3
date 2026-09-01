<head>
  <?php require_once("cabecera.php"); ?>
</head>
<body>
<?php
require_once("model/class.php");
require_once("config/config.php");
require_once("controller/controller.php");

$oControllerJuego = new controllerJuego();
$accionForm = $_POST["accionForm"] ?? 1;
$fIdJuego = (int) ($_POST["fIdJuego"] ?? 0);
$fDescJuego = trim((string) ($_POST["fDescJuego"] ?? ""));
$fIndActivo = (int) ($_POST["fIndActivo"] ?? 1);
$pagActual = max(1, (int) ($_POST["pagActual"] ?? 1));
$mensaje = "";
$mensajeClase = "mensaje-ok";
$mostrarAlta = false;
$mostrarEdicion = false;

$oUsuario = (new controllerUsuario())->recuperarDatosUsuario($_SESSION["usuario"] ?? 0);
if ($oUsuario === null || $oUsuario->rol !== "ADMIN") {
    echo "<p>No autorizado.</p>";
    exit;
}

if ($accionForm == 2) {
  if ($fDescJuego !== "") {
    $resultado = $oControllerJuego->altaNuevoJuego($fDescJuego, $fIndActivo);
    $mensaje = $resultado == 1 ? "Juego creado correctamente." : "No se pudo crear el juego. Comprueba que el nombre no esté repetido.";
    $mensajeClase = $resultado == 1 ? "mensaje-ok" : "mensaje-error";
  } else {
    $mostrarAlta = true;
  }
} elseif ($accionForm == 3) {
    $resultado = $oControllerJuego->borrarJuego((int) ($_POST["fIdJuegoBorrar"] ?? 0));
    $mensaje = $resultado ? "Juego eliminado correctamente." : "No se puede eliminar el juego porque está asociado a una liga o no existe.";
    $mensajeClase = $resultado ? "mensaje-ok" : "mensaje-error";
} elseif ($accionForm == 4) {
    $mostrarEdicion = true;
} elseif ($accionForm == 5) {
    $resultado = $oControllerJuego->modificarJuego($fIdJuego, $fDescJuego, $fIndActivo);
    $mensaje = $resultado == 1 ? "Juego modificado correctamente." : "No se pudo modificar el juego.";
    $mensajeClase = $resultado == 1 ? "mensaje-ok" : "mensaje-error";
}

$busqueda = $_POST["busqueda"] ?? "";
$filtroActivo = $_POST["fIndActivo"] ?? "";
$fFecIni = $_POST["fFecIni"] ?? "";
$total = $oControllerJuego->paginadorJuegos($busqueda, $filtroActivo, $fFecIni);
$numPaginas = max(1, (int) ceil($total / 10));
$pagActual = min($pagActual, $numPaginas);
$juegos = $oControllerJuego->recuperarListadoJuegos($busqueda, ($pagActual - 1) * 10, $filtroActivo, $fFecIni);
?>
<div id="contenedor-principal">
  <?php require_once("menu.php"); ?>
  <h2 class="h2"><span>Gestión de juegos</span></h2>
  <?php if ($mostrarAlta) { ?>
  <div id="form">
    <h3>Alta de nuevo juego</h3>
    <form method="POST" action="gestion-juegos.php">
      <input type="hidden" name="accionForm" value="2" />
      <p><label for="fDescJuego">Nombre: </label>
      <input type="text" name="fDescJuego" id="fDescJuego" maxlength="100" required /></p>
      <p><label for="fIndActivoAlta">Activo: </label>
      <select name="fIndActivo" id="fIndActivoAlta">
        <option value="1" selected>S&iacute;</option>
        <option value="0">No</option>
      </select></p>
      <p><input type="submit" value="Dar de alta" id="formButton" class="submit-button" /></p>
    </form>
  </div>
  <div id="div-volver"><a href="gestion-juegos.php" class="btn-volver">Volver</a></div>
  <?php } elseif ($mostrarEdicion) { ?>
  <div id="form">
    <h3>Editar juego</h3>
    <form method="POST" action="gestion-juegos.php">
      <input type="hidden" name="accionForm" value="5" />
      <input type="hidden" name="fIdJuego" value="<?php echo $fIdJuego; ?>" />
      <p><label for="fDescJuego">Nombre: </label>
      <input type="text" name="fDescJuego" id="fDescJuego" maxlength="100" required value="<?php echo htmlspecialchars($fDescJuego, ENT_QUOTES, "UTF-8"); ?>" /></p>
      <p><label for="fIndActivoEdicion">Activo: </label>
      <select name="fIndActivo" id="fIndActivoEdicion">
        <option value="1" <?php echo $fIndActivo === 1 ? "selected" : ""; ?>>S&iacute;</option>
        <option value="0" <?php echo $fIndActivo === 0 ? "selected" : ""; ?>>No</option>
      </select></p>
      <p><input type="submit" value="Guardar" id="formButton" class="submit-button" /></p>
    </form>
  </div>
  <div id="div-volver"><a href="gestion-juegos.php" class="btn-volver">Volver</a></div>
  <?php } else { ?>
  <p>Desde este panel puede dar de alta, modificar o eliminar cualquier juego de la aplicaci&oacute;n.</p>
  <div id="buscador">
    <form name="buscadorjuegos" id="buscadorjuegos" method="POST" action="">
      <input type="hidden" name="accionForm" id="accionForm" value="1"/>
      <input type="hidden" name="pagActual" id="pagActual" value="1" />
      <label for="busqueda">Nombre: </label>
      <input type="text" name="busqueda" id="busqueda" value="<?php echo htmlspecialchars($busqueda, ENT_QUOTES, "UTF-8"); ?>" />
      <label for="fIndActivo">Activo: </label>
      <select name="fIndActivo" id="fIndActivo">
        <option value="">Todos</option>
        <option value="1" <?php echo isset($_POST["fIndActivo"]) && $_POST["fIndActivo"] === "1" ? "selected" : ""; ?>>S&iacute;</option>
        <option value="0" <?php echo isset($_POST["fIndActivo"]) && $_POST["fIndActivo"] === "0" ? "selected" : ""; ?>>No</option>
      </select>
      <label for="fFecIni">Fecha: </label>
      <input type="text" name="fFecIni" id="fFecIni" class="fFecIniForm" maxlength="10" value="<?php echo htmlspecialchars($fFecIni, ENT_QUOTES, "UTF-8"); ?>" />
      <input type="submit" value="Buscar" id="formButton" class="submit-button"/>
    </form>
  </div>
  <?php if ($mensaje !== "") { echo "<p id=\"" . $mensajeClase . "\">" . htmlspecialchars($mensaje, ENT_QUOTES, "UTF-8") . "</p>"; } ?>
  <div id="btn-alta">
    <form name="btnFormAltaJuego" id="btnFormAltaJuego" method="POST" action="">
      <input type="hidden" name="accionForm" value="2" />
      <a href="#" class="button" id="btnAltaJuego"><img src="recursos/img/icon_nuevo.png" alt="Nuevo" /> Alta de nuevo juego</a>
    </form>
  </div>
  <?php if (count($juegos) > 0) { ?>
  <table class="table-6">
    <tr class="primerafilatabla"><th>Juego</th><th class="align-center">Activo</th><th class="align-center">Alta</th><th class="td-acciones"></th></tr>
    <?php foreach ($juegos as $juego) { ?>
    <tr>
      <td><?php echo htmlspecialchars($juego[1], ENT_QUOTES, "UTF-8"); ?></td>
      <td class="align-center"><?php echo $juego[2] == 1 ? "SI" : "NO"; ?></td>
      <td class="align-center"><?php echo htmlspecialchars($juego[3] ?? "", ENT_QUOTES, "UTF-8"); ?></td>
      <td class="align-center td-acciones">
        <form method="GET" action="gestion-facciones.php" class="form-btn-acciones">
          <input type="hidden" name="idJuego" value="<?php echo (int) $juego[0]; ?>" />
          <img src="recursos/img/icon_fases.png" title="Ver facciones del juego" alt="Ver facciones" class="btn-facciones" />
        </form>
        <form method="POST" action="gestion-juegos.php" class="form-btn-acciones">
          <input type="hidden" name="accionForm" value="3" />
          <input type="hidden" name="fIdJuegoBorrar" value="<?php echo (int) $juego[0]; ?>" />
          <img src="recursos/img/icon_eliminar.png" title="Eliminar juego" alt="Eliminar juego" class="btn-borrar" />
        </form>
        <form method="POST" action="gestion-juegos.php" class="form-btn-acciones">
          <input type="hidden" name="accionForm" value="4" />
          <input type="hidden" name="fIdJuego" value="<?php echo (int) $juego[0]; ?>" />
          <input type="hidden" name="fDescJuego" value="<?php echo htmlspecialchars($juego[1], ENT_QUOTES, "UTF-8"); ?>" />
          <input type="hidden" name="fIndActivo" value="<?php echo (int) $juego[2]; ?>" />
          <img src="recursos/img/icon_editar.png" title="Editar juego" alt="Editar juego" class="btn-editar-reg" />
        </form>
      </td>
    </tr>
    <?php } ?>
  </table>
  <?php } else { echo "<p>No hay juegos.</p>"; } ?>
  <?php } ?>
</div>
<script>
  $(function() {
    $(".fFecIniForm").datepicker({
      showOn: "both",
      buttonImage: "recursos/img/calendar.png",
      buttonImageOnly: true,
      buttonText: "Selecciona una fecha",
      dateFormat: "dd-mm-yy",
      firstDay: 1,
      changeMonth: true,
      changeYear: true,
      yearRange: "1950:2035"
    });
    $("#btnAltaJuego").click(function(evento) {
      evento.preventDefault();
      $("#btnFormAltaJuego").submit();
    });
    $(".btn-facciones, .btn-editar-reg").click(function() {
      $(this).closest("form").submit();
    });
    $(".btn-borrar").click(function() {
      if (confirm("&iquest;Est&aacute; seguro de realizar esta acci&oacute;n?")) {
        $(this).closest("form").submit();
      }
    });
  });
</script>
</body>
</html>
