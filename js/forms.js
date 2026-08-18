// ═══════════════════════════════════════════════
// نوآوا — مدیریت فرم‌ها
// ═══════════════════════════════════════════════

// مدیریت فرم تماس
const contactForm = document.getElementById('contactForm');
if (contactForm) {
  contactForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const submitBtn = contactForm.querySelector('#submitBtn');
    const formSuccess = contactForm.querySelector('#formSuccess');
    const formData = {
      name: contactForm.querySelector('#name').value.trim(),
      email: contactForm.querySelector('#email').value.trim(),
      subject: contactForm.querySelector('#subject').value,
      message: contactForm.querySelector('#message').value.trim(),
      csrf: NOVA.csrf
    };
    
    // اعتبارسنجی سمت کلاینت
    if (!formData.name || !formData.email || !formData.message) {
      toast('لطفاً همه فیلدها را پر کنید.', false);
      return;
    }
    
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
      toast('ایمیل معتبر نیست.', false);
      return;
    }
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'در حال ارسال...';
    
    try {
      const response = await fetch('api.php?action=submit_message', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams(formData).toString()
      });
      
      const result = await response.json();
      if (result.ok) {
        contactForm.reset();
        formSuccess.style.display = 'block';
        toast('پیام شما با موفقیت ارسال شد.');
      } else {
        toast(result.error || 'خطایی در ارسال پیام رخ داد.', false);
      }
    } catch (err) {
      handleError(err);
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = 'ارسال پیام ✨';
    }
  });
}