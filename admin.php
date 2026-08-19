<?php
/**
 * ═══════════════════════════════════════════════
 *  نوآوا — پنل مدیریت
 *  - قبل از ورود: فقط فرم لاگین رندر می‌شود
 *  - بعد از ورود: پنل کامل با داده‌های سروری
 * ═══════════════════════════════════════════════
 */
require __DIR__ . '/config.php';

$isAuth = is_admin();
$settings = $isAuth ? get_settings() : settings_default();
$adminUser = (string)($_SESSION['admin_user'] ?? '');
$mustChange = !empty($_SESSION['must_change']);

function log_admin(string $message): void { log_error("[ADMIN] $message"); }

$adminData = ['csrf' => csrf_token(), 'auth' => $isAuth, 'user' => $adminUser, 'must_change' => $mustChange];
if ($isAuth) $adminData['settings'] = $settings;
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $isAuth ? 'پنل مدیریت' : 'ورود' ?> | نوآوا</title>
  <meta name="robots" content="noindex" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@100..900&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="admin.css" />
</head>
<body>
<?php if (!$isAuth): ?>
<div class="login-wrap" id="loginView">
  <div class="login-card" id="loginCard">
    <div class="login-logo">◈</div>
    <h1>پنل مدیریت <span class="grad">نوآوا</span></h1>
    <p class="login-sub">برای مدیریت سایت وارد شوید</p>
    <form id="loginForm" method="post" action="auth.php?action=login" novalidate>
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>" />
      <div class="field"><label for="loginUser">نام کاربری</label><input type="text" id="loginUser" name="username" placeholder="نام کاربری" autocomplete="username" required /></div>
      <div class="field"><label for="loginPass">رمز عبور</label><input type="password" id="loginPass" name="password" placeholder="رمز عبور" autocomplete="current-password" required /></div>
      <button type="submit" class="btn-login" id="loginBtn">ورود به پنل</button>
      <p class="login-error" id="loginError"></p>
    </form>
  </div>
  <p class="login-back"><a href="index.php">← بازگشت به سایت</a></p>
