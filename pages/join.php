<?php
// /pages/join.php
require_once __DIR__ . '/../inc/db.php';

if (!isset($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(16)); }

// ---- Load invite/experiment
$code = strtoupper(trim($_GET['code'] ?? ''));
$invite = null;
if ($code !== '') {
  $invite = db_one("SELECT p.id AS pid, p.status, p.experiment_id,
                           e.title, e.modality, e.policy_json
                    FROM participants p
                    JOIN experiments e ON e.id = p.experiment_id
                    WHERE p.code = :c", [':c'=>$code]);
}
if (!$invite) { ?>
  <section class="py-5"><div class="container">
    <h2 class="mb-3">Join Experiment <?= help_icon('Participants use an invite link/code to begin.'); ?></h2>
    <div class="alert alert-danger">Invalid or missing invite code.</div>
    <a class="btn btn-primary" href="<?= url('?p=home'); ?>">Back to Home</a>
  </div></section>
<?php return; }

/* ---------- Hard-expire consumed invites (one link = one run) ---------- */
$latestSession = db_one(
  "SELECT id, ended_at FROM sessions
   WHERE participant_id=:p AND experiment_id=:e
   ORDER BY id DESC LIMIT 1",
  [':p' => (int)$invite['pid'], ':e' => (int)$invite['experiment_id']]
);
$alreadyFinished = false;
if (($invite['status'] ?? '') === 'completed') {
  $alreadyFinished = true;
} elseif ($latestSession && !empty($latestSession['ended_at'])) {
  $alreadyFinished = true;
}
if ($alreadyFinished) { ?>
  <section class="py-5"><div class="container">
    <h2 class="mb-3">Join Experiment</h2>
    <div class="alert alert-warning">This invite has already been used (expired).</div>
    <a class="btn btn-primary" href="<?= url('?p=home'); ?>">Back to Home</a>
  </div></section>
<?php return; }
/* ---------------------------------------------------------------------- */

$policy = json_decode($invite['policy_json'] ?? '[]', true) ?: [];
$pw = $policy['password'] ?? ['min_length'=>8,'require_upper'=>true,'require_lower'=>true,'require_digit'=>true,'require_symbol'=>false];
$pt = $policy['pattern']  ?? ['grid'=>3,'min_nodes'=>4,'allow_cross'=>false];

$participant_id = (int)$invite['pid'];
$experiment_id  = (int)$invite['experiment_id'];
$modality       = $invite['modality']; // 'password' | 'pattern' | 'ab'

// A/B randomization (sticky per participant)
if ($modality === 'ab') {
  if (!isset($_SESSION['ab_assign'][$participant_id])) {
    $_SESSION['ab_assign'][$participant_id] = (random_int(0,1) === 0) ? 'password' : 'pattern';
  }
  $modality = $_SESSION['ab_assign'][$participant_id];
}

// fetch session if exists (resume if in progress)
$session = db_one("SELECT * FROM sessions WHERE participant_id=:p AND experiment_id=:e ORDER BY id DESC LIMIT 1",
                  [':p'=>$participant_id, ':e'=>$experiment_id]);

// ---------- Helpers (password) ----------
function pw_metrics(string $s): array {
  return [
    'len' => strlen($s),
    'has_upper' => (bool)preg_match('/[A-Z]/', $s),
    'has_lower' => (bool)preg_match('/[a-z]/', $s),
    'has_digit' => (bool)preg_match('/\d/', $s),
    'has_symbol'=> (bool)preg_match('/[^A-Za-z0-9]/', $s),
  ];
}
function pw_valid(string $s, array $rules, array &$errs): bool {
  if (strlen($s) < (int)($rules['min_length'] ?? 8)) $errs[] = "Password must be at least {$rules['min_length']} characters.";
  if (!empty($rules['require_upper']) && !preg_match('/[A-Z]/', $s)) $errs[] = "Include at least one uppercase letter.";
  if (!empty($rules['require_lower']) && !preg_match('/[a-z]/', $s)) $errs[] = "Include at least one lowercase letter.";
  if (!empty($rules['require_digit']) && !preg_match('/\d/', $s)) $errs[]  = "Include at least one digit.";
  if (!empty($rules['require_symbol']) && !preg_match('/[^A-Za-z0-9]/', $s)) $errs[] = "Include at least one symbol.";
  return empty($errs);
}

// ---------- Helpers (pattern) ----------
function pt_bridge_between(int $a, int $b): ?int {
  // 3x3 grid indexed 1..9 like:
  // 1 2 3
  // 4 5 6
  // 7 8 9
  $map = [
    '1-3'=>2, '3-1'=>2,
    '1-7'=>4, '7-1'=>4,
    '3-9'=>6, '9-3'=>6,
    '7-9'=>8, '9-7'=>8,
    '1-9'=>5, '9-1'=>5,
    '3-7'=>5, '7-3'=>5,
    '2-8'=>5, '8-2'=>5,
    '4-6'=>5, '6-4'=>5,
  ];
  $k = $a.'-'.$b;
  return $map[$k] ?? null;
}
function pt_valid(array $seq, array $rules, array &$errs): bool {
  $min = (int)($rules['min_nodes'] ?? 4);
  if (count($seq) < $min) $errs[] = "Use at least {$min} nodes.";
  if (count($seq) !== count(array_unique($seq))) $errs[] = "Do not repeat nodes.";
  if (empty($rules['allow_cross'])) {
    $visited = [];
    for ($i=0; $i < count($seq)-1; $i++) {
      $a = (int)$seq[$i]; $b = (int)$seq[$i+1];
      $bridge = pt_bridge_between($a, $b);
      if ($bridge && !in_array($bridge, $visited, true)) {
        $errs[] = "You cannot jump over node {$bridge} unless you used it first.";
        break;
      }
      $visited[] = $a;
    }
    $visited[] = end($seq);
  }
  return empty($errs);
}
function pt_metrics(array $seq, int $elapsed_ms): array {
  return [
    'nodes' => count($seq),
    'create_time_ms' => $elapsed_ms,
  ];
}
function pt_hash(array $seq): string {
  // one-way, non-reversible representation of normalized sequence
  global $app;
  $salt = $app['pattern_salt'] ?? 'change-this-salt';
  $payload = implode('-', array_map('intval', $seq));
  return hash_hmac('sha256', $payload, $salt);
}

// ---------- State ----------
$errors = [];
$flash  = null;

// ---- Begin session (only if not started)
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && ($_POST['action'] ?? '') === 'start') {
  if (hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
    if (!$session) {
      db_exec("INSERT INTO sessions (participant_id, experiment_id) VALUES (:p,:e)",
              [':p'=>$participant_id, ':e'=>$experiment_id]);
      $session = db_one("SELECT * FROM sessions WHERE participant_id=:p AND experiment_id=:e ORDER BY id DESC LIMIT 1",
                        [':p'=>$participant_id, ':e'=>$experiment_id]);
      db_exec("UPDATE participants SET status='joined', joined_at=CURRENT_TIMESTAMP WHERE id=:id", [':id'=>$participant_id]);
    }
  } else {
    $errors[] = 'Invalid CSRF token.';
  }
}

