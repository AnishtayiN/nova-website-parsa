// ═══════════════════════════════════════════════
// نوآوا — مدیریت تم پنل مدیریت
// ═══════════════════════════════════════════════

const adminThemeToggle = document.getElementById('adminThemeToggle');
if (!adminThemeToggle) throw new Error('Admin theme toggle element not found');

// تنظیم تم اولیه از localStorage
const savedTheme = localStorage.getItem('admin-theme') || 'dark';
document.documentElement.setAttribute('data-theme', savedTheme);

// تغییر تم
function toggleAdminTheme() {
  const currentTheme = document.documentElement.getAttribute('data-theme');
  const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
  document.documentElement.setAttribute('data-theme', newTheme);
  localStorage.setItem('admin-theme', newTheme);
}

adminThemeToggle.addEventListener('click', toggleAdminTheme);