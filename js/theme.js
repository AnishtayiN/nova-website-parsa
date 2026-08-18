// ═══════════════════════════════════════════════
// نوآوا — مدیریت تم (تاریک/روشن)
// ═══════════════════════════════════════════════

const themeToggle = document.getElementById('themeToggle');
if (!themeToggle) throw new Error('Theme toggle element not found');

// تنظیم تم اولیه از داده‌های سرور یا localStorage
const savedTheme = localStorage.getItem('theme') || NOVA.theme || 'dark';
document.documentElement.setAttribute('data-theme', savedTheme);

// تغییر تم
function toggleTheme() {
  const currentTheme = document.documentElement.getAttribute('data-theme');
  const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
  document.documentElement.setAttribute('data-theme', newTheme);
  localStorage.setItem('theme', newTheme);
}

themeToggle.addEventListener('click', toggleTheme);