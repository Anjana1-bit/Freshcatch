<?php
define('DB_HOST', 'localhost'); define('DB_NAME', 'freshcatch'); define('DB_USER', 'root'); define('DB_PASS', '');
define('BASE_URL', '/freshcatch'); // Change only if your XAMPP folder has a different name.
define('UPLOAD_DIR', __DIR__ . '/../uploads/products/'); define('MAX_UPLOAD', 2 * 1024 * 1024);
if (session_status() === PHP_SESSION_NONE) session_start();
date_default_timezone_set('Asia/Kolkata');