// has credential?
$cred = db_one("SELECT * FROM credentials WHERE participant_id=:p AND experiment_id=:e LIMIT 1",
               [':p'=>$participant_id, ':e'=>$experiment_id]);

// ---- Create password credential
if ($session && $modality === 'password' && !$cred &&
    (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') && (($_POST['action'] ?? '') === 'create_password')) {

  if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
    $errors[] = 'Invalid CSRF token.';
  } else {
    $pass1 = (string)($_POST['pass1'] ?? '');
    $pass2 = (string)($_POST['pass2'] ?? '');
    if ($pass1 !== $pass2) $errors[] = 'Passwords do not match.';
    $v = [];
    if (pw_valid($pass1, $pw, $v)) {
      $metrics = pw_metrics($pass1);
      $elapsed = max(0, (int)($_POST['elapsed_ms'] ?? 0));
      $metrics['create_time_ms'] = $elapsed;
      $algo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT;
      $hash = password_hash($pass1, $algo);
      db_exec("INSERT INTO credentials (participant_id, experiment_id, type, verifier_hash, metrics_json)
               VALUES (:p,:e,'password',:h,:m)",
              [':p'=>$participant_id, ':e'=>$experiment_id, ':h'=>$hash, ':m'=>json_encode($metrics, JSON_UNESCAPED_SLASHES)]);
      $cred = db_one("SELECT * FROM credentials WHERE participant_id=:p AND experiment_id=:e LIMIT 1",
                     [':p'=>$participant_id, ':e'=>$experiment_id]);
      $flash = 'Password saved. Now try logging in.';
    } else {
      $errors = array_merge($errors, $v);
    }
  }
}

