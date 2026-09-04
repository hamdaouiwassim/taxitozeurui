<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Dashboard - Tozeur VIP Taxi')</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="/css/app.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Tajawal:wght@200;300;400;500;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="dashboard-body">

  <!-- Sidebar -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <a href="{{ route('home') }}" class="logo">
        <i class="fas fa-taxi"></i> Tozeur VIP Taxi
      </a>
      <button class="sidebar-close" id="sidebarClose">
        <i class="fas fa-times"></i>
      </button>
    </div>
    <ul class="sidebar-menu">
      <li><a href="{{ route('dashboard.index') }}" class="{{ request()->routeIs('dashboard.index') ? 'active' : '' }}"><i class="fas fa-tachometer-alt"></i> Overview</a></li>
      <li><a href="{{ route('dashboard.taxis') }}" class="{{ request()->routeIs('dashboard.taxis') ? 'active' : '' }}"><i class="fas fa-car"></i> Manage Taxis</a></li>
      <li><a href="{{ route('dashboard.drivers') }}" class="{{ request()->routeIs('dashboard.drivers') ? 'active' : '' }}"><i class="fas fa-user"></i> Drivers</a></li>
      <li><a href="{{ route('dashboard.reviews') }}" class="{{ request()->routeIs('dashboard.reviews') ? 'active' : '' }}"><i class="fas fa-star"></i> Reviews</a></li>
      <li><a href="{{ route('dashboard.settings') }}" class="{{ request()->routeIs('dashboard.settings') ? 'active' : '' }}"><i class="fas fa-cog"></i> Settings</a></li>
    </ul>
    <div class="sidebar-footer">
      <a href="{{ route('home') }}"><i class="fas fa-arrow-left"></i> Back to Site</a>
    </div>
  </aside>

  <!-- Main Content -->
  <div class="dashboard-main">
    <header class="dashboard-header">
      <button class="menu-toggle-dash" id="menuToggleDash">
        <i class="fas fa-bars"></i>
      </button>
      <div class="header-search">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search...">
      </div>
      <div class="header-actions">
        <div class="user-menu">
          <button class="user-profile" id="userMenuToggle">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=F59E0B&color=fff&rounded=true" alt="Admin">
            <span>{{ Auth::user()->name }}</span>
            <i class="fas fa-chevron-down"></i>
          </button>
          <div class="user-dropdown" id="userDropdown">
            <form method="POST" action="{{ route('logout') }}" id="logoutForm">
              @csrf
            </form>
            <a href="{{ route('dashboard.settings') }}"><i class="fas fa-cog"></i> Settings</a>
            <div class="dropdown-divider"></div>
            <a href="#" id="logoutBtn"><i class="fas fa-sign-out-alt"></i> Logout</a>
          </div>
        </div>
      </div>
    </header>

    @yield('content')
  </div>

  @include('partials.flash')

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <script src="/js/app.js"></script>
  @stack('scripts')
</body>
</html>
