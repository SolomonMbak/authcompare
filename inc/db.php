<?php
if (session_status() === PHP_SESSION_NONE) {
  session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax',
  ]);
  session_start();
}

$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_NAME = getenv('DB_NAME') ?: 'u954024395_authcompare'; // set to your actual DB name
$DB_USER = getenv('DB_USER') ?: 'u954024395_authcompare';
$DB_PASS = getenv('DB_PASS') ?: '6;ShzVZzeyBr';

$dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4";
$options = [
  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
  $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
  http_response_code(500);
  echo "<h3 style='font-family:system-ui,sans-serif'>Database connection failed.</h3>";
  // echo "<pre>".htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')."</pre>";
  exit;
}

function db_all(string $sql, array $params = []): array {
  global $pdo;
  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);
  return $stmt->fetchAll();
}
function db_one(string $sql, array $params = []): ?array {
  global $pdo;
  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);
  $row = $stmt->fetch();
  return $row !== false ? $row : null;
}
function db_exec(string $sql, array $params = []): int {
  global $pdo;
  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);
  return $stmt->rowCount();
}
