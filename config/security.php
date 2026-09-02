<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params(array(
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
    ));
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function csrfTokenPublico(): string {
    return $_SESSION['csrf_token'];
}

function validarCsrfPublico(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        exit('Solicitud no válida');
    }
}

function exigirLigaActivaPublica($idLiga): void {
    if (filter_var($idLiga, FILTER_VALIDATE_INT) === false || (int) $idLiga <= 0) {
        http_response_code(400);
        exit('Liga no válida');
    }

    $conexion = new conexBD();
    $resultado = $conexion->ejecutarConsultaPreparada(
        'SELECT 1 FROM mb_ligas WHERE idLiga = ? AND indActivo = 1 LIMIT 1',
        'i',
        array((int) $idLiga)
    );
    if (!is_array($resultado) || count($resultado) === 0) {
        http_response_code(403);
        exit('Liga no disponible');
    }
}
