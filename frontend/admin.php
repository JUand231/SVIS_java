<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
exigirSesion('admin');

$titulo = 'Mis votaciones';
$topRight = ['href' => 'logout.php', 'text' => 'Cerrar sesión', 'style' => 'btn-danger'];

$errorAdmin = null;

// Acciones del panel: crear / cerrar / generar tokens (con PRG).
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'crear') {
        $tituloN = trim($_POST['titulo'] ?? '');
        $descripcionN = trim($_POST['descripcion'] ?? '');
        // El textarea trae una opción por línea -> se parte por \n.
        $lineas = preg_split('/\r\n|\r|\n/', $_POST['opciones'] ?? '');
        $opciones = array_values(array_filter(array_map('trim', $lineas), fn($t) => $t !== ''));
        if ($tituloN === '' || count($opciones) < 2) {
            $errorAdmin = 'Para crear se requiere título y mínimo 2 opciones.';
        } else {
            [$http, $data] = apiPost('/api/encuestas', [
                'titulo' => $tituloN,
                'descripcion' => $descripcionN,
                'opciones' => $opciones,
            ]);
            if ($http === 201) {
                header('Location: admin.php?msg=creada');
                exit;
            } elseif ($http === 0) {
                $errorAdmin = 'No se pudo conectar con el backend Java (¿Tomcat apagado?).';
            } else {
                $errorAdmin = (is_array($data) && isset($data['mensaje'])) ? $data['mensaje'] : 'No se pudo crear la encuesta.';
            }
        }
    } elseif ($accion === 'cerrar') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            $errorAdmin = 'Id de encuesta inválido.';
        } else {
            [$http, $data] = apiPut('/api/encuestas?id=' . $id);
            if ($http === 200) {
                header('Location: admin.php?msg=cerrada');
                exit;
            } elseif ($http === 0) {
                $errorAdmin = 'No se pudo conectar con el backend Java (¿Tomcat apagado?).';
            } else {
                $errorAdmin = (is_array($data) && isset($data['mensaje'])) ? $data['mensaje'] : 'No se pudo cerrar la votación.';
            }
        }
    } elseif ($accion === 'tokens') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            $errorAdmin = 'Id de encuesta inválido.';
        } else {
            [$http, $data] = apiPost('/api/tokens/generar', ['encuestaId' => $id]);
            if ($http === 200) {
                $n = is_array($data) ? (int) ($data['creados'] ?? 0) : 0;
                header('Location: admin.php?msg=tokens&n=' . $n);
                exit;
            } elseif ($http === 0) {
                $errorAdmin = 'No se pudo conectar con el backend Java (¿Tomcat apagado?).';
            } else {
                $errorAdmin = (is_array($data) && isset($data['mensaje'])) ? $data['mensaje'] : 'No se pudieron generar los tokens.';
            }
        }
    }
}

// Listado real: GET /api/encuestas?estado=todas
$encuestas = [];
[$httpList, $dataList] = apiGet('/api/encuestas?estado=todas');
if ($httpList === 200 && is_array($dataList)) {
    $encuestas = $dataList;
} elseif ($httpList === 0) {
    $errorAdmin = $errorAdmin ?? 'No se pudo conectar con el backend Java (¿Tomcat apagado?).';
} else {
    $errorAdmin = $errorAdmin ?? 'No se pudieron cargar las encuestas.';
}

// Resultados por encuesta para pintar votos y barras:
// GET /api/resultados?encuestaId= -> [{texto, votos, porcentaje}]
$resultadosPorEncuesta = [];
foreach ($encuestas as $enc) {
    $eid = (int) ($enc['Id'] ?? $enc['id'] ?? 0);
    if ($eid <= 0) {
        continue;
    }
    [$http, $data] = apiGet('/api/resultados?encuestaId=' . $eid);
    if ($http === 200 && is_array($data)) {
        $resultadosPorEncuesta[$eid] = $data;
    } else {
        $resultadosPorEncuesta[$eid] = [];
    }
}

