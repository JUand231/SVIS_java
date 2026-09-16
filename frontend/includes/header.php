<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h($titulo) ?> · <?= h(SITE_NAME) ?></title>
<link rel="icon" type="image/png" href="assets/sena-logo.png">
<link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<div class="shell">
  <header class="topbar">
    <div class="mark">
      <img src="assets/sena-logo.png" alt="SENA" class="site-logo" onerror="this.style.display='none'">
      <div class="mark-text"><strong><?= h(SITE_NAME) ?></strong>Centro CIMM</div>
    </div>

    <?php if (!empty($navLinks)): ?>
      <nav class="topbar-nav">
        <?php foreach ($navLinks as $link): ?>
          <a href="<?= h($link['href']) ?>" class="nav-link"><?= h($link['text']) ?></a>
        <?php endforeach; ?>
      </nav>
    <?php endif; ?>

    <?php if (!empty($topRight)): ?>
      <a href="<?= h($topRight['href']) ?>" class="btn <?= h($topRight['style'] ?? 'btn-ghost') ?>" style="padding:9px 18px; font-size:13.5px;">
        <?= h($topRight['text']) ?>
      </a>
    <?php endif; ?>
  </header>