<?php
// /pages/analytics.php
require_once __DIR__ . '/../inc/db.php';

// --- helpers (use global h() from bootstrap) ---
function num($v, $d = 2) { return number_format((float)$v, $d); }
function median(array $a) {
  if (!$a) return 0;
  sort($a, SORT_NUMERIC);
  $n = count($a);
  $m = intdiv($n, 2);
  return ($n % 2) ? $a[$m] : (($a[$m-1] + $a[$m]) / 2);
}
function dist_count(array $arr, int $max=3): array {
  $c = array_fill(1, $max, 0);
  foreach ($arr as $v) {
    $v = (int)$v;
    if ($v >= 1 && $v <= $max) $c[$v]++;
  }
  return array_values($c); // [c1,c2,c3]
}

// --- Scope (experiment) ---
$experiments = db_all("SELECT id, title, modality FROM experiments ORDER BY created_at DESC");
$expId = (int)($_GET['e'] ?? 0);
$whereExp = $expId > 0 ? " WHERE experiment_id = :e" : "";
$whereSes = $expId > 0 ? " WHERE s.experiment_id = :e" : "";
$params  = $expId > 0 ? [':e' => $expId] : [];

// --- Method Focus (All / Password / Pattern) ---
$method = strtolower($_GET['m'] ?? 'all');
if (!in_array($method, ['all','password','pattern'], true)) $method = 'all';
$activeTypes = $method === 'all' ? ['password','pattern'] : [$method];

// --- topline counts (Scope-wide) ---
$parts = ['invited'=>0,'joined'=>0,'completed'=>0];
$rows = db_all("SELECT status, COUNT(*) c FROM participants{$whereExp} GROUP BY status", $params);
foreach ($rows as $r) { $parts[$r['status']] = (int)$r['c']; }
$sessions = (int)(db_one("SELECT COUNT(*) c FROM sessions{$whereExp}", $params)['c'] ?? 0);

// --- credentials (Scope-wide; feed modality stats) ---
$creds = db_all("SELECT participant_id, experiment_id, type, metrics_json FROM credentials{$whereExp}", $params);
$ptypeByPE   = [];             // participant|experiment -> type
$participantsByType = ['password'=>0,'pattern'=>0];
$createTimes = ['password'=>[],'pattern'=>[]];

foreach ($creds as $c) {
  $key = $c['participant_id'].'|'.$c['experiment_id'];
  $ptypeByPE[$key] = $c['type'];
  if (isset($participantsByType[$c['type']])) $participantsByType[$c['type']]++;
  $m = json_decode($c['metrics_json'] ?? '[]', true) ?: [];
  if (isset($m['create_time_ms'])) $createTimes[$c['type']][] = (int)$m['create_time_ms'];
}

