# نوآوا — سایت + پنل مدیریت (PHP + دیتابیس JSON)

سایت کامل با پنل مدیریت واقعی. بدون نیاز به MySQL — دیتابیس JSON با قفل‌گذاری فایل.

## اجرای سایت

### گزینه ۱: XAMPP (پیشنهادی — Apache)

1. پوشه website را داخل C:/xampp/htdocs/nova کپی کنید.
2. در پنل XAMPP، Apache را Start کنید.
3. مرورگر: http://localhost/nova/index.php

### گزینه ۲: سرور داخلی PHP (تست سریع)

    cd G:\github\parsa\website
    C:\xampp\php\php.exe -S 127.0.0.1:8080

    مرورگر: http://127.0.0.1:8080

> نکته: در سرور داخلی، قوانین .htaccess اعمال نمی‌شود و پوشه data/ در Apache واقعی (XAMPP) محافظت می‌شود.

## ورود به پنل مدیریت

- آدرس: admin.php (لینک «پنل مدیریت» در فوتر سایت هم هست)
- اطلاعات ورود اولیه در اولین اجرا به طور خودکار ایجاد می‌شود
- بعد از اولین ورود، تغییر رمز اجباری است (بنر هشدار نمایش داده می‌شود)
- برای تنظیم رمز اولیه، فایل config.env.php را ویرایش کنید

## دیتابیس JSON (پوشه data)

| فایل | محتوا |
|---|---|
| users.json | اکانت ادمین (رمز با bcrypt هش شده) |
| messages.json | پیام‌های فرم تماس (نام، ایمیل، موضوع، متن، IP، تاریخ) |
| views.json | بازدید کل + بازدید روزانه (۳۰ روز) |
| settings.json | محتوا و تم سایت |
| lockout.json | شمارنده قفل موقت لاگین |

نوشتن‌های هم‌زمان امن‌اند (قفل انحصاری flock).

## امنیت پیاده‌سازی‌شده

| لایه | توضیح |
|---|---|
| هش رمز | password_hash با bcrypt + تغییر اجباری رمز اولیه |
| سشن | HttpOnly + SameSite=Lax + تغییر ID سشن هنگام ورود (ضد fixation) |
| CSRF | توکن ۳۲ بایتی برای همه عملیات POST (فرم، پنل، لاگین) |
| ریت‌لیمیت | ۵ تلاش ناموفق در لاگین = قفل ۱۵ دقیقه |
| اعتبارسنجی | بررسی طول و قالب همه فیلدها سمت سرور + اعتبارسنجی ایمیل |
| خروجی | ماسک‌سازی همه خروجی‌ها (ضد XSS) |
| CSP | script-src 'self' — بدون اسکریپت درون‌خطی یا خارجی |
| هدرها | nosniff، X-Frame-Options، Referrer-Policy، Permissions-Policy |
| Apache | قفل پوشه data + ممنوعیت JSON ها + پنهان‌کردن نسخه PHP |
| مقایسه‌ها | hash_equals برای همه (ضد timing attack) |
| خطاها | هیچ خطایی به کاربر نمایش داده نمی‌شود |

## فایل‌ها

    index.php    صفحه اصلی (رندر سروری از settings.json + شمارش بازدید)
    admin.php    پنل مدیریت (قبل از ورود فقط فرم لاگین رندر می‌شود)
    api.php      همه عملیات (پیام، آمار، تنظیمات، رمز، حذف) با CSRF + سشن
    auth.php     ورود/خروج/وضعیت — ریت‌لیمیت + password_verify
    config.php   سشن، هدرها، دیتابیس JSON با flock، ابزارهای امنیتی
    main.js      افکت‌های گرافیکی + ارسال فرم به api.php
    admin.js     منطق پنل (fetch به api.php) + رفرش خودکار هر ۲۵ ثانیه
    style.css / admin.css
    data/        دیتابیس‌های JSON (خودکار در اولین اجرا ساخته می‌شوند)
    .htaccess    محافظت Apache

---

## آپلود به GitHub

برای آپلود پروژه به GitHub، از یکی از روش‌های زیر استفاده کنید:

### روش 1: خودکار (با اسکریپت)
1. فایل `upload_to_github.bat` را اجرا کنید
2. آدرس ریپازیتوری GitHub خود را وارد کنید
3. اسکریپت بقیه کارها را انجام می‌دهد

### روش 2: دستی
1. در GitHub، ریپازیتوری جدید بسازید (بدون README)
2. دستورات زیر را اجرا کنید:
   ```bash
   cd G:/github/parsa/website
   git remote add origin https://github.com/YOUR_USERNAME/nova-website.git
   git branch -M master
   git push -u origin master
   ```

### روش 3: با استفاده از اسکریپت Bash
   ```bash
   ./push_to_github.sh
   ```

> توجه: ریپازیتوری باید خالی باشد (بدون README، .gitignore، یا license).
