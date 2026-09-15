<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';

$titulo = 'Mis votaciones';
$topRight = ['href' => 'login.php', 'text' => 'Cerrar sesión', 'style' => 'btn-danger'];

// Datos de ejemplo — más adelante esto vendrá de la base de datos / API
$encuestas = [
    [
        'id' => 1, 'estado' => 'activa', 'titulo' => 'Representante de Aprendiz 2026',
        'descripcion' => 'Elección del vocero ante el comité de bienestar del Centro CIMM.',
        'votos' => 302,
        'opciones' => [['nombre' => 'Laura M.', 'pct' => 54], ['nombre' => 'Julián R.', 'pct' => 46]],
    ],
    [
        'id' => 2, 'estado' => 'cerrada', 'titulo' => 'Representante de Aprendiz 2025',
        'descripcion' => 'Resultados definitivos del periodo anterior.',
        'votos' => 289,
        'opciones' => [['nombre' => 'Camila T.', 'pct' => 61], ['nombre' => 'Andrés P.', 'pct' => 39]],
    ],
    [
        'id' => 3, 'estado' => 'cerrada', 'titulo' => 'Representante de Aprendiz 2024',
        'descripcion' => 'Resultados definitivos del periodo anterior.',
        'votos' => 349,
        'opciones' => [['nombre' => 'Camila T.', 'pct' => 61], ['nombre' => 'Andrés P.', 'pct' => 39]],
    ],
];

require __DIR__ . '/includes/header.php';
?>
  <main class="container mt-32" style="flex:1; padding-bottom:60px;">
    <div class="flex-between">
      <div>
        <h1 style="font-size:24px;">Encuestas electorales</h1>
        <p class="muted mt-8" style="font-size:14px;">Gestiona la apertura, cierre y resultados de cada proceso.</p>
      </div>
      <button class="btn btn-stamp" onclick="document.getElementById('modal-crear').style.display='flex'">
        + Nueva encuesta
      </button>
    </div>

    <div class="grid-2 mt-32">
      <?php foreach ($encuestas as $enc): ?>
  <div class="card card-ticket" id="encuesta-<?= (int) $enc['id'] ?>" style="<?= $enc['estado'] === 'cerrada' ? 'opacity:.85;' : '' ?>">
    <div class="flex-between">
      <span class="badge badge-<?= h($enc['estado']) ?>" id="badge-<?= (int) $enc['id'] ?>">
        <?= $enc['estado'] === 'activa' ? 'Activa' : 'Cerrada' ?>
      </span>
      <span class="muted" style="font-size:12.5px;"><?= (int) $enc['votos'] ?> votos</span>
    </div>
    <h3 class="mt-16" style="font-size:17px;"><?= h($enc['titulo']) ?></h3>
    <p class="muted mt-8" style="font-size:13.5px;"><?= h($enc['descripcion']) ?></p>

    <div class="mt-24">
      <?php foreach ($enc['opciones'] as $op): ?>
        <div class="result-row">
          <div class="result-head"><span><?= h($op['nombre']) ?></span><span class="pct"><?= (int) $op['pct'] ?>%</span></div>
          <div class="result-track"><div class="result-fill" style="width:<?= (int) $op['pct'] ?>%;"></div></div>
        </div>
      <?php endforeach; ?>
    </div>

    <?php if ($enc['estado'] === 'activa'): ?>
      <button class="btn btn-danger btn-block mt-16" id="btn-cerrar-<?= (int) $enc['id'] ?>"
        onclick="confirmarCierre(<?= (int) $enc['id'] ?>, '<?= h($enc['titulo']) ?>')">
        Cerrar votación
      </button>
    <?php else: ?>
      <button class="btn btn-ghost btn-block mt-16" disabled>Votación finalizada</button>
    <?php endif; ?>
  </div>
<?php endforeach; ?>
    </div>
  </main>

  <div id="modal-cerrar" style="display:none; position:fixed; inset:0; background:rgba(31,36,48,.55); align-items:center; justify-content:center; z-index:10;">
  <div class="card" style="width:380px;">
    <span class="badge badge-cerrada">Acción irreversible</span>
    <h3 style="font-size:18px; margin-top:14px;">¿Cerrar esta votación?</h3>
    <p class="muted mt-8" style="font-size:13.5px;">
      Se cerrará <strong id="modal-cerrar-titulo"></strong>. Los estudiantes ya no podrán emitir más votos en esta encuesta.
    </p>
    <div style="display:flex; gap:10px; margin-top:22px;">
      <button type="button" class="btn btn-ghost" style="flex:1;" onclick="document.getElementById('modal-cerrar').style.display='none'">Cancelar</button>
      <button type="button" class="btn btn-danger" style="flex:1;" onclick="cerrarEncuestaConfirmado()">Sí, cerrar</button>
    </div>
  </div>
</div>

  <div id="modal-crear" style="display:none; position:fixed; inset:0; background:rgba(22,35,61,.45); align-items:center; justify-content:center; z-index:10;">
    <div class="card modal-card-lg">
      <h3 style="font-size:20px;">Nueva encuesta</h3>
      <form class="mt-16" method="post" action="admin.php">
        <div class="field">
          <label>Título</label>
          <input name="titulo" type="text" placeholder="Ej. Representante de Bienestar 2026">
        </div>
        <div class="field">
          <label>Descripción institucional</label>
          <textarea name="descripcion" rows="3" placeholder="Describe el propósito de la votación"></textarea>
        </div>
        <div class="field">
          <label>Opciones (una por línea)</label>
          <textarea name="opciones" rows="3" placeholder="Laura M.&#10;Julián R."></textarea>
        </div>
        <div style="display:flex; gap:10px;">
          <button type="button" class="btn btn-ghost" style="flex:1;" onclick="document.getElementById('modal-crear').style.display='none'">Cancelar</button>
          <button type="submit" class="btn btn-solid" style="flex:1;">Crear encuesta</button>
        </div>
      </form>
    </div>
  </div>
  <script>
let encuestaAConcerrar = null;

function confirmarCierre(id, titulo) {
  encuestaAConcerrar = id;
  document.getElementById('modal-cerrar-titulo').textContent = titulo;
  document.getElementById('modal-cerrar').style.display = 'flex';
}

function cerrarEncuestaConfirmado() {
  const id = encuestaAConcerrar;
  document.getElementById('modal-cerrar').style.display = 'none';

  const badge = document.getElementById('badge-' + id);
  badge.textContent = 'Cerrada';
  badge.classList.remove('badge-activa');
  badge.classList.add('badge-cerrada');

  document.getElementById('encuesta-' + id).style.opacity = '.85';

  const btn = document.getElementById('btn-cerrar-' + id);
  btn.outerHTML = '<button class="btn btn-ghost btn-block mt-16" disabled>Votación finalizada</button>';
}
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>