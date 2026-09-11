<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h($titulo) ?> · <?= h(SITE_NAME) ?></title>
<link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<div class="shell">
  <header class="topbar">
    <div class="mark">
      <div class="mark-badge">SN</div>
      <div class="mark-text"><strong><?= h(SITE_NAME) ?></strong>Centro CIMM</div>
    </div>
    <?php if (!empty($topRight)): ?>
      <a href="<?= h($topRight['href']) ?>" class="muted" style="font-size:13.5px;"><?= h($topRight['text']) ?></a>
    <?php endif; ?>
  </header>