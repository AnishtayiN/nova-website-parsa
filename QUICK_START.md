# راهنمای سریع شروع

## گام 1: ریپازیتوری GitHub بسازید

🔹 **در مرورگر خود:**
1. به [https://github.com/new](https://github.com/new) بروید
2. فیلدها را اینگونه پر کنید:
   - **Repository name:** `nova-website-parsa`
   - **Description:** `سایت کامل نوآوا - استودیو خلاقیت دیجیتال`
   - **Public** ✅
   - **Initialize this repository with:** ❌ (هیچکدام رو انتخاب نکنید)
3. روی **Create repository** کلیک کنید

✅ ریپازیتوری ساخته شد!

## گام 2: آدرس ریپازیتوری رو کپی کنید

بعد از ساخت، صفحه‌ای مانند این می‌بینید:
```
https://github.com/YOUR_USERNAME/nova-website-parsa
```

روی دکمه **Code** کلیک کنید و آدرس را کپی کنید:
```
https://github.com/YOUR_USERNAME/nova-website-parsa.git
```

## گام 3: دستورات Git رو اجرا کنید

🔹 **در CMD یا Git Bash:**

```bash
cd G:/github/parsa/website
git remote add origin https://github.com/YOUR_USERNAME/nova-website-parsa.git
git branch -M master
git push -u origin master
```

## یا از اسکریپت آماده استفاده کنید

🔹 **روشی ساده‌تر:**

1. فایل `upload_to_github.bat` را اجرا کنید
2. وقتی از شما آدرس خواست، آدرس ریپازیتوری رو paste کنید
3. Enter بزنید
4. کار تمام است!

## اگر خطا گرفتید

### خطا: "Repository not found"
✅ **حل:** از آدرس درست استفاده کنید (با .git در انتها)

### خطا: "failed to push some refs"
✅ **حل:** ریپازیتوری شما با README ساخته شده. این دستورات رو اجرا کنید:
```bash
git pull origin master --allow-unrelated-histories
git push -u origin master
```

### خطا: "Authentication failed"
✅ **حل 1:** از آدرس SSH استفاده کنید:
```bash
git remote set-url origin git@github.com:YOUR_USERNAME/nova-website-parsa.git
git push -u origin master
```

✅ **حل 2:** اگر از HTTPS استفاده می‌کنید، در Git Bash:
```bash
git config --global credential.helper store
git push -u origin master
```
(بعد از اجرا، یوزرنیم و پسورد GitHub رو وارد کنید)

## گام 4: بررسی ریپازیتوری

✅ بعد از push موفق:
- به صفحه ریپازیتوری خود در GitHub بروید
- تمام فایل‌ها باید آنجا باشند
- می‌توانید فایل‌ها رو آنلاین ببینید

## گام 5: اجرا روی سرور محلی

🔹 **با XAMPP:**
1. پوشه `website` را به `C:/xampp/htdocs/nova` کپی کنید
2. XAMPP را اجرا کنید و Apache را Start کنید
3. در مرورگر: `http://localhost/nova`

🔹 **با سرور داخلی PHP:**
```bash
cd G:/github/parsa/website
php -S localhost:8000
```
در مرورگر: `http://localhost:8000`

## ورود به پنل مدیریت

🔹 **نکات مهم:**
- آدرس پنل مدیریت: `admin.php`
- در اولین اجرا، یک اکانت ادمین به صورت خودکار ساخته می‌شود
- رمز عبور اولیه خالی است، پس در اولین ورود باید رمز رو تنظیم کنید
- بعد از ورود، بنر هشدار برای تغییر رمز نمایش داده می‌شود
- برای تنظیمات، فایل `config.env.php` را ویرایش کنید ( قبل از آپلود)

## فایل‌های مهم

| فایل/پوشه | شرح |
|---|---|
| `index.php` | صفحه اصلی سایت |
| `admin.php` | پنل مدیریت |
| `config.php` | تنظیمات اصلی |
| `config.env.php` | تنظیمات محیطی (در .gitignore) |
| `data/` | دیتابیس JSON |
| `css/` | فایل‌های CSS |
| `js/` | فایل‌های JavaScript |
| `pages/` | صفحات دیگر |
| `.htaccess` | تنظیمات Apache |
| `.gitignore` | فایل‌هایی که آپلود نمی‌شوند |

## امنیت

✅ همه مسائل امنیتی رعایت شده:
- هش کردن رمزها با bcrypt
- حفاظت CSRF
- ریت‌لیمیت برای لاگین
- حفره‌های XSS رفع شده
- هدرهای امنیتی اضافه شده

---

## نیاز به کمک؟

- اگر در هر مرحله مشکل داشتید، فایل `UPLOAD_GUIDE.md` را مطالعه کنید
- یا با من تماس بگیرید

✅ **موفق باشید!**
