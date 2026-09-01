<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params(array(
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
    ));
    session_start();
}

if (($_SESSION['autorizado'] ?? 0) != 1) {
    http_response_code(403);
    exit('Forbidden');
}
