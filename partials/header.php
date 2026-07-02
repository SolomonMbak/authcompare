<?php
// /partials/header.php
$current = $_GET['p'] ?? 'home';
function nav_active(string $name, string $current): string {
  return $name === $current ? 'active fw-semibold' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title><?= htmlspecialchars(page_title($page_title ?? ''), ENT_QUOTES, 'UTF-8'); ?></title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= url('assets/favicon.ico') ?>" />
    <!-- Google font -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
    <!-- Bootstrap icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- Core theme CSS (includes Bootstrap) -->
    <link href="<?= url('css/styles.css') ?>" rel="stylesheet" />
  </head>
  <body class="d-flex flex-column h-100">
    <!-- Top Nav -->
    <header class="border-bottom bg-white sticky-top">
      <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
          <a class="navbar-brand fw-bold" href="<?= url('?p=home') ?>">AuthCompare</a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
              <li class="nav-item">
                <a class="nav-link <?= nav_active('home', $current) ?>" href="<?= url('?p=home') ?>">Home</a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?= nav_active('experiments', $current) ?>" href="<?= url('?p=experiments') ?>">Experiments</a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?= nav_active('participants', $current) ?>" href="<?= url('?p=participants') ?>">Participants</a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?= nav_active('analytics', $current) ?>" href="<?= url('?p=analytics') ?>">Analytics</a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?= nav_active('about', $current) ?>" href="<?= url('?p=about') ?>">About</a>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </header>

    <!-- Page content starts -->
    <main id="content" class="flex-shrink-0">
