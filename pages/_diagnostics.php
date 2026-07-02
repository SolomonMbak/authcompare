<?php
require_once __DIR__ . '/../inc/db.php';
$ok = true; $err = null;
try {
  db(); // try connect
} catch (Throwable $e) {
  $ok = false; $err = $e->getMessage();
}
?>
<section class="py-5">
  <div class="container">
    <h2 class="mb-3">Diagnostics</h2>
    <p><strong>Base URL:</strong> <?= htmlspecialchars(base_url(), ENT_QUOTES, 'UTF-8'); ?></p>
    <p><strong>DB Connection:</strong> <?= $ok ? 'OK ✅' : 'FAILED ❌'; ?></p>
    <?php if (!$ok): ?>
      <pre class="bg-light p-3 border"><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8'); ?></pre>
    <?php endif; ?>
  </div>
</section>
