<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';

$titulo = 'Bienvenida';
require __DIR__ . '/includes/header.php';
?>
  <main style="flex:1; display:flex; align-items:center;">
    <div class="container" style="display:grid; grid-template-columns: 1.1fr .9fr; gap:60px; align-items:center;">
      <div>
        <span class="badge badge-activa">Proceso electoral vigente</span>
        <h1 style="font-size:44px; line-height:1.15; margin-top:18px; max-width:9.5em;">
          Tu voto define quién representa al Centro.
        </h1>
        <p class="muted mt-16" style="max-width:34em; font-size:15.5px;">
          Elige tus representantes de bienestar y comité estudiantil de forma segura,
          verificable y anónima. Cada voto se confirma con un comprobante digital único.
        </p>
        <div class="mt-32" style="display:flex; gap:14px;">
          <a href="login.php" class="btn btn-stamp">Ingresar a votar</a>
          <a href="#como-funciona" class="btn btn-ghost">Cómo funciona</a>
        </div>
      </div>

      <div class="card card-ticket" style="padding:32px;">
        <p class="muted" style="font-size:12.5px;">Comprobante de ejemplo</p>
        <h3 style="margin-top:8px; font-size:19px;">Voto registrado</h3>
        <div class="divider">recibo anónimo</div>
        <p style="font-family:'IBM Plex Mono', monospace; font-size:14px; word-break:break-all; color:var(--ink-soft);">
          7F3A-91C2-BE04-KX7Q
        </p>
        <p class="muted mt-16" style="font-size:13px;">
          Este código no identifica al votante — solo confirma que el sufragio fue procesado con éxito.
        </p>
      </div>
    </div>
  </main>

  <section id="como-funciona" style="border-top:1px solid var(--line); padding:48px 0;">
    <div class="container grid-2" style="max-width:1080px;">
      <div>
        <h3 style="font-size:16px;">1. Inicia sesión</h3>
        <p class="muted mt-8" style="font-size:14px;">Con tus credenciales institucionales del SENA.</p>
      </div>
      <div>
        <h3 style="font-size:16px;">2. Vota con tu token</h3>
        <p class="muted mt-8" style="font-size:14px;">Selecciona una opción e ingresa tu Token OTP para confirmar.</p>
      </div>
    </div>
  </section>
<?php require __DIR__ . '/includes/footer.php'; ?>