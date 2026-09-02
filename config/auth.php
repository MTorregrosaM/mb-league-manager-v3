<?php

require_once __DIR__ . '/db.class.php';

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
    if (!empty($_SESSION['ultima_actividad']) && time() - $_SESSION['ultima_actividad'] > 1800) {
        $_SESSION = array();
        session_destroy();
        http_response_code(403);
        exit('Sesión expirada');
    }
    $_SESSION['ultima_actividad'] = time();

function csrfToken(): string {
    return $_SESSION['csrf_token'];
}

function validarCsrf(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        exit('Solicitud no válida');
    }
}

function usuarioPuedeAccederLiga(int $idLiga): bool {
    if ($idLiga <= 0 || empty($_SESSION['usuario'])) {
        return false;
    }
    if (($_SESSION['rol'] ?? '') === 'ADMIN') {
        return true;
    }

    $conexion = new conexBD();
    $resultado = $conexion->ejecutarConsultaPreparada(
        'SELECT 1 FROM mb_ligas_usuarios WHERE idUsuario = ? AND idLiga = ? LIMIT 1',
        'ii',
        array((int) $_SESSION['usuario'], $idLiga)
    );
    return is_array($resultado) && count($resultado) > 0;
}

function ligasPermitidasUsuario(): ?string {
    if (($_SESSION['rol'] ?? '') === 'ADMIN') {
        return null;
    }

    if (empty($_SESSION['usuario'])) {
        return '0';
    }

    $conexion = new conexBD();
    $resultado = $conexion->ejecutarConsultaPreparada(
        'SELECT idLiga FROM mb_ligas_usuarios WHERE idUsuario = ? ORDER BY idLiga',
        'i',
        array((int) $_SESSION['usuario'])
    );
    if (!is_array($resultado) || count($resultado) === 0) {
        return '0';
    }

    $idsLigas = array();
    foreach ($resultado as $fila) {
        $idsLigas[] = (int) $fila[0];
    }
    return implode(',', $idsLigas);
}

function exigirAccesoLiga(int $idLiga): void {
    if (!usuarioPuedeAccederLiga($idLiga)) {
        http_response_code(403);
        exit('Acceso no autorizado');
    }
}

function exigirAdministrador(): void {
    if (($_SESSION['rol'] ?? '') !== 'ADMIN') {
        http_response_code(403);
        exit('Acceso no autorizado');
    }
}

function validarAccesoLigaEnPeticion(): void {
    $valores = array();
    $nombresLiga = array(
        'fIdLiga',
        'fIdLigaBorrar',
        'fIdLigaEditar',
        'idLiga',
        'idLigaBorrar',
        'idLigaEditar',
        'ligaId'
    );

    foreach (array($_GET, $_POST) as $parametros) {
        foreach ($nombresLiga as $nombre) {
            if (array_key_exists($nombre, $parametros)) {
                $valores[] = $parametros[$nombre];
            }
        }
    }

    $idJugador = $_POST['fIdJugador'] ?? $_GET['fIdJugador'] ?? null;
    $hayLigaValida = false;
    foreach ($valores as $valor) {
        if ($valor !== null && $valor !== '' && filter_var($valor, FILTER_VALIDATE_INT) !== false && (int) $valor > 0) {
            $hayLigaValida = true;
            break;
        }
    }
    $ligaResueltaDesdeJugador = false;
    if (!$hayLigaValida && $idJugador !== null && filter_var($idJugador, FILTER_VALIDATE_INT) !== false && (int) $idJugador > 0) {
        $conexion = new conexBD();
        $resultado = $conexion->ejecutarConsultaPreparada(
            'SELECT idLiga FROM mb_jugadores WHERE idJugador = ? LIMIT 1',
            'i',
            array((int) $idJugador)
        );
        if (is_array($resultado) && count($resultado) > 0) {
            $valores[] = (int) $resultado[0][0];
            $ligaResueltaDesdeJugador = true;
        }
    }

    foreach ($valores as $valor) {
        if ($ligaResueltaDesdeJugador && (string) $valor === '0') {
            continue;
        }
        if (is_array($valor)) {
            http_response_code(403);
            exit('Solicitud no válida');
        }
        if ($valor !== null && $valor !== '' && filter_var($valor, FILTER_VALIDATE_INT) !== false) {
            exigirAccesoLiga((int) $valor);
        } elseif ($valor !== null && $valor !== '') {
            http_response_code(403);
            exit('Solicitud no válida');
        }
    }
}

if (($_SESSION['autorizado'] ?? 0) != 1 || empty($_SESSION['usuario'])) {
    http_response_code(403);
    exit('Forbidden');
}

validarCsrf();
validarAccesoLigaEnPeticion();
