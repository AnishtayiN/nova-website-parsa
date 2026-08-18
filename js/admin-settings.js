// ═══════════════════════════════════════════════
// نوآوا — مدیریت تنظیمات
// ═══════════════════════════════════════════════

// ذخیره تنظیمات محتوا
const saveContentBtn = document.getElementById('saveContent');
if (saveContentBtn) {
  saveContentBtn.addEventListener('click', async () => {
    const title = document.getElementById('cfTitle').value.trim();
    const heroSub = document.getElementById('cfHeroSub').value.trim();
    const words = document.getElementById('cfWords').value.trim();
    const address = document.getElementById('cfAddress').value.trim();
    const phone = document.getElementById('cfPhone').value.trim();
    const email = document.getElementById('cfEmail').value.trim();
    
    if (!title || !heroSub || !words || !address || !phone || !email) {
      toast('لطفاً همه فیلدها را پر کنید.', false);
      return;
    }
    
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      toast('ایمیل معتبر نیست.', false);
      return;
    }
    
    try {
      const result = await api('api.php?action=save_settings', {
        title,
        heroSub,
        words,
        address,
        phone,
        email
      });
      
      if (result.ok) {
        toast('تنظیمات با موفقیت ذخیره شد.');
      }
    } catch (err) {
      handleError(err);
    }
  });
}

// بازنشانی تنظیمات به پیش‌فرض
const resetContentBtn = document.getElementById('resetContent');
if (resetContentBtn) {
  resetContentBtn.addEventListener('click', async () => {
    if (!confirm('آیا از بازنشانی تنظیمات به پیش‌فرض اطمینان دارید؟')) return;
    
    try {
      const result = await api('api.php?action=reset_settings', {});
      if (result.ok) {
        toast('تنظیمات به پیش‌فرض بازنشانی شدند.');
        setTimeout(() => {
          window.location.reload();
        }, 1000);
      }
    } catch (err) {
      handleError(err);
    }
  });
}

// تغییر رمز عبور
const savePassBtn = document.getElementById('savePass');
if (savePassBtn) {
  savePassBtn.addEventListener('click', async () => {
    const current = document.getElementById('setPass0').value;
    const newPass = document.getElementById('setPass1').value;
    const newPass2 = document.getElementById('setPass2').value;
    
    if (!current || !newPass || !newPass2) {
      toast('لطفاً همه فیلدها را پر کنید.', false);
      return;
    }
    
    if (newPass.length < 8) {
      toast('رمز جدید باید حداقل ۸ کاراکتر باشد.', false);
      return;
    }
    
    if (newPass !== newPass2) {
      toast('تکرار رمز جدید یکسان نیست.', false);
      return;
    }
    
    try {
      const result = await api('api.php?action=change_password', {
        current,
        new: newPass,
        new2: newPass2
      });
      
      if (result.ok) {
        toast('رمز عبور با موفقیت تغییر کرد.');
        document.getElementById('setPass0').value = '';
        document.getElementById('setPass1').value = '';
        document.getElementById('setPass2').value = '';
        
        // مخفی کردن بنر تغییر رمز اجباری
        const mustChangeBanner = document.getElementById('mustChangeBanner');
        if (mustChangeBanner) {
          mustChangeBanner.style.display = 'none';
        }
      }
    } catch (err) {
      handleError(err);
    }
  });
}

// تغییر نام کاربری
const saveUserBtn = document.getElementById('saveUser');
if (saveUserBtn) {
  saveUserBtn.addEventListener('click', async () => {
    const newUser = document.getElementById('setUser').value.trim();
    
    if (!newUser) {
      toast('لطفاً نام کاربری جدید را وارد کنید.', false);
      return;
    }
    
    try {
      const result = await api('api.php?action=change_user', { new_user: newUser });
      if (result.ok) {
        toast('نام کاربری با موفقیت تغییر کرد.');
        document.querySelector('.topbar-user b').textContent = newUser;
      }
    } catch (err) {
      handleError(err);
    }
  });
}

// تغییر تم پیش‌فرض
const saveThemeBtn = document.getElementById('saveTheme');
if (saveThemeBtn) {
  saveThemeBtn.addEventListener('click', async () => {
    const theme = document.getElementById('setTheme').value;
    
    try {
      const result = await api('api.php?action=change_theme', { theme });
      if (result.ok) {
        toast('تم پیش‌فرض سایت اعمال شد.');
      }
    } catch (err) {
      handleError(err);
    }
  });
}

// حذف همه داده‌ها
const wipeDataBtn = document.getElementById('wipeData');
if (wipeDataBtn) {
  wipeDataBtn.addEventListener('click', async () => {
    const confirmWipe = document.getElementById('confirmWipe').value.trim();
    
    if (confirmWipe !== 'حذف') {
      toast('لطفاً برای تأیید، کلمه «حذف» را تایپ کنید.', false);
      return;
    }
    
    if (!confirm('⚠️ این عملیات همه داده‌ها را به صورت دائمی حذف می‌کند. آیا مطمئن هستید؟')) return;
    
    try {
      const result = await api('api.php?action=wipe', { confirm: confirmWipe });
      if (result.ok) {
        toast('همه داده‌ها پاک شدند. لطفاً دوباره وارد شوید.');
        setTimeout(() => {
          window.location.href = 'admin.php';
        }, 1000);
      }
    } catch (err) {
      handleError(err);
    }
  });
}