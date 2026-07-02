<?php
// inc/bootstrap.php
// Loads config, sets timezone, errors, session, and shared helpers.

$app = require __DIR__ . '/../config/app.php';

// Timezone & errors
date_default_timezone_set($app['timezone'] ?? 'Africa/Lagos');
error_reporting(E_ALL);
ini_set('display_errors', ($app['env'] ?? 'local') === 'local' ? '1' : '0');

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// -------- URL Helpers --------
function is_https(): bool {
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') return true;
    if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') return true;
    return false;
}
if (!function_exists('lstrip')) { // polyfill if needed
    function lstrip(string $s, string $chars = '/'): string { return ltrim($s, $chars); }
}
function base_url(string $path = ''): string {
    $scheme = is_https() ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $dir    = rtrim(str_replace(basename($_SERVER['SCRIPT_NAME'] ?? ''), '', $_SERVER['SCRIPT_NAME'] ?? ''), '/');
    $root   = rtrim($dir, '/');
    $url    = $scheme . '://' . $host . $root;
    return rtrim($url, '/') . '/' . lstrip($path, '/');
}
function url(string $path = ''): string { return base_url($path); }
function asset(string $path): string { return base_url('assets/' . lstrip($path, '/')); }

// -------- Output helpers --------
function h($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function page_title(string $pageTitle = ''): string {
    global $app;
    $appName = $app['app_name'] ?? 'AuthCompare';
    return $pageTitle ? ($pageTitle . ' · ' . $appName) : $appName;
}

/**
 * Small inline “?” with tooltip. Usage:
 *   <?= help_icon('Explain this field'); ?>
 */
function help_icon(string $text, string $placement = 'top'): string {
    $style = "display:inline-block;width:1.1em;height:1.1em;border:1px solid currentColor;border-radius:50%;"
           . "text-align:center;line-height:1.1em;font-size:.85em;opacity:.75;cursor:help;margin-left:.35rem;";
    return '<span class="help-icon" style="'.$style.'" data-bs-toggle="tooltip" data-bs-placement="'.h($placement).'" title="'.h($text).'">?</span>';
}


$app['pattern_salt'] = '9dfa2a92f47b3b10573a0ccbc396a44bf29924adc27b9754988afb0f84d55c75';