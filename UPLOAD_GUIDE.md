# راهنمای آپلود به GitHub

## مرحله 1: ریپازیتوری جدید بسازید

1. به آدرس زیر بروید:
   [https://github.com/new](https://github.com/new)

2. فیلدها رو پر کنید:
   - **Repository name:** `nova-website-parsa` (یا هر اسم دیگه‌ای)
   - **Description:** `سایت کامل نوآوا - استودیو خلاقیت دیجیتال با پنل مدیریت و دیتابیس JSON`
   - **Public/Private:** Public
   - **Initialize this repository with a README:** ❌ (غیرفعال)
   - **Add .gitignore:** ❌ (غیرفعال)
   - **Add a license:** ❌ (غیرفعال)

3. روی **Create repository** کلیک کنید

## مرحله 2: آدرس ریپازیتوری رو کپی کنید

بعد از ساخت ریپازیتوری، آدرس آن به صورت زیر هست:
```
https://github.com/AnishtayiN/nova-website-parsa.git
```

(به جای AnishtayiN، اسم کاربری خودتون رو بذارید)

## مرحله 3: دستورات Git رو اجرا کنید

```bash
cd G:/github/parsa/website

# ریپازیتوری رو به عنوان remote اضافه کنید
git remote add origin https://github.com/YOUR_USERNAME/nova-website-parsa.git

# برنچ رو به master تغییر بدید (اگر لازم بود)
git branch -M master

# تمام فایل‌ها رو push کنید
git push -u origin master
```

## یا با اسکریپت خودکار:

فایل `upload_to_github.bat` رو اجرا کنید و آدرس ریپازیتوری رو وقتی خواست وارد کنید.

---

## اگر خطا گرفتید:

### خطا: "Repository not found"
- مطمئن بشید ریپازیتوری رو درست ساختید
- مطمئن بشید اسم کاربری رو درست وارد کردید

### خطا: "failed to push some refs"
- مطمئن بشید که ریپازیتوری خالی هست (Without README, .gitignore, etc.)
- اگر ریپازیتوری با README ساخته شده، این دستور رو اجرا کنید:
  ```bash
  git pull origin master --allow-unrelated-histories
  git push -u origin master
  ```

### خطا: "Authentication failed"
- اگر از HTTPS استفاده می‌کنید، ممکنه به توکن یا رمز عبور نیاز داشته باشید
- از SSH استفاده کنید:
  ```bash
  git remote set-url origin git@github.com:YOUR_USERNAME/nova-website-parsa.git
  git push -u origin master
  ```

---

## فایل‌هایی که آپلود می‌شوند:

- همه فایل‌های PHP، CSS، JS
- پوشه‌های css/, js/, pages/, data/
- فایل‌های پیکربندی (.htaccess, .gitignore, config.php, etc.)
- به استثنای فایل‌های موجود در .gitignore (config.env.php, data/*.json به جز defaults.json, etc.)

---

## بعد از آپلود:

1. ریپازیتوری شما آماده‌ست!
2. برای اجرا روی سرور محلی:
   - پوشه رو توی XAMPP/htdocs کپی کنید
   - یا از سرور داخلی PHP استفاده کنید: `php -S localhost:8000`
3. برای ورود به پنل مدیریت:
   - به صفحه `admin.php` بروید
   - اطلاعات ورود اولیه در اولین اجرا به صورت خودکار ساخته می‌شود
   - بعد از اولین ورود، تغییر رمز اجباری هست

---

## نکات امنیتی:

- فایل `config.env.php` در .gitignore قرار داره و آپلود نمی‌شه
- فایل‌های داده (data/*.json) هم آپلود نمی‌شوند
- برای محیط تولید، حتماً فایل config.env.php رو با تنظیمات خودتون بسازید
