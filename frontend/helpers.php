<?php
// Escapa texto para salida segura en HTML
function h($s) {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}
// ---- Jornadas (datos reales del backend) ----
// Normaliza a 'manana'|'tarde'|'noche'. Devuelve null si no hay jornada válida.
function normalizarJornada($j) {
    if ($j === null) {
        return null;
    }
    $t = strtolower(trim((string) $j));
    $t = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], $t);
    if ($t === 'manana') {
        return 'manana';
    }
    if ($t === 'tarde') {
        return 'tarde';
    }
    if ($t === 'noche') {
        return 'noche';
    }
    return null;
}

function jornadaEtiqueta($norm) {
    return match ($norm) {
        'manana' => 'Mañana',
        'tarde' => 'Tarde',
        'noche' => 'Noche',
        default => 'General',
    };
}

// Clave para filtrar/agrupar: slug conocido u 'otras' (jornada libre sin color propio).
function jornadaClave($j) {
    $norm = normalizarJornada($j);
    return $norm ?? 'otras';
}

// Etiqueta a mostrar: la conocida, el texto libre tal cual, o 'General'.
function jornadaTexto($j) {
    $norm = normalizarJornada($j);
    if ($norm !== null) {
        return jornadaEtiqueta($norm);
    }
    $t = trim((string) ($j ?? ''));
    return $t !== '' ? $t : 'General';
}

// Clase CSS del badge/foto: jornada-manana|tarde|noche, o badge-activa genérico.
function jornadaBadgeClass($j) {
    $norm = normalizarJornada($j);
    return $norm !== null ? 'jornada-' . $norm : null;
}

// ---- Puente PHP -> backend Java (cURL + JSON) ----
// Devuelve [httpCode, datosDecodificados|null].
function apiRequest($metodo, $ruta, $payload = null) {
    $url = rtrim(API_BASE_URL, '/') . $ruta;
    $ch = curl_init($url);
    $headers = ['Content-Type: application/json'];
    $opts = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => strtoupper($metodo),
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => API_TIMEOUT,
    ];
    if ($payload !== null) {
        $opts[CURLOPT_POSTFIELDS] = json_encode($payload);
    }
    curl_setopt_array($ch, $opts);
    $body = curl_exec($ch);
    $http = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $errno = curl_errno($ch);
    curl_close($ch);
    if ($body === false || ($http === 0 && $errno !== 0)) {
        return [0, null];
    }
    $data = json_decode($body, true);
    return [$http, $data];
}

function apiGet($ruta) {
    return apiRequest('GET', $ruta);
}

function apiPost($ruta, $payload) {
    return apiRequest('POST', $ruta, $payload);
}

function apiPut($ruta, $payload = null) {
    return apiRequest('PUT', $ruta, $payload);
}

// ---- Sesión ----
function usuarioSesion() {
    return $_SESSION['user'] ?? null;
}

// El JSON del backend serializa los campos tal cual: "Id", "Username", ...
// Se aceptan ambas capitalizaciones por robustez.
function usuarioIdSesion() {
    $u = usuarioSesion();
    if (!is_array($u)) {
        return null;
    }
    foreach (['Id', 'id', 'ID'] as $k) {
        if (isset($u[$k])) {
            return $u[$k];
        }
    }
    return null;
}

// Guarda de sesión: si no hay login (o no es del tipo esperado),
// redirige a login.php. Llamar antes de cualquier salida HTML.
function exigirSesion($tipoEsperado = null) {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
    if ($tipoEsperado !== null && ($_SESSION['tipo'] ?? '') !== $tipoEsperado) {
        header('Location: login.php');
        exit;
    }
}