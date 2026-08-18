<?php
// تنظیمات صفحه
$currentPage = 'about';
$pageTitle = 'درباره ما | نوآوا';

require __DIR__ . '/../config.php';

$settings = get_settings();

// داده‌های امن برای جاوااسکریپت
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

<!-- ─── درباره ما ─── -->
<section class="section about" id="about">
  <div class="about-grid">
    <div class="about-visual reveal">
      <div class="about-frame glass">
        <div class="about-emoji">👩‍💻</div>
        <h3>تیم خلاق نوآوا</h3>
        <p>«ما عاشق ساختن چیزهایی هستیم که مردم عاشق‌شان می‌شوند.»</p>
      </div>
      <div class="badge-float">🏆 <b>برگزیده جشنواره وب ۲۰۲۴</b></div>
    </div>

    <div class="about-content reveal">
      <span class="section-tag">درباره ما</span>
      <h2 class="section-title">ما فقط کد نمی‌زنیم، <span class="gradient-text">تجربه می‌سازیم</span></h2>
      <p>
        نوآوا از سال ۱۳۹۵ با یک هدف ساده شروع شد: ساخت محصولات دیجیتالی که هم زیبا باشند،
        هم کار کنند. امروز با تیمی از طراحان، توسعه‌دهندگان و استراتژیست‌ها،
        روی پروژه‌هایی کار می‌کنیم که مرز طراحی و فناوری را جابه‌جا می‌کنند.
      </p>
      <div class="skills">
        <?php foreach ($pageData['defaults']['skills'] as $skill): ?>
        <div class="skill">
          <div class="skill-head"><span><?= e($skill['name']) ?></span><span class="skill-val" data-target="<?= $skill['value'] ?>">۰</span><span>٪</span></div>
          <div class="skill-bar"><div class="skill-fill" style="--w:<?= $skill['value'] ?>%"></div></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- فرآیند کار -->
<section class="section process" id="process">
  <div class="section-head reveal">
    <span class="section-tag">فرآیند کار</span>
    <h2 class="section-title">مسیر پروژه‌ات از ایده تا <span class="gradient-text">میر</span></h2>
    <p class="section-desc">چهار گام شفاف، بدون پیچیدگی.</p>
  </div>

  <div class="process-steps">
    <div class="process-step reveal">
      <span class="step-num">۰۱</span>
      <div class="step-icon glass">💡</div>
      <h3>کشف و ایده‌پردازی</h3>
      <p>گفتگو با شما، تحلیل رقبا و ترسیم نقشه‌راه پروژه.</p>
    </div>
    <div class="process-step reveal">
      <span class="step-num">۰۲</span>
      <div class="step-icon glass">🎨</div>
      <h3>طراحی تجربه</h3>
      <p>وایرفریم، پروتوتایپ و رابط کاربری خیره‌کننده.</p>
    </div>
    <div class="process-step reveal">
      <span class="step-num">۰۳</span>
      <div class="step-icon glass">⚡</div>
      <h3>توسعه و پیاده‌سازی</h3>
      <p>کدنویسی تمیز، تست دقیق و بهینه‌سازی عملکرد.</p>
    </div>
    <div class="process-step reveal">
      <span class="step-num">۰۴</span>
      <div class="step-icon glass">🚀</div>
      <h3>انتشار و رشد</h3>
      <p>رونمایی، پایش عملکرد و بهبود مستمر.</p>
    </div>
  </div>
</section>

<!-- تیم ما -->
<section class="section team">
  <div class="section-head reveal">
    <span class="section-tag">تیم ما</span>
    <h2 class="section-title">ما که هستیم؟</h2>
    <p class="section-desc">تیمی از خلاقان و متخصصان دیجیتال.</p>
  </div>

  <div class="team-grid">
    <div class="team-card glass reveal">
      <div class="team-avatar" style="--a1:#8b5cf6;--a2:#6366f1">س</div>
      <h3>سارا محمدی</h3>
      <p>مدیر خلاقیت و طراح ارشد</p>
    </div>
    <div class="team-card glass reveal">
      <div class="team-avatar" style="--a1:#06b6d4;--a2:#3b82f6">ع</div>
      <h3>علی رضایی</h3>
      <p>توسعه‌دهنده فول‌استک</p>
    </div>
    <div class="team-card glass reveal">
      <div class="team-avatar" style="--a1:#f59e0b;--a2:#ef4444">ن</div>
      <h3>نگار کریمی</h3>
      <p>متخصص سئو و دیجیتال مارکتینگ</p>
    </div>
  </div>
</section>

<?php
// فوتر صفحه
require __DIR__ . '/footer.php';
?>