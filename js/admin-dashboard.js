// ═══════════════════════════════════════════════
// نوآوا — مدیریت داشبورد
// ═══════════════════════════════════════════════

// مدیریت ناوبری بین بخش‌ها
const sideLinks = document.querySelectorAll('.side-link');
const views = document.querySelectorAll('.view');

sideLinks.forEach(link => {
  link.addEventListener('click', () => {
    const viewId = link.getAttribute('data-view');
    if (!viewId) return;
    
    // بروزرسانی کلاس فعال
    sideLinks.forEach(l => l.classList.remove('active'));
    link.classList.add('active');
    
    // نمایش بخش مورد نظر
    views.forEach(view => {
      view.classList.remove('active');
      if (view.id === `view-${viewId}`) {
        view.classList.add('active');
        document.getElementById('viewTitle').textContent = view.querySelector('h3')?.textContent || viewId;
        document.getElementById('viewSub').textContent = view.querySelector('p')?.textContent || '';
      }
    });
  });
});

// مدیریت دکمه‌های مینی
const miniBtns = document.querySelectorAll('.mini-btn[data-goto]');
miniBtns.forEach(btn => {
  btn.addEventListener('click', () => {
    const goto = btn.getAttribute('data-goto');
    const targetLink = document.querySelector(`.side-link[data-view="${goto}"]`);
    if (targetLink) {
      targetLink.click();
    }
  });
});

// بارگذاری آمار داشبورد
async function loadDashboardStats() {
  try {
    const result = await api('api.php?action=stats', {});
    if (result.ok) {
      const data = result.data;
      document.getElementById('stViews').textContent = faNum(data.views_total);
      document.getElementById('stToday').textContent = faNum(data.views_today);
      document.getElementById('stMsgs').textContent = faNum(data.msgs_total);
      document.getElementById('stUnread').textContent = faNum(data.msgs_unread);
      
      // بروزرسانی نشانگر پیام‌های خوانده‌نشده
      const msgBadge = document.getElementById('msgBadge');
      if (msgBadge) {
        msgBadge.textContent = data.msgs_unread;
        msgBadge.hidden = data.msgs_unread === 0;
      }
      
      // رسم نمودار بازدید ۷ روز اخیر
      drawChart(data.last7);
      
      // بارگذاری پیام‌های اخیر
      loadRecentMessages(data.msgs_unread);
    }
  } catch (err) {
    handleError(err);
  }
}

// رسم نمودار بازدید ۷ روز اخیر
function drawChart(data) {
  const ctx = document.getElementById('chart7');
  if (!ctx) return;
  
  const maxCount = Math.max(...data.map(d => d.count), 1);
  const days = data.map(d => d.date.split('-')[2]);
  const counts = data.map(d => d.count);
  
  ctx.innerHTML = '';
  const barWidth = 30;
  const gap = 10;
  const maxHeight = 150;
  
  data.forEach((day, i) => {
    const barHeight = (day.count / maxCount) * maxHeight;
    const bar = document.createElement('div');
    bar.className = 'chart-bar';
    bar.style.height = `${barHeight}px`;
    bar.style.left = `${i * (barWidth + gap)}px`;
    bar.innerHTML = `
      <div class="chart-bar-value">${faNum(day.count)}</div>
      <div class="chart-bar-label">${days[i]}</div>
    `;
    ctx.appendChild(bar);
  });
}

// بارگذاری پیام‌های اخیر
async function loadRecentMessages(unreadCount) {
  try {
    const result = await api('api.php?action=list_messages', {});
    if (result.ok) {
      const messages = result.data.slice(0, 5);
      const recentList = document.getElementById('recentList');
      if (!recentList) return;
      
      recentList.innerHTML = '';
      messages.forEach(msg => {
        const msgEl = document.createElement('div');
        msgEl.className = `msg-item ${msg.read ? '' : 'unread'}`;
        msgEl.innerHTML = `
          <div class="msg-header">
            <span class="msg-name">${escapeHtml(msg.name)}</span>
            <span class="msg-date">${faDate(msg.date)}</span>
          </div>
          <div class="msg-subject">${escapeHtml(msg.subject)}</div>
          <div class="msg-preview">${escapeHtml(msg.message.substring(0, 50))}${msg.message.length > 50 ? '...' : ''}</div>
        `;
        recentList.appendChild(msgEl);
      });
    }
  } catch (err) {
    handleError(err);
  }
}

// مدیریت دکمه خروج
const logoutBtn = document.getElementById('logoutBtn');
if (logoutBtn) {
  logoutBtn.addEventListener('click', async () => {
    try {
      const result = await api('auth.php?action=logout', {});
      if (result.ok) {
        window.location.reload();
      }
    } catch (err) {
      handleError(err);
    }
  });
}

// بارگذاری اولیه داشبورد
loadDashboardStats();