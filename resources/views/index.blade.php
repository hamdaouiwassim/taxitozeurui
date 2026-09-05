@extends('layouts.app')

@section('title', __('Tozeur VIP Taxi - Premium Service'))

@push('schema')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "TaxiService",
  "name": "Tozeur VIP Taxi",
  "description": "{{ __('Private taxi service in Tozeur, Tunisia with verified drivers, airport transfers, VIP rides and 24/7 availability.') }}",
  "url": "{{ url('/') }}",
  "image": "{{ url('/asstes/images/og-cover.png') }}",
  "priceRange": "$$",
  "telephone": "+21600000000",
  "areaServed": {
    "@@type": "City",
    "name": "Tozeur",
    "address": {
      "@@type": "PostalAddress",
      "addressLocality": "Tozeur",
      "addressCountry": "TN"
    }
  },
  "address": {
    "@@type": "PostalAddress",
    "addressLocality": "Tozeur",
    "addressCountry": "TN"
  },
  "openingHoursSpecification": {
    "@@type": "OpeningHoursSpecification",
    "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"],
    "opens": "00:00",
    "closes": "23:59"
  }
}
</script>
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "WebSite",
  "name": "Tozeur VIP Taxi",
  "url": "{{ url('/') }}",
  "potentialAction": {
    "@@type": "SearchAction",
    "target": {
      "@@type": "EntryPoint",
      "urlTemplate": "{{ url('/') }}/#available?q={search_term_string}"
    },
    "query-input": "required name=search_term_string"
  }
}
</script>
@endpush

