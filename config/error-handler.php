<?php

require_once __DIR__ . '/log.class.php';

if (!function_exists('mostrarPaginaError')) {
    function mostrarPaginaError(int $codigo = 500, string $mensaje = 'Se ha producido un error inesperado.'): void {
        if (defined('MB_ERROR_PAGE_RENDERED')) {
            return;
        }
        define('MB_ERROR_PAGE_RENDERED', true);
        $codigoError = $codigo >= 400 ? $codigo : 500;
        $mensajeError = $mensaje;
        require dirname(__DIR__) . '/error.php';
    }
}

if (!defined('MB_ERROR_HANDLERS_REGISTERED')) {
    define('MB_ERROR_HANDLERS_REGISTERED', true);

    set_exception_handler(function (Throwable $exception): void {
        Log::getInstance()->trazaLog($exception, 'Excepción no controlada');
        mostrarPaginaError(500);
    });

    set_error_handler(function (int $severity, string $message, string $file, int $line): bool {
        if ($severity === E_DEPRECATED || $severity === E_USER_DEPRECATED) {
            return false;
        }
        if (!(error_reporting() & $severity)) {
            return false;
        }
        throw new ErrorException($message, 0, $severity, $file, $line);
    });

    register_shutdown_function(function (): void {
        $ultimoError = error_get_last();
        $erroresFatales = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR);
        if (is_array($ultimoError) && in_array($ultimoError['type'], $erroresFatales, true) && !defined('MB_ERROR_PAGE_RENDERED')) {
            mostrarPaginaError(500);
        }
    });
}
