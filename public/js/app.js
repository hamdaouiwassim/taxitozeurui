// ==================== MOBILE MENU (Landing) ====================
const menuToggle = document.getElementById('menuToggle');
const menuIcon = document.getElementById('menuIcon');
const navLinks = document.querySelector('.nav-links');
const navActions = document.querySelector('.nav-actions');

if (menuToggle && navLinks) {
  menuToggle.addEventListener('click', () => {
    const isOpen = navLinks.classList.contains('active');
    navLinks.classList.toggle('active');
    if (navActions) navActions.classList.toggle('active');
    menuIcon.className = isOpen ? 'fas fa-bars' : 'fas fa-times';
    document.body.style.overflow = isOpen ? '' : 'hidden';
  });
}

// ==================== SCROLL REVEAL ====================
const revealElements = document.querySelectorAll('.reveal');

function checkReveals() {
  revealElements.forEach(el => {
    const position = el.getBoundingClientRect().top;
    const windowHeight = window.innerHeight;
    if (position < windowHeight - 50) {
      const delay = el.dataset.delay || 0;
      el.style.transitionDelay = `${delay * 0.15}s`;
      el.classList.add('active');
    }
  });
}

if (revealElements.length) {
  window.addEventListener('scroll', checkReveals);
  window.addEventListener('load', checkReveals);
  checkReveals();
}

// ==================== DRIVER FILTER (Landing) ====================
const driverSearch = document.getElementById('driverSearch');
const typeFilter = document.getElementById('typeFilter');
const driverCards = document.querySelectorAll('.available-grid .driver-card');

function filterDrivers() {
  const search = driverSearch ? driverSearch.value.toLowerCase() : '';
  const type = typeFilter ? typeFilter.value : 'all';

  driverCards.forEach(card => {
    const text = card.textContent.toLowerCase();
    const cardType = (card.dataset.type || '').toLowerCase();
    const matchSearch = text.includes(search);
    const matchType = type === 'all' || cardType === type;
    card.style.display = matchSearch && matchType ? '' : 'none';
  });
}

if (driverSearch) driverSearch.addEventListener('input', filterDrivers);
if (typeFilter) typeFilter.addEventListener('change', filterDrivers);

// ==================== SIDEBAR TOGGLE ====================
const menuToggleDash = document.getElementById('menuToggleDash');
const sidebar = document.getElementById('sidebar');
const sidebarClose = document.getElementById('sidebarClose');
const sidebarOverlay = document.getElementById('sidebarOverlay');

if (menuToggleDash && sidebar) {
  menuToggleDash.addEventListener('click', () => {
    sidebar.classList.toggle('open');
    if (sidebarOverlay) sidebarOverlay.classList.toggle('active');
  });
}

if (sidebarClose && sidebar) {
  sidebarClose.addEventListener('click', () => {
    sidebar.classList.remove('open');
    if (sidebarOverlay) sidebarOverlay.classList.remove('active');
  });
}

if (sidebarOverlay) {
  sidebarOverlay.addEventListener('click', () => {
    sidebar.classList.remove('open');
    sidebarOverlay.classList.remove('active');
  });
}

// ==================== USER DROPDOWN ====================
const userMenuToggle = document.getElementById('userMenuToggle');
const userMenu = document.querySelector('.user-menu');
const logoutBtn = document.getElementById('logoutBtn');

if (logoutBtn) {
  logoutBtn.addEventListener('click', (e) => {
    e.preventDefault();
    const form = document.getElementById('logoutForm');
    if (form) form.submit();
  });
}

if (userMenuToggle) {
  userMenuToggle.addEventListener('click', (e) => {
    e.stopPropagation();
    userMenu.classList.toggle('open');
  });

  document.addEventListener('click', (e) => {
    if (userMenu && !userMenu.contains(e.target)) {
      userMenu.classList.remove('open');
    }
  });
}

// ==================== MODALS ====================
document.querySelectorAll('[data-modal-open]').forEach(btn => {
  btn.addEventListener('click', () => {
    const modal = document.getElementById(btn.dataset.modalOpen);
    if (modal) modal.classList.add('active');
  });
});

document.querySelectorAll('[data-modal-close]').forEach(btn => {
  btn.addEventListener('click', () => {
    const modal = btn.closest('.modal');
    if (modal) modal.classList.remove('active');
  });
});

document.querySelectorAll('.modal').forEach(modal => {
  modal.addEventListener('click', (e) => {
    if (e.target === modal) modal.classList.remove('active');
  });
});

// ==================== DELETE CONFIRMATION ====================
document.querySelectorAll('form[data-confirm]').forEach(form => {
  form.addEventListener('submit', (e) => {
    if (!confirm(form.dataset.confirm)) {
      e.preventDefault();
    }
  });
});

// ==================== REVIEW STAR RATING (Driver Page) ====================
const starRating = document.getElementById('starRating');
let selectedRating = 0;

function updateStarDisplay() {
  if (!starRating) return;
  starRating.querySelectorAll('i').forEach((s, i) => {
    s.className = i < selectedRating ? 'fas fa-star' : 'far fa-star';
  });
}

if (starRating) {
  starRating.addEventListener('click', (e) => {
    const star = e.target.closest('[data-rating]');
    if (star) {
      selectedRating = parseInt(star.dataset.rating);
      updateStarDisplay();
      const hidden = document.querySelector('[name="rating"]');
      if (hidden) hidden.value = selectedRating;
    }
  });

  starRating.addEventListener('mouseover', (e) => {
    const star = e.target.closest('[data-rating]');
    if (star) {
      const hoverRating = parseInt(star.dataset.rating);
      starRating.querySelectorAll('i').forEach((s, i) => {
        s.className = i < hoverRating ? 'fas fa-star' : 'far fa-star';
      });
    }
  });

  starRating.addEventListener('mouseleave', updateStarDisplay);
}

// ==================== TOAST ====================
function showToast(message, type = 'success') {
  const existing = document.querySelector('.toast-notification');
  if (existing) existing.remove();

  const toast = document.createElement('div');
  toast.className = 'toast-notification';
  toast.style.cssText = `
    position: fixed;
    bottom: 24px;
    right: 24px;
    background: ${type === 'error' ? '#dc2626' : '#16a34a'};
    color: #fff;
    padding: 14px 24px;
    border-radius: 10px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    font-weight: 600;
    font-size: 0.9rem;
    z-index: 3000;
    animation: toastIn 0.3s ease;
  `;
  toast.textContent = message;
  document.body.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transition = 'opacity 0.3s';
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

// Display Laravel flash messages
const flashSuccess = document.getElementById('flashSuccess');
const flashError = document.getElementById('flashError');
if (flashSuccess && flashSuccess.dataset.message) showToast(flashSuccess.dataset.message);
if (flashError && flashError.dataset.message) showToast(flashError.dataset.message, 'error');
