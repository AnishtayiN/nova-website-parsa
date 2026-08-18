// ═══════════════════════════════════════════════
// نوآوا — انیمیشن‌ها و افکت‌های بصری
// ═══════════════════════════════════════════════

// انیمیشن اعداد شمارنده
function animateCounter() {
  const counters = document.querySelectorAll('[data-target]');
  counters.forEach(counter => {
    const target = +counter.getAttribute('data-target');
    const increment = target / 100;
    let current = 0;
    
    const updateCounter = () => {
      current += increment;
      if (current < target) {
        counter.textContent = faNum(Math.floor(current));
        requestAnimationFrame(updateCounter);
      } else {
        counter.textContent = faNum(target);
      }
    };
    
    // شروع انیمیشن وقتی که عنصر در دید کاربر قرار گرفت
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          updateCounter();
          observer.unobserve(entry.target);
        }
      });
    });
    observer.observe(counter);
  });
}

// انیمیشن‌های اسکرول (Reveal)
function initScrollAnimations() {
  const reveals = document.querySelectorAll('.reveal');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('active');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });
  
  reveals.forEach(reveal => {
    observer.observe(reveal);
  });
}

// مدیریت نشانگر سفارشی
function initCustomCursor() {
  const cursorDot = document.getElementById('cursorDot');
  const cursorRing = document.getElementById('cursorRing');
  
  if (!cursorDot || !cursorRing) return;
  
  document.addEventListener('mousemove', (e) => {
    cursorDot.style.transform = `translate(${e.clientX}px, ${e.clientY}px)`;
    cursorRing.style.transform = `translate(${e.clientX}px, ${e.clientY}px)`;
  });
  
  document.querySelectorAll('a, button, .tilt').forEach(el => {
    el.addEventListener('mouseenter', () => {
      cursorRing.style.transform = 'scale(1.5)';
    });
    el.addEventListener('mouseleave', () => {
      cursorRing.style.transform = 'scale(1)';
    });
  });
}

// مدیریت کانفتی
function initConfetti() {
  const confettiCanvas = document.getElementById('confetti');
  if (!confettiCanvas) return;
  
  const ctx = confettiCanvas.getContext('2d');
  confettiCanvas.width = window.innerWidth;
  confettiCanvas.height = window.innerHeight;
  
  const confettiPieces = [];
  const confettiColors = ['#8b5cf6', '#06b6d4', '#ec4899', '#10b981', '#f59e0b'];
  
  function createConfetti() {
    for (let i = 0; i < 100; i++) {
      confettiPieces.push({
        x: Math.random() * confettiCanvas.width,
        y: Math.random() * confettiCanvas.height - confettiCanvas.height,
        size: Math.random() * 8 + 4,
        color: confettiColors[Math.floor(Math.random() * confettiColors.length)],
        velocity: {
          x: (Math.random() - 0.5) * 5,
          y: Math.random() * 5 + 2
        },
        rotation: Math.random() * 360,
        rotationSpeed: (Math.random() - 0.5) * 10
      });
    }
  }
  
  function drawConfetti() {
    ctx.clearRect(0, 0, confettiCanvas.width, confettiCanvas.height);
    confettiPieces.forEach((piece, index) => {
      ctx.save();
      ctx.translate(piece.x, piece.y);
      ctx.rotate(piece.rotation * Math.PI / 180);
      ctx.fillStyle = piece.color;
      ctx.fillRect(-piece.size / 2, -piece.size / 2, piece.size, piece.size);
      ctx.restore();
      
      piece.x += piece.velocity.x;
      piece.y += piece.velocity.y;
      piece.rotation += piece.rotationSpeed;
      
      // حذف قطعاتی که از صفحه خارج شدند
      if (piece.y > confettiCanvas.height) {
        confettiPieces.splice(index, 1);
      }
    });
    
    if (confettiPieces.length > 0) {
      requestAnimationFrame(drawConfetti);
    }
  }
  
  // ایجاد کانفتی در صفحه اصلی
  if (window.location.pathname.endsWith('index.php')) {
    createConfetti();
    drawConfetti();
  }
}

// مدیریت لودینگ اولیه
function initPreloader() {
  const preloader = document.getElementById('preloader');
  const preloaderFill = document.getElementById('preloaderFill');
  const preloaderPct = document.getElementById('preloaderPct');
  
  if (!preloader || !preloaderFill || !preloaderPct) return;
  
  let progress = 0;
  const interval = setInterval(() => {
    progress += Math.random() * 10;
    if (progress >= 100) {
      progress = 100;
      clearInterval(interval);
      setTimeout(() => {
        preloader.style.opacity = '0';
        setTimeout(() => {
          preloader.style.display = 'none';
        }, 500);
      }, 300);
    }
    preloaderFill.style.width = `${progress}%`;
    preloaderPct.textContent = faNum(Math.floor(progress)) + '٪';
  }, 100);
}

// اجرای توابع
animateCounter();
initScrollAnimations();
initCustomCursor();
initConfetti();
initPreloader();