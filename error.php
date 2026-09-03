<?php
$status = isset($codigoError) ? (int) $codigoError : http_response_code();
if ($status < 400) {
    $status = 500;
}
http_response_code($status);
$detalleError = isset($mensajeError) && is_string($mensajeError) ? $mensajeError : 'Se ha producido un error inesperado.';
$detalleError = htmlspecialchars($detalleError, ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Error <?php echo $status; ?> - MB League</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lekton:wght@400;700&family=VT323&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/estilos.css?v=20260908" type="text/css" media="screen, projection">
</head>
<body class="login-page error-page">
    <div id="boxlogomodelbrush" aria-label="MB League">
        <span class="login-brand-mark" aria-hidden="true"></span>
        <span class="login-brand-name">MB League</span>
    </div>
    <main class="error-panel" role="main">
        <p class="error-code">ERROR <?php echo $status; ?></p>
        <h1>Vaya tela, algo se ha roto<br></h1>
        <p><?php echo $detalleError; ?></p>
        <a class="button btn btn-primary" href="index.php">Volver</a>
    </main>
    <?php require __DIR__ . '/footer.php'; ?>
</body>
</html>
