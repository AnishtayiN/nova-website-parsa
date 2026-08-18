// ═══════════════════════════════════════════════
// نوآوا — مدیریت داده‌های سرور و توابع کمکی پنل مدیریت
// ═══════════════════════════════════════════════

// داده‌های سرور (PHP)
const ADMIN = (function () {
  const el = document.getElementById('nova-admin-data');
  if (!el) return {};
  try {
    return JSON.parse(el.textContent);
  } catch (e) {
    console.error('Failed to parse ADMIN data:', e);
    return {};
  }
})();

// توابع کمکی
const $ = (s) => document.querySelector(s);
const $$ = (s) => Array.from(document.querySelectorAll(s));
const FA = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
const faNum = (n) => String(n).replace(/\d/g, (d) => FA[d]);
const faDate = (iso) => {
  const d = new Date(iso);
  if (isNaN(d.getTime())) return String(iso);
  return (
    faNum(d.toLocaleDateString('fa-IR')) + ' — ' +
    faNum(d.toLocaleTimeString('fa-IR', { hour: '2-digit', minute: '2-digit' }))
  );
};
const escapeHtml = (s) => String(s).replace(/[&<>"]/g, (c) => ({
  '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;'
}[c]));

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

// فراخوانی امن API
const api = async (url, data) => {
  const fd = new FormData();
  fd.append('csrf', ADMIN.csrf || '');
  Object.keys(data).forEach((k) => fd.append(k, data[k]));
  
  try {
    const res = await fetch(url, {
      method: 'POST',
      body: fd,
      credentials: 'same-origin'
    });
    
    let out = {};
    try {
      out = await res.json();
    } catch (e) {
      // ignore
    }
    
    if (!res.ok && !out.ok) {
      out = {
        ok: false,
        error: out.error || 'خطای سرور (کد ' + res.status + ')'
      };
    }
    return out;
  } catch (err) {
    handleError(err);
    return { ok: false, error: 'خطای شبکه' };
  }
};

// بارگذاری ماژول‌ها
document.addEventListener('DOMContentLoaded', () => {
  if (!ADMIN.auth) {
    import('./admin-auth.js').catch(handleError);
  } else {
    import('./admin-dashboard.js').catch(handleError);
    import('./admin-messages.js').catch(handleError);
    import('./admin-settings.js').catch(handleError);
    import('./admin-theme.js').catch(handleError);
  }
});