<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
exigirSesion('votante');
$titulo = 'Mis votaciones';
$topRight = ['href' => 'logout.php', 'text' => 'Cerrar sesión', 'style' => 'btn-danger'];

$usuarioId = usuarioIdSesion();
$errorPagina = null;
$errorVoto = null;
$recibo = null;
$nombreVotado = '';
$encuesta = null;
$candidatos = [];

// 1) Procesar el voto: POST /api/votar {codigo, opcionId, encuestaId, usuarioId}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'votar') {
    $codigo = trim($_POST['codigo'] ?? '');
    $opcionId = (int) ($_POST['opcionId'] ?? 0);
    $encuestaId = (int) ($_POST['encuestaId'] ?? 0);
    $nombreVotado = trim($_POST['nombre'] ?? '');
    if ($codigo === '' || $opcionId <= 0 || $encuestaId <= 0 || $usuarioId === null) {
        $errorVoto = 'Faltan datos para registrar el voto: token y candidato son requeridos.';
    } else {
        [$http, $data] = apiPost('/api/votar', [
            'codigo' => $codigo,
            'opcionId' => $opcionId,
            'encuestaId' => $encuestaId,
            'usuarioId' => $usuarioId,
        ]);
        if ($http === 200) {
            $recibo = is_array($data) ? ($data['recibo'] ?? null) : null;
        } elseif ($http === 409) {
            header('Location: login.php?estado=ya_voto');
            exit;
        } elseif ($http === 0) {
            $errorVoto = 'No se pudo conectar con el backend Java (¿Tomcat apagado?).';
        } else {
            $errorVoto = (is_array($data) && isset($data['mensaje'])) ? $data['mensaje'] : 'No se pudo registrar el voto. Intenta más tarde.';
        }
    }
}

// 2) Cargar la encuesta: ?id= puntual o la primera activa.
//    GET /api/encuestas?id=  |  GET /api/encuestas (solo activas)
$idSel = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($idSel > 0) {
    [$http, $data] = apiGet('/api/encuestas?id=' . $idSel);
    if ($http === 200 && is_array($data)) {
        $encuesta = $data;
    } elseif ($http === 0) {
        $errorPagina = 'No se pudo conectar con el backend Java (¿Tomcat apagado?).';
    } else {
        $errorPagina = 'No se encontró la votación solicitada.';
    }
} else {
    [$http, $data] = apiGet('/api/encuestas');
    if ($http === 200 && is_array($data) && count($data) > 0) {
        $encuesta = $data[0];
    } elseif ($http === 0) {
        $errorPagina = 'No se pudo conectar con el backend Java (¿Tomcat apagado?).';
    }
}

$encuestaId = null;
$encuestaTitulo = '';
$encuestaDescripcion = '';
if (is_array($encuesta)) {
    $encuestaId = $encuesta['Id'] ?? $encuesta['id'] ?? null;
    $encuestaTitulo = $encuesta['titulo'] ?? '';
    $encuestaDescripcion = $encuesta['descripcion'] ?? '';
}

// 3) Cargar los candidatos (opciones) de la encuesta.
//    El backend expone texto/votos/porcentaje por opción en
//    GET /api/resultados?encuestaId=  -> [{opcionId, texto, votos, porcentaje}]
if ($encuestaId !== null) {
    [$http, $data] = apiGet('/api/resultados?encuestaId=' . urlencode((string) $encuestaId));
    if ($http === 200 && is_array($data)) {
        $candidatos = $data;
    } elseif ($http === 0 && $errorPagina === null) {
        $errorPagina = 'No se pudo conectar con el backend Java (¿Tomcat apagado?).';
    }
}

// 4) Token OTP de esta sesión para esta encuesta (para mostrarlo en el paso 2).
$miCodigo = null;
$miEstadoToken = null;
$miJornada = null;
if ($usuarioId !== null) {
    [$httpP, $dataP] = apiGet('/api/perfil?usuarioId=' . urlencode((string) $usuarioId));
    if ($httpP === 200 && is_array($dataP) && isset($dataP['ficha']['jornada'])) {
        $miJornada = normalizarJornada($dataP['ficha']['jornada']);
    }
}
if ($encuestaId !== null && $usuarioId !== null) {
    [$httpT, $dataT] = apiGet('/api/tokens/generar?encuestaId=' . urlencode((string) $encuestaId));
    if ($httpT === 200 && is_array($dataT)) {
        foreach ($dataT as $t) {
            $uid = $t['usuario_id'] ?? $t['usuarioId'] ?? $t['usuario_id'] ?? null;
            if ($uid !== null && (int) $uid === (int) $usuarioId) {
                $miCodigo = $t['codigo'] ?? null;
                $miEstadoToken = $t['estado'] ?? null;
                break;
            }
        }
    }
}
// 4b) Si la encuesta tiene jornadas mezcladas, el votante solo ve su jornada (Index muestra todas).
if ($miJornada !== null && count($candidatos) > 0) {
    $filtrados = array_values(array_filter($candidatos, fn($c) => normalizarJornada($c['jornada'] ?? null) === $miJornada));
    // Solo filtra si hay al menos 1 de su jornada; si no, deja vacío para mostrar mensaje específico.
    $candidatos = $filtrados;
}

