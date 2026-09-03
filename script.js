// ==================== LANDING PAGE ====================
const menuToggle = document.getElementById('menuToggle');
const navLinks = document.querySelector('.nav-links');
const navActions = document.querySelector('.nav-actions');

if (menuToggle) {
  const menuIcon = document.getElementById('menuIcon');

  function closeMenu() {
    navLinks.classList.remove('active');
    navActions.classList.remove('active');
    if (menuIcon) {
      menuIcon.classList.remove('fa-times');
      menuIcon.classList.add('fa-bars');
    }
  }

  menuToggle.addEventListener('click', () => {
    const isOpen = navLinks.classList.contains('active');
    if (isOpen) {
      closeMenu();
    } else {
      navLinks.classList.add('active');
      navActions.classList.add('active');
      if (menuIcon) {
        menuIcon.classList.remove('fa-bars');
        menuIcon.classList.add('fa-times');
      }
    }
  });

  // Close menu when clicking outside
  document.addEventListener('click', (e) => {
    if (!menuToggle.contains(e.target) && !navLinks.contains(e.target) && !navActions.contains(e.target)) {
      closeMenu();
    }
  });

  // Close menu when clicking on a link
  navLinks.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', closeMenu);
  });

  // Reset menu state when resizing to desktop
  window.addEventListener('resize', () => {
    if (window.innerWidth > 768 && navLinks.classList.contains('active')) {
      closeMenu();
    }
  });
}

// Smooth scroll for nav links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    const href = this.getAttribute('href');
    if (href === '#') return;
    e.preventDefault();
    const target = document.querySelector(href);
    if (target) {
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
});

// Scroll reveal animations
const revealElements = document.querySelectorAll('.reveal');

function checkReveals() {
  const windowHeight = window.innerHeight;
  revealElements.forEach(el => {
    const rect = el.getBoundingClientRect();
    if (rect.top < windowHeight - 80) {
      el.classList.add('active');
    }
  });
}

if (revealElements.length) {
  window.addEventListener('scroll', checkReveals);
  window.addEventListener('load', checkReveals);
  checkReveals();
}

// ==================== LOGIN ====================
const loginForm = document.getElementById('loginForm');
const loginError = document.getElementById('loginError');
const togglePass = document.getElementById('togglePass');
const loginPassword = document.getElementById('loginPassword');

if (togglePass && loginPassword) {
  togglePass.addEventListener('click', () => {
    const isPassword = loginPassword.type === 'password';
    loginPassword.type = isPassword ? 'text' : 'password';
    togglePass.innerHTML = `<i class="fas fa-eye${isPassword ? '-slash' : ''}"></i>`;
  });
}

if (loginForm) {
  loginForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const email = document.getElementById('loginEmail').value.trim();
    const password = loginPassword.value.trim();

    if (email === 'admin@taxigo.com' && password === 'admin123') {
      localStorage.setItem('taxiAdminAuth', 'true');
      window.location.href = 'dashboard.html';
    } else {
      loginError.classList.add('visible');
      setTimeout(() => loginError.classList.remove('visible'), 3000);
    }
  });
}

// Dashboard auth guard
if (window.location.pathname.includes('dashboard.html')) {
  if (!localStorage.getItem('taxiAdminAuth')) {
    window.location.href = 'login.html';
  }
}

// Available taxis search & filter
const driverSearch = document.getElementById('driverSearch');
const typeFilter = document.getElementById('typeFilter');
const driverCards = document.querySelectorAll('.available-grid .driver-card');

function filterDrivers() {
  const search = driverSearch ? driverSearch.value.toLowerCase() : '';
  const type = typeFilter ? typeFilter.value : 'all';

  driverCards.forEach(card => {
    const text = card.textContent.toLowerCase();
    const matchSearch = text.includes(search);
    const matchType = type === 'all' || card.textContent.toLowerCase().includes(type);
    card.style.display = matchSearch && matchType ? '' : 'none';
  });
}

if (driverSearch) driverSearch.addEventListener('input', filterDrivers);
if (typeFilter) typeFilter.addEventListener('change', filterDrivers);

