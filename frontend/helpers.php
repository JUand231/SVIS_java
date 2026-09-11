<?php
// Escapa texto para salida segura en HTML
function h($s) {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}