// ---- Create pattern credential
if ($session && $modality === 'pattern' && !$cred &&
    (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') && (($_POST['action'] ?? '') === 'create_pattern')) {

  if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
    $errors[] = 'Invalid CSRF token.';
  } else {
    $raw = (string)($_POST['vector'] ?? '');                // e.g., "1-5-9"
    $seq = array_values(array_filter(array_map('intval', explode('-', $raw))));
    $v = [];
    if (pt_valid($seq, $pt, $v)) {
      $elapsed = max(0, (int)($_POST['elapsed_ms'] ?? 0));
      $metrics = pt_metrics($seq, $elapsed);
      $hash = pt_hash($seq);
      db_exec("INSERT INTO credentials (participant_id, experiment_id, type, verifier_hash, metrics_json)
               VALUES (:p,:e,'pattern',:h,:m)",
              [':p'=>$participant_id, ':e'=>$experiment_id, ':h'=>$hash, ':m'=>json_encode($metrics, JSON_UNESCAPED_SLASHES)]);
      $cred = db_one("SELECT * FROM credentials WHERE participant_id=:p AND experiment_id=:e LIMIT 1",
                     [':p'=>$participant_id, ':e'=>$experiment_id]);
      $flash = 'Pattern saved. Now try logging in.';
    } else {
      $errors = array_merge($errors, $v);
    }
  }
}

// ---- Handle password login attempts
if ($session && $cred && $modality === 'password' &&
    (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') && (($_POST['action'] ?? '') === 'login_password')) {

  if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
    $errors[] = 'Invalid CSRF token.';
  } else {
    $input = (string)($_POST['pass'] ?? '');
    $elapsed = max(0, (int)($_POST['elapsed_ms'] ?? 0));
    $tries = (int)(db_one("SELECT COUNT(*) AS c FROM attempts WHERE session_id=:s", [':s'=>$session['id']])['c'] ?? 0);
    $attempt_no = $tries + 1;

    $ok = password_verify($input, $cred['verifier_hash']);
    db_exec("INSERT INTO attempts (session_id, attempt_no, success, time_ms) VALUES (:s,:n,:ok,:ms)",
            [':s'=>$session['id'], ':n'=>$attempt_no, ':ok'=>$ok?1:0, ':ms'=>$elapsed]);

    if ($ok) {
      $flash = 'Login successful. Thank you!';
      db_exec("UPDATE sessions SET ended_at=CURRENT_TIMESTAMP WHERE id=:id", [':id'=>$session['id']]);
      db_exec("UPDATE participants SET status='completed' WHERE id=:id", [':id'=>$participant_id]);
    } elseif ($attempt_no >= 3) {
      $errors[] = 'Maximum attempts reached.';
      db_exec("UPDATE sessions SET ended_at=CURRENT_TIMESTAMP WHERE id=:id", [':id'=>$session['id']]);
      db_exec("UPDATE participants SET status='completed' WHERE id=:id", [':id'=>$participant_id]); // expire invite on fail
    } else {
      $errors[] = 'Incorrect password. Try again.';
    }
  }
}

