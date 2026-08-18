<?php
/**
 * نوآوا — تنظیمات محیطی
 * این فایل حاوی تنظیمات پایه سایت است
 * برای امنیت بیشتر، این فایل را خارج از وب سرور قرار دهید
 * یا آن را در .gitignore قرار دهید
 */

// تنظیمات دیتابیس JSON
define('DB_PATH', __DIR__ . '/data');

// تنظیمات سشن
define('SESSION_NAME', 'NOVA_SESSION');
define('SESSION_LIFETIME', 3600); // 1 ساعت

// تنظیمات امنیتی
define('CSRF_TOKEN_LIFETIME', 1800); // 30 دقیقه
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900); // 15 دقیقه

// تنظیمات ادمین (در صورت نیاز به تغییر، اینجا ویرایش کنید)
// توجه: پسورد باید هش شده باشد (با استفاده از password_hash)
// مثال: password_hash('your_password', PASSWORD_BCRYPT)
define('ADMIN_USERNAME', 'admin');

// پسورد پیش‌فرض - لطفاً آن را تغییر دهید
// این پسورد هش شده است: "admin123"
define('ADMIN_PASSWORD_HASH', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

// تنظیمات سایت
define('SITE_NAME', 'نوآوا');
define('SITE_DESCRIPTION', 'استودیو خلاقیت دیجیتال');
define('SITE_URL', 'http://localhost/nova-website-parsa');

// تنظیمات ایمیل (اختیاری)
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your@email.com');
define('SMTP_PASSWORD', 'your_password');
define('SMTP_FROM', 'your@email.com');

// تنظیمات دیباگ
define('DEBUG_MODE', false);

// تنظیمات زمان
define('TIMEZONE', 'Asia/Tehran');

// تنظیمات زبان
define('LANGUAGE', 'fa');

// پایان فایل تنظیمات
?>