$msg = $_GET['msg'] ?? null;

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

    <?php if ($msg === 'creada'): ?>
      <div class="card mt-16" style="border-left:3px solid var(--green);">
        <p style="font-size:14px;">Encuesta creada correctamente.</p>
      </div>
    <?php elseif ($msg === 'cerrada'): ?>
      <div class="card mt-16" style="border-left:3px solid var(--green);">
        <p style="font-size:14px;">Votación cerrada correctamente.</p>
      </div>
    <?php elseif ($msg === 'tokens'): ?>
      <div class="card mt-16" style="border-left:3px solid var(--green);">
        <p style="font-size:14px;">Tokens generados: <strong><?= (int) ($_GET['n'] ?? 0) ?></strong>.</p>
      </div>
    <?php endif; ?>

    <?php if ($errorAdmin !== null): ?>
      <div class="card mt-16" style="border-left:3px solid var(--red);">
        <span class="badge badge-cerrada">Atención</span>
        <p class="muted mt-8" style="font-size:13.5px;"><?= h($errorAdmin) ?></p>
      </div>
    <?php endif; ?>

    <?php if (count($encuestas) === 0 && $errorAdmin === null): ?>
      <div class="card mt-32">
        <p class="muted" style="font-size:14px;">Aún no hay encuestas. Crea la primera con “+ Nueva encuesta”.</p>
      </div>
    <?php endif; ?>

    <div class="grid-2 mt-32">
      <?php foreach ($encuestas as $enc):
        $eid = (int) ($enc['Id'] ?? $enc['id'] ?? 0);
        $estado = strtoupper((string) ($enc['estado'] ?? ''));
        $esActiva = $estado === 'ACTIVA';
        $res = $resultadosPorEncuesta[$eid] ?? [];
        $totalVotos = 0;
        foreach ($res as $r) { $totalVotos += (int) ($r['votos'] ?? 0); }
      ?>
  <div class="card card-ticket" id="encuesta-<?= $eid ?>" style="<?= $esActiva ? '' : 'opacity:.85;' ?>">
    <div class="flex-between">
      <span class="badge badge-<?= $esActiva ? 'activa' : 'cerrada' ?>" id="badge-<?= $eid ?>">
        <?= $esActiva ? 'Activa' : 'Cerrada' ?>
      </span>
      <span class="muted" style="font-size:12.5px;"><?= $totalVotos ?> votos</span>
    </div>
    <h3 class="mt-16" style="font-size:17px;"><?= h($enc['titulo'] ?? ('Encuesta #' . $eid)) ?></h3>
    <p class="muted mt-8" style="font-size:13.5px;"><?= h($enc['descripcion'] ?? '') ?></p>

    <div class="mt-24">
      <?php foreach ($res as $op):
        $pct = round((float) ($op['porcentaje'] ?? 0));
      ?>
        <div class="result-row">
          <div class="result-head"><span><?= h($op['texto'] ?? '') ?></span><span class="pct"><?= $pct ?>%</span></div>
          <div class="result-track"><div class="result-fill" style="width:<?= $pct ?>%;"></div></div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="mt-16" style="display:flex; gap:10px; flex-wrap:wrap;">
      <a href="resultados.php?encuestaId=<?= $eid ?>" class="btn btn-ghost" style="flex:1; text-align:center;">Ver resultados</a>
      <?php if ($esActiva): ?>
        <form method="post" action="admin.php" style="flex:1; display:flex;">
          <input type="hidden" name="accion" value="tokens">
          <input type="hidden" name="id" value="<?= $eid ?>">
          <button type="submit" class="btn btn-stamp btn-block" title="Genera un token por votante para esta encuesta">Generar tokens</button>
        </form>
      <?php endif; ?>
    </div>

    <?php if ($esActiva): ?>
      <button class="btn btn-danger btn-block mt-16" id="btn-cerrar-<?= $eid ?>"
        onclick="confirmarCierre(<?= $eid ?>, '<?= h($enc['titulo'] ?? '') ?>')">
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
    <form id="form-cerrar" method="post" action="admin.php">
      <input type="hidden" name="accion" value="cerrar">
      <input type="hidden" name="id" id="cerrar-id" value="">
      <div style="display:flex; gap:10px; margin-top:22px;">
        <button type="button" class="btn btn-ghost" style="flex:1;" onclick="document.getElementById('modal-cerrar').style.display='none'">Cancelar</button>
        <button type="submit" class="btn btn-danger" style="flex:1;">Sí, cerrar</button>
      </div>
    </form>
  </div>
</div>

  <div id="modal-crear" style="display:none; position:fixed; inset:0; background:rgba(22,35,61,.45); align-items:center; justify-content:center; z-index:10;">
    <div class="card modal-card-lg">
      <h3 style="font-size:20px;">Nueva encuesta</h3>
      <form class="mt-16" method="post" action="admin.php">
        <input type="hidden" name="accion" value="crear">
        <div class="field">
          <label>Título</label>
          <input name="titulo" type="text" placeholder="Ej. Representante de Bienestar 2026" required>
        </div>
        <div class="field">
          <label>Descripción institucional</label>
          <textarea name="descripcion" rows="3" placeholder="Describe el propósito de la votación"></textarea>
        </div>
        <div class="field">
          <label>Opciones (una por línea, formato Nombre | documento | foto)</label>
          <textarea name="opciones" rows="3" placeholder="Laura M. | 1058274558 | https://.../foto.jpg&#10;Julián R." required></textarea>
          <p class="muted mt-8" style="font-size:12.5px;">El documento enlaza al candidato con su ficha real (jornada y programa). La foto es opcional.</p>
        </div>
        <div style="display:flex; gap:10px;">
          <button type="button" class="btn btn-ghost" style="flex:1;" onclick="document.getElementById('modal-crear').style.display='none'">Cancelar</button>
          <button type="submit" class="btn btn-solid" style="flex:1;">Crear encuesta</button>
        </div>
      </form>
    </div>
  </div>
  <script>
function confirmarCierre(id, titulo) {
  document.getElementById('cerrar-id').value = id;
  document.getElementById('modal-cerrar-titulo').textContent = titulo;
  document.getElementById('modal-cerrar').style.display = 'flex';
}
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>
