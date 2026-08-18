<?php
/**
 * ═══════════════════════════════════════════════
 *  نوآوا — نقطه‌های اتصال API
 *  همه عملیات: POST + CSRF (+ سشن ادمین برای عملیات حساس)
 * ═══════════════════════════════════════════════
 */

require __DIR__ . '/config.php';

$action = isset($_GET['action']) && is_string($_GET['action']) ? $_GET['action'] : '';

/**
 * خواندن پیام‌ها از دیتابیس
 */
function get_messages(): array
{
    $msgs = db_read('messages.json', ['items' => []]);
    return is_array($msgs['items'] ?? null) ? $msgs['items'] : [];
}

/* همه عملیات فقط POST */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('روش درخواست نامعتبر است.', 405);
}

/* همه عملیات CSRF می‌خواهند */
verify_csrf();

/* عملیات نیازمند ورود ادمین */
$adminActions = [
    'stats', 'list_messages', 'set_read', 'delete_message', 'clear_read',
    'save_settings', 'reset_settings', 'change_password', 'change_user',
    'change_theme', 'wipe',
];
if (in_array($action, $adminActions, true)) {
    require_admin();
}

switch ($action) {

    /* ─────────── فرم تماس (عمومی) ─────────── */
    case 'submit_message': {
        $name    = post_str('name', 60);
        $email   = post_str('email', 100);
        $subject = post_str('subject', 100);
        $message = post_str('message', 2000);

        if (len($name) < 2)  json_error('نام باید حداقل ۲ حرف باشد.');
        if (len($name) > 60) json_error('نام بیش از حد مجاز است.');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) json_error('ایمیل معتبر نیست.');
        if (len($subject) < 2)  json_error('موضوع را وارد کنید.');
        if (len($message) < 10) json_error('پیام باید حداقل ۱۰ حرف باشد.');

        $msgs = db_read('messages.json', ['items' => []]);
        $msgs['items'][] = [
            'id'      => new_id(),
            'name'    => $name,
            'email'   => $email,
            'subject' => $subject,
            'message' => $message,
            'ip'      => client_ip(),
            'date'    => date('c'),
            'read'    => false,
        ];
        /* حداکثر ۵۰۰ پیام */
        if (count($msgs['items']) > 500) {
            $msgs['items'] = array_slice($msgs['items'], -500);
        }
        db_write('messages.json', $msgs);
        json_out(['ok' => true, 'msg' => 'پیام شما با موفقیت ثبت شد.']);
    }

    /* ─────────── آمار (ادمین) ─────────── */
    case 'stats': {
        $views = db_read('views.json', ['total' => 0, 'daily' => []]);
        $daily = is_array($views['daily'] ?? null) ? $views['daily'] : [];

        $last7 = [];
        for ($i = 6; $i >= 0; $i--) {
            $key = date('Y-m-d', strtotime("-$i days"));
            $last7[] = ['date' => $key, 'count' => (int)($daily[$key] ?? 0)];
        }

        $items = get_messages();

        json_out([
            'ok'   => true,
            'data' => [
                'views_total' => (int)($views['total'] ?? 0),
                'views_today' => (int)($daily[date('Y-m-d')] ?? 0),
                'last7'       => $last7,
                'msgs_total'  => count($items),
                'msgs_unread' => count(array_filter($items, static function ($m) {
                    return empty($m['read']);
                })),
            ],
        ]);
    }

    /* ─────────── لیست پیام‌ها (ادمین) ─────────── */
    case 'list_messages': {
        $items = get_messages();
        /* جدیدترین اول */
        $items = array_reverse($items);
        json_out(['ok' => true, 'data' => $items]);
    }

    /* ─────────── خوانده/نخوانده (ادمین) ─────────── */
    case 'set_read': {
        $id   = post_str('id', 40);
        $read = (($_POST['read'] ?? '') === '1');

        $msgs = db_read('messages.json', ['items' => []]);
        $found = false;
        foreach ($msgs['items'] as &$m) {
            if (is_array($m) && hash_equals((string)($m['id'] ?? ''), $id)) {
                $m['read'] = $read;
                $found = true;
                break;
            }
        }
        unset($m);
        if (!$found) json_error('پیام یافت نشد.', 404);

        db_write('messages.json', $msgs);
        json_out(['ok' => true]);
    }

    /* ─────────── حذف پیام (ادمین) ─────────── */
    case 'delete_message': {
        $id = post_str('id', 40);

        $msgs = db_read('messages.json', ['items' => []]);
        $before = count($msgs['items']);
        $msgs['items'] = array_values(array_filter($msgs['items'], static function ($m) use ($id) {
            return !(is_array($m) && hash_equals((string)($m['id'] ?? ''), $id));
        }));
        if (count($msgs['items']) === $before) {
            json_error('پیام یافت نشد.', 404);
        }
        db_write('messages.json', $msgs);
        json_out(['ok' => true]);
    }

    /* ─────────── پاک‌سازی خوانده‌شده‌ها (ادمین) ─────────── */
    case 'clear_read': {
        $msgs = db_read('messages.json', ['items' => []]);
        $msgs['items'] = array_values(array_filter($msgs['items'], static function ($m) {
            return empty($m['read']);
        }));
        db_write('messages.json', $msgs);
        json_out(['ok' => true]);
    }

    /* ─────────── ذخیره تنظیمات (ادمین) ─────────── */
    case 'save_settings': {
        $title   = post_str('title', 120);
        $heroSub = post_str('heroSub', 400);
        $wordsRaw = post_str('words', 400);
        $address = post_str('address', 160);
        $phone   = post_str('phone', 32);
        $email   = post_str('email', 100);

        if (len($title) < 3)   json_error('عنوان سایت معتبر نیست (حداقل ۳ حرف).');
        if (len($heroSub) < 5) json_error('زیرعنوان معتبر نیست (حداقل ۵ حرف).');
        if (len($address) < 3) json_error('آدرس معتبر نیست.');
        if (len($phone) < 3)   json_error('تلفن معتبر نیست.');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) json_error('ایمیل معتبر نیست.');

        $words = [];
        foreach (explode(',', $wordsRaw) as $w) {
            $w = trim($w);
            if ($w !== '') $words[] = $w;
        }
        if (count($words) < 1 || count($words) > 8) {
            json_error('بین ۱ تا ۸ کلمه وارد کنید.');
        }
        foreach ($words as $w) {
            if (len($w) < 2 || len($w) > 40) {
                json_error('هر کلمه باید بین ۲ تا ۴۰ حرف باشد.');
            }
        }

        $s = get_settings();
        $s['title']   = $title;
        $s['heroSub'] = $heroSub;
        $s['words']   = $words;
        $s['address'] = $address;
        $s['phone']   = $phone;
        $s['email']   = $email;
        db_write('settings.json', $s);
        json_out(['ok' => true, 'msg' => 'تنظیمات با موفقیت ذخیره شد.']);
    }

    /* ─────────── بازنشانی تنظیمات (ادمین) ─────────── */
    case 'reset_settings': {
        db_write('settings.json', settings_default());
        json_out(['ok' => true, 'msg' => 'تنظیمات به حالت پیش‌فرض بازگشت.']);
    }

    /* ─────────── تغییر رمز (ادمین) ─────────── */
    case 'change_password': {
        $current = (string)($_POST['current'] ?? '');
        $new     = (string)($_POST['new'] ?? '');
        $new2    = (string)($_POST['new2'] ?? '');

        /* راستی‌آزمایی رمز فعلی */
        $users = db_read('users.json', ['items' => []]);
        $me = null;
        foreach ($users['items'] as $u) {
            if (is_array($u) && hash_equals((string)($u['user'] ?? ''), (string)($_SESSION['admin_user'] ?? ''))) {
                $me = $u;
                break;
            }
        }
        if ($me === null || !password_verify($current, (string)($me['pass_hash'] ?? ''))) {
            json_error('رمز فعلی صحیح نیست.', 403);
        }

        /* قوانین رمز جدید */
        if (strlen($new) < 8 || strlen($new) > 64) {
            json_error('رمز جدید باید بین ۸ تا ۶۴ حرف باشد.');
        }
        if (!preg_match('/[A-Za-z]/', $new) || !preg_match('/\d/', $new)) {
            json_error('رمز جدید باید شامل حداقل یک حرف انگلیسی و یک رقم باشد.');
        }
        if ($new !== $new2) {
            json_error('تکرار رمز با رمز جدید یکسان نیست.');
        }

        foreach ($users['items'] as &$u) {
            if (is_array($u) && hash_equals((string)($u['user'] ?? ''), (string)$me['user'])) {
                $u['pass_hash']   = password_hash($new, PASSWORD_DEFAULT);
                $u['must_change'] = false;
            }
        }
        unset($u);
        db_write('users.json', $users);
        $_SESSION['must_change'] = false;
        json_out(['ok' => true, 'msg' => 'رمز عبور با موفقیت تغییر کرد.']);
    }

    /* ─────────── تغییر نام کاربری (ادمین) ─────────── */
    case 'change_user': {
        $newUser = post_str('new_user', 20);
        if (!preg_match('/^[A-Za-z0-9_]{3,20}$/', $newUser)) {
            json_error('نام کاربری باید ۳ تا ۲۰ حرف انگلیسی، عدد یا آندرلاین باشد.');
        }

        $users = db_read('users.json', ['items' => []]);
        foreach ($users['items'] as $u) {
            $uUser = (string)($u['user'] ?? '');
            $isMe = hash_equals($uUser, (string)($_SESSION['admin_user'] ?? ''));
            if (hash_equals($uUser, $newUser) && !$isMe) {
                json_error('این نام کاربری قبلا‌ً گرفته شده است.');
            }
        }

        foreach ($users['items'] as &$u) {
            if (is_array($u) && hash_equals((string)($u['user'] ?? ''), (string)($_SESSION['admin_user'] ?? ''))) {
                $u['user'] = $newUser;
            }
        }
        unset($u);
        db_write('users.json', $users);
        $_SESSION['admin_user'] = $newUser;
        json_out(['ok' => true, 'msg' => 'نام کاربری با موفقیت تغییر کرد.']);
    }

    /* ─────────── تم پیش‌فرض (ادمین) ─────────── */
    case 'change_theme': {
        $t = post_str('theme', 10);
        if (!in_array($t, ['dark', 'light'], true)) {
            json_error('تم نامعتبر است.');
        }
        $s = get_settings();
        $s['theme'] = $t;
        db_write('settings.json', $s);
        json_out(['ok' => true, 'msg' => 'تم پیش‌فرض سایت اعمال شد.']);
    }

    /* ─────────── حذف همه داده‌ها (ادمین) ─────────── */
    case 'wipe': {
        $confirm = post_str('confirm', 10);
        if ($confirm !== 'حذف') {
            json_error('برای تأیید، «حذف» را تایپ کنید.');
        }
        db_write('messages.json', ['items' => []]);
        db_write('views.json', ['total' => 0, 'daily' => []]);
        db_write('settings.json', settings_default());
        db_write('lockout.json', ['failed' => 0, 'until' => 0]);
        json_out(['ok' => true, 'msg' => 'همه داده‌ها پاک شد.']);
    }

    default:
        json_error('عملیات نامعتبر است.', 404);
}
