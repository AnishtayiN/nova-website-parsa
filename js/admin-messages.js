// ═══════════════════════════════════════════════
// نوآوا — مدیریت پیام‌ها
// ═══════════════════════════════════════════════

// بارگذاری لیست پیام‌ها
async function loadMessages() {
  try {
    const result = await api('api.php?action=list_messages', {});
    if (result.ok) {
      renderMessages(result.data);
    }
  } catch (err) {
    handleError(err);
  }
}

// رندر لیست پیام‌ها
function renderMessages(messages) {
  const msgList = document.getElementById('msgList');
  if (!msgList) return;
  
  msgList.innerHTML = '';
  messages.forEach(msg => {
    const msgEl = document.createElement('div');
    msgEl.className = `msg-item ${msg.read ? '' : 'unread'}`;
    msgEl.innerHTML = `
      <div class="msg-header">
        <span class="msg-name">${escapeHtml(msg.name)}</span>
        <span class="msg-date">${faDate(msg.date)}</span>
        <span class="msg-email">${escapeHtml(msg.email)}</span>
      </div>
      <div class="msg-subject">${escapeHtml(msg.subject)}</div>
      <div class="msg-text">${escapeHtml(msg.message)}</div>
      <div class="msg-actions">
        <button class="mini-btn" data-action="toggle-read" data-id="${msg.id}">
          ${msg.read ? 'علامت‌گذاری به عنوان خوانده‌نشده' : 'علامت‌گذاری به عنوان خوانده‌شده'}
        </button>
        <button class="mini-btn warn" data-action="delete" data-id="${msg.id}">حذف</button>
      </div>
    `;
    msgList.appendChild(msgEl);
  });
  
  // مدیریت رویدادهای دکمه‌ها
  msgList.querySelectorAll('[data-action]').forEach(btn => {
    btn.addEventListener('click', async () => {
      const action = btn.getAttribute('data-action');
      const id = btn.getAttribute('data-id');
      
      if (action === 'toggle-read') {
        await toggleRead(id, btn);
      } else if (action === 'delete') {
        await deleteMessage(id);
      }
    });
  });
}

// تغییر وضعیت خوانده‌شده/خوانده‌نشده
async function toggleRead(id, btn) {
  try {
    const result = await api('api.php?action=set_read', {
      id,
      read: btn.textContent.includes('خوانده‌نشده') ? '0' : '1'
    });
    
    if (result.ok) {
      toast('وضعیت پیام به‌روزرسانی شد.');
      loadMessages();
      loadDashboardStats(); // بروزرسانی آمار داشبورد
    }
  } catch (err) {
    handleError(err);
  }
}

// حذف پیام
async function deleteMessage(id) {
  if (!confirm('آیا از حذف این پیام اطمینان دارید؟')) return;
  
  try {
    const result = await api('api.php?action=delete_message', { id });
    if (result.ok) {
      toast('پیام با موفقیت حذف شد.');
      loadMessages();
      loadDashboardStats(); // بروزرسانی آمار داشبورد
    }
  } catch (err) {
    handleError(err);
  }
}

// جستجو در پیام‌ها
const msgSearch = document.getElementById('msgSearch');
if (msgSearch) {
  msgSearch.addEventListener('input', () => {
    const searchTerm = msgSearch.value.toLowerCase();
    const msgItems = document.querySelectorAll('.msg-item');
    
    msgItems.forEach(item => {
      const text = item.textContent.toLowerCase();
      item.style.display = text.includes(searchTerm) ? '' : 'none';
    });
  });
}

// پاک‌کردن پیام‌های خوانده‌شده
const clearReadBtn = document.getElementById('clearRead');
if (clearReadBtn) {
  clearReadBtn.addEventListener('click', async () => {
    if (!confirm('آیا از پاک‌کردن همه پیام‌های خوانده‌شده اطمینان دارید؟')) return;
    
    try {
      const result = await api('api.php?action=clear_read', {});
      if (result.ok) {
        toast('پیام‌های خوانده‌شده پاک شدند.');
        loadMessages();
        loadDashboardStats(); // بروزرسانی آمار داشبورد
      }
    } catch (err) {
      handleError(err);
    }
  });
}

// خروجی CSV
const exportCsvBtn = document.getElementById('exportCsv');
if (exportCsvBtn) {
  exportCsvBtn.addEventListener('click', async () => {
    try {
      const result = await api('api.php?action=list_messages', {});
      if (result.ok) {
        const messages = result.data;
        const headers = ['نام', 'ایمیل', 'موضوع', 'پیام', 'تاریخ', 'وضعیت'];
        const rows = messages.map(msg => [
          msg.name,
          msg.email,
          msg.subject,
          msg.message,
          msg.date,
          msg.read ? 'خوانده‌شده' : 'خوانده‌نشده'
        ]);
        
        const csvContent = [
          headers.join(','),
          ...rows.map(row => row.map(field => `"${field.replace(/\"/g, '\"')}"`).join(','))
        ].join('\n');
        
        const blob = new Blob([`\uFEFF${csvContent}`], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'messages.csv';
        link.click();
        URL.revokeObjectURL(url);
      }
    } catch (err) {
      handleError(err);
    }
  });
}

// خروجی JSON
const exportJsonBtn = document.getElementById('exportJson');
if (exportJsonBtn) {
  exportJsonBtn.addEventListener('click', async () => {
    try {
      const result = await api('api.php?action=list_messages', {});
      if (result.ok) {
        const blob = new Blob([JSON.stringify(result.data, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'messages.json';
        link.click();
        URL.revokeObjectURL(url);
      }
    } catch (err) {
      handleError(err);
    }
  });
}

// بارگذاری اولیه پیام‌ها
if (document.getElementById('view-messages')) {
  loadMessages();
}