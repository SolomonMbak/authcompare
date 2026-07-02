<?php
require_once __DIR__ . '/../inc/db.php';

if (!isset($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(16)); }

$errors = []; $flash = null;
$experiments = db_all("SELECT id,title FROM experiments ORDER BY created_at DESC");

// create invite
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && ($_POST['action'] ?? '') === 'invite') {
  if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
    $errors[] = 'Invalid CSRF token.';
  } else {
    $experiment_id = (int)($_POST['experiment_id'] ?? 0);
    $exists = db_one("SELECT id FROM experiments WHERE id = :id", [':id'=>$experiment_id]);
    if (!$exists) {
      $errors[] = 'Please select a valid experiment.';
    } else {
      $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
      do {
        $code = '';
        for ($i=0; $i<8; $i++) { $code .= $alphabet[random_int(0, strlen($alphabet)-1)]; }
        $dupe = db_one("SELECT id FROM participants WHERE code = :c", [':c'=>$code]);
      } while ($dupe);

      db_exec("INSERT INTO participants (code, experiment_id, status) VALUES (:c,:e,'invited')",
              [':c'=>$code, ':e'=>$experiment_id]);
      $flash = "Invite created.";
    }
  }
}
$list = db_all("SELECT p.id,p.code,p.status,p.created_at,e.title
                FROM participants p JOIN experiments e ON e.id=p.experiment_id
                ORDER BY p.id DESC");
?>
<section class="py-5">
  <div class="container">
    <h2 class="mb-3">Participants <?= help_icon('Manage invite codes and joining status.'); ?></h2>

    <div class="alert alert-primary">
      <strong>How this page works</strong> <?= help_icon('Create an invite for an experiment, then share the link.'); ?>
      <ol class="mb-0">
        <li>Select an experiment and click <em>Create Invite</em>.</li>
        <li>Send the generated link to your participant.</li>
        <li>They’ll land on the Join page to begin tasks.</li>
      </ol>
    </div>

    <?php if ($flash): ?><div class="alert alert-success"><?= h($flash) ?></div><?php endif; ?>
    <?php foreach ($errors as $e): ?><div class="alert alert-danger"><?= h($e) ?></div><?php endforeach; ?>

    <div class="card mb-4">
      <div class="card-body">
        <h5 class="card-title mb-3">Create Invite</h5>
        <form method="post" action="<?= h(url('?p=participants')) ?>">
          <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">
          <input type="hidden" name="action" value="invite">

          <div class="row g-3 align-items-end">
            <div class="col-md-6">
              <label class="form-label">
                Experiment <?= help_icon('Which experiment this participant will join.'); ?>
              </label>
              <select class="form-select" name="experiment_id" required>
                <option value="">-- Select experiment --</option>
                <?php foreach ($experiments as $ex): ?>
                  <option value="<?= (int)$ex['id'] ?>"><?= h($ex['title']) ?></option>
                <?php endforeach; ?>
              </select>
              <div class="form-text">If empty, create an experiment first.</div>
            </div>
            <div class="col-md-6">
              <button class="btn btn-primary">Create Invite</button>
              <?= help_icon('Generates an 8-character code and link for the participant.','right') ?>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-striped align-middle" id="invitesTable">
        <thead>
          <tr>
            <th># <?= help_icon('Invite ID.'); ?></th>
            <th>Code <?= help_icon('Shareable code embedded in the link.'); ?></th>
            <th>Experiment <?= help_icon('Which experiment this code belongs to.'); ?></th>
            <th>Status <?= help_icon('invited / joined / completed.'); ?></th>
            <th>Invite Link <?= help_icon('Direct URL for the participant to join.'); ?></th>
            <th>Created <?= help_icon('When this invite was generated.'); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$list): ?>
            <tr><td colspan="6" class="text-muted">No invites yet. Create one above.</td></tr>
          <?php else: foreach ($list as $r):
            $link = url('?p=join&code=' . urlencode($r['code']));
          ?>
            <tr>
              <td><?= (int)$r['id'] ?></td>
              <td><code><?= h($r['code']) ?></code></td>
              <td><?= h($r['title']) ?></td>
              <td><?= h($r['status']) ?></td>
              <td>
                <input class="form-control form-control-sm d-inline-block" style="max-width:420px" value="<?= h($link) ?>" readonly>
              </td>
              <td><?= h($r['created_at']) ?></td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>
