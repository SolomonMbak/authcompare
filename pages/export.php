<?php
require_once __DIR__ . '/../inc/db.php';

$kind = $_GET['kind'] ?? 'attempts'; // 'attempts' | 'credentials'
$expId = (int)($_GET['e'] ?? 0);

$dir = __DIR__ . '/../storage/exports';
if (!is_dir($dir)) { @mkdir($dir, 0775, true); }

$ts = date('Ymd_His');
$path = $dir . '/' . $kind . '_' . ($expId ?: 'all') . '_' . $ts . '.csv';

$fp = fopen($path, 'w');
if (!$fp) {
  echo '<section class="py-5"><div class="container"><div class="alert alert-danger">Cannot write to storage/exports. Check permissions.</div></div></section>';
  return;
}

// map participant+experiment => type
$creds = db_all("SELECT participant_id, experiment_id, type, metrics_json FROM credentials WHERE (:e=0 OR experiment_id=:e)", [':e'=>$expId]);
$ptypeByPE = [];
$credRows = [];
foreach ($creds as $c) {
  $ptypeByPE[$c['participant_id'].'|'.$c['experiment_id']] = $c['type'];
  $m = json_decode($c['metrics_json'] ?? '[]', true) ?: [];
  $c['_len']   = $m['len']   ?? null;
  $c['_upper'] = isset($m['has_upper']) ? (int)$m['has_upper'] : null;
  $c['_lower'] = isset($m['has_lower']) ? (int)$m['has_lower'] : null;
  $c['_digit'] = isset($m['has_digit']) ? (int)$m['has_digit'] : null;
  $c['_symbol']= isset($m['has_symbol'])? (int)$m['has_symbol']: null;
  $c['_nodes'] = $m['nodes'] ?? null;
  $c['_create_ms'] = $m['create_time_ms'] ?? null;
  $credRows[] = $c;
}

if ($kind === 'credentials') {
  fputcsv($fp, ['participant_id','experiment_id','type','create_time_ms','pw_len','pw_has_upper','pw_has_lower','pw_has_digit','pw_has_symbol','pt_nodes']);
  foreach ($credRows as $r) {
    fputcsv($fp, [
      $r['participant_id'], $r['experiment_id'], $r['type'],
      $r['_create_ms'], $r['_len'], $r['_upper'], $r['_lower'], $r['_digit'], $r['_symbol'], $r['_nodes'],
    ]);
  }
} else { // attempts
  $rows = db_all("
    SELECT a.session_id, a.attempt_no, a.success, a.time_ms, a.created_at,
           s.participant_id, s.experiment_id
    FROM attempts a
    JOIN sessions s ON s.id = a.session_id
    WHERE (:e=0 OR s.experiment_id=:e)
    ORDER BY a.session_id, a.attempt_no
  ", [':e'=>$expId]);

  fputcsv($fp, ['session_id','participant_id','experiment_id','type','attempt_no','success','time_ms','attempt_created_at']);
  foreach ($rows as $a) {
    $type = $ptypeByPE[$a['participant_id'].'|'.$a['experiment_id']] ?? '';
    fputcsv($fp, [
      $a['session_id'], $a['participant_id'], $a['experiment_id'], $type,
      $a['attempt_no'], $a['success'], $a['time_ms'], $a['created_at'],
    ]);
  }
}

fclose($fp);

$url = url('storage/exports/' . basename($path));
?>
<section class="py-5">
  <div class="container">
    <h2 class="mb-3">Export</h2>
    <div class="alert alert-success mb-3">CSV generated successfully.</div>
    <a class="btn btn-primary" href="<?= h($url) ?>">Download CSV</a>
    <a class="btn btn-outline-secondary ms-2" href="<?= url('?p=analytics&e='.$expId) ?>">Back to Analytics</a>
  </div>
</section>
