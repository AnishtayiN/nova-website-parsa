<?php
// تنظیمات صفحه
$currentPage = 'portfolio';
$pageTitle = 'نمونه‌کارها | نوآوا';

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

<!-- ─── نمونه‌کارها ─── -->
<section class="section portfolio" id="portfolio">
  <div class="section-head reveal">
    <span class="section-tag">نمونه‌کارها</span>
    <h2 class="section-title">پروژه‌هایی که به آن‌ها <span class="gradient-text">افتخار می‌کنیم</span></h2>
  </div>

  <div class="filter-btns reveal">
    <button class="filter-btn active" data-filter="all">همه</button>
    <button class="filter-btn" data-filter="web">وب‌سایت</button>
    <button class="filter-btn" data-filter="app">اپلیکیشن</button>
    <button class="filter-btn" data-filter="brand">برندینگ</button>
  </div>

  <div class="portfolio-grid">
    <?php foreach ($pageData['defaults']['portfolio'] as $item): ?>
    <div class="portfolio-item reveal" data-cat="<?= $item['cat'] ?>">
      <div class="p-item-inner" style="--grad:<?= $item['grad'] ?>">
        <div class="p-emoji"><?= $item['emoji'] ?></div>
        <div class="p-overlay">
          <h3><?= e($item['title']) ?></h3>
          <p><?= e($item['desc']) ?></p>
          <span class="p-tag"><?= e($item['cat'] === 'web' ? 'وب‌سایت' : ($item['cat'] === 'app' ? 'اپلیکیشن' : 'برندینگ')) ?></span>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- مشتریان ما -->
<section class="section clients">
  <div class="section-head reveal">
    <span class="section-tag">مشتریان ما</span>
    <h2 class="section-title">برخی از مشتریان ما</h2>
  </div>

  <div class="clients-grid">
    <div class="client-logo glass reveal">فروشگاه مدینا</div>
    <div class="client-logo glass reveal">استارتاپ رایا</div>
    <div class="client-logo glass reveal">کافه آفتاب</div>
    <div class="client-logo glass reveal">درمانگاه سلامت</div>
    <div class="client-logo glass reveal">جشنواره موسیقی</div>
  </div>
</section>

<?php
// فوتر صفحه
require __DIR__ . '/footer.php';
?>