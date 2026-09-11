<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';

$titulo = 'Panel administrador';
$topRight = ['href' => 'login.php', 'text' => 'Cerrar sesión'];

// Datos de ejemplo — más adelante esto vendrá de la base de datos / API
$encuestas = [
    [
        'id' => 1, 'estado' => 'activa', 'titulo' => 'Representante de Bienestar 2026',
        'descripcion' => 'Elección del vocero ante el comité de bienestar del Centro CIMM.',
        'votos' => 142,
        'opciones' => [['nombre' => 'Laura M.', 'pct' => 54], ['nombre' => 'Julián R.', 'pct' => 46]],
    ],
    [
        'id' => 2, 'estado' => 'cerrada', 'titulo' => 'Comité Estudiantil 2025',
        'descripcion' => 'Resultados definitivos del periodo anterior.',
        'votos' => 289,
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
        <div class="card card-ticket" style="<?= $enc['estado'] === 'cerrada' ? 'opacity:.85;' : '' ?>">
          <div class="flex-between">
            <span class="badge badge-<?= h($enc['estado']) ?>"><?= $enc['estado'] === 'activa' ? 'Activa' : 'Cerrada' ?></span>
            <span class="muted" style="font-size:12.5px;"><?= (int) $enc['votos'] ?> votos</span>
          </div>
          <h3 class="mt-16" style="font-size:17px;"><?= h($enc['titulo']) ?></h3>
          <p class="muted mt-8" style="font-size:13.5px;"><?= h($enc['descripcion']) ?></p>

          <div class="mt-24">
            <?php foreach ($enc['opciones'] as $op): ?>
              <div class="result-row">
                <div class="result-head"><span><?= h($op['nombre']) ?></span><span class="pct"><?= (int) $op['pct'] ?>%</span></div>
                <div class="result-track"><div class="result-fill" style="width:<?= (int) $op['pct'] ?>%; <?= $enc['estado'] === 'activa' && $op === $enc['opciones'][0] ? 'background:var(--verified);' : '' ?>"></div></div>
              </div>
            <?php endforeach; ?>
          </div>

          <?php if ($enc['estado'] === 'activa'): ?>
            <button class="btn btn-ghost btn-block mt-16" style="color:var(--alert); border-color:var(--alert-bg);">Cerrar votación</button>
          <?php else: ?>
            <button class="btn btn-ghost btn-block mt-16" disabled>Votación finalizada</button>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </main>

  <div id="modal-crear" style="display:none; position:fixed; inset:0; background:rgba(22,35,61,.45); align-items:center; justify-content:center; z-index:10;">
    <div class="card" style="width:420px; background:var(--paper-raised);">
      <h3 style="font-size:18px;">Nueva encuesta</h3>
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
<?php require __DIR__ . '/includes/footer.php'; ?>