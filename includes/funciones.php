<?php
function usuario_activo($fecha) {
    if (!$fecha) return false;
    $ahora = new DateTime();
    $ultima = new DateTime($fecha);
    $dif = $ahora->getTimestamp() - $ultima->getTimestamp();
    return $dif <= 120; // menos de 2 minutos
}

