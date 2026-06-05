/* ============================================================
   LUXE Store Dashboard — app.js (DB version, minimal)
   ============================================================ */

// ── SIDEBAR TOGGLE ──
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
}

// Close sidebar on outside click (mobile)
document.addEventListener('click', function (e) {
  const sidebar = document.getElementById('sidebar');
  const toggle = document.querySelector('.menu-toggle');
  if (
    window.innerWidth <= 900 &&
    sidebar.classList.contains('open') &&
    !sidebar.contains(e.target) &&
    toggle && !toggle.contains(e.target)
  ) {
    sidebar.classList.remove('open');
  }
});

// ── CLEAR ADD FORM ──
function clearAddForm() {
  const fields = ['add-name', 'add-price', 'bar-code'];
  fields.forEach(id => {
    const el = document.getElementById(id);
    if (el) el.value = '';
  });
}

// ── TOAST ──
let toastTimer;
function showToast(msg, type = 'info') {
  const t = document.getElementById('toast');
  if (!t) return;
  t.textContent = msg;
  t.className = 'toast show ' + type;
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => { t.classList.remove('show'); }, 3000);
}

// ── SHOW TOAST ON PAGE LOAD IF SESSION MESSAGE EXISTS ──
document.addEventListener('DOMContentLoaded', function () {
  const flash = document.getElementById('flash-msg');
  if (flash) {
    showToast(flash.dataset.msg, flash.dataset.type || 'success');
  }
});