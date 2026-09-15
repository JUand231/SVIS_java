<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';

$titulo = 'Bienvenida';
$navLinks = [
    ['href' => 'index.php', 'text' => 'Inicio'],
    ['href' => '#candidatos', 'text' => 'Candidatos'],
    ['href' => '#como-votar', 'text' => '¿Cómo Votar?'],
];
$topRight = ['href' => 'login.php', 'text' => 'Iniciar Sesión', 'style' => 'btn-stamp'];

$candidatos = [
    ['id' => 1, 'numero' => 2, 'nombre' => 'Laura M.', 'jornada' => 'manana', 'programa' => 'Análisis y Desarrollo de Software',
     'propuestas' => ['Más espacios de bienestar para aprendices', 'Flexibilidad horaria para prácticas']],
    ['id' => 2, 'numero' => 6, 'nombre' => 'Julián R.', 'jornada' => 'manana', 'programa' => 'Producción de Componentes Mecánicos',
     'propuestas' => ['Comité permanente de seguimiento a quejas', 'Canal digital anónimo de reportes']],
    ['id' => 3, 'numero' => 1, 'nombre' => 'Camila T.', 'jornada' => 'tarde', 'programa' => 'Técnico en Atención Integral',
     'propuestas' => ['Jornadas de salud mental mensuales', 'Acompañamiento psicosocial permanente']],
    ['id' => 4, 'numero' => 4, 'nombre' => 'Andrés P.', 'jornada' => 'noche', 'programa' => 'Implementación de Infraestructura TIC',
     'propuestas' => ['Horarios de biblioteca extendidos', 'Rutas de transporte para jornada nocturna']],
];

require __DIR__ . '/includes/header.php';
?>
  <main style="flex:1;">
    <div class="container" style="display:grid; grid-template-columns: 1.1fr .9fr; gap:60px; align-items:center; padding-top:56px;">
      <div>
        <span class="badge badge-activa">Proceso electoral vigente</span>
        <h1 style="font-size:42px; margin-top:18px; max-width:9.5em;">Tu voto define quién representa al Centro.</h1>
        <p class="muted mt-16" style="max-width:34em; font-size:15.5px;">
          Elige tu representante de aprendiz CIMM.
        </p>
        <div class="mt-32" style="display:flex; gap:14px;">
          <a href="login.php" class="btn btn-stamp">Ingresar a votar</a>
          <a href="#candidatos" class="btn btn-ghost">Ver candidatos</a>
        </div>
      </div>

      <div class="card card-ticket" style="padding:32px;">
        <p class="muted" style="font-size:12.5px;">Comprobante de ejemplo</p>
        <h3 style="margin-top:8px; font-size:18px;">Voto registrado</h3>
        <div class="divider">recibo anónimo</div>
        <p style="font-family:'IBM Plex Mono', monospace; font-size:14px; color:var(--ink-soft);">123-456-789</p>
        <p class="muted mt-16" style="font-size:13px;">Este código no identifica al votante — solo confirma que el voto fue procesado.</p>
      </div>
    </div>

    <section id="candidatos" class="container" style="padding:64px 40px;">
      <h2 style="font-size:24px;">Candidatos por jornada</h2>
      <p class="muted mt-8" style="font-size:14px;">Conoce a los aprendices postulados para representarte.</p>

      <div class="jornada-tabs">
        <button class="jornada-tab active" data-filter="todas">Todas las jornadas</button>
        <button class="jornada-tab" data-filter="manana">Jornada Mañana</button>
        <button class="jornada-tab" data-filter="tarde">Jornada Tarde</button>
        <button class="jornada-tab" data-filter="noche">Jornada Noche</button>
      </div>

      <div class="candidate-grid">
        <?php foreach ($candidatos as $c): ?>
          <div class="card candidate-card" data-jornada="<?= h($c['jornada']) ?>">
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
              <?php foreach ($c['propuestas'] as $p): ?>
                <li><?= h($p) ?></li>
              <?php endforeach; ?>
            </ul>

            <div class="candidate-footer">
              <span class="link-plan">Ver plan de gobierno <span>→</span></span>
              <a href="login.php" class="btn btn-solid btn-block" style="font-size:13.5px;"> Iniciar sesión para votar</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
        <section id="como-votar" style="border-top:1px solid var(--line); padding:56px 0;">
      <div class="container">
        <h2 style="font-size:24px;">¿Cómo votar?</h2>
        <p class="muted mt-8" style="font-size:14px;">Tres pasos, facil y rapido.</p>

        <div class="grid-2 mt-24" style="grid-template-columns: repeat(3, 1fr);">
          <div class="card">
            <span class="candidate-chip">1</span>
            <h3 class="mt-16" style="font-size:16px;">Inicia sesión</h3>
            <p class="muted mt-8" style="font-size:13.5px;">Con tu documento y contraseña institucional del SENA.</p>
          </div>
          <div class="card">
            <span class="candidate-chip">2</span>
            <h3 class="mt-16" style="font-size:16px;">Elige tu candidato</h3>
            <p class="muted mt-8" style="font-size:13.5px;">Verás solo los candidatos de tu jornada y sus propuestas.</p>
          </div>
          <div class="card">
            <span class="candidate-chip">3</span>
            <h3 class="mt-16" style="font-size:16px;">Confirma con tu Token</h3>
            <p class="muted mt-8" style="font-size:13.5px;">Ingresas tu Token OTP y recibes tu comprobante digital.</p>
          </div>
        </div>
      </div>
    </section>
  </main>

<script>
document.querySelectorAll('.jornada-tab').forEach(tab => {
  tab.addEventListener('click', () => {
    document.querySelectorAll('.jornada-tab').forEach(t => t.classList.remove('active'));
    tab.classList.add('active');
    const filtro = tab.dataset.filter;
    document.querySelectorAll('.candidate-card').forEach(card => {
      card.style.display = (filtro === 'todas' || card.dataset.jornada === filtro) ? '' : 'none';
    });
  });
});
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>