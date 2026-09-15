<?php
// Escapa texto para salida segura en HTML
function h($s) {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}
function jornadaClass($jornada) {
    return match (strtolower($jornada)) {
        'mañana' => 'jornada-manana',
        'tarde'  => 'jornada-tarde',
        'noche'  => 'jornada-noche',
        default  => 'jornada-manana',
    };
}