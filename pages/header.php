<!DOCTYPE html>
<html lang="fa" dir="rtl" class="no-js">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= e($pageTitle ?? 'نوآوا | استودیو خلاقیت دیجیتال') ?></title>
  <meta name="description" content="نوآوا — استودیو طراحی و توسعه دیجیتال." />

  <!-- فونت وزیرمتن -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@100..900&display=swap" rel="stylesheet" />

  <!-- داده‌های سرور (برای جاوااسکریپت؛ خارج از اجرای CSP) -->
  <?php if (isset($pageData)): ?>
  <script type="application/json" id="nova-data"><?= json_encode($pageData, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?></script>
  <?php endif; ?>

  <!-- استایل‌ها -->
  <link rel="stylesheet" href="../style.css" />
</head>
<body>

  <!-- ─── لودینگ اولیه ─── -->
  <div class="preloader" id="preloader">
    <div class="preloader-inner">
      <div class="preloader-logo">◈</div>
      <div class="preloader-bar"><div class="preloader-bar-fill" id="preloaderFill"></div></div>
      <div class="preloader-pct" id="preloaderPct">۰٪</div>
    </div>
  </div>

  <!-- ─── نشانگر سفارشی ─── -->
  <div class="cursor-dot" id="cursorDot"></div>
  <div class="cursor-ring" id="cursorRing"></div>

  <!-- ─── نوار پیشرفت اسکرول ─── -->
  <div class="scroll-progress" id="scrollProgress"></div>

  <!-- ─── کانواس کانفتی ─── -->
  <canvas id="confetti"></canvas>

  <!-- ─── پس‌زمینه ذرات ─── -->
  <canvas id="particles"></canvas>
  <div class="blob blob-1"></div>
  <div class="blob blob-2"></div>
  <div class="blob blob-3"></div>

  <!-- ─── نوار ناوبری ─── -->
  <header class="navbar" id="navbar">
    <nav class="nav-inner">
      <a href="../index.php" class="logo">
        <span class="logo-icon">◈</span>
        نوآوا
      </a>

      <ul class="nav-links" id="navLinks">
        <li><a href="../index.php" class="nav-link <?= ($currentPage === 'home') ? 'active' : '' ?>">خانه</a></li>
        <li><a href="services.php" class="nav-link <?= ($currentPage === 'services') ? 'active' : '' ?>">خدمات</a></li>
        <li><a href="about.php" class="nav-link <?= ($currentPage === 'about') ? 'active' : '' ?>">درباره ما</a></li>
        <li><a href="portfolio.php" class="nav-link <?= ($currentPage === 'portfolio') ? 'active' : '' ?>">نمونه‌کارها</a></li>
        <li><a href="contact.php" class="nav-link <?= ($currentPage === 'contact') ? 'active' : '' ?>">تماس</a></li>
      </ul>

      <div class="nav-actions">
        <button class="theme-toggle" id="themeToggle" aria-label="تغییر حالت شب و روز">
          <span class="sun">☀️</span>
          <span class="moon">🌙</span>
        </button>
        <a href="contact.php" class="btn btn-glow nav-cta">شروع پروژه</a>
        <button class="hamburger" id="hamburger" aria-label="منو">
          <span></span><span></span><span></span>
        </button>
      </div>
    </nav>
  </header>