// ---- Handle pattern login attempts
if ($session && $cred && $modality === 'pattern' &&
    (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') && (($_POST['action'] ?? '') === 'login_pattern')) {

  if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
    $errors[] = 'Invalid CSRF token.';
  } else {
    $raw = (string)($_POST['vector'] ?? '');
    $seq = array_values(array_filter(array_map('intval', explode('-', $raw))));
    $elapsed = max(0, (int)($_POST['elapsed_ms'] ?? 0));
    $tries = (int)(db_one("SELECT COUNT(*) AS c FROM attempts WHERE session_id=:s", [':s'=>$session['id']])['c'] ?? 0);
    $attempt_no = $tries + 1;

    $ok = hash_equals(pt_hash($seq), $cred['verifier_hash']);
    db_exec("INSERT INTO attempts (session_id, attempt_no, success, time_ms) VALUES (:s,:n,:ok,:ms)",
            [':s'=>$session['id'], ':n'=>$attempt_no, ':ok'=>$ok?1:0, ':ms'=>$elapsed]);

    if ($ok) {
      $flash = 'Login successful. Thank you!';
      db_exec("UPDATE sessions SET ended_at=CURRENT_TIMESTAMP WHERE id=:id", [':id'=>$session['id']]);
      db_exec("UPDATE participants SET status='completed' WHERE id=:id", [':id'=>$participant_id]);
    } elseif ($attempt_no >= 3) {
      $errors[] = 'Maximum attempts reached.';
      db_exec("UPDATE sessions SET ended_at=CURRENT_TIMESTAMP WHERE id=:id", [':id'=>$session['id']]);
      db_exec("UPDATE participants SET status='completed' WHERE id=:id", [':id'=>$participant_id]); // expire invite on fail
    } else {
      $errors[] = 'Pattern did not match. Try again.';
    }
  }
}

// Current attempts count (for UI)
$attempts = $session
  ? (int)(db_one("SELECT COUNT(*) AS c FROM attempts WHERE session_id=:s", [':s'=>$session['id']])['c'] ?? 0)
  : 0;