</div>
<?php else: ?>
<div class="admin-app" id="adminApp">
  <aside class="sidebar">
    <div class="side-logo">◈ <b>نوآوا</b> <span>پنل مدیریت</span></div>
    <nav class="side-nav">
      <button class="side-link active" data-view="dashboard"><span>📊</span> داشبورد</button>
      <button class="side-link" data-view="messages"><span>💬</span> پیام‌ها <em class="badge" id="msgBadge" hidden>۰</em></button>
      <button class="side-link" data-view="content"><span>📝</span> مدیریت محتوا</button>
      <button class="side-link" data-view="settings"><span>⚙️</span> تنظیمات</button>
    </nav>
    <div class="side-foot">
      <a class="side-link" href="index.php" target="_blank"><span>🌐</span> مشاهده سایت</a>
      <button class="side-link danger" id="logoutBtn"><span>🚪</span> خروج</button>
    </div>
  </aside>
  <main class="main">
    <header class="topbar">
      <div><h2 id="viewTitle">داشبورد</h2><p class="topbar-sub" id="viewSub">نمای کلی وضعیت سایت</p></div>
      <div class="topbar-actions">
        <button class="icon-btn" id="adminThemeToggle" title="تغییر تم">🌙</button>
        <span class="topbar-user">👤 <b><?= e($adminUser) ?></b></span>
      </div>
    </header>
    <?php if ($mustChange): ?>
    <div class="warn-banner" id="mustChangeBanner">
      ⚠️ شما با <b>رمز پیش‌فرض</b> وارد شده‌اید. برای امنیت، همین حالا رمز عبور را تغییر دهید.
      <button class="mini-btn" data-goto="settings" type="button">تغییر رمز ←</button>
    </div>
    <?php endif; ?>
    <section class="view active" id="view-dashboard">
      <div class="stats-grid">
        <div class="stat-card sc-1"><div class="stat-ico">👁️</div><div><span class="stat-big" id="stViews">۰</span><span class="stat-cap">بازدید کل</span></div></div>
        <div class="stat-card sc-2"><div class="stat-ico">📅</div><div><span class="stat-big" id="stToday">۰</span><span class="stat-cap">بازدید امروز</span></div></div>
        <div class="stat-card sc-3"><div class="stat-ico">💬</div><div><span class="stat-big" id="stMsgs">۰</span><span class="stat-cap">کل پیام‌ها</span></div></div>
        <div class="stat-card sc-4"><div class="stat-ico">🔴</div><div><span class="stat-big" id="stUnread">۰</span><span class="stat-cap">خوانده‌نشده</span></div></div>
      </div>
      <div class="panel"><h3>📈 بازدید ۷ روز اخیر</h3><div class="chart" id="chart7"></div></div>
      <div class="panel">
        <div class="panel-head"><h3>🆕 آخرین پیام‌ها</h3><button class="mini-btn" data-goto="messages" type="button">مشاهده همه ←</button></div>
        <div class="msg-list" id="recentList"></div>
      </div>
    </section>
    <section class="view" id="view-messages">
      <div class="toolbar">
        <input type="text" class="search" id="msgSearch" placeholder="🔍 جستجو در نام، ایمیل، موضوع یا متن..." />
        <div class="toolbar-btns">
          <button class="mini-btn" id="exportCsv" type="button">⬇️ خروجی CSV</button>
          <button class="mini-btn" id="exportJson" type="button">⬇️ خروجی JSON</button>
          <button class="mini-btn warn" id="clearRead" type="button">🧹 پاک‌کردن خوانده‌شده‌ها</button>
        </div>
      </div>
      <div class="msg-list" id="msgList"></div>
    </section>
    <section class="view" id="view-content">
      <div class="panel form-panel">
        <h3>🌐 اطلاعات عمومی سایت</h3>
        <div class="form-grid">
          <div class="field"><label for="cfTitle">عنوان سایت</label><input id="cfTitle" type="text" value="<?= e($settings['title'] ?? '') ?>" /></div>
          <div class="field"><label for="cfHeroSub">زیرعنوان بخش قهرمان</label><textarea id="cfHeroSub" rows="3"><?= e($settings['heroSub'] ?? '') ?></textarea></div>
          <div class="field full"><label for="cfWords">کلمات افکت تایپ (با ویرگول)</label><input id="cfWords" type="text" value="<?= e(implode(', ', $settings['words'] ?? [])) ?>" /></div>
        </div>
        <h3 style="margin-top:28px">📞 اطلاعات تماس</h3>
        <div class="form-grid">
          <div class="field"><label for="cfAddress">آدرس</label><input id="cfAddress" type="text" value="<?= e($settings['address'] ?? '') ?>" /></div>
          <div class="field"><label for="cfPhone">تلفن</label><input id="cfPhone" type="text" value="<?= e($settings['phone'] ?? '') ?>" /></div>
          <div class="field full"><label for="cfEmail">ایمیل</label><input id="cfEmail" type="email" value="<?= e($settings['email'] ?? '') ?>" /></div>
        </div>
        <div class="form-actions">
          <button class="btn-save" id="saveContent" type="button">💾 ذخیره تغییرات</button>
          <button class="btn-reset" id="resetContent" type="button">↩️ بازنشانی پیش‌فرض</button>
        </div>
        <p class="form-note" style="margin-top:12px">💡 تغییرات در دیتابیس JSON ذخیره می‌شوند.</p>
      </div>
    </section>
    <section class="view" id="view-settings">
      <div class="panel form-panel">
        <h3>🔐 تغییر رمز عبور</h3>
        <div class="form-grid">
          <div class="field"><label for="setPass0">رمز فعلی</label><input id="setPass0" type="password" /></div>
          <div class="field"><label for="setPass1">رمز جدید (حداقل ۸ حرف)</label><input id="setPass1" type="password" /></div>
          <div class="field full"><label for="setPass2">تکرار رمز جدید</label><input id="setPass2" type="password" /></div>
        </div>
        <div class="form-actions"><button class="btn-save" id="savePass" type="button">به‌روزرسانی رمز</button></div>
      </div>
      <div class="panel form-panel">
        <h3>👤 نام کاربری</h3>
        <div class="form-grid">
          <div class="field"><label for="setUser">نام کاربری جدید</label><input id="setUser" type="text" value="<?= e($adminUser) ?>" /></div>
        </div>
        <div class="form-actions"><button class="btn-save" id="saveUser" type="button">اعمال</button></div>
      </div>
      <div class="panel form-panel">
        <h3>🎨 تم پیش‌فرض سایت</h3>
        <div class="form-grid">
          <div class="field"><label for="setTheme">تم پیش‌فرض</label>
            <select id="setTheme">
              <option value="dark" <?= ($settings['theme'] ?? '') === 'dark' ? 'selected' : '' ?>>🌙 تیره</option>
              <option value="light" <?= ($settings['theme'] ?? '') === 'light' ? 'selected' : '' ?>>☀️ روشن</option>
            </select>
          </div>
        </div>
        <div class="form-actions"><button class="btn-save" id="saveTheme" type="button">اعمال</button></div>
      </div>
      <div class="panel form-panel danger-zone">
        <h3>⚠️ ناحیه خطر</h3>
        <p>با حذف داده‌ها، همه پیام‌ها، آمار بازدید و تنظیمات پاک می‌شوند. این کار <b>بازگشت‌ناپذیر</b> است.</p>
        <div class="form-grid">
          <div class="field"><label for="confirmWipe">برای تأیید، کلمه <b>حذف</b> را تایپ کنید</label><input id="confirmWipe" type="text" placeholder="حذف" /></div>
        </div>
        <div class="form-actions"><button class="btn-danger" id="wipeData" type="button">🗑️ حذف همه داده‌ها</button></div>
      </div>
    </section>
  </main>
</div>
<?php endif; ?>
<script type="application/json" id="nova-admin-data"><?= json_encode($adminData, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?></script>
<div class="toast" id="toast"></div>
<script type="module" src="admin.js"></script>
</body>
</html>
