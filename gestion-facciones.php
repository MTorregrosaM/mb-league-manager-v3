<head>
  <?php require_once("cabecera.php"); ?>
</head>
<body>
<?php
require_once("model/class.php");
require_once("config/config.php");
require_once("controller/controller.php");

$idJuego = (int) ($_GET["idJuego"] ?? $_POST["idJuego"] ?? 0);
$accionForm = (int) ($_POST["accionForm"] ?? 1);
$fIdFaccion = (int) ($_POST["fIdFaccion"] ?? 0);
$fDescFaccion = trim((string) ($_POST["fDescFaccion"] ?? ""));
$fIndActivo = (int) ($_POST["fIndActivo"] ?? 1);
$busqueda = trim((string) ($_POST["busqueda"] ?? ""));
$mensaje = "";
$mensajeClase = "mensaje-ok";
$mostrarAlta = false;
$mostrarEdicion = false;
$oUsuario = (new controllerUsuario())->recuperarDatosUsuario($_SESSION["usuario"] ?? 0);
$oControllerJuego = new controllerJuego();
$oControllerFaccion = new controllerFaccion();
$oJuego = $oControllerJuego->recuperarDatosJuego($idJuego);

if ($oUsuario === null || $oUsuario->rol !== "ADMIN" || $oJuego === null) {
    echo "<p>No autorizado o juego inexistente.</p>";
    exit;
}

if ($accionForm == 2) {
  if ($fDescFaccion !== "") {
    $resultado = $oControllerFaccion->altaNuevaFaccion($idJuego, $fDescFaccion);
    $mensaje = $resultado == 1 ? "Facción creada correctamente." : "No se pudo crear la facción. Comprueba que no esté repetida.";
    $mensajeClase = $resultado == 1 ? "mensaje-ok" : "mensaje-error";
  } else {
    $mostrarAlta = true;
  }
} elseif ($accionForm == 3) {
    $resultado = $oControllerFaccion->borrarFaccion((int) ($_POST["fIdFaccionBorrar"] ?? 0));
    $mensaje = $resultado ? "Facción eliminada correctamente." : "No se puede eliminar porque la facción está asignada a un jugador o no existe.";
    $mensajeClase = $resultado ? "mensaje-ok" : "mensaje-error";
} elseif ($accionForm == 4) {
  $mostrarEdicion = true;
} elseif ($accionForm == 5) {
  $resultado = $oControllerFaccion->modificarFaccion($fIdFaccion, $fDescFaccion, $fIndActivo);
    $mensaje = $resultado == 1 ? "Facción modificada correctamente." : "No se pudo modificar la facción.";
  $mensajeClase = $resultado == 1 ? "mensaje-ok" : "mensaje-error";
}

