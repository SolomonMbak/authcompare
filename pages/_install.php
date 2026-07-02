<?php
require_once __DIR__ . '/../inc/bootstrap.php';
require_once __DIR__ . '/../inc/db.php';

// Detect JSON support (fallback to LONGTEXT for older MariaDB/MySQL)
try {
  $ver = db_one("SELECT VERSION() AS v")['v'] ?? '';
} catch (Throwable $e) {
  $ver = '';
}
$jsonType = 'JSON';
if (stripos($ver, 'mariadb') !== false) $jsonType = 'LONGTEXT';

$ddl = [
  // Sessions: one per participant run
"CREATE TABLE IF NOT EXISTS sessions (
   id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
   participant_id INT UNSIGNED NOT NULL,
   experiment_id INT UNSIGNED NOT NULL,
   started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   ended_at TIMESTAMP NULL,
   CONSTRAINT fk_sessions_participant
     FOREIGN KEY (participant_id) REFERENCES participants(id) ON DELETE CASCADE,
   CONSTRAINT fk_sessions_experiment
     FOREIGN KEY (experiment_id) REFERENCES experiments(id) ON DELETE CASCADE
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

// Credentials: stores a one-way verifier + metrics (no raw secrets)
"CREATE TABLE IF NOT EXISTS credentials (
   id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
   participant_id INT UNSIGNED NOT NULL,
   experiment_id INT UNSIGNED NOT NULL,
   type ENUM('password','pattern') NOT NULL,
   verifier_hash VARCHAR(255) NOT NULL,
   metrics_json LONGTEXT NULL,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   CONSTRAINT fk_credentials_participant
     FOREIGN KEY (participant_id) REFERENCES participants(id) ON DELETE CASCADE,
   CONSTRAINT fk_credentials_experiment
     FOREIGN KEY (experiment_id) REFERENCES experiments(id) ON DELETE CASCADE
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

// Attempts: each login try with timing
"CREATE TABLE IF NOT EXISTS attempts (
   id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
   session_id INT UNSIGNED NOT NULL,
   attempt_no TINYINT UNSIGNED NOT NULL,
   success TINYINT(1) NOT NULL,
   time_ms INT UNSIGNED NOT NULL,
   error_code VARCHAR(50) NULL,
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   CONSTRAINT fk_attempts_session
     FOREIGN KEY (session_id) REFERENCES sessions(id) ON DELETE CASCADE
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

];

$ok = true; $messages = [];
try {
  foreach ($ddl as $sql) db_exec($sql);
  $messages[] = "Migrations ran successfully. Tables are ready ✅";
} catch (Throwable $e) {
  $ok = false; $messages[] = "Error: " . $e->getMessage();
}
?>
<section class="py-5">
  <div class="container">
    <h2 class="mb-3">Install</h2>
    <?php foreach ($messages as $m): ?>
      <div class="alert <?= $ok ? 'alert-success' : 'alert-danger'; ?>"><?= htmlspecialchars($m, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endforeach; ?>
    <a class="btn btn-primary" href="<?= url('?p=experiments'); ?>">Go to Experiments</a>
  </div>
</section>
