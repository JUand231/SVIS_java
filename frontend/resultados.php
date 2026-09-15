<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
exigirSesion();

$titulo = 'Resultados';
$tipo = $_SESSION['tipo'] ?? '';
$volver = $tipo === 'admin' ? 'admin.php' : 'estudiante.php';
$topRight = ['href' => $volver, 'text' => 'Volver', 'style' => 'btn-ghost'];

$errorResultados = null;
$encuesta = null;
$filas = [];
$totalVotos = 0;

// Encuesta pedida (?encuestaId=) o la primera disponible.
$encuestaId = isset($_GET['encuestaId']) ? (int) $_GET['encuestaId'] : 0;
if ($encuestaId <= 0) {
    $rutaLista = $tipo === 'admin' ? '/api/encuestas?estado=todas' : '/api/encuestas';
    [$http, $data] = apiGet($rutaLista);
    if ($http === 200 && is_array($data) && count($data) > 0) {
        $primera = $data[0];
        $encuestaId = (int) ($primera['Id'] ?? $primera['id'] ?? 0);
    } elseif ($http === 0) {
        $errorResultados = 'No se pudo conectar con el backend Java (¿Tomcat apagado?).';
    }
}

if ($encuestaId > 0 && $errorResultados === null) {
    // Encabezado de la votación: GET /api/encuestas?id=
    [$http, $data] = apiGet('/api/encuestas?id=' . $encuestaId);
    if ($http === 200 && is_array($data)) {
        $encuesta = $data;
    } elseif ($http === 0) {
        $errorResultados = 'No se pudo conectar con el backend Java (¿Tomcat apagado?).';
    } else {
        $errorResultados = 'No se encontró la encuesta solicitada.';
    }

    // Filas listas para pintar: GET /api/resultados?encuestaId= -> [{texto, votos, porcentaje}]
    if ($errorResultados === null) {
        [$http, $data] = apiGet('/api/resultados?encuestaId=' . $encuestaId);
        if ($http === 200 && is_array($data)) {
            $filas = $data;
            foreach ($filas as $f) {
                $totalVotos += (int) ($f['votos'] ?? 0);
            }
        } elseif ($http === 0) {
            $errorResultados = 'No se pudo conectar con el backend Java (¿Tomcat apagado?).';
        } else {
            $errorResultados = 'No se pudieron cargar los resultados.';
        }
    }
} elseif ($encuestaId <= 0 && $errorResultados === null) {
    $errorResultados = 'No hay encuestas disponibles para mostrar resultados.';
}

require __DIR__ . '/includes/header.php';
?>
  <main class="container mt-32" style="flex:1; padding-bottom:60px;">
    <?php if ($errorResultados !== null): ?>
      <div class="card" style="border-left:3px solid var(--red);">
        <span class="badge badge-cerrada">Sin resultados</span>
        <p class="muted mt-8" style="font-size:13.5px;"><?= h($errorResultados) ?></p>
        <a href="<?= h($volver) ?>" class="btn btn-ghost mt-16">Volver</a>
      </div>
    <?php else:
      $encTitulo = $encuesta['titulo'] ?? ('Encuesta #' . $encuestaId);
      $encDesc = $encuesta['descripcion'] ?? '';
      $encEstado = strtoupper((string) ($encuesta['estado'] ?? ''));
    ?>
      <span class="badge badge-<?= $encEstado === 'ACTIVA' ? 'activa' : 'cerrada' ?>">
        <?= $encEstado === 'ACTIVA' ? 'Activa' : 'Cerrada' ?>
      </span>
      <h1 style="font-size:24px; margin-top:12px;">Resultados · <?= h($encTitulo) ?></h1>
      <?php if ($encDesc !== ''): ?>
        <p class="muted mt-8" style="font-size:14px;"><?= h($encDesc) ?></p>
      <?php endif; ?>
      <p class="muted mt-8" style="font-size:13.5px;">Total de votos: <strong><?= $totalVotos ?></strong></p>

      <div class="card mt-24" style="overflow:hidden; padding:0;">
        <table style="width:100%; border-collapse:collapse; font-size:14px;">
          <thead>
            <tr class="muted" style="text-align:left; font-size:12.5px; border-bottom:1px solid var(--line);">
              <th style="padding:14px 20px;">Opción</th>
              <th style="padding:14px 20px; width:110px;">Votos</th>
              <th style="padding:14px 20px; width:220px;">Porcentaje</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($filas as $f):
              $pct = round((float) ($f['porcentaje'] ?? 0), 1);
            ?>
              <tr style="border-bottom:1px solid var(--line);">
                <td style="padding:14px 20px;"><?= h($f['texto'] ?? '') ?></td>
                <td style="padding:14px 20px;"><?= (int) ($f['votos'] ?? 0) ?></td>
                <td style="padding:14px 20px;">
                  <div style="display:flex; align-items:center; gap:10px;">
                    <div class="result-track" style="flex:1;"><div class="result-fill" style="width:<?= $pct ?>%;"></div></div>
                    <span class="pct"><?= $pct ?>%</span>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (count($filas) === 0): ?>
              <tr><td colspan="3" class="muted" style="padding:20px;">Aún no hay opciones o votos registrados.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <a href="<?= h($volver) ?>" class="btn btn-ghost mt-24">← Volver</a>
    <?php endif; ?>
  </main>
<?php require __DIR__ . '/includes/footer.php'; ?>
