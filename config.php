<?php
/**
 * ═══════════════════════════════════════════════
 *  نوآوا — پیکربندی مرکزی و امنیت
 *  - سشن امن، هدرهای امنیتی، CSRF
 *  - دیتابیس JSON با قفل‌گذاری فایل (flock)
 *  - پشتیبانی از فایل تنظیمات محیطی (config.env.php)
 * ═══════════════════════════════════════════════
 */

// بررسی وجود فایل تنظیمات محیطی
$envConfigPath = __DIR__ . '/config.env.php';
if (file_exists($envConfigPath)) {
    require_once $envConfigPath;
} else {
    if (!defined('DATA_DIR')) define('DATA_DIR', __DIR__ . '/data');
    if (!defined('SESSION_NAME')) define('SESSION_NAME', 'NOVA_SESSION');
    if (!defined('SESSION_LIFETIME')) define('SESSION_LIFETIME', 3600);
    if (!defined('CSRF_TOKEN_LIFETIME')) define('CSRF_TOKEN_LIFETIME', 1800);
    if (!defined('MAX_LOGIN_ATTEMPTS')) define('MAX_LOGIN_ATTEMPTS', 5);
    if (!defined('LOCKOUT_TIME')) define('LOCKOUT_TIME', 900);
    if (!defined('ADMIN_USERNAME')) define('ADMIN_USERNAME', 'admin');
    if (!defined('ADMIN_PASSWORD_HASH')) define('ADMIN_PASSWORD_HASH', password_hash('admin123', PASSWORD_BCRYPT));
    if (!defined('SITE_NAME')) define('SITE_NAME', 'نوآوا');
    if (!defined('SITE_DESCRIPTION')) define('SITE_DESCRIPTION', 'استودیو خلاقیت دیجیتال');
    if (!defined('SITE_URL')) define('SITE_URL', 'http://localhost/nova-website-parsa');
    if (!defined('DEBUG_MODE')) define('DEBUG_MODE', false);
    if (!defined('TIMEZONE')) define('TIMEZONE', 'Asia/Tehran');
    if (!defined('LANGUAGE')) define('LANGUAGE', 'fa');
}

if (defined('DEBUG_MODE') && DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/error.log');
}

ini_set('session.name', SESSION_NAME);
ini_set('session.cookie_lifetime', SESSION_LIFETIME);
ini_set('session.cookie_secure', !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
ini_set('session.cookie_httponly', true);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
    if (!isset($_SESSION['initiated'])) {
        session_regenerate_id(true);
        $_SESSION['initiated'] = true;
    }
}

header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' fonts.googleapis.com; font-src 'self' fonts.gstatic.com; img-src 'self' data:; connect-src 'self'");
header("Permissions-Policy: geolocation=(), microphone=(), camera=()");

if (!defined('DATA_DIR')) define('DATA_DIR', __DIR__ . '/data');
if (!file_exists(DATA_DIR)) mkdir(DATA_DIR, 0755, true);
$defaultFiles = ['settings.json', 'users.json', 'messages.json', 'views.json', 'lockout.json'];
foreach ($defaultFiles as $file) {
    $filePath = DATA_DIR . '/' . $file;
    if (!file_exists($filePath)) file_put_contents($filePath, '{}');
}

function get_json_data($filename) {
    $filePath = DATA_DIR . '/' . $filename;
    if (!file_exists($filePath)) return [];
    $handle = fopen($filePath, 'r');
    if ($handle === false) return [];
    try {
        $size = filesize($filePath);
        if ($size === 0) return [];
        if (flock($handle, LOCK_SH)) {
            $content = fread($handle, $size);
            flock($handle, LOCK_UN);
            $data = json_decode($content, true);
            return is_array($data) ? $data : [];
        }
    } finally {
        fclose($handle);
    }
    return [];
}

function save_json_data($filename, $data) {
    $filePath = DATA_DIR . '/' . $filename;
    $tempPath = tempnam(sys_get_temp_dir(), 'nova_');
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents($tempPath, $json);
    $handle = fopen($filePath, 'c');
    if ($handle !== false && flock($handle, LOCK_EX)) {
        rename($tempPath, $filePath);
        flock($handle, LOCK_UN);
        fclose($handle);
        @unlink($tempPath);
        return true;
    }
    @unlink($tempPath);
    return false;
}

function db_has_data($filename) { return !empty(get_json_data($filename)); }
function get_settings() { static $s = null; if ($s === null) $s = get_json_data('settings.json'); return $s; }
function get_admin_user() { $u = get_json_data('users.json'); return $u['admin'] ?? null; }

function settings_default() {
    return ['title' => 'نوآوا | استودیو خلاقیت دیجیتال', 'heroSub' => 'با نوآوا، خلاقیت خود را به واقعیت تبدیل کنید', 'words' => ['طراحی', 'توسعه', 'هوش مصنوعی', 'وب‌سایت'], 'address' => 'تهران، ایران', 'phone' => '۰۲۱-۱۲۳۴۵۶۷۸', 'email' => 'info@nova-studio.ir', 'theme' => 'dark'];
}

function e($s) { return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function csrf_token() { return generate_csrf_token(); }

function generate_csrf_token() {
    if (empty($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_expires']) || $_SESSION['csrf_token_expires'] < time()) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_expires'] = time() + CSRF_TOKEN_LIFETIME;
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf() { $t = $_POST['csrf_token'] ?? ''; if (!verify_csrf_token($t)) json_error('توکن امنیتی نامعتبر است.', 403); }
function verify_csrf_token($t) { return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $t); }

function get_client_ip() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}
function client_ip() { return get_client_ip(); }

function log_error($m) { if (defined('DEBUG_MODE') && DEBUG_MODE) error_log('[NOVA ERROR] ' . date('Y-m-d H:i:s') . ' - ' . $m); }
function db_read($f, $d = []) { $data = get_json_data($f); return !empty($data) ? $data : $d; }
function db_write($f, $d) { return save_json_data($f, $d); }
function post_str($k, $m = 255) { $v = $_POST[$k] ?? ''; return is_string($v) ? mb_substr($v, 0, $m) : ''; }
function len($s) { return mb_strlen($s, 'UTF-8'); }
function new_id() { return bin2hex(random_bytes(20)); }
function is_admin() { return !empty($_SESSION['admin']) && $_SESSION['admin'] === true; }

function require_admin() { if (!is_admin()) json_error('دسترسی غیرمجاز. لطفاً وارد شوید.', 401); }
function json_error($m, $c = 400) { header('Content-Type: application/json; charset=UTF-8'); http_response_code($c); echo json_encode(['ok' => false, 'error' => $m], JSON_UNESCAPED_UNICODE); exit; }
function json_out($d) { header('Content-Type: application/json; charset=UTF-8'); echo json_encode($d, JSON_UNESCAPED_UNICODE); exit; }
?>