// ═══════════════════════════════════════════════
// نوآوا — پس‌زمینه ذرات متحرک
// ═══════════════════════════════════════════════

const canvas = document.getElementById('particles');
if (!canvas) throw new Error('Canvas element not found');

const ctx = canvas.getContext('2d');
let particles = [];
const PARTICLE_COUNT = window.innerWidth < 768 ? 40 : 80;

function resizeCanvas() {
  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight;
}

resizeCanvas();
window.addEventListener('resize', () => {
  resizeCanvas();
  initParticles();
});

function initParticles() {
  particles = [];
  for (let i = 0; i < PARTICLE_COUNT; i++) {
    particles.push({
      x: Math.random() * canvas.width,
      y: Math.random() * canvas.height,
      r: Math.random() * 2.5 + 0.5,
      vx: (Math.random() - 0.5) * 0.6,
      vy: (Math.random() - 0.5) * 0.6,
      color: ['139,92,246', '6,182,212', '236,72,153', '99,102,241'][Math.floor(Math.random() * 4)],
      alpha: Math.random() * 0.6 + 0.2,
      pulse: Math.random() * Math.PI * 2
    });
  }
}

function drawParticles() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  particles.forEach(p => {
    ctx.beginPath();
    ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
    ctx.fillStyle = `rgba(${p.color}, ${p.alpha})`;
    ctx.fill();
    
    // حرکت ذرات
    p.x += p.vx;
    p.y += p.vy;
    
    // برخورد با لبه‌ها
    if (p.x < 0 || p.x > canvas.width) p.vx *= -1;
    if (p.y < 0 || p.y > canvas.height) p.vy *= -1;
    
    // افکت پالس
    p.pulse += 0.02;
    p.alpha = 0.2 + Math.sin(p.pulse) * 0.1;
  });
  requestAnimationFrame(drawParticles);
}

initParticles();
drawParticles();