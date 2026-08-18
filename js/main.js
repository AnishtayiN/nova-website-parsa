// ═══════════════════════════════════════════════
// نوآوا — مدیریت داده‌های سرور و توابع کمکی
// ═══════════════════════════════════════════════

// داده‌های سرور (PHP): کلمات تایپ، csrf، تم پیش‌فرض
const NOVA = (function () {
  const el = document.getElementById('nova-data');
  if (!el) return {};
  try {
    return JSON.parse(el.textContent);
  } catch (err) {
    console.error('Failed to parse NOVA data:', err);
    return {};
  }
})();

// توابع کمکی
const $ = (s) => document.querySelector(s);
const $$ = (s) => Array.from(document.querySelectorAll(s));

// تبدیل اعداد به فرمت فارسی
const FA = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
const faNum = (n) => String(n).replace(/\d/g, (d) => FA[d]);

// مدیریت خطا
const handleError = (err) => {
  console.error('Error:', err);
  toast('خطایی رخ داد. لطفاً دوباره تلاش کنید.', false);
};

// نمایش توست
let toastTimer = null;
const toast = (msg, ok = true) => {
  const t = $('#toast');
  if (!t) return;
  t.textContent = (ok ? '✅ ' : '⚠️ ') + msg;
  t.className = 'toast show ' + (ok ? 'ok' : 'err');
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => {
    t.className = 'toast';
  }, 3000);
};

// بارگذاری کتابخانه‌های خارجی
function loadScript(src) {
  return new Promise((resolve, reject) => {
    const script = document.createElement('script');
    script.src = src;
    script.onload = resolve;
    script.onerror = reject;
    document.head.appendChild(script);
  });
}

// بارگذاری ماژول‌ها
document.addEventListener('DOMContentLoaded', async () => {
  // فعال‌سازی انیمیشن‌های اسکرول (حذف گارد no-js)
  document.documentElement.classList.replace('no-js', 'js');
  
  try {
    // بارگذاری GSAP و ScrollTrigger
    await loadScript('js/gsap.min.js');
    await loadScript('js/ScrollTrigger.min.js');
    // بارگذاری Three.js
    await loadScript('js/three.min.js');
    // بارگذاری ماژول‌های داخلی
    await import('./scroll-animations.js');
    await import('./hero-3d.js');
    await import('./particles.js');
    await import('./typing.js');
    await import('./navigation.js');
    await import('./theme.js');
    await import('./animations.js');
    await import('./forms.js');
  } catch (err) {
    handleError(err);
  }
});