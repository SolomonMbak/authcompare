<?php
require_once __DIR__ . '/../inc/db.php';

if (!isset($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(16)); }

$errors = []; $flash = null;

// create
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && ($_POST['action'] ?? '') === 'create') {
  if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
    $errors[] = 'Invalid CSRF token.';
  } else {
    $title    = trim($_POST['title'] ?? '');
    $modality = $_POST['modality'] ?? 'password';

    $policy = [
      'password' => [
        'min_length'    => max(1, (int)($_POST['pw_min_len'] ?? 8)),
        'require_upper' => isset($_POST['pw_upper']),
        'require_lower' => isset($_POST['pw_lower']),
        'require_digit' => isset($_POST['pw_digit']),
        'require_symbol'=> isset($_POST['pw_symbol']),
      ],
      'pattern' => [
        'grid'        => max(3, (int)($_POST['pt_grid'] ?? 3)),
        'min_nodes'   => max(2, (int)($_POST['pt_min_nodes'] ?? 4)),
        'allow_cross' => isset($_POST['pt_allow_cross']),
      ],
    ];
    if ($title === '') $errors[] = 'Title is required.';
    if (!in_array($modality, ['password','pattern','ab'], true)) $modality = 'password';

    if (!$errors) {
      db_exec("INSERT INTO experiments (title, modality, policy_json) VALUES (:t,:m,:p)", [
        ':t'=>$title, ':m'=>$modality, ':p'=>json_encode($policy, JSON_UNESCAPED_SLASHES)
      ]);
      $flash = 'Experiment created.';
    }
  }
}
$rows = db_all("SELECT id,title,modality,created_at FROM experiments ORDER BY id DESC");
?>
<section class="py-5">
  <div class="container">
    <h2 class="mb-3">Experiments <?= help_icon('A container for your study. Each experiment has a modality and policies.'); ?></h2>

    <div class="alert alert-primary">
      <strong>How this page works</strong> <?= help_icon('Follow the steps to create an experiment and move on to invites.'); ?>
      <ol class="mb-0">
        <li>Fill the form and click <em>Save Experiment</em>.</li>
        <li>Go to <strong>Participants</strong> to generate invite codes.</li>
        <li>Share the invite link with participants.</li>
      </ol>
    </div>

    <?php if ($flash): ?><div class="alert alert-success"><?= h($flash) ?></div><?php endif; ?>
    <?php foreach ($errors as $e): ?><div class="alert alert-danger"><?= h($e) ?></div><?php endforeach; ?>

    <div class="card mb-4">
      <div class="card-body">
        <h5 class="card-title mb-3">New Experiment</h5>
        <form method="post" action="<?= h(url('?p=experiments')) ?>">
          <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
          <input type="hidden" name="action" value="create">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">
                Title <?= help_icon('Human-friendly name, e.g., “Class A – Week 1”.'); ?>
              </label>
              <input class="form-control" name="title" placeholder="e.g., Pilot Study A" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">
                Modality <?= help_icon('Password = text secrets; Pattern = 3×3+ dots; A/B = randomize participants.'); ?>
              </label>
              <select class="form-select" name="modality">
                <option value="password">Password</option>
                <option value="pattern">Pattern</option>
                <option value="ab">A/B (randomized)</option>
              </select>
            </div>
          </div>

          <hr class="my-4">

          <div class="row g-3">
            <div class="col-md-6">
              <h6>Password policy <?= help_icon('Rules enforced when creating a password credential.'); ?></h6>
              <div class="row g-2">
                <div class="col-6">
                  <label class="form-label">
                    Min length <?= help_icon('Minimum number of characters required.'); ?>
                  </label>
                  <input type="number" class="form-control" name="pw_min_len" value="8" min="1">
                </div>
                <div class="col-6 d-flex align-items-end flex-wrap gap-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="pw_upper" id="pw_upper" checked>
                    <label class="form-check-label" for="pw_upper">
                      Uppercase <?= help_icon('Require at least one A–Z.'); ?>
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="pw_lower" id="pw_lower" checked>
                    <label class="form-check-label" for="pw_lower">
                      Lowercase <?= help_icon('Require at least one a–z.'); ?>
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="pw_digit" id="pw_digit" checked>
                    <label class="form-check-label" for="pw_digit">
                      Digits <?= help_icon('Require at least one 0–9.'); ?>
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="pw_symbol" id="pw_symbol">
                    <label class="form-check-label" for="pw_symbol">
                      Symbols <?= help_icon('Require at least one symbol, e.g., ! @ # $ %.'); ?>
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <h6>Pattern policy <?= help_icon('Rules for the Android-style unlock pattern.'); ?></h6>
              <div class="row g-2">
                <div class="col-4">
                  <label class="form-label">
                    Grid <?= help_icon('Pattern grid size: 3 means 3×3.'); ?>
                  </label>
                  <input type="number" class="form-control" name="pt_grid" value="3" min="3">
                </div>
                <div class="col-4">
                  <label class="form-label">
                    Min nodes <?= help_icon('Minimum dots that must be connected.'); ?>
                  </label>
                  <input type="number" class="form-control" name="pt_min_nodes" value="4" min="2">
                </div>
                <div class="col-4 d-flex align-items-end">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="pt_allow_cross" id="pt_allow_cross">
                    <label class="form-check-label" for="pt_allow_cross">
                      Allow crossings <?= help_icon('Permit lines to jump over intermediate dots.'); ?>
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-4">
            <button class="btn btn-primary">Save Experiment</button>
            <a class="btn btn-outline-secondary ms-2" href="<?= url('?p=participants'); ?>">Next: Invite Participants →</a>
          </div>
        </form>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th># <?= help_icon('Experiment ID.'); ?></th>
            <th>Title <?= help_icon('Your label for the experiment.'); ?></th>
            <th>Modality <?= help_icon('Password / Pattern / A/B.'); ?></th>
            <th>Created <?= help_icon('When it was added.'); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$rows): ?>
            <tr><td colspan="4" class="text-muted">No experiments yet. Create one above.</td></tr>
          <?php else: foreach ($rows as $r): ?>
            <tr>
              <td><?= (int)$r['id'] ?></td>
              <td><?= h($r['title']) ?></td>
              <td><?= h($r['modality']) ?></td>
              <td><?= h($r['created_at']) ?></td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>
