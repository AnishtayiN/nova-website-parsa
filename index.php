<?php
// تنظیمات صفحه
$currentPage = 'home';
$pageTitle = 'نوآوا | استودیو خلاقیت دیجیتال';

require __DIR__ . '/config.php';

// شمارش بازدید در دیتابیس JSON
$views = db_read('views.json', ['total' => 0, 'daily' => []]);
$views['total'] = (int)($views['total'] ?? 0) + 1;
$todayKey = date('Y-m-d');
$views['daily'][$todayKey] = (int)($views['daily'][$todayKey] ?? 0) + 1;
// فقط ۳۰ روز اخیر نگه داشته شود
$limit = date('Y-m-d', strtotime('-30 days'));
foreach (array_keys($views['daily'] ?? []) as $k) {
    if ($k < $limit) {
        unset($views['daily'][$k]);
    }
}
db_write('views.json', $views);

$settings = get_settings();

// داده‌های امن برای جاوااسکریپت
$defaults = [];
if (is_file(__DIR__ . '/data/defaults.json')) {
    $defaults = json_decode(file_get_contents(__DIR__ . '/data/defaults.json'), true) ?: [];
}

$pageData = [
    'words' => $settings['words'],
    'csrf'  => csrf_token(),
    'theme' => $settings['theme'],
    'defaults' => $defaults,
];

// هدر صفحه
require __DIR__ . '/pages/header.php';
?>

  <!-- ─── بخش قهرمان ─── -->
  <section class="hero" id="home">
    <div class="hero-content">
      <span class="hero-badge">✨ استودیو خلاقیت دیجیتال</span>
      <h1 class="hero-title" id="typingTarget">
        ما <span class="gradient-text" id="typingText"></span><span class="caret">|</span>
      </h1>
      <p class="hero-sub" id="heroSub"><?= e($settings['heroSub']) ?></p>
      <div class="hero-buttons">
        <a href="pages/portfolio.php" class="btn btn-primary">مشاهده نمونه‌کارها 🚀</a>
        <a href="pages/contact.php" class="btn btn-outline">گفتگو با ما</a>
      </div>

      <div class="hero-stats">
        <?php foreach ($pageData['defaults']['stats'] as $stat): ?>
        <div class="stat">
          <span class="stat-num" data-target="<?= $stat['target'] ?>">۰</span><span class="stat-plus">+</span>
          <span class="stat-label"><?= e($stat['label']) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="hero-visual">
      <div class="orbit orbit-outer">
        <div class="orbit orbit-inner">
          <div class="core">
            <span class="core-emoji">🎨</span>
          </div>
        </div>
      </div>
      <div class="float-card fc-1 glass">💡 <b>ایده</b></div>
      <div class="float-card fc-2 glass">🎯 <b>طراحی</b></div>
      <div class="float-card fc-3 glass">⚡ <b>توسعه</b></div>
      <div class="float-card fc-4 glass">🚀 <b>انتشار</b></div>
    </div>
  </section>

  <!-- ─── نوار متحرک ─── -->
  <div class="marquee" aria-hidden="true">
    <div class="marquee-track">
      <span>طراحی رابط کاربری</span><span class="mq-star">✦</span>
      <span>توسعه فرانت‌اند</span><span class="mq-star">✦</span>
      <span>هوش مصنوعی</span><span class="mq-star">✦</span>
      <span>برندینگ</span><span class="mq-star">✦</span>
      <span>سئو و رشد</span><span class="mq-star">✦</span>
      <span>اپلیکیشن موبایل</span><span class="mq-star">✦</span>
      <span>طراحی رابط کاربری</span><span class="mq-star">✦</span>
      <span>توسعه فرانت‌اند</span><span class="mq-star">✦</span>
      <span>هوش مصنوعی</span><span class="mq-star">✦</span>
      <span>برندینگ</span><span class="mq-star">✦</span>
      <span>سئو و رشد</span><span class="mq-star">✦</span>
      <span>اپلیکیشن موبایل</span><span class="mq-star">✦</span>
    </div>
  </div>

  <!-- ─── خدمات (پیش‌نمایش) ─── -->
  <section class="section services-preview">
    <div class="section-head reveal">
      <span class="section-tag">خدمات ما</span>
      <h2 class="section-title">برخی از خدمات ما</h2>
      <p class="section-desc">ما در نوآوا خدمات متنوعی را ارائه می‌دهیم تا کسب‌وکار شما را به سطح بعدی ببریم.</p>
    </div>

    <div class="cards-grid">
      <?php foreach (array_slice($pageData['defaults']['services'], 0, 3) as $service): ?>
      <div class="service-card glass tilt reveal">
        <div class="card-icon" style="--c1:<?= $service['colors']['c1'] ?>;--c2:<?= $service['colors']['c2'] ?>;"><?= $service['icon'] ?></div>
        <h3><?= e($service['title']) ?></h3>
        <p><?= e($service['desc']) ?></p>
        <a href="pages/services.php" class="card-link">ادامه ←</a>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="section-actions">
      <a href="pages/services.php" class="btn btn-outline">مشاهده همه خدمات</a>
    </div>
  </section>

  <!-- ─── نمونه‌کارها (پیش‌نمایش) ─── -->
  <section class="section portfolio-preview">
    <div class="section-head reveal">
      <span class="section-tag">نمونه‌کارها</span>
      <h2 class="section-title">برخی از نمونه‌کارهای ما</h2>
      <p class="section-desc">ما به کیفیت کارهایمان افتخار می‌کنیم. اینجا برخی از پروژه‌های اخیر ما را ببینید.</p>
    </div>

    <div class="portfolio-grid">
      <?php foreach (array_slice($pageData['defaults']['portfolio'], 0, 3) as $item): ?>
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

    <div class="section-actions">
      <a href="pages/portfolio.php" class="btn btn-outline">مشاهده همه نمونه‌کارها</a>
    </div>
  </section>

  <!-- ─── نظرات (پیش‌نمایش) ─── -->
  <section class="section testimonials-preview">
    <div class="section-head reveal">
      <span class="section-tag">نظرات مشتریان</span>
      <h2 class="section-title">آن‌ها درباره ما <span class="gradient-text">چه می‌گویند؟</span></h2>
    </div>

    <div class="slider reveal">
      <div class="slider-viewport">
        <div class="slider-track" id="sliderTrack">
          <?php foreach (array_slice($pageData['defaults']['testimonials'], 0, 2) as $testimonial): ?>
          <div class="slide glass">
            <div class="quote-mark">❝</div>
            <p class="slide-text"><?= e($testimonial['text']) ?></p>
            <div class="slide-user">
              <span class="avatar" style="--a1:<?= $testimonial['colors']['a1'] ?>;--a2:<?= $testimonial['colors']['a2'] ?>;"><?= mb_substr($testimonial['name'], 0, 1) ?></span>
              <div><b><?= e($testimonial['name']) ?></b><span><?= e($testimonial['role']) ?></span></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="slider-dots" id="sliderDots"></div>
      <button class="slide-arrow prev" id="slidePrev">→</button>
      <button class="slide-arrow next" id="slideNext">←</button>
    </div>
  </section>

<?php
// فوتر صفحه
require __DIR__ . '/pages/footer.php';
?>