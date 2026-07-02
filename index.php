<?php
require __DIR__ . '/inc/bootstrap.php';

// Whitelist pages (add install/diagnostics)
$allowed = ['home','about','experiments','participants','contact','404','_install','_diagnostics','join','analytics','export'];


$p = $_GET['p'] ?? 'home';
$page = in_array($p, $allowed, true) ? $p : '404';

// Dynamic page title
$page_title = ucfirst($page);

// Shared header
include __DIR__ . '/partials/header.php';

// Page content
$pageFile = __DIR__ . '/pages/' . $page . '.php';
if (is_file($pageFile)) {
    include $pageFile;
} else {
    include __DIR__ . '/pages/404.php';
}

// Shared footer
include __DIR__ . '/partials/footer.php';