$facciones = $oControllerFaccion->recuperarListadoFacciones($idJuego, $busqueda);
?>
<div id="contenedor-principal">
  <?php require_once("menu.php"); ?>
  <h2 class="h2"><span>Facciones de <?php echo htmlspecialchars($oJuego->descJuego, ENT_QUOTES, "UTF-8"); ?></span></h2>
  <?php if ($mostrarAlta) { ?>
  <div id="form">
    <h3>Alta de nueva facci&oacute;n</h3>
    <form method="POST" action="gestion-facciones.php">
      <input type="hidden" name="idJuego" value="<?php echo $idJuego; ?>" />
      <input type="hidden" name="accionForm" value="2" />
      <p><label for="fDescFaccion">Nombre: </label>
      <input type="text" name="fDescFaccion" id="fDescFaccion" maxlength="100" required /></p>
      <p><input type="submit" value="Dar de alta" id="formButton" class="submit-button" /></p>
    </form>
  </div>
  <div id="div-volver"><a href="gestion-facciones.php?idJuego=<?php echo $idJuego; ?>" class="btn-volver">Volver</a></div>
  <?php } elseif ($mostrarEdicion) { ?>
  <div id="form">
    <h3>Editar facci&oacute;n</h3>
    <form method="POST" action="gestion-facciones.php">
      <input type="hidden" name="idJuego" value="<?php echo $idJuego; ?>" />
      <input type="hidden" name="accionForm" value="5" />
      <input type="hidden" name="fIdFaccion" value="<?php echo $fIdFaccion; ?>" />
      <p><label for="fDescFaccion">Nombre: </label>
      <input type="text" name="fDescFaccion" id="fDescFaccion" maxlength="100" required value="<?php echo htmlspecialchars($fDescFaccion, ENT_QUOTES, "UTF-8"); ?>" /></p>
      <p><label for="fIndActivoEdicion">Activo: </label>
      <select name="fIndActivo" id="fIndActivoEdicion">
        <option value="1" <?php echo $fIndActivo === 1 ? "selected" : ""; ?>>S&iacute;</option>
        <option value="0" <?php echo $fIndActivo === 0 ? "selected" : ""; ?>>No</option>
      </select></p>
      <p><input type="submit" value="Guardar" id="formButton" class="submit-button" /></p>
    </form>
  </div>
  <div id="div-volver"><a href="gestion-facciones.php?idJuego=<?php echo $idJuego; ?>" class="btn-volver">Volver</a></div>
  <?php } else { ?>
  <p>Desde este panel puede dar de alta, modificar o eliminar cualquier facci&oacute;n del juego.</p>
  <div id="buscador">
    <form name="buscadorfacciones" id="buscadorfacciones" method="POST" action="">
      <input type="hidden" name="idJuego" value="<?php echo $idJuego; ?>" />
      <input type="hidden" name="accionForm" value="1" />
      <label for="busqueda">Nombre: </label>
      <input type="text" name="busqueda" id="busqueda" value="<?php echo htmlspecialchars($busqueda, ENT_QUOTES, "UTF-8"); ?>" />
      <input type="submit" value="Buscar" id="formButton" class="submit-button" />
    </form>
  </div>
  <?php if ($mensaje !== "") { echo "<p id=\"" . $mensajeClase . "\">" . htmlspecialchars($mensaje, ENT_QUOTES, "UTF-8") . "</p>"; } ?>
  <div id="btn-alta">
    <form method="POST" action="gestion-facciones.php">
      <input type="hidden" name="idJuego" value="<?php echo $idJuego; ?>" />
      <input type="hidden" name="accionForm" value="2" />
      <a href="#" class="button" id="btnAltaFaccion"><img src="recursos/img/icon_nuevo.png" alt="Nuevo" /> Alta de nueva facci&oacute;n</a>
    </form>
  </div>
  <?php if (count($facciones) > 0) { ?>
  <table class="table-6">
    <tr class="primerafilatabla"><th>Facción</th><th class="align-center">Activo</th><th class="td-acciones"></th></tr>
    <?php foreach ($facciones as $faccion) { ?>
    <tr>
      <td><?php echo htmlspecialchars($faccion[2], ENT_QUOTES, "UTF-8"); ?></td>
      <td class="align-center"><?php echo $faccion[3] == 1 ? "SI" : "NO"; ?></td>
      <td class="align-center td-acciones">
        <form method="POST" action="gestion-facciones.php" class="form-btn-acciones">
          <input type="hidden" name="idJuego" value="<?php echo $idJuego; ?>" />
          <input type="hidden" name="accionForm" value="3" />
          <input type="hidden" name="fIdFaccionBorrar" value="<?php echo (int) $faccion[0]; ?>" />
          <img src="recursos/img/icon_eliminar.png" title="Eliminar facción" alt="Eliminar facción" class="btn-borrar" />
        </form>
        <form method="POST" action="gestion-facciones.php" class="form-btn-acciones">
          <input type="hidden" name="idJuego" value="<?php echo $idJuego; ?>" />
          <input type="hidden" name="accionForm" value="4" />
          <input type="hidden" name="fIdFaccion" value="<?php echo (int) $faccion[0]; ?>" />
          <input type="hidden" name="fDescFaccion" value="<?php echo htmlspecialchars($faccion[2], ENT_QUOTES, "UTF-8"); ?>" />
          <input type="hidden" name="fIndActivo" value="<?php echo (int) $faccion[3]; ?>" />
          <img src="recursos/img/icon_editar.png" title="Editar facción" alt="Editar facción" class="btn-editar-reg" />
        </form>
      </td>
    </tr>
    <?php } ?>
  </table>
  <?php } else { echo "<p>No hay facciones para este juego.</p>"; } ?>
  <div id="div-volver"><a href="gestion-juegos.php" class="btn-volver">Volver</a></div>
  <?php } ?>
</div>
<script>
  $(function() {
    $("#btnAltaFaccion").click(function(evento) {
      evento.preventDefault();
      $(this).closest("form").submit();
    });
    $(".btn-editar-reg").click(function() {
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