require __DIR__ . '/includes/header.php';
?>
  <main class="container mt-32" style="flex:1; padding-bottom:60px;">
    <?php if ($errorPagina !== null): ?>
      <div class="card" style="border-left:3px solid var(--red);">
        <span class="badge badge-cerrada">Sin conexión</span>
        <p class="muted mt-8" style="font-size:13.5px;"><?= h($errorPagina) ?></p>
      </div>
    <?php elseif ($encuesta === null): ?>
      <div class="card">
        <span class="badge badge-cerrada">Sin votaciones</span>
        <h1 style="font-size:23px; margin-top:12px;">No hay votaciones activas</h1>
        <p class="muted mt-8" style="font-size:14px;">En este momento no tienes procesos electorales abiertos. Vuelve a intentarlo más tarde.</p>
      </div>
    <?php else: ?>
      <span class="badge badge-activa">Votación activa</span>
      <h1 style="font-size:23px; margin-top:12px;"><?= h($encuestaTitulo !== '' ? $encuestaTitulo : ('Encuesta #' . (int) $encuestaId)) ?></h1>
      <?php if ($encuestaDescripcion !== ''): ?>
        <p class="muted mt-8" style="font-size:14px;"><?= h($encuestaDescripcion) ?></p>
      <?php endif; ?>
      <?php if ($miJornada !== null): ?>
        <p class="muted mt-8" style="font-size:13.5px;">Solo saldran los candidatos de su jornada: <span class="badge jornada-<?= h($miJornada) ?>"><?= h(jornadaTexto($miJornada)) ?></span></p>
      <?php endif; ?>
      <p class="muted mt-8" style="font-size:14px;">Elija su candidato e ingrese su Token OTP para confirmar el voto.</p>

      <?php if ($errorVoto !== null): ?>
        <div class="card mt-16" style="border-left:3px solid var(--red);">
          <span class="badge badge-cerrada">No se pudo votar</span>
          <p class="muted mt-8" style="font-size:13.5px;"><?= h($errorVoto) ?></p>
        </div>
      <?php endif; ?>

      <?php if (count($candidatos) === 0): ?>
        <div class="card mt-24">
          <p class="muted" style="font-size:14px;"><?= $miJornada !== null ? 'No hay candidatos de su jornada en estas elecciones.' : 'Estas votaciónes aún no tiene candidatos publicados.' ?></p>
        </div>
      <?php else: ?>
        <div class="candidate-grid mt-24">
          <?php foreach ($candidatos as $i => $c):
            $opId = (int) ($c['opcionId'] ?? 0);
            $nombre = (string) ($c['texto'] ?? ('Opción ' . ($i + 1)));
            if ($opId <= 0) { continue; }
            $jor = trim((string) ($c['jornada'] ?? ''));
            $prog = trim((string) ($c['programa'] ?? ''));
            $foto = trim((string) ($c['fotoUrl'] ?? ''));
            $norm = normalizarJornada($jor);
          ?>
            <div class="card candidate-card">
              <div class="candidate-top">
                <span class="candidate-chip">N° <?= $i + 1 ?></span>
                <?php if ($jor !== ''): ?>
                  <span class="badge <?= h($norm !== null ? 'jornada-' . $norm : 'badge-activa') ?>">Jornada <?= h($jor) ?></span>
                <?php endif; ?>
              </div>
              <div class="candidate-photo-wrap <?= h($norm !== null ? 'jornada-' . $norm : '') ?>">
                <div class="candidate-photo"><?= h(strtoupper(substr($nombre, 0, 1))) ?></div>
                <?php if ($foto !== ''): ?>
                  <img class="candidate-photo-img" src="<?= h($foto) ?>" alt="<?= h($nombre) ?>" onerror="this.remove()">
                <?php endif; ?>
              </div>
              <h3 class="candidate-name"><?= h($nombre) ?></h3>
              <?php if ($prog !== ''): ?>
                <p class="candidate-program">🎓 <?= h($prog) ?></p>
              <?php endif; ?>
              <div class="candidate-footer">
                <button class="btn btn-stamp btn-block" onclick="abrirModal(<?= $opId ?>)">Votar por <?= h($nombre) ?></button>
              </div>
            </div>

            <!-- Modal wizard de 2 pasos -->
            <div id="modal-<?= $opId ?>" style="display:none; position:fixed; inset:0; background:rgba(31,36,48,.55); align-items:center; justify-content:center; z-index:10;">
              <div class="card" style="width:380px;">
                <div class="step-indicator"><span id="bar1-<?= $opId ?>" class="done"></span><span id="bar2-<?= $opId ?>"></span></div>

                <div id="paso1-<?= $opId ?>">
                  <span class="muted" style="font-size:12.5px;">Paso 1 de 2 · Confirmar candidato</span>
                  <h3 style="font-size:18px; margin-top:8px;">Vas a votar por <?= h($nombre) ?></h3>
                  <p class="muted mt-8" style="font-size:13.5px;">Esta acción no se puede deshacer. Se le pedirá su Token OTP institucional para confirmar.</p>
                  <div style="display:flex; gap:10px; margin-top:24px;">
                    <button type="button" class="btn btn-ghost" style="flex:1;" onclick="cerrarModal(<?= $opId ?>)">Cancelar</button>
                    <button type="button" class="btn btn-stamp" style="flex:1;" onclick="irPaso2(<?= $opId ?>)">Continuar</button>
                  </div>
                </div>

                <div id="paso2-<?= $opId ?>" style="display:none; position:relative;">
                  <button type="button" class="modal-close" onclick="cerrarModal(<?= $opId ?>)">✕</button>
                  <span class="muted" style="font-size:12.5px;">Paso 2 de 2 · Confirmar con token</span>
                  <h3 style="font-size:18px; margin-top:8px;">Confirme su voto con el Token OTP</h3>

                  <?php if ($miCodigo !== null): ?>
                    <div class="card mt-16" style="background:#f0fdf4; border:1px solid #86efac; text-align:center;">
                      <p class="muted" style="font-size:12px;">Su token para esta votación</p>
                      <p style="font-family:'IBM Plex Mono',monospace; font-size:22px; font-weight:700; letter-spacing:2px; margin-top:6px;"><?= h($miCodigo) ?></p>
                      <?php if ($miEstadoToken === 'USADO'): ?><p class="muted" style="font-size:12px; color:#dc2626; margin-top:4px;">Ya se uso, no podrás votar de nuevo.</p><?php endif; ?>
                    </div>
                  <?php else: ?>
                    <div class="card mt-16" style="border-left:3px solid var(--red);">
                      <p class="muted" style="font-size:13px;">Aún no tienes token para esta votación. Pide al administrador que genere los tokens.</p>
                    </div>
                  <?php endif; ?>

                  <form id="form-voto-<?= $opId ?>" method="post" action="estudiante.php<?= $idSel > 0 ? '?id=' . $idSel : '' ?>">
                    <input type="hidden" name="accion" value="votar">
                    <input type="hidden" name="opcionId" value="<?= $opId ?>">
                    <input type="hidden" name="encuestaId" value="<?= (int) $encuestaId ?>">
                    <input type="hidden" name="nombre" value="<?= h($nombre) ?>">
                    <div class="otp-box">
                      <label for="token-<?= $opId ?>">Escriba su Token OTP para confirmar</label>
                      <input class="input-otp" id="token-<?= $opId ?>" name="codigo" maxlength="8" placeholder="••••••••" autocomplete="one-time-code">
                    </div>
                    <div style="display:flex; gap:10px; margin-top:18px;">
                      <button type="submit" class="btn btn-stamp" style="flex:1;">Confirmar voto</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </main>

  <!-- Mensaje de éxito con recibo del backend -->
  <div id="toast-exito" style="display:<?= $recibo !== null ? 'flex' : 'none' ?>; position:fixed; inset:0; background:rgba(31,36,48,.6); align-items:center; justify-content:center; z-index:20;">
    <div class="card" style="width:360px; text-align:center;">
      <div style="width:56px;height:56px;border-radius:50%;background:var(--green-bg);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none"><path d="M5 12.5L10 17.5L19 7.5" stroke="#2E9E4F" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </div>
      <h3 style="font-size:18px;">¡Voto registrado con éxito!</h3>
      <p class="muted mt-8" style="font-size:13.5px;">Gracias por participar<?= $nombreVotado !== '' ? ', tu voto por <strong>' . h($nombreVotado) . '</strong> quedó registrado' : '' ?>.</p>
      <?php if ($recibo !== null): ?>
        <div class="divider">recibo anónimo</div>
        <p style="font-family:'IBM Plex Mono', monospace; font-size:14px; color:var(--ink-soft);"><?= h($recibo) ?></p>
        <p class="muted mt-16" style="font-size:12.5px;">Este código no identifica al votante — solo confirma que el voto fue procesado.</p>
      <?php endif; ?>
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
<?php if ($recibo !== null): ?>
(function () {
  let seg = 5;
  const contador = document.getElementById('toast-counter');
  const timer = setInterval(() => {
    seg--; contador.textContent = seg;
    if (seg <= 0) { clearInterval(timer); window.location.href = 'index.php'; }
  }, 1000);
})();
<?php endif; ?>
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>
