<?php
// تنظیمات صفحه
$currentPage = 'contact';
$pageTitle = 'تماس با ما | نوآوا';

require __DIR__ . '/../config.php';

$settings = get_settings();

// داده‌های امن برای جاوااسکریپت
$pageData = [
    'theme' => $settings['theme'] ?? 'dark',
    'csrf' => csrf_token(),
    'settings' => $settings,
];

// هدر صفحه
require __DIR__ . '/header.php';
?>

<!-- ─── تماس ─── -->
<section class="section contact" id="contact">
  <div class="contact-grid">
    <div class="contact-info reveal">
      <span class="section-tag">تماس با ما</span>
      <h2 class="section-title">بیا با هم <span class="gradient-text">چیزی بسازیم</span></h2>
      <p>پروژه‌ای در ذهن داری؟ همین حالا به ما پیام بده. در کمتر از ۲۴ ساعت جواب می‌دهیم.</p>

      <ul class="contact-list">
        <li id="addrItem">📍 <?= e($settings['address']) ?></li>
        <li id="phoneItem">📞 <?= e($settings['phone']) ?></li>
        <li id="emailItem">✉️ <?= e($settings['email']) ?></li>
        <li>🕘 شنبه تا پنجشنبه، ۹ صبح تا ۶ عصر</li>
      </ul>

      <div class="socials">
        <a href="#" class="social" aria-label="اینستاگرام">📷</a>
        <a href="#" class="social" aria-label="تلگرام">✈️</a>
        <a href="#" class="social" aria-label="لینکدین">💼</a>
        <a href="#" class="social" aria-label="توئیتر">🐦</a>
      </div>
    </div>

    <form class="contact-form glass reveal" id="contactForm" novalidate>
      <div class="form-row">
        <div class="form-group">
          <label for="name">نام و نام خانوادگی</label>
          <input type="text" id="name" placeholder="مثلا‌ً: مریم احمدی" required />
          <span class="error-msg"></span>
        </div>
        <div class="form-group">
          <label for="email">ایمیل</label>
          <input type="email" id="email" placeholder="you@example.com" required />
          <span class="error-msg"></span>
        </div>
      </div>
      <div class="form-group">
        <label for="subject">موضوع</label>
        <select id="subject">
          <option>طراحی وب‌سایت</option>
          <option>اپلیکیشن موبایل</option>
          <option>برندینگ</option>
          <option>سایر</option>
        </select>
      </div>
      <div class="form-group">
        <label for="message">پیام شما</label>
        <textarea id="message" rows="4" placeholder="درباره پروژه‌ات بنویس..." required></textarea>
        <span class="error-msg"></span>
      </div>
      <button type="submit" class="btn btn-primary btn-full" id="submitBtn">
        ارسال پیام ✨
      </button>
      <p class="form-success" id="formSuccess">✅ پیام شما با موفقیت ارسال شد! به‌زودی با شما تماس می‌گیریم.</p>
    </form>
  </div>
</section>

<!-- موقعیت مکانی -->
<section class="section map">
  <div class="section-head reveal">
    <span class="section-tag">موقعیت مکانی</span>
    <h2 class="section-title">ما کجا هستیم؟</h2>
  </div>
  <div class="map-container reveal">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3239.8280159999997!2d51.4166279!3d35.7447912!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3f8e07a4a0a3b8d9%3A0x4d3c3e8e3e3e3e3e!2z2KjZhNin2YXYudmI2LHZitmG2Kc!5e0!3m2!1sfa!2sir!4v1234567890123!5m2!1sfa!2sir" width="100%" height="450" style="border:0; border-radius: var(--radius);" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
  </div>
</section>

<?php
// فوتر صفحه
require __DIR__ . '/footer.php';
?>