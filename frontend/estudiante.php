<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';

$titulo = 'Mis votaciones';
$topRight = ['href' => 'login.php', 'text' => 'Cerrar sesión'];

// Datos de ejemplo
$activas = [
    ['id' => 1, 'titulo' => 'Representante de Bienestar 2026', 'descripcion' => 'Elección del vocero ante el comité de bienestar del Centro CIMM.', 'cierre' => 'Cierra en 2 días'],
];
$cerradas = [
    ['id' => 2, 'titulo' => 'Comité Estudiantil 2025', 'descripcion' => 'Tu voto fue registrado el 12 de noviembre de 2025.'],
];

require __DIR__ . '/includes/header.php';
?>
  <main class="container mt-32" style="flex:1; padding-bottom:60px; max-width:640px;">
    <h1 style="font-size:22px;">Encuestas activas</h1>
    <p class="muted mt-8" style="font-size:14px;">Estas son las votaciones en las que tienes derecho a participar.</p>

    <?php foreach ($activas as $enc): ?>
      <div class="card card-ticket mt-24">
        <div class="flex-between">
          <span class="badge badge-activa">Activa</span>
          <span class="muted" style="font-size:12.5px;"><?= h($enc['cierre']) ?></span>
        </div>
        <h3 class="mt-16" style="font-size:17px;"><?= h($enc['titulo']) ?></h3>
        <p class="muted mt-8" style="font-size:13.5px;"><?= h($enc['descripcion']) ?></p>
        <button class="btn btn-stamp btn-block mt-16" onclick="document.getElementById('modal-votar-<?= (int) $enc['id'] ?>').style.display='flex'">
          Votar ahora
        </button>
      </div>

      <div id="modal-votar-<?= (int) $enc['id'] ?>" style="display:none; position:fixed; inset:0; background:rgba(22,35,61,.45); align-items:center; justify-content:center; z-index:10;">
        <div class="card" style="width:400px; background:var(--paper-raised);">
          <h3 style="font-size:18px;"><?= h($enc['titulo']) ?></h3>
          <p class="muted mt-8" style="font-size:13px;">Selecciona una opción e ingresa tu token para confirmar.</p>

          <form method="post" action="votar.php">
            <input type="hidden" name="encuesta_id" value="<?= (int) $enc['id'] ?>">
            <div class="mt-24" style="display:flex; flex-direction:column; gap:10px;">
              <label class="card" style="display:flex; align-items:center; gap:10px; padding:14px; cursor:pointer; font-weight:500; font-size:14px;">
                <input type="radio" name="opcion" value="Laura M." style="width:auto;"> Laura M.
              </label>
              <label class="card" style="display:flex; align-items:center; gap:10px; padding:14px; cursor:pointer; font-weight:500; font-size:14px;">
                <input type="radio" name="opcion" value="Julián R." style="width:auto;"> Julián R.
              </label>
            </div>

            <div class="field mt-24">
              <label>Token OTP</label>
              <input class="input-otp" name="token" type="text" maxlength="8" placeholder="••••••••">
            </div>

            <div style="display:flex; gap:10px;">
              <button type="button" class="btn btn-ghost" style="flex:1;" onclick="document.getElementById('modal-votar-<?= (int) $enc['id'] ?>').style.display='none'">Cancelar</button>
              <button type="submit" class="btn btn-solid" style="flex:1;">Confirmar voto</button>
            </div>
          </form>
        </div>
      </div>
    <?php endforeach; ?>

    <?php foreach ($cerradas as $enc): ?>
      <div class="card card-ticket mt-24" style="opacity:.6;">
        <div class="flex-between">
          <span class="badge badge-cerrada">Cerrada</span>
          <span class="muted" style="font-size:12.5px;">Ya votaste</span>
        </div>
        <h3 class="mt-16" style="font-size:17px;"><?= h($enc['titulo']) ?></h3>
        <p class="muted mt-8" style="font-size:13.5px;"><?= h($enc['descripcion']) ?></p>
      </div>
    <?php endforeach; ?>
  </main>
<?php require __DIR__ . '/includes/footer.php'; ?>