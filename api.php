<?php
/**
 * ═══════════════════════════════════════════════
 *  نوآوا — نقطه‌های اتصال API
 *  همه عملیات: POST + CSRF (+ سشن ادمین برای عملیات حساس)
 * ═══════════════════════════════════════════════
 */
require __DIR__ . '/config.php';

$action = isset($_GET['action']) && is_string($_GET['action']) ? $_GET['action'] : '';

function get_messages(): array {
    $msgs = db_read('messages.json', ['items' => []]);
    return is_array($msgs['items'] ?? null) ? $msgs['items'] : [];
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('روش درخواست نامعتبر است.', 405);
verify_csrf();

$adminActions = ['stats', 'list_messages', 'set_read', 'delete_message', 'clear_read', 'save_settings', 'reset_settings', 'change_password', 'change_user', 'change_theme', 'wipe'];
if (in_array($action, $adminActions, true)) require_admin();

switch ($action) {
    case 'submit_message': {
        $name = post_str('name', 60);
        $email = post_str('email', 100);
        $subject = post_str('subject', 100);
        $message = post_str('message', 2000);
        if (len($name) < 2) json_error('نام باید حداقل ۲ حرف باشد.');
        if (len($name) > 60) json_error('نام بیش از حد مجاز است.');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) json_error('ایمیل معتبر نیست.');
        if (len($subject) < 2) json_error('موضوع را وارد کنید.');
        if (len($message) < 10) json_error('پیام باید حداقل ۱۰ حرف باشد.');
        $msgs = db_read('messages.json', ['items' => []]);
        $msgs['items'][] = ['id' => new_id(), 'name' => $name, 'email' => $email, 'subject' => $subject, 'message' => $message, 'ip' => get_client_ip(), 'date' => date('c'), 'read' => false];
        if (count($msgs['items']) > 500) $msgs['items'] = array_slice($msgs['items'], -500);
        db_write('messages.json', $msgs);
        json_out(['ok' => true, 'msg' => 'پیام شما با موفقیت ثبت شد.']);
    }

    case 'stats': {
        $views = db_read('views.json', ['total' => 0, 'daily' => []]);
        $daily = is_array($views['daily'] ?? null) ? $views['daily'] : [];
        $last7 = [];
        for ($i = 6; $i >= 0; $i--) {
            $key = date('Y-m-d', strtotime("-$i days"));
            $last7[] = ['date' => $key, 'count' => (int)($daily[$key] ?? 0)];
        }
        $items = get_messages();
        json_out(['ok' => true, 'data' => ['views_total' => (int)($views['total'] ?? 0), 'views_today' => (int)($daily[date('Y-m-d')] ?? 0), 'last7' => $last7, 'msgs_total' => count($items), 'msgs_unread' => count(array_filter($items, fn($m) => empty($m['read'])))]]);
    }

    case 'list_messages': {
        $items = get_messages();
        $items = array_reverse($items);
        json_out(['ok' => true, 'data' => $items]);
    }

    case 'set_read': {
        $id = post_str('id', 40);
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

    case 'delete_message': {
        $id = post_str('id', 40);
        $msgs = db_read('messages.json', ['items' => []]);
        $before = count($msgs['items']);
        $msgs['items'] = array_values(array_filter($msgs['items'], fn($m) => !(is_array($m) && hash_equals((string)($m['id'] ?? ''), $id))));
        if (count($msgs['items']) === $before) json_error('پیام یافت نشد.', 404);
        db_write('messages.json', $msgs);
        json_out(['ok' => true]);
    }

    case 'clear_read': {
        $msgs = db_read('messages.json', ['items' => []]);
        $msgs['items'] = array_values(array_filter($msgs['items'], fn($m) => empty($m['read'])));
        db_write('messages.json', $msgs);
        json_out(['ok' => true]);
    }

    case 'save_settings': {
        $title = post_str('title', 120);
        $heroSub = post_str('heroSub', 400);
        $wordsRaw = post_str('words', 400);
        $address = post_str('address', 160);
        $phone = post_str('phone', 32);
        $email = post_str('email', 100);
        if (len($title) < 3) json_error('عنوان سایت معتبر نیست.');
        if (len($heroSub) < 5) json_error('زیرعنوان معتبر نیست.');
        if (len($address) < 3) json_error('آدرس معتبر نیست.');
        if (len($phone) < 3) json_error('تلفن معتبر نیست.');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) json_error('ایمیل معتبر نیست.');
        $words = [];
        foreach (explode(',', $wordsRaw) as $w) {
            $w = trim($w);
            if ($w !== '') $words[] = $w;
        }
        if (count($words) < 1 || count($words) > 8) json_error('بین ۱ تا ۸ کلمه وارد کنید.');
        foreach ($words as $w) if (len($w) < 2 || len($w) > 40) json_error('هر کلمه باید بین ۲ تا ۴۰ حرف باشد.');
        $s = get_settings();
        $s['title'] = $title;
        $s['heroSub'] = $heroSub;
        $s['words'] = $words;
        $s['address'] = $address;
        $s['phone'] = $phone;
        $s['email'] = $email;
        db_write('settings.json', $s);
        json_out(['ok' => true, 'msg' => 'تنظیمات ذخیره شد.']);
    }

    case 'reset_settings': {
        db_write('settings.json', settings_default());
        json_out(['ok' => true, 'msg' => 'تنظیمات بازنشانی شد.']);
    }

    case 'change_password': {
        $current = post_str('current', 100);
        $newPass1 = post_str('new1', 100);
        $newPass2 = post_str('new2', 100);
        if (len($current) < 1) json_error('رمز فعلی را وارد کنید.');
        if (len($newPass1) < 8) json_error('رمز جدید باید حداقل ۸ حرف باشد.');
        if ($newPass1 !== $newPass2) json_error('رمزهای جدید با هم مطابقت ندارند.');
        if (!preg_match('/[A-Za-z]/', $newPass1) || !preg_match('/[0-9]/', $newPass1)) json_error('رمز جدید باید شامل حرف انگلیسی و رقم باشد.');
        $users = db_read('users.json', ['items' => []]);
        $adminUser = (string)($_SESSION['admin_user'] ?? '');
        $found = null;
        foreach ($users['items'] as &$u) {
            if (is_array($u) && hash_equals((string)($u['user'] ?? ''), $adminUser)) {
                if (!password_verify($current, (string)($u['pass_hash'] ?? ''))) json_error('رمز فعلی اشتباه است.', 401);
                $u['pass_hash'] = password_hash($newPass1, PASSWORD_BCRYPT);
                $u['must_change'] = false;
                $found = $u;
                break;
            }
        }
        if ($found === null) {
            if (!password_verify($current, ADMIN_PASSWORD_HASH)) json_error('رمز فعلی اشتباه است.', 401);
            $users['items'] = [['user' => $adminUser, 'pass_hash' => password_hash($newPass1, PASSWORD_BCRYPT), 'must_change' => false]];
        }
        unset($u);
        db_write('users.json', $users);
        $_SESSION['must_change'] = false;
        json_out(['ok' => true, 'msg' => 'رمز عبور تغییر یافت.']);
    }

    case 'change_user': {
        $newUser = post_str('username', 32);
        if (len($newUser) < 3 || len($newUser) > 20) json_error('نام کاربری باید بین ۳ تا ۲۰ حرف باشد.');
        if (!preg_match('/^[A-Za-z0-9_]+$/', $newUser)) json_error('نام کاربری فقط می‌تواند شامل حروف انگلیسی، اعداد و زیرخط باشد.');
        $users = db_read('users.json', ['items' => []]);
        $oldUser = (string)($_SESSION['admin_user'] ?? '');
        $found = false;
        foreach ($users['items'] as &$u) {
            if (is_array($u) && hash_equals((string)($u['user'] ?? ''), $oldUser)) {
                $u['user'] = $newUser;
                $found = true;
                break;
            }
        }
        if (!$found) $users['items'] = [['user' => $newUser, 'pass_hash' => ADMIN_PASSWORD_HASH, 'must_change' => true]];
        unset($u);
        db_write('users.json', $users);
        $_SESSION['admin_user'] = $newUser;
        json_out(['ok' => true, 'msg' => 'نام کاربری تغییر یافت.']);
    }

    case 'change_theme': {
        $theme = post_str('theme', 10);
        if (!in_array($theme, ['dark', 'light'], true)) json_error('تم باید dark یا light باشد.');
        $s = get_settings();
        $s['theme'] = $theme;
        db_write('settings.json', $s);
        json_out(['ok' => true, 'msg' => 'تم تغییر یافت.']);
    }

    case 'wipe': {
        $confirm = post_str('confirm', 20);
        if ($confirm !== 'حذف') json_error('تاییدیه حذف معتبر نیست.', 400);
        $defaultFiles = ['settings.json', 'users.json', 'messages.json', 'views.json', 'lockout.json'];
        foreach ($defaultFiles as $file) {
            $filePath = DATA_DIR . '/' . $file;
            if (file_exists($filePath)) file_put_contents($filePath, '{}');
        }
        db_write('users.json', ['items' => [['user' => ADMIN_USERNAME, 'pass_hash' => ADMIN_PASSWORD_HASH, 'must_change' => true]]]);
        db_write('settings.json', settings_default());
        json_out(['ok' => true, 'msg' => 'همه داده‌ها پاک شدند. کاربر پیش‌فرض مجدداً ایجاد شد.']);
    }

    default:
        json_error('عملیات نامعتبر است.', 404);
}
