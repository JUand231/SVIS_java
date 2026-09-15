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