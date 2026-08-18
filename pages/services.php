<?php
// تنظیمات صفحه
$currentPage = 'services';
$pageTitle = 'خدمات | نوآوا';

require __DIR__ . '/../config.php';

$settings = get_settings();

// داده‌های سرور برای جاوااسکریپت
$defaults = [];
if (is_file(__DIR__ . '/../data/defaults.json')) {
    $defaults = json_decode(file_get_contents(__DIR__ . '/../data/defaults.json'), true) ?: [];
}

$pageData = [
    'theme' => $settings['theme'] ?? 'dark',
    'defaults' => $defaults,
];

// هدر صفحه
require __DIR__ . '/header.php';
?>

<!-- ─── خدمات ─── -->
<section class="section services" id="services">
  <div class="section-head reveal">
    <span class="section-tag">خدمات ما</span>
    <h2 class="section-title">هر چیزی که برای <span class="gradient-text">موفقیت دیجیتال</span> لازم داری</h2>
    <p class="section-desc">از ایده تا اجرا، تیم ما در هر مرحله کنارت است.</p>
  </div>

  <div class="cards-grid">
    <?php foreach ($pageData['defaults']['services'] as $service): ?>
    <div class="service-card glass tilt reveal">
      <div class="card-icon" style="--c1:<?= $service['colors']['c1'] ?>;--c2:<?= $service['colors']['c2'] ?>;"><?= $service['icon'] ?></div>
      <h3><?= e($service['title']) ?></h3>
      <p><?= e($service['desc']) ?></p>
      <a href="contact.php" class="card-link">ادامه ←</a>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- چرا نوآوا؟ -->
<section class="section why-us">
  <div class="section-head reveal">
    <span class="section-tag">چرا نوآوا؟</span>
    <h2 class="section-title">چرا ما را انتخاب می‌کنید؟</h2>
  </div>

  <div class="why-grid">
    <div class="why-card reveal">
      <div class="why-icon">🎯</div>
      <h3>تمرکز بر کاربر</h3>
      <p>ما همیشه کاربر را در مرکز همه تصمیمات طراحی و توسعه قرار می‌دهیم.</p>
    </div>
    <div class="why-card reveal">
      <div class="why-icon">⚡</div>
      <h3>سرعت و کیفیت</h3>
      <p>ما پروژه‌ها را با سرعت بالا و کیفیت بی‌نظیر تحویل می‌دهیم.</p>
    </div>
    <div class="why-card reveal">
      <div class="why-icon">💡</div>
      <h3>خلاقیت بی‌پایان</h3>
      <p>ما همیشه به دنبال راه‌حل‌های خلاقانه و نوآورانه هستیم.</p>
    </div>
  </div>
</section>

<?php
// فوتر صفحه
require __DIR__ . '/footer.php';
?>