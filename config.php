<?php
/**
 * ═══════════════════════════════════════════════
 *  نوآوا — پیکربندی مرکزی و امنیت
 *  - سشن امن، هدرهای امنیتی، CSRF
 *  - دیتابیس JSON با قفل‌گذاری فایل (flock)
 * ═══════════════════════════════════════════════
 */

declare(strict_types=1);

/* ── مدیریت خطا: نشت اطلاعات به کاربر ممنوع ── */
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

/* ── سشن با پیکربندی امن ── */
if (session_status() === PHP_SESSION_NONE) {
    session_name('NOVA_SESS');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/* ── هدرهای امنیتی ── */
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
header(
    'Content-Security-Policy: ' .
    "default-src 'self'; " .
    "script-src 'self'; " .
    "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
    "font-src 'self' https://fonts.gstatic.com; " .
    "img-src 'self' data:; " .
    "connect-src 'self'; " .
    "base-uri 'self'; " .
    "form-action 'self'; " .
    'object-src \'none\''
);
header('Cache-Control: no-store, max-age=0');

/* ── مسیرها و مقادیر پیش‌فرض ── */
define('DATA_DIR', __DIR__ . '/data');

// بارگذاری متغیرهای محیطی
require __DIR__ . '/config.env.php';

// اگر رمز پیش‌فرض خالی باشد، از کاربر خواسته می‌شود که در اولین ورود آن را تنظیم کند.
if (DEFAULT_ADMIN_PASS === '') {
    // در اولین اجرا، یک رمز موقت ایجاد می‌شود.
    define('DEFAULT_ADMIN_PASS', bin2hex(random_bytes(8)));
    error_log("[NOVA] رمز پیش‌فرض خالی است. یک رمز موقت ایجاد شد: " . DEFAULT_ADMIN_PASS);
}

/* ─────────── دیتابیس JSON با قفل فایل ─────────── */

/**
 * خواندن امن یک فایل JSON (با قفل اشتراکی)
 */
function db_read(string $file, array $default): array
{
    $path = DATA_DIR . '/' . $file;
    if (!is_file($path)) {
        return $default;
    }
    $h = @fopen($path, 'rb');
    if ($h === false) {
        return $default;
    }
    flock($h, LOCK_SH);
    $raw = stream_get_contents($h);
    flock($h, LOCK_UN);
    fclose($h);

    $data = json_decode((string)$raw, true);
    return is_array($data) ? $data : $default;
}

/**
 * نوشتن امن یک فایل JSON (با قفل انحصاری + برش قبلی)
 */
function db_write(string $file, array $data): void
{
    if (!is_dir(DATA_DIR)) {
        @mkdir(DATA_DIR, 0775, true);
    }
    $path = DATA_DIR . '/' . $file;
    $h = @fopen($path, 'c+b');
    if ($h === false) {
        return;
    }
    flock($h, LOCK_EX);
    ftruncate($h, 0);
    fwrite($h, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    fflush($h);
    flock($h, LOCK_UN);
    fclose($h);
}

/* ─────────── داده‌های پیش‌فرض ─────────── */

function settings_default(): array
{
    return [
        'title'   => 'نوآوا | استودیو خلاقیت دیجیتال',
        'heroSub' => 'با نوآوا، ایده‌هایت را به تجربه‌های دیجیتال خیره‌کننده تبدیل کن. طراحی، توسعه و برندینگ — همه در یک‌جا، با وسواس در جزئیات.',
        'words'   => ['طراحی می‌کنیم', 'کد می‌زنیم', 'برند می‌سازیم', 'رویا می‌بافیم'],
        'address' => 'تهران، خیابان ولیعصر، برج نوآوا، طبقه ۱۲',
        'phone'   => '۰۲۱-۱۲۳۴۵۶۷۸',
        'email'   => 'hello@nova.studio',
        'theme'   => 'dark',
    ];
}

function get_settings(): array
{
    $s = db_read('settings.json', []);
    $merged = array_merge(settings_default(), $s);
    if (!is_array($merged['words'])) {
        $merged['words'] = settings_default()['words'];
    }
    return $merged;
}

/**
 * ساخت خودکار داده‌های اولیه در اولین اجرا
 * (اکانت ادمین با رمز هش‌شده + تغییر اجباری رمز)
 */
function db_bootstrap(): void
{
    if (!is_dir(DATA_DIR)) {
        @mkdir(DATA_DIR, 0775, true);
    }
    if (!is_file(DATA_DIR . '/settings.json')) {
        db_write('settings.json', settings_default());
    }
    if (!is_file(DATA_DIR . '/messages.json')) {
        db_write('messages.json', ['items' => []]);
    }
    if (!is_file(DATA_DIR . '/views.json')) {
        db_write('views.json', ['total' => 0, 'daily' => []]);
    }
    if (!is_file(DATA_DIR . '/lockout.json')) {
        db_write('lockout.json', ['failed' => 0, 'until' => 0]);
    }
    if (!is_file(DATA_DIR . '/users.json')) {
        db_write('users.json', [
            'items' => [[
                'user'        => DEFAULT_ADMIN_USER,
                'pass_hash'   => password_hash(DEFAULT_ADMIN_PASS, PASSWORD_DEFAULT),
                'must_change' => true,
                'created'     => date('c'),
            ]],
        ]);
    }
}

/* ─────────── ابزارهای خروجی ─────────── */

/** ماسک‌سازی امن خروجی HTML */
function e(?string $s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * لاگ خطاها در فایل مشخص شده
 */
function log_error(string $message): void
{
    $logPath = ERROR_LOG_PATH;
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message\n";
    error_log($logMessage, 3, $logPath);
}

/** خروجی JSON یکنواخت */
function json_out(array $data, int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP);
    exit;
}

function json_error(string $msg, int $code = 400): void
{
    log_error("JSON Error [$code]: $msg");
    json_out(['ok' => false, 'error' => $msg], $code);
}

/* ─────────── CSRF ─────────── */

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function verify_csrf(): void
{
    $t = $_POST['csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!is_string($t) || $t === '' || !hash_equals($_SESSION['csrf'] ?? '', $t)) {
        json_error('نشانه‌ی امنیتی (CSRF) نامعتبر است. صفحه را تازه‌سازی کنید و دوباره تلاش کنید.', 403);
    }
}

/* ─────────── دسترسی ادمین ─────────── */

function is_admin(): bool
{
    return !empty($_SESSION['admin']);
}

function require_admin(): void
{
    if (!is_admin()) {
        json_error('دسترسی غیرمجاز. ابتدا وارد شوید.', 401);
    }
}

/* ─────────── اعتبارسنجی ورودی ─────────── */

/** طول رشته با پشتیبانی از فونت کامل (فارسی) */
function len(string $s): int
{
    return function_exists('mb_strlen') ? mb_strlen($s, 'UTF-8') : strlen($s);
}

/** خواندن رشته از POST با محدودیت طول */
function post_str(string $key, int $max = 255): string
{
    $v = $_POST[$key] ?? '';
    if (!is_string($v)) {
        json_error('ورودی نامعتبر است.', 400);
    }
    $v = trim($v);
    if (len($v) > $max) {
        json_error('طول ورودی بیش از حد مجاز است.', 400);
    }
    return $v;
}

function client_ip(): string
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
}

/** شناسه‌ی یکتا برای رکوردها */
function new_id(): string
{
    return (string)time() . (string)random_int(100000, 999999);
}

/**
 * بررسی اینکه آیا دیتابیس داده دارد
 */
function db_has_data(): bool
{
    return is_file(DATA_DIR . '/settings.json') && filesize(DATA_DIR . '/settings.json') > 2;
}

// فقط در اولین اجرا فراخوانی شود
if (!db_has_data()) {
    db_bootstrap();
}