// ==================== DASHBOARD ====================
// Tab Navigation
const sidebarLinks = document.querySelectorAll('.sidebar-menu a[data-tab]');
const dashboardContents = document.querySelectorAll('.dashboard-content');

function switchTab(tabName) {
  dashboardContents.forEach(content => content.classList.remove('active'));
  sidebarLinks.forEach(link => link.classList.remove('active'));

  const tab = document.getElementById(`tab-${tabName}`);
  const link = document.querySelector(`.sidebar-menu a[data-tab="${tabName}"]`);

  if (tab) tab.classList.add('active');
  if (link) link.classList.add('active');

  // Close sidebar on mobile
  const sidebar = document.getElementById('sidebar');
  const overlay = document.querySelector('.sidebar-overlay');
  if (sidebar) sidebar.classList.remove('open');
  if (overlay) overlay.classList.remove('active');
}

sidebarLinks.forEach(link => {
  link.addEventListener('click', (e) => {
    e.preventDefault();
    switchTab(link.dataset.tab);
  });
});

// View all links in cards
document.querySelectorAll('.view-all[data-tab]').forEach(link => {
  link.addEventListener('click', (e) => {
    e.preventDefault();
    switchTab(link.dataset.tab);
  });
});

// Mobile Sidebar Toggle
const menuToggleDash = document.getElementById('menuToggleDash');
const sidebar = document.getElementById('sidebar');
const sidebarClose = document.getElementById('sidebarClose');

// Create overlay element
const overlay = document.createElement('div');
overlay.className = 'sidebar-overlay';
document.body.appendChild(overlay);

if (menuToggleDash) {
  menuToggleDash.addEventListener('click', () => {
    sidebar.classList.toggle('open');
    overlay.classList.toggle('active');
  });
}

if (sidebarClose) {
  sidebarClose.addEventListener('click', () => {
    sidebar.classList.remove('open');
    overlay.classList.remove('active');
  });
}

overlay.addEventListener('click', () => {
  sidebar.classList.remove('open');
  overlay.classList.remove('active');
});

// Add Taxi Modal
const addTaxiBtn = document.getElementById('addTaxiBtn');
const taxiModal = document.getElementById('taxiModal');
const taxiModalClose = document.getElementById('taxiModalClose');
const taxiModalCancel = document.getElementById('taxiModalCancel');
const taxiForm = document.getElementById('taxiForm');

if (addTaxiBtn) {
  addTaxiBtn.addEventListener('click', () => {
    taxiModal.classList.add('active');
  });
}

if (taxiModalClose) {
  taxiModalClose.addEventListener('click', () => {
    taxiModal.classList.remove('active');
  });
}

if (taxiModalCancel) {
  taxiModalCancel.addEventListener('click', () => {
    taxiModal.classList.remove('active');
  });
}

if (taxiModal) {
  taxiModal.addEventListener('click', (e) => {
    if (e.target === taxiModal) {
      taxiModal.classList.remove('active');
    }
  });
}

if (taxiForm) {
  taxiForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const inputs = taxiForm.querySelectorAll('input, select');
    const values = Array.from(inputs).map(i => i.value);

    const newId = `T${String(document.querySelectorAll('#taxiTableBody tr').length + 1).padStart(3, '0')}`;
    const row = document.createElement('tr');
    row.innerHTML = `
      <td>${newId}</td>
      <td>${values[0]}</td>
      <td>${values[1]}</td>
      <td>${values[2]}</td>
      <td>${values[3]}</td>
      <td><span class="status ${values[4].toLowerCase()}">${values[4]}</span></td>
      <td class="actions">
        <button class="action-btn edit" title="Edit"><i class="fas fa-edit"></i></button>
        <button class="action-btn delete" title="Delete"><i class="fas fa-trash"></i></button>
      </td>
    `;

    document.getElementById('taxiTableBody').appendChild(row);
    taxiModal.classList.remove('active');
    taxiForm.reset();
    attachDeleteListeners();
  });
}

