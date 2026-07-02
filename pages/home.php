<?php
require_once __DIR__ . '/../inc/db.php';
$exp_count = (int)(db_one("SELECT COUNT(*) AS c FROM experiments")['c'] ?? 0);
$inv_count = (int)(db_one("SELECT COUNT(*) AS c FROM participants")['c'] ?? 0);
function done_badge($ok){ return $ok ? '<span class="badge bg-success">Done</span>' : '<span class="badge bg-secondary">Next</span>'; }
?>
<main class="py-5">
  <div class="container">
    <div class="row justify-content-center text-center">
      <div class="col-lg-10">
        <h1 class="mb-3">Welcome to AuthCompare</h1>
        <p class="lead mb-4">
          A lightweight lab to compare password vs pattern logins—measure speed, errors, recall, and usability.
        </p>

        <div class="d-flex gap-2 justify-content-center">
          <a class="btn btn-primary btn-lg" href="<?= url('?p=experiments'); ?>">Create Experiment</a>
          <a class="btn btn-outline-secondary btn-lg" href="<?= url('?p=about'); ?>">Learn More</a>
        </div>

        <hr class="my-5">
        <h4 class="mb-3 text-start">Quick Start</h4>

        <div class="list-group text-start">
          <div class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <strong>1) Create your first experiment</strong>
              <?= help_icon('Set up a study: choose Password, Pattern, or A/B (random split), and basic rules.') ?>
              <div class="text-muted">Pick modality and set simple policies.</div>
            </div>
            <div>
              <?= done_badge($exp_count>0) ?>
              <a class="btn btn-sm btn-primary ms-2" href="<?= url('?p=experiments'); ?>">Open</a>
            </div>
          </div>

          <div class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <strong>2) Generate invite codes</strong>
              <?= help_icon('Create per-participant codes linked to an experiment. Share the link to start tasks.') ?>
              <div class="text-muted">Make participant links for that experiment.</div>
            </div>
            <div>
              <?= done_badge($inv_count>0) ?>
              <a class="btn btn-sm btn-primary ms-2" href="<?= url('?p=participants'); ?>">Open</a>
            </div>
          </div>

          <div class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <strong>3) Share links & run tasks</strong>
              <?= help_icon('Participants create a credential and attempt logins; timings & errors are recorded.') ?>
              <div class="text-muted">Participants join and complete the flow.</div>
            </div>
            <div><span class="badge bg-secondary">Next</span></div>
          </div>

          <div class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <strong>4) Compare and export</strong>
              <?= help_icon('View charts for speed/errors/recall; export CSV for deeper analysis.') ?>
              <div class="text-muted">Results and CSV exports.</div>
            </div>
            <div><span class="badge bg-secondary">Next</span></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
