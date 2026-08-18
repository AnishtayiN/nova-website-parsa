// ═══════════════════════════════════════════════
// نوآوا — مدیریت لاگین و احراز هویت
// ═══════════════════════════════════════════════

const loginForm = document.getElementById('loginForm');
if (!loginForm) throw new Error('Login form not found');

loginForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  
  const username = document.getElementById('loginUser').value.trim();
  const password = document.getElementById('loginPass').value;
  const loginBtn = document.getElementById('loginBtn');
  const loginError = document.getElementById('loginError');
  
  if (!username || !password) {
    toast('لطفاً نام کاربری و رمز عبور را وارد کنید.', false);
    return;
  }
  
  loginBtn.disabled = true;
  loginBtn.textContent = 'در حال ورود...';
  loginError.textContent = '';
  
  try {
    const result = await api('auth.php?action=login', {
      username,
      password
    });
    
    if (result.ok) {
      window.location.reload();
    } else {
      loginError.textContent = result.error || 'خطایی در ورود رخ داد.';
      toast(result.error || 'خطایی در ورود رخ داد.', false);
    }
  } catch (err) {
    handleError(err);
  } finally {
    loginBtn.disabled = false;
    loginBtn.textContent = 'ورود به پنل';
  }
});