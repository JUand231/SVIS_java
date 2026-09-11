<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';

$titulo = 'Iniciar sesión';
require __DIR__ . '/includes/header.php';
?>
  <main style="flex:1; display:flex; align-items:center; justify-content:center;">
    <div class="card" style="width:380px;">
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
        <button class="btn btn-solid btn-block" type="submit">Ingresar</button>
      </form>

      <div class="divider">vista previa de diseño</div>

      <p class="muted" style="font-size:12.5px; margin-bottom:10px;">
        Aún sin lógica de roles conectada — enlaces directos para revisar cada panel:
      </p>
      <div style="display:flex; gap:10px;">
        <a href="admin.php" class="btn btn-ghost" style="flex:1; font-size:13px; padding:10px;">Ver Administrador</a>
        <a href="estudiante.php" class="btn btn-ghost" style="flex:1; font-size:13px; padding:10px;">Ver Estudiante</a>
      </div>
    </div>
  </main>
<?php require __DIR__ . '/includes/footer.php'; ?>