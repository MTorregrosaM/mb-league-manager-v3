<?php

function maximoPuntosFowPorResultado($resultadoRadio) {
    if ((int) $resultadoRadio === 3) {
        return 5;
    }

    if ((int) $resultadoRadio === 0) {
        return 4;
    }

    return 3;
}

function validarPuntosFow($puntos, $resultadoRadio) {
    if (filter_var($puntos, FILTER_VALIDATE_INT) === false) {
        return false;
    }

    $puntos = (int) $puntos;
    return $puntos >= 0 && $puntos <= maximoPuntosFowPorResultado($resultadoRadio);
}

function estadoResultadoFow($resultadoRadio) {
    $resultadoRadio = (int) $resultadoRadio;
    return in_array($resultadoRadio, array(0, 1, 3), true) ? $resultadoRadio : 1;
}

function calcularMarcadorFow($puntosJugador1, $puntosJugador2) {
    if (!validarPuntosFow($puntosJugador1, 3) || !validarPuntosFow($puntosJugador2, 3)) {
        return null;
    }

    if ((int) $puntosJugador1 === (int) $puntosJugador2) {
        if ((int) $puntosJugador1 > 3) {
            return null;
        }

        return array((int) $puntosJugador1, (int) $puntosJugador2);
    }

    return ((int) $puntosJugador1 > (int) $puntosJugador2)
        ? array(5, 4)
        : array(4, 5);
}

function resultadoRadioDesdePuntuacionFow($puntosJugador1, $puntosJugador2) {
    if ((int) $puntosJugador1 > (int) $puntosJugador2) {
        return 3;
    }

    if ((int) $puntosJugador1 < (int) $puntosJugador2) {
        return 0;
    }

    return 1;
}

