// ═══════════════════════════════════════════════
// نوآوا — انیمیشن‌های اسکرول با GSAP ScrollTrigger
// ═══════════════════════════════════════════════

if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
  console.error('GSAP or ScrollTrigger is not loaded');
  throw new Error('GSAP and ScrollTrigger are required for scroll animations');
}

// ثبت پلاگین ScrollTrigger
gsap.registerPlugin(ScrollTrigger);

// انیمیشن‌های اسکرول برای بخش قهرمان
gsap.from('.hero-content', {
  opacity: 0,
  y: 50,
  duration: 1,
  ease: 'power3.out'
});

gsap.from('.hero-visual', {
  opacity: 0,
  x: 100,
  duration: 1,
  delay: 0.3,
  ease: 'power3.out'
});

// انیمیشن‌های اسکرول برای بخش خدمات
gsap.utils.toArray('.service-card').forEach((card, i) => {
  gsap.from(card, {
    scrollTrigger: {
      trigger: card,
      start: 'top 80%',
      toggleActions: 'play none none none'
    },
    opacity: 0,
    y: 50,
    duration: 0.8,
    delay: i * 0.2,
    ease: 'back.out(1.7)'
  });
});

// انیمیشن‌های اسکرول برای بخش نمونه‌کارها
gsap.utils.toArray('.portfolio-item').forEach((item, i) => {
  gsap.from(item, {
    scrollTrigger: {
      trigger: item,
      start: 'top 80%',
      toggleActions: 'play none none none'
    },
    opacity: 0,
    scale: 0.8,
    duration: 0.8,
    delay: i * 0.1,
    ease: 'back.out(1.7)'
  });
});

// انیمیشن‌های اسکرول برای بخش نظرات
gsap.from('.slider', {
  scrollTrigger: {
    trigger: '.testimonials',
    start: 'top 80%',
    toggleActions: 'play none none none'
  },
  opacity: 0,
  y: 50,
  duration: 1,
  ease: 'power3.out'
});

// انیمیشن‌های اسکرول برای بخش تماس
gsap.from('.contact-form', {
  scrollTrigger: {
    trigger: '.contact',
    start: 'top 80%',
    toggleActions: 'play none none none'
  },
  opacity: 0,
  y: 50,
  duration: 1,
  ease: 'power3.out'
});

// انیمیشن نوار ناوبری
ScrollTrigger.create({
  trigger: 'body',
  start: 'top -50px',
  onEnter: () => {
    document.querySelector('.navbar').classList.add('scrolled');
  },
  onLeaveBack: () => {
    document.querySelector('.navbar').classList.remove('scrolled');
  }
});