// --- attempts (Scope-wide) ---
$attRows = db_all("
  SELECT a.session_id, a.attempt_no, a.success, a.time_ms, s.participant_id, s.experiment_id
  FROM attempts a
  JOIN sessions s ON s.id = a.session_id
  {$whereSes}
  ORDER BY a.session_id, a.attempt_no
", $params);

$mod = [
  'password' => [
    'participants' => $participantsByType['password'],
    'sessions' => 0,
    'attempts' => 0,
    'success' => 0,
    'times_success' => [],
    'first_total' => 0,
    'first_success' => 0,
    'attempts_to_success' => [],
    'create_times' => $createTimes['password'],
  ],
  'pattern' => [
    'participants' => $participantsByType['pattern'],
    'sessions' => 0,
    'attempts' => 0,
    'success' => 0,
    'times_success' => [],
    'first_total' => 0,
    'first_success' => 0,
    'attempts_to_success' => [],
    'create_times' => $createTimes['pattern'],
  ],
];

$seenSessionType = [];          // type => set(session_id)
$firstSuccessSeen = [];         // session_id => attempt_no of first success

$totalAttempts = 0; $totalSuccess = 0;

foreach ($attRows as $a) {
  $key  = $a['participant_id'].'|'.$a['experiment_id'];
  $type = $ptypeByPE[$key] ?? null;
  if (!$type || !isset($mod[$type])) continue;

  $sid = (int)$a['session_id'];
  $totalAttempts++;
  if ((int)$a['success'] === 1) $totalSuccess++;

  if (!isset($seenSessionType[$type][$sid])) {
    $seenSessionType[$type][$sid] = true;
    $mod[$type]['sessions']++;
  }

  $mod[$type]['attempts']++;
  if ((int)$a['attempt_no'] === 1) {
    $mod[$type]['first_total']++;
    if ((int)$a['success'] === 1) $mod[$type]['first_success']++;
  }

  if ((int)$a['success'] === 1) {
    $mod[$type]['success']++;
    $mod[$type]['times_success'][] = (int)$a['time_ms'];
    if (!isset($firstSuccessSeen[$sid])) {
      $firstSuccessSeen[$sid] = (int)$a['attempt_no'];
      $mod[$type]['attempts_to_success'][] = (int)$a['attempt_no'];
    }
  }
}

// --- global headline (Scope) ---
$overallSuccessRate = $totalAttempts ? ($totalSuccess / $totalAttempts * 100) : 0;
$firstAttemptSuccessRate = 0;
if (!empty($seenSessionType['password']) || !empty($seenSessionType['pattern'])) {
  $firstTotal = ($mod['password']['first_total'] + $mod['pattern']['first_total']);
  $firstSucc  = ($mod['password']['first_success'] + $mod['pattern']['first_success']);
  $firstAttemptSuccessRate = $firstTotal ? ($firstSucc / $firstTotal * 100) : 0;
}

// --- modality stats helper ---
function modality_stats(array $m): array {
  $avgCreate  = $m['create_times'] ? array_sum($m['create_times'])/count($m['create_times']) : 0;
  $medCreate  = median($m['create_times']);
  $avgLoginOk = $m['times_success'] ? array_sum($m['times_success'])/count($m['times_success']) : 0;
  $medLoginOk = median($m['times_success']);
  $succRate   = $m['attempts'] ? ($m['success'] / $m['attempts'] * 100) : 0;
  $medAttemptsToSuccess = median($m['attempts_to_success']);
  return [
    'participants' => (int)$m['participants'],
    'sessions'     => (int)$m['sessions'],
    'attempts'     => (int)$m['attempts'],
    'success_rate' => $succRate,
    'first_attempt_success' => ($m['first_total'] ? ($m['first_success'] / $m['first_total'] * 100) : 0),
    'avg_create'   => $avgCreate,
    'med_create'   => $medCreate,
    'avg_login_ok' => $avgLoginOk,
    'med_login_ok' => $medLoginOk,
    'med_attempts_to_success' => $medAttemptsToSuccess,
  ];
}

$statsPw = modality_stats($mod['password']);
$statsPt = modality_stats($mod['pattern']);

// --- Method Focus aggregation ---
$focus = [
  'participants' => 0,
  'sessions' => 0,
  'attempts' => 0,
  'success' => 0,
  'first_total' => 0,
  'first_success' => 0,
  'create_times' => [],
  'times_success' => [],
  'attempts_to_success' => [],
];
foreach ($activeTypes as $t) {
  $focus['participants'] += $mod[$t]['participants'];
  $focus['sessions']     += $mod[$t]['sessions'];
  $focus['attempts']     += $mod[$t]['attempts'];
  $focus['success']      += $mod[$t]['success'];
  $focus['first_total']  += $mod[$t]['first_total'];
  $focus['first_success']+= $mod[$t]['first_success'];
  $focus['create_times'] = array_merge($focus['create_times'], $mod[$t]['create_times']);
  $focus['times_success']= array_merge($focus['times_success'], $mod[$t]['times_success']);
  $focus['attempts_to_success'] = array_merge($focus['attempts_to_success'], $mod[$t]['attempts_to_success']);
}
$focusStats = modality_stats($focus);

// --- chart data (PHP -> JS) ---
$chartData = [
  'participation' => [
    'labels' => ['Invited','Joined','Completed','Sessions'],
    'data'   => [ (int)$parts['invited'], (int)$parts['joined'], (int)$parts['completed'], (int)$sessions ],
  ],
  'modalitySuccess' => [
    'labels' => ['Password','Pattern'],
    'overall' => [ $statsPw['success_rate'], $statsPt['success_rate'] ],
    'first'   => [ $statsPw['first_attempt_success'], $statsPt['first_attempt_success'] ],
  ],
  'modalityTimes' => [
    'labels' => ['Password','Pattern'],
    'avgCreate' => [ $statsPw['avg_create'], $statsPt['avg_create'] ],
    'avgLoginOk'=> [ $statsPw['avg_login_ok'], $statsPt['avg_login_ok'] ],
  ],
  'focus' => [
    'method' => $method,
    'labels' => ['1st Attempt','2nd Attempt','3rd Attempt'],
    'attemptDist' => dist_count($focus['attempts_to_success'], 3),
  ],
];

$baseUrl = url('?p=analytics&e='.$expId);
?>
<section class="py-5">
  <div class="container">
    <h2 class="mb-3">Analytics <?= help_icon('Scope filters experiments; Method Focus aggregates by Password/Pattern.'); ?></h2>

    <!-- Scope + Exports -->
    <form class="mb-3" method="get" action="<?= h(url('?p=analytics')) ?>">
      <input type="hidden" name="p" value="analytics">
      <label class="form-label">Scope <?= help_icon('Pick one experiment to focus, or All Experiments to combine everything.'); ?></label>
      <div class="row g-2 align-items-center">
        <div class="col-md-8">
          <select name="e" class="form-select" onchange="this.form.submit()">
            <option value="0">All Experiments (combined)</option>
            <?php foreach ($experiments as $ex): ?>
              <option value="<?= (int)$ex['id'] ?>" <?= $expId===(int)$ex['id']?'selected':''; ?>>
                <?= h($ex['title']) ?> (<?= h($ex['modality']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4 text-md-end">
          <a class="btn btn-outline-secondary me-2" href="<?= url('?p=export&kind=credentials&e='.$expId) ?>">Export Credentials CSV</a>
          <a class="btn btn-outline-secondary" href="<?= url('?p=export&kind=attempts&e='.$expId) ?>">Export Attempts CSV</a>
        </div>
      </div>
    </form>

    <!-- Headline (Scope-wide) -->
    <div class="row g-3 mb-4">
      <div class="col-md-3"><div class="card"><div class="card-body">
        <div class="text-muted small">Invited</div><div class="fs-4"><?= (int)$parts['invited'] ?></div>
      </div></div></div>
      <div class="col-md-3"><div class="card"><div class="card-body">
        <div class="text-muted small">Joined</div><div class="fs-4"><?= (int)$parts['joined'] ?></div>
      </div></div></div>
      <div class="col-md-3"><div class="card"><div class="card-body">
        <div class="text-muted small">Completed</div><div class="fs-4"><?= (int)$parts['completed'] ?></div>
      </div></div></div>
      <div class="col-md-3"><div class="card"><div class="card-body">
        <div class="text-muted small">Sessions</div><div class="fs-4"><?= (int)$sessions ?></div>
      </div></div></div>
    </div>

    <div class="row g-3 mb-4">
      <div class="col-md-6"><div class="card"><div class="card-body">
        <div class="text-muted small">Total Attempts (Scope)</div>
        <div class="fs-4"><?= (int)$totalAttempts ?></div>
      </div></div></div>
      <div class="col-md-6"><div class="card"><div class="card-body">
        <div class="text-muted small">Overall Success Rate (Scope)</div>
        <div class="fs-4"><?= num($overallSuccessRate) ?>%</div>
        <div class="text-muted small">First-Attempt (per session): <?= num($firstAttemptSuccessRate) ?>%</div>
      </div></div></div>
    </div>

    <!-- Method Focus pills -->
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h5 class="mb-0">Method Focus</h5>
      <ul class="nav nav-pills">
        <li class="nav-item"><a class="nav-link <?= $method==='all'?'active':'' ?>" href="<?= $baseUrl ?>&m=all">All</a></li>
        <li class="nav-item"><a class="nav-link <?= $method==='password'?'active':'' ?>" href="<?= $baseUrl ?>&m=password">Password</a></li>
        <li class="nav-item"><a class="nav-link <?= $method==='pattern'?'active':'' ?>" href="<?= $baseUrl ?>&m=pattern">Pattern</a></li>
      </ul>
    </div>

    <!-- Method Focus: Attempts to Success distribution -->
    <div class="card mb-4">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <h6 class="card-title mb-0">Attempts to Success (<?= ucfirst($method) ?>)</h6>
          <span class="text-muted small">Counts of sessions that succeeded on 1st, 2nd, or 3rd attempt.</span>
        </div>
        <canvas id="chartFocusAttempts" height="120"></canvas>
      </div>
    </div>

    <!-- Global Modality Charts -->
    <div class="row g-4 mb-4">
      <div class="col-md-6">
        <div class="card h-100">
          <div class="card-body">
            <h6 class="card-title">Modality Success Rates</h6>
            <canvas id="chartSuccessRates" height="160"></canvas>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card h-100">
          <div class="card-body">
            <h6 class="card-title">Avg Times (ms)</h6>
            <canvas id="chartAvgTimes" height="160"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Global Modality Summary (table stays) -->
    <div class="card mb-4">
      <div class="card-body">
        <h5 class="card-title">Global Modality Summary</h5>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr><th>Metric</th><th>Password</th><th>Pattern</th></tr>
            </thead>
            <tbody>
              <tr><th>Participants</th><td><?= $statsPw['participants'] ?></td><td><?= $statsPt['participants'] ?></td></tr>
              <tr><th>Sessions</th><td><?= $mod['password']['sessions'] ?></td><td><?= $mod['pattern']['sessions'] ?></td></tr>
              <tr><th>Total Attempts</th><td><?= $mod['password']['attempts'] ?></td><td><?= $mod['pattern']['attempts'] ?></td></tr>
              <tr><th>Success Rate</th><td><?= num($statsPw['success_rate']) ?>%</td><td><?= num($statsPt['success_rate']) ?>%</td></tr>
              <tr><th>First-Attempt Success (per session)</th><td><?= num($statsPw['first_attempt_success']) ?>%</td><td><?= num($statsPt['first_attempt_success']) ?>%</td></tr>
              <tr><th>Avg Create Time</th><td><?= $statsPw['avg_create'] ? num($statsPw['avg_create']).' ms' : '—' ?></td><td><?= $statsPt['avg_create'] ? num($statsPt['avg_create']).' ms' : '—' ?></td></tr>
              <tr><th>Avg Login Time (success only)</th><td><?= $statsPw['avg_login_ok'] ? num($statsPw['avg_login_ok']).' ms' : '—' ?></td><td><?= $statsPt['avg_login_ok'] ? num($statsPt['avg_login_ok']).' ms' : '—' ?></td></tr>
              <tr><th>Median Create Time</th><td><?= $statsPw['med_create'] ? num($statsPw['med_create']).' ms' : '—' ?></td><td><?= $statsPt['med_create'] ? num($statsPt['med_create']).' ms' : '—' ?></td></tr>
              <tr><th>Median Login Time (success only)</th><td><?= $statsPw['med_login_ok'] ? num($statsPw['med_login_ok']).' ms' : '—' ?></td><td><?= $statsPt['med_login_ok'] ? num($statsPt['med_login_ok']).' ms' : '—' ?></td></tr>
              <tr><th>Median Attempts to Success</th><td><?= $statsPw['med_attempts_to_success'] ? num($statsPw['med_attempts_to_success'],0) : '—' ?></td><td><?= $statsPt['med_attempts_to_success'] ? num($statsPt['med_attempts_to_success'],0) : '—' ?></td></tr>
            </tbody>
          </table>
        </div>
        <div class="text-muted small">Side-by-side comparison within the current Scope.</div>
      </div>
    </div>

    <!-- Participation chart -->
    <div class="card">
      <div class="card-body">
        <h6 class="card-title">Participation</h6>
        <canvas id="chartParticipation" height="120"></canvas>
      </div>
    </div>

  </div>
</section>

<!-- Chart.js (only on this page) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function(){
  const DATA = <?= json_encode($chartData, JSON_UNESCAPED_SLASHES) ?>;

  function mk(ctx, cfg){ return new Chart(ctx, cfg); }
  function pct(v){ return Math.round((v + Number.EPSILON) * 100) / 100; }

  // 1) Method Focus: Attempts to Success distribution
  const focusCtx = document.getElementById('chartFocusAttempts');
  if (focusCtx) {
    mk(focusCtx, {
      type: 'bar',
      data: {
        labels: DATA.focus.labels,
        datasets: [{
          label: 'Sessions',
          data: DATA.focus.attemptDist,
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false },
          tooltip: { callbacks: { label: (c)=> ` ${c.parsed.y} session(s)` } },
          title: { display:false }
        },
        scales: {
          y: { beginAtZero: true, ticks: { precision:0 } }
        }
      }
    });
  }

  // 2) Modality Success Rates (overall + first attempt)
  const succCtx = document.getElementById('chartSuccessRates');
  if (succCtx) {
    mk(succCtx, {
      type: 'bar',
      data: {
        labels: DATA.modalitySuccess.labels,
        datasets: [
          { label: 'Overall Success %', data: DATA.modalitySuccess.overall },
          { label: 'First Attempt %',  data: DATA.modalitySuccess.first }
        ]
      },
      options: {
        responsive:true,
        scales: { y: { beginAtZero:true, max:100, ticks:{ callback:(v)=> v + '%' } } }
      }
    });
  }

  // 3) Avg Times (ms) – grouped bars
  const timesCtx = document.getElementById('chartAvgTimes');
  if (timesCtx) {
    mk(timesCtx, {
      type: 'bar',
      data: {
        labels: DATA.modalityTimes.labels,
        datasets: [
          { label: 'Avg Create Time (ms)', data: DATA.modalityTimes.avgCreate },
          { label: 'Avg Login Time – Success (ms)', data: DATA.modalityTimes.avgLoginOk }
        ]
      },
      options: {
        responsive:true,
        scales: { y: { beginAtZero:true } }
      }
    });
  }

  // 4) Participation
  const partCtx = document.getElementById('chartParticipation');
  if (partCtx) {
    mk(partCtx, {
      type: 'bar',
      data: {
        labels: DATA.participation.labels,
        datasets: [{ label: 'Count', data: DATA.participation.data }]
      },
      options: {
        responsive:true,
        scales: { y: { beginAtZero:true, ticks: { precision:0 } } }
      }
    });
  }
})();
</script>
