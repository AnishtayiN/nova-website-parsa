// ═══════════════════════════════════════════════
// نوآوا — افکت تایپ
// ═══════════════════════════════════════════════

const typingTarget = document.getElementById('typingText');
if (!typingTarget) throw new Error('Typing target element not found');

const words = NOVA.words || ['طراحی می‌کنیم', 'کد می‌زنیم', 'برند می‌سازیم', 'رویا می‌بافیم'];
let wordIndex = 0;
let charIndex = 0;
let isDeleting = false;

function type() {
  const currentWord = words[wordIndex];
  if (isDeleting) {
    typingTarget.textContent = currentWord.substring(0, charIndex - 1);
    charIndex--;
  } else {
    typingTarget.textContent = currentWord.substring(0, charIndex + 1);
    charIndex++;
  }

  if (!isDeleting && charIndex === currentWord.length) {
    setTimeout(() => {
      isDeleting = true;
    }, 2000);
  } else if (isDeleting && charIndex === 0) {
    isDeleting = false;
    wordIndex = (wordIndex + 1) % words.length;
  }

  const typingSpeed = isDeleting ? 100 : 200;
  setTimeout(type, typingSpeed);
}

type();