@section('content')
  <!-- Navbar -->
  <nav class="navbar">
    <div class="container nav-container">
      <a href="{{ lroute('home') }}" class="logo">
        <i class="fas fa-taxi"></i>
        <span class="logo-text">Tozeur VIP Taxi <small>@lang('Premium Service')</small></span>
      </a>
      <ul class="nav-links">
        <li><a href="#home">@lang('Home')</a></li>
        <li><a href="#services">@lang('Our Services')</a></li>
        <li><a href="#about">@lang('About Us')</a></li>
        <li><a href="#contact">@lang('Contact Us')</a></li>
      </ul>
      <div class="nav-actions">
        <a href="#available" class="btn btn-primary">@lang('Book a Taxi')</a>
        @include('partials.lang-switcher')
      </div>
      <button class="menu-toggle" id="menuToggle">
        <i class="fas fa-bars" id="menuIcon"></i>
      </button>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero" id="home">
    <div class="hero-overlay"></div>
    <div class="container hero-content">
      <h1>@lang('Your ride starts') <span class="highlight">@lang('here')</span> <i class="fas fa-map-marker-alt map-pin-icon"></i></h1>
      <p>@lang('Book the nearest taxi in your city and travel comfortably, safely and quickly.')</p>
      <a href="#available" class="btn btn-primary btn-lg">@lang('View Available Taxis') <i class="fas fa-taxi hero-btn-icon"></i></a>
    </div>
  </section>

  <!-- Available Taxis Section -->
  <section class="available" id="available">
    <div class="container">
      <h2 class="section-title reveal">@lang('Available Taxis')</h2>
      <p class="section-subtitle reveal" data-delay="1">@lang('Find your perfect driver and start chatting instantly')</p>
      <div class="available-filters reveal" data-delay="2">
        <div class="filter-search">
          <i class="fas fa-search"></i>
          <input type="text" id="driverSearch" placeholder="@lang('Search by driver or vehicle...')">
        </div>
        <select id="typeFilter">
          <option value="all">@lang('All Types')</option>
          <option value="economy">@lang('Economy')</option>
          <option value="comfort">@lang('Comfort')</option>
          <option value="luxury">@lang('Luxury')</option>
        </select>
      </div>
      <div class="available-grid">
        @forelse ($drivers as $index => $driver)
          <div class="driver-card reveal" data-type="{{ $driver->taxi?->type }}" data-delay="{{ $index }}">
            <img src="{{ $driver->taxi?->image_url ?: '/asstes/images/47f7a327-5fa8-47db-a183-d4f127272c07.png' }}" alt="{{ $driver->taxi?->name }}" class="vehicle-image">
            <div class="card-divider"></div>
            <div class="card-body">
              <div class="driver-avatar">
                <img src="{{ $driver->avatar_url }}" alt="{{ $driver->name }}">
              </div>
              <h3>{{ $driver->name }}</h3>
              <div class="driver-rating">
                <i class="fas fa-star"></i>
                @if ($driver->reviews_count > 0)
                  <span>{{ number_format($driver->rating, 1) }}</span>
                  <small>({{ $driver->reviews_count }} @lang('reviews'))</small>
                @else
                  <span>@lang('No reviews yet')</span>
                @endif
              </div>
              <div class="driver-location">
                <i class="fas fa-map-marker-alt"></i> {{ $driver->location }}
              </div>
              @if ($driver->working_hours)
                <div class="driver-hours">
                  <i class="far fa-clock"></i> {{ $driver->working_hours }}
                  @if (!$driver->isWorkingNow())
                    <span class="hours-status hours-closed">@lang('Closed now')</span>
                  @else
                    <span class="hours-status hours-open">@lang('Open now')</span>
                  @endif
                </div>
              @endif
              <a href="https://wa.me/{{ preg_replace('/\D/', '', $driver->whatsapp) }}?text=Hi%20{{ urlencode($driver->name) }}%2C%20I%27d%20like%20to%20book%20your%20taxi" target="_blank" class="btn btn-success btn-block">
                <i class="fab fa-whatsapp"></i> @lang('Chat on WhatsApp')
              </a>
              <a href="{{ lroute('drivers.show', ['driver' => $driver]) }}" class="btn btn-outline btn-block btn-profile-link">
                <i class="fas fa-user"></i> @lang('View Profile')
              </a>
            </div>
            <span class="driver-type">@lang(ucfirst($driver->taxi?->type))</span>
          </div>
        @empty
          <p class="empty-state">@lang('No drivers available right now.')</p>
        @endforelse
      </div>
    </div>
  </section>

  <!-- Services Section -->
  <section class="services" id="services">
    <div class="container">
      <h2 class="section-title reveal">@lang('Our Services')</h2>
      <p class="section-subtitle reveal" data-delay="1">@lang('Choose the ride that suits your needs')</p>
      <div class="services-grid">
        <div class="service-card reveal">
          <div class="service-icon"><i class="fas fa-briefcase"></i></div>
          <h3>@lang('Business Class')</h3>
          <p>@lang('Premium vehicles for corporate travel with professional drivers.')</p>
        </div>
        <div class="service-card reveal" data-delay="1">
          <div class="service-icon"><i class="fas fa-users"></i></div>
          <h3>@lang('Shared Ride')</h3>
          <p>@lang('Save money by sharing your ride with other passengers heading the same way.')</p>
        </div>
        <div class="service-card reveal" data-delay="2">
          <div class="service-icon"><i class="fas fa-plane"></i></div>
          <h3>@lang('Airport Transfer')</h3>
          <p>@lang('Reliable airport pickups and drop-offs with flight tracking.')</p>
        </div>
        <div class="service-card reveal" data-delay="3">
          <div class="service-icon"><i class="fas fa-clock"></i></div>
          <h3>@lang('Schedule Ride')</h3>
          <p>@lang('Book your ride in advance and travel stress-free.')</p>
        </div>
      </div>
    </div>
  </section>

  <!-- About Section -->
  <section class="about" id="about">
    <div class="container about-container">
      <div class="about-image reveal">
        <i class="fas fa-road"></i>
      </div>
      <div class="about-content reveal" data-delay="1">
        <h2>@lang('Why Choose Tozeur VIP Taxi?')</h2>
        <p>@lang('We are committed to providing the best ride experience with safety, comfort, and affordability as our core values.')</p>
        <ul class="about-list">
          <li><i class="fas fa-check-circle"></i> @lang('24/7 Availability - Rides anytime, anywhere')</li>
          <li><i class="fas fa-check-circle"></i> @lang('Verified Drivers - All drivers undergo background checks')</li>
          <li><i class="fas fa-check-circle"></i> @lang('Transparent Pricing - No hidden fees, ever')</li>
          <li><i class="fas fa-check-circle"></i> @lang('Real-time Tracking - Know exactly where your ride is')</li>
          <li><i class="fas fa-check-circle"></i> @lang('Safe & Secure - Emergency button and trip sharing')</li>
        </ul>
        <a href="#available" class="btn btn-primary">@lang('Book Now')</a>
      </div>
    </div>
  </section>

  <!-- Contact Section -->
  <section class="contact" id="contact">
    <div class="container">
      <h2 class="section-title reveal">@lang('Get In Touch')</h2>
      <p class="section-subtitle reveal" data-delay="1">@lang("Have questions? We'd love to hear from you.")</p>
      <div class="contact-grid">
        <div class="contact-info reveal">
          <div class="contact-item">
            <i class="fas fa-map-marker-alt"></i>
            <div>
              <h4>@lang('Address')</h4>
              <p>@lang('Tozeur, Tunisia')</p>
            </div>
          </div>
          <div class="contact-item">
            <i class="fas fa-phone"></i>
            <div>
              <h4>@lang('Phone')</h4>
              <p>+216 00 000 000</p>
            </div>
          </div>
          <div class="contact-item">
            <i class="fas fa-envelope"></i>
            <div>
              <h4>@lang('Email')</h4>
              <p>info@tozeurtaxi.com</p>
            </div>
          </div>
          <div class="contact-socials">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-linkedin-in"></i></a>
          </div>
        </div>
        <form class="contact-form reveal" data-delay="2">
          <input type="text" placeholder="@lang('Your Name')" required>
          <input type="email" placeholder="@lang('Your Email')" required>
          <input type="text" placeholder="@lang('Subject')">
          <textarea placeholder="@lang('Your Message')" rows="5" required></textarea>
          <button type="submit" class="btn btn-primary">@lang('Send Message')</button>
        </form>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <div class="footer-grid reveal">
        <div class="footer-col">
          <a href="{{ lroute('home') }}" class="logo">
            <i class="fas fa-taxi"></i> Tozeur VIP Taxi
          </a>
          <p>@lang('Making transportation accessible, affordable, and safe for everyone.')</p>
        </div>
        <div class="footer-col">
          <h4>@lang('Quick Links')</h4>
          <ul>
            <li><a href="#home">@lang('Home')</a></li>
            <li><a href="#services">@lang('Services')</a></li>
            <li><a href="#available">@lang('Taxis')</a></li>
            <li><a href="#about">@lang('About')</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>@lang('Dashboard')</h4>
          <ul>
            <li><a href="{{ lroute('login') }}">@lang('Admin Login')</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h4>@lang('Download App')</h4>
          <div class="app-buttons">
            <a href="#" class="app-btn"><i class="fab fa-apple"></i> @lang('App Store')</a>
            <a href="#" class="app-btn"><i class="fab fa-google-play"></i> @lang('Google Play')</a>
          </div>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; {{ date('Y') }} Tozeur VIP Taxi. @lang('All rights reserved.')</p>
      </div>
    </div>
  </footer>
@endsection
