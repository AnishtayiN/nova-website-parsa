// ═══════════════════════════════════════════════
// نوآوا — مدیریت ناوبری و منوها
// ═══════════════════════════════════════════════

// مدیریت منوی همبرگری
const hamburger = document.getElementById('hamburger');
const navLinks = document.getElementById('navLinks');

if (hamburger && navLinks) {
  hamburger.addEventListener('click', () => {
    navLinks.classList.toggle('active');
    hamburger.classList.toggle('active');
  });
}

// مدیریت ناوبری
const navLinksItems = document.querySelectorAll('.nav-link');
navLinksItems.forEach(link => {
  link.addEventListener('click', (e) => {
    // اگر لینک به بخش‌های داخلی صفحه است
    if (link.getAttribute('href').startsWith('#')) {
      e.preventDefault();
      const targetId = link.getAttribute('href');
      const targetSection = document.querySelector(targetId);
      if (targetSection) {
        targetSection.scrollIntoView({
          behavior: 'smooth'
        });
      }
    }
    // بستن منوی موبایل
    if (navLinks.classList.contains('active')) {
      navLinks.classList.remove('active');
      hamburger.classList.remove('active');
    }
  });
});

// تغییر کلاس فعال بر اساس صفحه جاری
document.addEventListener('DOMContentLoaded', () => {
  const currentPage = document.body.getAttribute('data-page');
  if (currentPage) {
    navLinksItems.forEach(link => {
      link.classList.remove('active');
      if (link.getAttribute('href') === `${currentPage}.php`) {
        link.classList.add('active');
      }
    });
  }
});

// تغییر کلاس فعال بر اساس اسکرول (فقط برای صفحه اصلی)
if (window.location.pathname.endsWith('index.php')) {
  window.addEventListener('scroll', () => {
    const scrollPosition = window.scrollY;
    const sections = document.querySelectorAll('section');
    
    sections.forEach(section => {
      const sectionTop = section.offsetTop - 100;
      const sectionHeight = section.offsetHeight;
      const sectionId = section.getAttribute('id');
      
      if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
        navLinksItems.forEach(link => {
          link.classList.remove('active');
          if (link.getAttribute('href') === `#${sectionId}`) {
            link.classList.add('active');
          }
        });
      }
    });
  });
}

// مدیریت دکمه بازگشت به بالا
const backToTop = document.getElementById('backToTop');
if (backToTop) {
  window.addEventListener('scroll', () => {
    if (window.scrollY > 300) {
      backToTop.style.opacity = '1';
    } else {
      backToTop.style.opacity = '0';
    }
  });
  
  backToTop.addEventListener('click', () => {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });
}

// مدیریت نوار پیشرفت اسکرول
const scrollProgress = document.getElementById('scrollProgress');
if (scrollProgress) {
  window.addEventListener('scroll', () => {
    const scrollTop = document.documentElement.scrollTop;
    const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const scrollPercent = (scrollTop / scrollHeight) * 100;
    scrollProgress.style.width = `${scrollPercent}%`;
  });
}