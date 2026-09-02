<?php

function enviarCorreoSeguro(array $destinatarios, string $asunto, string $cuerpo): bool
{
    $destinatariosValidos = array();
    foreach ($destinatarios as $destinatario) {
        $destinatario = filter_var($destinatario, FILTER_VALIDATE_EMAIL);
        if ($destinatario !== false) {
            $destinatariosValidos[] = $destinatario;
        }
    }

    if (count($destinatariosValidos) === 0) {
        return false;
    }

    $asunto = str_replace(array("\r", "\n"), '', $asunto);
    $cabeceras = array(
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: Rangers Modelbrush <no-reply@modelbrush.com>',
        'Reply-To: Rangers Modelbrush <hola@modelbrush.com>',
        'Bcc: hola@modelbrush.com'
    );

    return mail(implode(',', $destinatariosValidos), $asunto, $cuerpo, implode("\r\n", $cabeceras));
}
