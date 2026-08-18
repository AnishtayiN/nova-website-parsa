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
    // تنظیمات پیش‌فرض در صورت عدم وجود config.env.php
    if (!defined('DATA_DIR')) {
        define('DATA_DIR', __DIR__ . '/data');
    }
    if (!defined('SESSION_NAME')) {
        define('SESSION_NAME', 'NOVA_SESSION');
    }
    if (!defined('SESSION_LIFETIME')) {
        define('SESSION_LIFETIME', 3600);
    }
    if (!defined('CSRF_TOKEN_LIFETIME')) {
        define('CSRF_TOKEN_LIFETIME', 1800);
    }
    if (!defined('MAX_LOGIN_ATTEMPTS')) {
        define('MAX_LOGIN_ATTEMPTS', 5);
    }
    if (!defined('LOCKOUT_TIME')) {
        define('LOCKOUT_TIME', 900);
    }
    if (!defined('ADMIN_USERNAME')) {
        define('ADMIN_USERNAME', 'admin');
    }
    if (!defined('ADMIN_PASSWORD_HASH')) {
        // پسورد پیش‌فرض هش شده: "admin123"
        define('ADMIN_PASSWORD_HASH', password_hash('admin123', PASSWORD_BCRYPT));
    }
    if (!defined('SITE_NAME')) {
        define('SITE_NAME', 'نوآوا');
    }
    if (!defined('SITE_DESCRIPTION')) {
        define('SITE_DESCRIPTION', 'استودیو خلاقیت دیجیتال');
    }
    if (!defined('SITE_URL')) {
        define('SITE_URL', 'http://localhost/nova-website-parsa');
    }
    if (!defined('DEBUG_MODE')) {
        define('DEBUG_MODE', false);
    }
    if (!defined('TIMEZONE')) {
        define('TIMEZONE', 'Asia/Tehran');
    }
    if (!defined('LANGUAGE')) {
        define('LANGUAGE', 'fa');
    }
}

// تنظیمات خطا
if (defined('DEBUG_MODE') && DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/error.log');
}

// تنظیمات سشن
ini_set('session.name', SESSION_NAME);
ini_set('session.cookie_lifetime', SESSION_LIFETIME);
ini_set('session.cookie_secure', true);
ini_set('session.cookie_httponly', true);
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', 1);

// شروع سشن
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    // جلوگیری از session fixation
    if (!isset($_SESSION['initiated'])) {
        session_regenerate_id(true);
        $_SESSION['initiated'] = true;
    }
}

/* ── هدرهای امنیتی ── */
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self' 'unsafe-inline' 'unsafe-eval' data:; script-src 'self' 'unsafe-inline' 'unsafe-eval' cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' fonts.googleapis.com; font-src 'self' fonts.gstatic.com; img-src 'self' data:; connect-src 'self'");
header("Permissions-Policy: geolocation=(), microphone=(), camera=()");

/* ── مسیرها و مقادیر پیش‌فرض ── */
if (!defined('DATA_DIR')) {
    define('DATA_DIR', __DIR__ . '/data');
}

// ایجاد دایرکتوری data اگر وجود ندارد
if (!file_exists(DATA_DIR)) {
    mkdir(DATA_DIR, 0755, true);
}

// فایل‌های پیش‌فرض دیتابیس
$defaultFiles = [
    'settings.json',
    'users.json',
    'messages.json',
    'views.json',
    'lockout.json'
];

// ایجاد فایل‌های پیش‌فرض در صورت عدم وجود
foreach ($defaultFiles as $file) {
    $filePath = DATA_DIR . '/' . $file;
    if (!file_exists($filePath)) {
        file_put_contents($filePath, '{}');
    }
}

/* ── توابع کمکی ── */

/**
 * دریافت تنظیمات از فایل JSON
 */
function get_json_data($filename) {
    $filePath = DATA_DIR . '/' . $filename;
    if (!file_exists($filePath)) {
        return [];
    }
    
    $handle = fopen($filePath, 'r');
    if ($handle === false) {
        return [];
    }
    
    try {
        $size = filesize($filePath);
        if ($size === 0) {
            return [];
        }
        
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

/**
 * ذخیره داده در فایل JSON با قفل‌گذاری
 */
function save_json_data($filename, $data) {
    $filePath = DATA_DIR . '/' . $filename;
    $tempPath = tempnam(sys_get_temp_dir(), 'nova_');
    
    // نوشتن در فایل موقت
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents($tempPath, $json);
    
    // انتقال به فایل اصلی با قفل
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

/**
 * بررسی وجود داده در دیتابیس
 */
function db_has_data($filename) {
    $data = get_json_data($filename);
    return !empty($data);
}

/**
 * دریافت تنظیمات سایت
 */
function get_settings() {
    static $settings = null;
    if ($settings === null) {
        $settings = get_json_data('settings.json');
    }
    return $settings;
}

/**
 * دریافت کاربر ادمین
 */
function get_admin_user() {
    $users = get_json_data('users.json');
    return $users['admin'] ?? null;
}

/**
 * اسکیپ کردن خروجی برای جلوگیری از XSS
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * تولید توکن CSRF
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_expires']) || $_SESSION['csrf_token_expires'] < time()) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_expires'] = time() + CSRF_TOKEN_LIFETIME;
    }
    return $_SESSION['csrf_token'];
}

/**
 * بررسی توکن CSRF
 */
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * دریافت آدرس IP کاربر
 */
function get_client_ip() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
    return $ip;
}

/**
 * لاگ کردن خطاها
 */
function log_error($message) {
    if (DEBUG_MODE) {
        error_log('[NOVA ERROR] ' . date('Y-m-d H:i:s') . ' - ' . $message);
    }
}

// پایان فایل config.php
?>