// Delete Taxi
function attachDeleteListeners() {
  document.querySelectorAll('.action-btn.delete').forEach(btn => {
    btn.onclick = function () {
      if (confirm('Are you sure you want to delete this?')) {
        this.closest('tr').remove();
      }
    };
  });
}
attachDeleteListeners();

// Taxi Search & Filter
const taxiSearch = document.getElementById('taxiSearch');
const taxiFilter = document.getElementById('taxiFilter');

function filterTaxis() {
  const search = taxiSearch ? taxiSearch.value.toLowerCase() : '';
  const filter = taxiFilter ? taxiFilter.value : 'all';
  const rows = document.querySelectorAll('#taxiTableBody tr');

  rows.forEach(row => {
    const text = row.textContent.toLowerCase();
    const status = row.querySelector('.status').textContent.toLowerCase();
    const matchSearch = text.includes(search);
    const matchFilter = filter === 'all' || status === filter;
    row.style.display = matchSearch && matchFilter ? '' : 'none';
  });
}

if (taxiSearch) taxiSearch.addEventListener('input', filterTaxis);
if (taxiFilter) taxiFilter.addEventListener('change', filterTaxis);

// Booking Search & Filter
const bookingSearch = document.getElementById('bookingSearch');
const bookingFilter = document.getElementById('bookingFilter');

function filterBookings() {
  const search = bookingSearch ? bookingSearch.value.toLowerCase() : '';
  const filter = bookingFilter ? bookingFilter.value : 'all';
  const rows = document.querySelectorAll('#bookingTableBody tr');

  rows.forEach(row => {
    const text = row.textContent.toLowerCase();
    const status = row.querySelector('.status').textContent.toLowerCase();
    const matchSearch = text.includes(search);
    const matchFilter = filter === 'all' || status === filter;
    row.style.display = matchSearch && matchFilter ? '' : 'none';
  });
}

if (bookingSearch) bookingSearch.addEventListener('input', filterBookings);
if (bookingFilter) bookingFilter.addEventListener('change', filterBookings);

// Settings Forms
document.querySelectorAll('.settings-form').forEach(form => {
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    showToast('Settings saved successfully!');
  });
});

// Toast Notification
function showToast(message) {
  const toast = document.createElement('div');
  toast.style.cssText = `
    position: fixed;
    bottom: 24px;
    right: 24px;
    background: #16a34a;
    color: white;
    padding: 14px 24px;
    border-radius: 10px;
    font-weight: 600;
    z-index: 3000;
    animation: slideIn 0.3s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  `;
  toast.textContent = message;
  document.body.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(10px)';
    toast.style.transition = 'all 0.3s ease';
    setTimeout(() => toast.remove(), 300);
  }, 2500);
}

// Add Driver Modal (placeholder)
const addDriverBtn = document.getElementById('addDriverBtn');
if (addDriverBtn) {
  addDriverBtn.addEventListener('click', () => {
    showToast('Driver registration form coming soon!');
  });
}

// User dropdown toggle
const userMenuToggle = document.getElementById('userMenuToggle');
const userMenu = document.querySelector('.user-menu');

if (userMenuToggle) {
  userMenuToggle.addEventListener('click', (e) => {
    e.stopPropagation();
    userMenu.classList.toggle('open');
  });

  document.addEventListener('click', (e) => {
    if (!userMenu.contains(e.target)) {
      userMenu.classList.remove('open');
    }
  });
}

// Logout
const logoutBtn = document.getElementById('logoutBtn');
if (logoutBtn) {
  logoutBtn.addEventListener('click', (e) => {
    e.preventDefault();
    localStorage.removeItem('taxiAdminAuth');
    window.location.href = 'login.html';
  });
}

// Navbar scroll effect
window.addEventListener('scroll', () => {
  const navbar = document.querySelector('.navbar');
  if (navbar) {
    if (window.scrollY > 50) {
      navbar.style.boxShadow = '0 2px 20px rgba(0,0,0,0.3)';
    } else {
      navbar.style.boxShadow = 'none';
    }
  }
});
