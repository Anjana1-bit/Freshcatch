<?php
/* Environment variables take precedence. On shared hosting, copy
 * config.local.example.php to config.local.php; it is Git-ignored. */
$localConfig = [];
$localConfigFile = __DIR__ . '/config.local.php';
if (is_file($localConfigFile)) {
    $loadedConfig = require $localConfigFile;
    if (is_array($loadedConfig)) $localConfig = $loadedConfig;
}
function freshcatch_config(string $key, mixed $default = null): mixed {
    global $localConfig;
    $value = getenv('FRESHCATCH_' . $key);
    return $value !== false ? $value : ($localConfig[$key] ?? $default);
}
function freshcatch_detect_base_url(): string {
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    if ($script === '') return '';
    $parts = explode('/', trim(dirname($script), '/'));
    if (in_array(end($parts), ['admin', 'buyer', 'seller'], true)) array_pop($parts);
    $path = implode('/', array_filter($parts, static fn ($part) => $part !== '.' && $part !== ''));
    return $path === '' ? '' : '/' . $path;
}
define('APP_ENV', freshcatch_config('ENV', 'production'));
define('DB_HOST', freshcatch_config('DB_HOST', 'localhost'));
define('DB_NAME', freshcatch_config('DB_NAME', 'freshcatch'));
define('DB_USER', freshcatch_config('DB_USER', 'root'));
define('DB_PASS', freshcatch_config('DB_PASS', ''));
define('BASE_URL', rtrim((string) freshcatch_config('BASE_URL', freshcatch_detect_base_url()), '/'));
define('UPLOAD_DIR', __DIR__ . '/../uploads/products/');
define('MAX_UPLOAD', 2 * 1024 * 1024);
ini_set('display_errors', APP_ENV === 'development' ? '1' : '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);
date_default_timezone_set((string) freshcatch_config('TIMEZONE', 'Asia/Kolkata'));
if (session_status() === PHP_SESSION_NONE) {
    $forwardedProto = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '';
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? 80) == 443) || strtolower($forwardedProto) === 'https';
    session_name('freshcatch_session');
    session_set_cookie_params(['lifetime' => 0, 'path' => BASE_URL ?: '/', 'secure' => $https, 'httponly' => true, 'samesite' => 'Lax']);
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_start();
}