?>
<section class="py-5">
  <div class="container">
    <h2 class="mb-3">Join Experiment <?= help_icon('Participants use an invite link/code to begin.'); ?></h2>

    <div class="alert alert-info">
      <strong>Welcome!</strong> You’re joining <em><?= h($invite['title']) ?></em>
      (modality: <code><?= h($modality) ?></code> <?= help_icon('Determines whether you’ll create a password or a pattern.'); ?>).
    </div>

    <?php foreach ($errors as $e): ?>
      <div class="alert alert-danger"><?= h($e) ?></div>
    <?php endforeach; ?>
    <?php if ($flash): ?>
      <div class="alert alert-success"><?= h($flash) ?></div>
    <?php endif; ?>

    <?php if (!$session): ?>
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Begin</h5>
          <p>Click start to begin. We’ll time how long the steps take.</p>
          <form method="post" action="<?= h(url('?p=join&code='.$code)) ?>">
            <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
            <input type="hidden" name="action" value="start">
            <button class="btn btn-primary">Start</button>
          </form>
        </div>
      </div>

    <?php elseif ($modality === 'password' && !$cred): ?>
      <!-- Password creation -->
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Create Password <?= help_icon('We’ll enforce the rules shown below and record creation time.'); ?></h5>
          <ul class="text-muted small">
            <li>Min length: <strong><?= (int)$pw['min_length'] ?></strong></li>
            <li>Require:
              <?= !empty($pw['require_upper'])?'Uppercase, ':'' ?>
              <?= !empty($pw['require_lower'])?'Lowercase, ':'' ?>
              <?= !empty($pw['require_digit'])?'Digit, ':'' ?>
              <?= !empty($pw['require_symbol'])?'Symbol':'' ?>
            </li>
          </ul>
          <form id="createForm" method="post" action="<?= h(url('?p=join&code='.$code)) ?>">
            <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
            <input type="hidden" name="action" value="create_password">
            <input type="hidden" name="elapsed_ms" id="create_elapsed" value="0">
            <div class="mb-3"><label class="form-label">Password</label><input type="password" name="pass1" class="form-control" required autocomplete="new-password"></div>
            <div class="mb-3"><label class="form-label">Confirm Password</label><input type="password" name="pass2" class="form-control" required autocomplete="new-password"></div>
            <button class="btn btn-primary">Save Password</button>
          </form>
        </div>
      </div>
      <script>
      (function(){
        var t0=performance.now();
        document.getElementById('createForm').addEventListener('submit',function(){
          document.getElementById('create_elapsed').value=Math.round(performance.now()-t0);
        });
      })();
      </script>

    <?php elseif ($modality === 'password' && $cred): ?>
      <!-- Password login -->
      <?php $done = ($attempts >= 3) || (db_one("SELECT success FROM attempts WHERE session_id=:s ORDER BY id DESC LIMIT 1", [':s'=>$session['id']])['success'] ?? 0); ?>
      <?php if ($done): ?>
        <div class="alert alert-success">This session is finished. Thank you!</div>
        <a class="btn btn-primary" href="<?= url('?p=home'); ?>">Back to Home</a>
      <?php else: ?>
        <div class="card"><div class="card-body">
          <h5 class="card-title">Login Attempt <?= ($attempts+1) ?> of 3 <?= help_icon('Enter the password you just created. We time each attempt.'); ?></h5>
          <form id="loginForm" method="post" action="<?= h(url('?p=join&code='.$code)) ?>">
            <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
            <input type="hidden" name="action" value="login_password">
            <input type="hidden" name="elapsed_ms" id="login_elapsed" value="0">
            <div class="mb-3"><label class="form-label">Password</label><input type="password" name="pass" class="form-control" required autocomplete="current-password"></div>
            <button class="btn btn-primary">Submit</button>
          </form>
        </div></div>
        <script>
        (function(){
          var t0=performance.now();
          document.getElementById('loginForm').addEventListener('submit',function(){
            document.getElementById('login_elapsed').value=Math.round(performance.now()-t0);
          });
        })();
        </script>
      <?php endif; ?>

    <?php elseif ($modality === 'pattern' && !$cred): ?>
      <!-- Pattern creation -->
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Create Pattern <?= help_icon('Connect dots to form your unlock pattern. We record creation time.'); ?></h5>
          <ul class="text-muted small">
            <li>Grid: <strong><?= (int)$pt['grid'] ?>×<?= (int)$pt['grid'] ?></strong> (currently 3×3 supported)</li>
            <li>Min nodes: <strong><?= (int)$pt['min_nodes'] ?></strong></li>
            <li><?= empty($pt['allow_cross']) ? 'No crossings allowed (unless the intermediate dot is used first).' : 'Crossings allowed.' ?></li>
          </ul>

          <form id="ptCreateForm" method="post" action="<?= h(url('?p=join&code='.$code)) ?>">
            <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
            <input type="hidden" name="action" value="create_pattern">
            <input type="hidden" name="elapsed_ms" id="pt_create_elapsed" value="0">
            <input type="hidden" name="vector" id="pt_vector" value="">
            <div id="ptCanvas" class="user-select-none"
                 style="width:320px;max-width:90vw;aspect-ratio:1;margin-bottom:1rem;position:relative;border:1px dashed #ccc;border-radius:12px;touch-action:none;"></div>
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-outline-secondary" id="ptReset">Reset</button>
              <button class="btn btn-primary" id="ptSave" disabled>Save Pattern</button>
            </div>
          </form>
        </div>
      </div>

      <script>
      (function(){
        var grid=3, minNodes=<?= (int)$pt['min_nodes'] ?>, allowCross=<?= !empty($pt['allow_cross']) ? 'true':'false' ?>;
        var box=document.getElementById('ptCanvas');
        var seq=[], used={}, drawing=false, t0=performance.now();

        function bridge(a,b){var m={'1-3':2,'3-1':2,'1-7':4,'7-1':4,'3-9':6,'9-3':6,'7-9':8,'9-7':8,'1-9':5,'9-1':5,'3-7':5,'7-3':5,'2-8':5,'8-2':5,'4-6':5,'6-4':5};return m[a+'-'+b]||null;}
        function add(n){
          if (used[n]) return;
          if (seq.length>0 && !allowCross){
            var mid=bridge(seq[seq.length-1], n);
            if (mid && !used[mid]) return; // cannot jump unless middle used
          }
          used[n]=true; seq.push(n); redraw(); updateState();
        }
        function reset(){ seq=[]; used={}; redraw(); updateState(); t0=performance.now(); }
        function updateState(){
          document.getElementById('pt_vector').value = seq.join('-');
          document.getElementById('ptSave').disabled = (seq.length < minNodes);
        }
        function redraw(){
          box.innerHTML='';
          var pad=20, size=box.clientWidth, step=(size-2*pad)/2;
          var coords={}, num=1;
          // dots
          for(var r=0;r<3;r++){
            for(var c=0;c<3;c++){
              var x=pad+c*step, y=pad+r*step;
              coords[num]={x:x,y:y};
              var dot=document.createElement('div');
              dot.className='pt-dot';
              dot.style.cssText='position:absolute;width:26px;height:26px;border-radius:50%;border:2px solid #666;background:#fff;left:'+(x-13)+'px;top:'+(y-13)+'px;cursor:pointer;z-index:2;';
              if (used[num]) dot.style.background='#0d6efd22';
              (function(n){ dot.addEventListener('pointerdown', function(e){ e.preventDefault(); drawing=true; add(n); }); })(num);
              (function(n){ dot.addEventListener('pointerenter', function(e){ if(drawing) add(n); }); })(num);
              box.appendChild(dot);
              num++;
            }
          }
          // lines (svg behind dots)
          var svg=document.createElementNS('http://www.w3.org/2000/svg','svg');
          svg.setAttribute('width', size); svg.setAttribute('height', size);
          svg.style.position='absolute'; svg.style.left='0'; svg.style.top='0';
          svg.style.pointerEvents='none'; svg.style.zIndex='1';
          for(var i=0;i<seq.length-1;i++){
            var a=coords[seq[i]], b=coords[seq[i+1]];
            var line=document.createElementNS('http://www.w3.org/2000/svg','line');
            line.setAttribute('x1',a.x); line.setAttribute('y1',a.y); line.setAttribute('x2',b.x); line.setAttribute('y2',b.y);
            line.setAttribute('stroke','#0d6efd'); line.setAttribute('stroke-width','4'); line.setAttribute('stroke-linecap','round');
            svg.appendChild(line);
          }
          box.appendChild(svg);
        }
        box.addEventListener('pointerup', function(){ drawing=false; });
        document.getElementById('ptReset').addEventListener('click', reset);
        document.getElementById('ptCreateForm').addEventListener('submit', function(){
          document.getElementById('pt_create_elapsed').value = Math.round(performance.now()-t0);
        });
        window.addEventListener('resize', redraw);
        redraw(); updateState();
      })();
      </script>

    <?php elseif ($modality === 'pattern' && $cred): ?>
      <!-- Pattern login -->
      <?php $done = ($attempts >= 3) || (db_one("SELECT success FROM attempts WHERE session_id=:s ORDER BY id DESC LIMIT 1", [':s'=>$session['id']])['success'] ?? 0); ?>
      <?php if ($done): ?>
        <div class="alert alert-success">This session is finished. Thank you!</div>
        <a class="btn btn-primary" href="<?= url('?p=home'); ?>">Back to Home</a>
      <?php else: ?>
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Draw Your Pattern (Attempt <?= ($attempts+1) ?> of 3) <?= help_icon('Re-draw the same pattern; we time each attempt.'); ?></h5>
            <form id="ptLoginForm" method="post" action="<?= h(url('?p=join&code='.$code)) ?>">
              <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
              <input type="hidden" name="action" value="login_pattern">
              <input type="hidden" name="elapsed_ms" id="pt_login_elapsed" value="0">
              <input type="hidden" name="vector" id="pt_login_vector" value="">
              <div id="ptLoginCanvas" class="user-select-none"
                   style="width:320px;max-width:90vw;aspect-ratio:1;margin-bottom:1rem;position:relative;border:1px dashed #ccc;border-radius:12px;touch-action:none;"></div>
              <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary" id="ptLoginReset">Reset</button>
                <button class="btn btn-primary" id="ptLoginSubmit" disabled>Submit</button>
              </div>
            </form>
          </div>
        </div>

        <script>
        (function(){
          var minNodes=2; // must draw at least 2 to submit
          var box=document.getElementById('ptLoginCanvas');
          var seq=[], used={}, drawing=false, t0=performance.now();

          function add(n){
            if (used[n]) return;
            used[n]=true; seq.push(n); redraw(); update();
          }
          function reset(){ seq=[]; used={}; redraw(); update(); t0=performance.now(); }
          function update(){
            document.getElementById('pt_login_vector').value = seq.join('-');
            document.getElementById('ptLoginSubmit').disabled = (seq.length < minNodes);
          }
          function redraw(){
            box.innerHTML='';
            var pad=20, size=box.clientWidth, step=(size-2*pad)/2;
            var coords={}, num=1;
            for(var r=0;r<3;r++){
              for(var c=0;c<3;c++){
                var x=pad+c*step, y=pad+r*step;
                coords[num]={x:x,y:y};
                var dot=document.createElement('div');
                dot.style.cssText='position:absolute;width:26px;height:26px;border-radius:50%;border:2px solid #666;background:#fff;left:'+(x-13)+'px;top:'+(y-13)+'px;cursor:pointer;z-index:2;';
                if (used[num]) dot.style.background='#0d6efd22';
                (function(n){ dot.addEventListener('pointerdown', function(e){ e.preventDefault(); drawing=true; add(n); }); })(num);
                (function(n){ dot.addEventListener('pointerenter', function(e){ if(drawing) add(n); }); })(num);
                box.appendChild(dot); num++;
              }
            }
            var svg=document.createElementNS('http://www.w3.org/2000/svg','svg');
            svg.setAttribute('width', size); svg.setAttribute('height', size);
            svg.style.position='absolute'; svg.style.left='0'; svg.style.top='0';
            svg.style.pointerEvents='none'; svg.style.zIndex='1';
            for(var i=0;i<seq.length-1;i++){
              var a=coords[seq[i]], b=coords[seq[i+1]];
              var line=document.createElementNS('http://www.w3.org/2000/svg','line');
              line.setAttribute('x1',a.x); line.setAttribute('y1',a.y); line.setAttribute('x2',b.x); line.setAttribute('y2',b.y);
              line.setAttribute('stroke','#0d6efd'); line.setAttribute('stroke-width','4'); line.setAttribute('stroke-linecap','round');
              svg.appendChild(line);
            }
            box.appendChild(svg);
          }
          box.addEventListener('pointerup', function(){ drawing=false; });
          document.getElementById('ptLoginReset').addEventListener('click', reset);
          document.getElementById('ptLoginForm').addEventListener('submit', function(){
            document.getElementById('pt_login_elapsed').value = Math.round(performance.now()-t0);
          });
          window.addEventListener('resize', redraw);
          redraw(); update();
        })();
        </script>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>
