<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
$titulo = 'Mis votaciones';
$topRight = ['href' => 'login.php', 'text' => 'Cerrar sesión', 'style' => 'btn-danger'];

$miJornada = 'manana';
$candidatos = [
    ['id' => 1, 'numero' => 2, 'nombre' => 'Laura M.', 'jornada' => 'manana', 'programa' => 'Análisis y Desarrollo de Software',
     'propuestas' => ['Más espacios de bienestar para aprendices', 'Flexibilidad horaria para prácticas']],
    ['id' => 2, 'numero' => 6, 'nombre' => 'Julián R.', 'jornada' => 'manana', 'programa' => 'Producción de Componentes Mecánicos',
     'propuestas' => ['Comité permanente de seguimiento a quejas', 'Canal digital anónimo de reportes']],
];

require __DIR__ . '/includes/header.php';
?>
  <main class="container mt-32" style="flex:1; padding-bottom:60px;">
    <span class="badge jornada-<?= h($miJornada) ?>">Jornada <?= ucfirst(h($miJornada)) ?></span>
    <h1 style="font-size:23px; margin-top:12px;">Candidatos a Representante 2026</h1>
    <p class="muted mt-8" style="font-size:14px;">Elige tu candidato e ingresa tu Token OTP para confirmar el voto.</p>

    <div class="candidate-grid mt-24">
      <?php foreach ($candidatos as $c): ?>
        <div class="card candidate-card">
          <div class="candidate-top">
            <span class="candidate-chip">N° <?= (int) $c['numero'] ?></span>
            <span class="badge jornada-<?= h($c['jornada']) ?>">Jornada <?= ucfirst(h($c['jornada'])) ?></span>
          </div>
          <div class="candidate-photo-wrap jornada-<?= h($c['jornada']) ?>">
            <div class="candidate-photo"><?= h(strtoupper(substr($c['nombre'], 0, 1))) ?></div>
          </div>
          <h3 class="candidate-name"><?= h($c['nombre']) ?></h3>
          <p class="candidate-program">🎓 <?= h($c['programa']) ?></p>
          <ul class="candidate-proposals">
            <?php foreach ($c['propuestas'] as $p): ?><li><?= h($p) ?></li><?php endforeach; ?>
          </ul>
          <div class="candidate-footer">
            <button class="btn btn-stamp btn-block" onclick="abrirModal(<?= (int) $c['id'] ?>)">Votar por <?= h($c['nombre']) ?></button>
          </div>
        </div>

        <!-- Modal wizard de 2 pasos -->
        <div id="modal-<?= (int) $c['id'] ?>" style="display:none; position:fixed; inset:0; background:rgba(31,36,48,.55); align-items:center; justify-content:center; z-index:10;">
          <div class="card" style="width:380px;">
            <div class="step-indicator"><span id="bar1-<?= (int) $c['id'] ?>" class="done"></span><span id="bar2-<?= (int) $c['id'] ?>"></span></div>

            <div id="paso1-<?= (int) $c['id'] ?>">
              <span class="muted" style="font-size:12.5px;">Paso 1 de 2 · Confirmar candidato</span>
              <h3 style="font-size:18px; margin-top:8px;">Vas a votar por <?= h($c['nombre']) ?></h3>
              <p class="muted mt-8" style="font-size:13.5px;">Esta acción no se puede deshacer. Se te pedirá tu Token OTP institucional para confirmar.</p>
              <div style="display:flex; gap:10px; margin-top:24px;">
                <button type="button" class="btn btn-ghost" style="flex:1;" onclick="cerrarModal(<?= (int) $c['id'] ?>)">Cancelar</button>
                <button type="button" class="btn btn-stamp" style="flex:1;" onclick="irPaso2(<?= (int) $c['id'] ?>)">Continuar</button>
              </div>
            </div>

            <div id="paso2-<?= (int) $c['id'] ?>" style="display:none; position:relative;">
  <button type="button" class="modal-close" onclick="cerrarModal(<?= (int) $c['id'] ?>)">✕</button>
  <span class="muted" style="font-size:12.5px;">Paso 2 de 2 · Confirmar con token</span>
  <h3 style="font-size:18px; margin-top:8px;">Confirma tu voto con tu Token OTP</h3>

  <div class="otp-display mt-16">
    <span class="muted" style="font-size:12px;">Tu token asignado (ejemplo de diseño)</span>
    <div class="otp-display-value">1234 5678</div>
  </div>

  <div class="otp-box">
    <label for="token-<?= (int) $c['id'] ?>">Escribe tu Token OTP para confirmar</label>
    <input class="input-otp" id="token-<?= (int) $c['id'] ?>" maxlength="8" placeholder="••••••••">
  </div>

  <div style="display:flex; gap:10px; margin-top:18px;">
    <button type="button" class="btn btn-ghost" style="flex:1;" onclick="irPaso1(<?= (int) $c['id'] ?>)">← Atrás</button>
    <button type="button" class="btn btn-stamp" style="flex:1;" onclick="confirmarVoto(<?= (int) $c['id'] ?>, '<?= h($c['nombre']) ?>')">Confirmar voto</button>
  </div>
</div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </main>

  <!-- Mensaje de éxito -->
  <div id="toast-exito" style="display:none; position:fixed; inset:0; background:rgba(31,36,48,.6); align-items:center; justify-content:center; z-index:20;">
    <div class="card" style="width:360px; text-align:center;">
      <div style="width:56px;height:56px;border-radius:50%;background:var(--green-bg);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none"><path d="M5 12.5L10 17.5L19 7.5" stroke="#2E9E4F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </div>
      <h3 style="font-size:18px;">¡Voto registrado con éxito!</h3>
      <p class="muted mt-8" style="font-size:13.5px;">Gracias por participar, tu voto por <strong id="toast-nombre"></strong> quedó registrado.</p>
      <p class="muted mt-16" style="font-size:12.5px;">Volviendo a la bienvenida en <span id="toast-counter">5</span>s...</p>
      <button class="btn btn-solid btn-block mt-16" onclick="window.location.href='index.php'">Aceptar</button>
    </div>
  </div>

<script>
function abrirModal(id) { document.getElementById('modal-'+id).style.display = 'flex'; }
function cerrarModal(id) { document.getElementById('modal-'+id).style.display = 'none'; }
function irPaso2(id) {
  document.getElementById('paso1-'+id).style.display = 'none';
  document.getElementById('paso2-'+id).style.display = 'block';
  document.getElementById('bar2-'+id).classList.add('done');
}
function irPaso1(id) {
  document.getElementById('paso2-'+id).style.display = 'none';
  document.getElementById('paso1-'+id).style.display = 'block';
  document.getElementById('bar2-'+id).classList.remove('done');
}
function confirmarVoto(id, nombre) {
  document.getElementById('modal-'+id).style.display = 'none';
  document.getElementById('toast-nombre').textContent = nombre;
  document.getElementById('toast-exito').style.display = 'flex';
  let seg = 5;
  const contador = document.getElementById('toast-counter');
  const timer = setInterval(() => {
    seg--; contador.textContent = seg;
    if (seg <= 0) { clearInterval(timer); window.location.href = 'index.php'; }
  }, 1000);
}
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>