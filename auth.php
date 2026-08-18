<?php
/**
 * ═══════════════════════════════════════════════
 *  نوآوا — احراز هویت
 *  - لاگین با password_verify + قفل موقت (ریت‌لیمیت)
 *  - جلوگیری از session fixation با regenerate_id
 * ═══════════════════════════════════════════════
 */

require __DIR__ . '/config.php';

$action = isset($_GET['action']) && is_string($_GET['action']) ? $_GET['action'] : '';

/**
 * لاگ عملیات احراز هویت
 */
function log_auth(string $message): void
{
    log_error("[AUTH] $message");
}

switch ($action) {

    /* ─────────── ورود ─────────── */
    case 'login': {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_error('روش درخواست نامعتبر است.', 405);
        }
        verify_csrf();

        /* بررسی قفل موقت (بعد از ۵ تلاش ناموفق: ۱۵ دقیقه) */
        $lock = db_read('lockout.json', ['failed' => 0, 'until' => 0]);
        if ((int)($lock['until'] ?? 0) > time()) {
            $mins = (int)ceil(((int)($lock['until'] ?? 0) - time()) / 60);
            json_error("تلاش‌های ناموفق پشت سر هم؛ ورود موقتا‌ً قفل شد. بعد از $mins دقیقه دوباره تلاش کنید.", 429);
        }

        $user = post_str('username', 32);
        $pass = (string)($_POST['password'] ?? '');

        if ($user === '' || $pass === '') {
            json_error('نام کاربری و رمز عبور را وارد کنید.', 422);
        }

        $users = db_read('users.json', ['items' => []]);
        $found = null;
        foreach ($users['items'] as $u) {
            if (is_array($u) && hash_equals((string)($u['user'] ?? ''), $user)) {
                $found = $u;
                break;
            }
        }

        $ok = $found !== null && password_verify($pass, (string)($found['pass_hash'] ?? ''));

        if ($ok) {
            /* بازنشانی شمارنده قفل */
            db_write('lockout.json', ['failed' => 0, 'until' => 0]);

            /* جلوگیری از session fixation */
            session_regenerate_id(true);
            $_SESSION['admin']       = true;
            $_SESSION['admin_user']  = (string)$found['user'];
            $_SESSION['must_change'] = !empty($found['must_change']);
            $_SESSION['login_time']  = time();

            log_auth("ورود موفق کاربر: {$found['user']} از IP: " . client_ip());
            json_out(['ok' => true]);
        }

        /* ── تلاش ناموفق ── */
        $lock['failed'] = (int)($lock['failed'] ?? 0) + 1;
        if ($lock['failed'] >= 5) {
            $lock['until']  = time() + 900;
            $lock['failed'] = 0;
            db_write('lockout.json', $lock);
            json_error('تلاش‌های ناموفق پشت سر هم؛ ورود برای ۱۵ دقیقه قفل شد.', 429);
        }
        db_write('lockout.json', $lock);
        json_error('نام کاربری یا رمز عبور اشتباه است.', 401);
    }

    /* ─────────── خروج ─────────── */
    case 'logout': {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_error('روش درخواست نامعتبر است.', 405);
        }
        verify_csrf();

        $adminUser = (string)($_SESSION['admin_user'] ?? '');
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', [
                'expires'  => time() - 42000,
                'path'     => $p['path'],
                'domain'   => $p['domain'],
                'secure'   => $p['secure'],
                'httponly' => $p['httponly'],
                'samesite' => $p['samesite'],
            ]);
        }
        session_destroy();
        log_auth("خروج کاربر: $adminUser از IP: " . client_ip());
        json_out(['ok' => true]);
    }

    /* ─────────── وضعیت لاگین ─────────── */
    case 'status': {
        if (is_admin()) {
            json_out([
                'ok'          => true,
                'auth'        => true,
                'user'        => (string)($_SESSION['admin_user'] ?? ''),
                'must_change' => !empty($_SESSION['must_change']),
            ]);
        }
        json_out(['ok' => true, 'auth' => false]);
    }

    default:
        json_error('عملیات نامعتبر است.', 404);
}
