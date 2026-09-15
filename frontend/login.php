<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
$titulo = 'Iniciar sesión';
$yaVoto = isset($_GET['estado']) && $_GET['estado'] === 'ya_voto'; // demo — luego se valida contra el token real
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h($titulo) ?> · <?= h(SITE_NAME) ?></title>
<link rel="stylesheet" href="assets/styles.css">
</head>
<body>

<div class="login-split">
  <a href="index.php" class="btn btn-ghost login-back">← Volver</a>

  <div class="login-visual">
    <img src="assets/login-photo.jpg" alt="" class="login-photo" onerror="this.style.display='none'">
    <div class="login-visual-content">
      <svg width="64" height="64" viewBox="0 0 72 72" fill="none">
        <rect x="10" y="30" width="52" height="30" rx="4" stroke="white" stroke-width="2.5"/>
        <path d="M10 30L36 14L62 30" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
        <rect x="30" y="24" width="12" height="10" rx="2" fill="#01f74a" stroke="white" stroke-width="2"/>
      </svg>
      <h2 style="font-size:26px; margin-top:22px; color:#fff;">Tu voz construye el Centro.</h2>
      <p>Participa en las elecciones estudiantiles del SENA de forma segura y verificable.</p>
    </div>
  </div>

  <div class="login-form-side">
    <div style="width:100%; max-width:360px;">
      <div class="mark" style="margin-bottom:32px;">
        <img src="assets/sena-logo.png" alt="SENA" class="site-logo" onerror="this.style.display='none'">
        <div class="mark-text" style="color:var(--ink-soft);"><strong style="color:var(--ink);">Votaciones SENA</strong>Centro CIMM</div>
      </div>

      <?php if ($yaVoto): ?>
        <div class="card" style="border-left:3px solid var(--red);">
          <span class="badge badge-cerrada">Voto ya registrado</span>
          <h2 style="font-size:19px; margin-top:14px;">Ya ejerciste tu derecho al voto</h2>
          <p class="muted mt-8" style="font-size:13.5px;">Tu token ya fue utilizado en esta jornada electoral. Gracias por participar.</p>
          <a href="index.php" class="btn btn-solid btn-block mt-16">Volver a la bienvenida</a>
        </div>
      <?php else: ?>
        <h2 style="font-size:22px;">Iniciar sesión</h2>
        <p class="muted mt-8" style="font-size:13.5px;">Ingresa con tu usuario institucional.</p>

        <form class="mt-24" method="post" action="login.php">
          <div class="field">
            <label for="doc">Número de documento</label>
            <input id="doc" name="documento" type="text" placeholder="Ej. 1052xxxxxx">
          </div>
          <div class="field">
            <label for="pass">Contraseña</label>
            <input id="pass" name="password" type="password" placeholder="••••••••">
          </div>
          <button class="btn btn-solid btn-block" type="submit" style="background:var(--login-brand);">Ingresar</button>
        </form>

        <div class="divider">vista previa de diseño</div>
        <div style="display:flex; gap:10px;">
          <a href="admin.php" class="btn btn-ghost" style="flex:1; font-size:13px; padding:10px;">Ver Administrador</a>
          <a href="estudiante.php" class="btn btn-ghost" style="flex:1; font-size:13px; padding:10px;">Ver Estudiante</a>
        </div>
        <a href="login.php?estado=ya_voto" class="muted" style="display:block; text-align:center; font-size:12px; margin-top:14px;">(Demo) Ver mensaje de "ya voté"</a>
      <?php endif; ?>
    </div>
  </div>
</div>
</body>
</html>