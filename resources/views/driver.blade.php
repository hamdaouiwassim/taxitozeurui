@extends('layouts.app')

@section('title', __(':name - Tozeur VIP Taxi', ['name' => $driver->name]))

@section('meta_description', __('Book :name, a rated :type taxi driver in :location. Rated :rating/5 from :count reviews. Chat on WhatsApp to book your ride.', [
    'name' => $driver->name,
    'type' => $driver->taxi?->type,
    'location' => $driver->location,
    'rating' => number_format($driver->rating, 1),
    'count' => $driver->reviews_count,
]))

@section('og_title', __(':name - Tozeur VIP Taxi', ['name' => $driver->name]))
@section('og_description', __('Rated :rating/5 from :count reviews. Book :type taxi in :location.', [
    'rating' => number_format($driver->rating, 1),
    'count' => $driver->reviews_count,
    'type' => ucfirst($driver->taxi?->type),
    'location' => $driver->location,
]))
@section('og_type', 'profile')
@section('og_image', $driver->avatar_url ?: url('/asstes/images/og-cover.png'))
@section('og_url', lroute('drivers.show', ['driver' => $driver]))
@section('canonical', lroute('drivers.show', ['driver' => $driver]))

@push('schema')
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "Service",
  "name": "{{ __(':name - :type Taxi in :location', ['name' => $driver->name, 'type' => ucfirst($driver->taxi?->type), 'location' => $driver->location]) }}",
  "serviceType": "Taxi Service",
  "provider": {
    "@@type": "LocalBusiness",
    "name": "Tozeur VIP Taxi",
    "url": "{{ url('/') }}"
  },
  "areaServed": {
    "@@type": "City",
    "name": "{{ $driver->location }}",
    "address": {
      "@@type": "PostalAddress",
      "addressLocality": "{{ $driver->location }}",
      "addressCountry": "TN"
    }
  },
  "offers": {
    "@@type": "Offer",
    "availability": "https://schema.org/InStock"
  },
  @if ($driver->reviews_count > 0)
  "aggregateRating": {
    "@@type": "AggregateRating",
    "ratingValue": "{{ number_format($driver->rating, 1) }}",
    "reviewCount": "{{ $driver->reviews_count }}",
    "bestRating": "5"
  },
  @endif
  "review": [
    @foreach ($reviews as $i => $review)
    {
      "@@type": "Review",
      "reviewRating": {
        "@@type": "Rating",
        "ratingValue": "{{ $review->rating }}",
        "bestRating": "5"
      },
      "author": {
        "@@type": "Person",
        "name": "{{ $review->name }}"
      },
      "reviewBody": "{{ $review->comment }}"
    }{{ ! $loop->last ? ',' : '' }}
    @endforeach
  ]
}
</script>
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@@type": "ListItem",
      "position": 1,
      "name": "@lang('Home')",
      "item": "{{ url('/') }}"
    },
    {
      "@@type": "ListItem",
      "position": 2,
      "name": "{{ $driver->name }}",
      "item": "{{ lroute('drivers.show', ['driver' => $driver]) }}"
    }
  ]
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
        <li><a href="{{ lroute('home') }}">@lang('Home')</a></li>
        <li><a href="{{ lroute('home') }}#services">@lang('Our Services')</a></li>
        <li><a href="{{ lroute('home') }}#about">@lang('About Us')</a></li>
        <li><a href="{{ lroute('home') }}#contact">@lang('Contact Us')</a></li>
      </ul>
      <div class="nav-actions">
        <a href="{{ lroute('home') }}#available" class="btn btn-primary">@lang('Book a Taxi')</a>
        @include('partials.lang-switcher')
      </div>
      <button class="menu-toggle" id="menuToggle">
        <i class="fas fa-bars" id="menuIcon"></i>
      </button>
    </div>
  </nav>

  <!-- Driver Profile Hero -->
  <section class="driver-profile-hero">
    <div class="container">
      <div class="driver-profile-card">
        <div class="driver-profile-top">
          <div class="driver-profile-avatar">
            <img src="{{ $driver->avatar_url }}" alt="{{ $driver->name }}">
          </div>
          <div class="driver-profile-info">
            <div class="driver-profile-name-row">
              <h1>{{ $driver->name }}</h1>
              <span class="driver-badge">@lang(ucfirst($driver->taxi?->type))</span>
            </div>
            <div class="driver-profile-rating">
              @if ($driver->reviews_count > 0)
                <div class="stars">
                  @for ($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star{{ $i <= round($driver->rating) ? '' : '-half-alt' }}"></i>
                  @endfor
                </div>
                <span class="rating-value">{{ number_format($driver->rating, 1) }}</span>
              @endif
              <span class="rating-count">
                @if ($driver->reviews_count > 0)
                  ({{ $driver->reviews_count }} @lang('reviews'))
                @else
                  @lang('No reviews yet')
                @endif
              </span>
            </div>
            <div class="driver-profile-location">
              <i class="fas fa-map-marker-alt"></i>
              <span>{{ $driver->location }}</span>
            </div>
          </div>
          <div class="driver-profile-actions">
            @if ($driver->is_active && $driver->taxi?->is_active)
              <a href="https://wa.me/{{ preg_replace('/\D/', '', $driver->whatsapp) }}" target="_blank" class="btn btn-success">
                <i class="fab fa-whatsapp"></i> @lang('Chat on WhatsApp')
              </a>
              <a href="tel:{{ $driver->phone }}" class="btn btn-outline">
                <i class="fas fa-phone"></i> @lang('Call Now')
              </a>
            @else
              <span class="btn btn-outline disabled btn-block not-available-badge">
                <i class="fas fa-pause"></i> @lang('Currently Unavailable')
              </span>
            @endif
          </div>
        </div>
        <div class="driver-profile-stats">
          <div class="stat-item">
            <i class="fas fa-calendar-check"></i>
            <div>
              <strong>{{ $driver->years_experience }}</strong>
              <span>@lang('Years Experience')</span>
            </div>
          </div>
          <div class="stat-item">
            <i class="fas fa-shield-alt"></i>
            <div>
              <strong>100%</strong>
              <span>@lang('Safety Rating')</span>
            </div>
          </div>
          <div class="stat-item">
            <i class="fas fa-clock"></i>
            <div>
              <strong>{{ $driver->working_hours ?? '24/7' }}</strong>
              <span>
                @if ($driver->working_hours)
                  {{ $driver->isWorkingNow() ? __('Open now') : __('Closed now') }}
                @else
                  @lang('Availability')
                @endif
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Vehicle Section -->
  <section class="section vehicle-section">
    <div class="container">
      <h2 class="section-title">@lang('Vehicle Details')</h2>
      <div class="vehicle-details-card">
        <div class="vehicle-gallery">
          @php $gallery = ($driver->taxi?->getImages() ?? []); @endphp
          @if (count($gallery) > 0)
            <div class="vehicle-main-image">
              <img src="{{ $gallery[0] }}" alt="{{ $driver->taxi?->name }}" id="vehicleMainImg">
            </div>
            @if (count($gallery) > 1)
              <div class="vehicle-thumbnails">
                @foreach ($gallery as $img)
                  <button type="button" class="vehicle-thumb {{ $loop->first ? 'active' : '' }}" data-img="{{ $img }}">
                    <img src="{{ $img }}" alt="{{ $driver->taxi?->name }}">
                  </button>
                @endforeach
              </div>
            @endif
          @else
            <div class="vehicle-main-image">
              <img src="/asstes/images/47f7a327-5fa8-47db-a183-d4f127272c07.png" alt="{{ $driver->taxi?->name }}">
            </div>
          @endif
        </div>
        <div class="vehicle-info-grid">
          <div class="vehicle-info-item">
            <i class="fas fa-car"></i>
            <div>
              <small>@lang('Vehicle')</small>
              <strong>{{ $driver->taxi?->name ?? __('N/A') }}</strong>
            </div>
          </div>
          <div class="vehicle-info-item">
            <i class="fas fa-calendar-alt"></i>
            <div>
              <small>@lang('Year')</small>
              <strong>{{ $driver->taxi?->year ?? __('N/A') }}</strong>
            </div>
          </div>
          <div class="vehicle-info-item">
            <i class="fas fa-palette"></i>
            <div>
              <small>@lang('Color')</small>
              @php
                $vehicleColor = $driver->taxi?->color;
                $isColor = is_string($vehicleColor) && $vehicleColor !== '';
              @endphp
              <strong>
                @if ($isColor)
                  <span class="color-swatch" style="background-color: {{ $vehicleColor }}" title="{{ $vehicleColor }}" aria-hidden="true"></span>
                @else
                  @lang('N/A')
                @endif
              </strong>
            </div>
          </div>
          <div class="vehicle-info-item">
            <i class="fas fa-users"></i>
            <div>
              <small>@lang('Capacity')</small>
              <strong>{{ $driver->taxi?->capacity ?? __('N/A') }}</strong>
            </div>
          </div>
          <div class="vehicle-info-item">
            <i class="fas fa-suitcase"></i>
            <div>
              <small>@lang('Luggage')</small>
              <strong>{{ $driver->taxi?->luggage ?? __('N/A') }}</strong>
            </div>
          </div>
          <div class="vehicle-info-item">
            <i class="fas fa-tag"></i>
            <div>
              <small>@lang('Type')</small>
              <strong>@lang(ucfirst($driver->taxi?->type))</strong>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Reviews Section -->
  <section class="section reviews-section">
    <div class="container">
      <div class="reviews-header">
        <h2 class="section-title">@lang('Reviews')</h2>
        <button class="btn btn-primary" data-modal-open="reviewModal">
          <i class="fas fa-plus"></i> @lang('Add Review')
        </button>
      </div>

      <div class="reviews-summary">
        <div class="reviews-avg">
          @if ($driver->reviews_count > 0)
            <span class="avg-number">{{ number_format($driver->rating, 1) }}</span>
            <div class="avg-stars">
              @for ($i = 1; $i <= 5; $i++)
                <i class="fas fa-star{{ $i <= round($driver->rating) ? '' : '-half-alt' }}"></i>
              @endfor
            </div>
            <span class="avg-count">{{ $driver->reviews_count }} @lang('reviews')</span>
          @else
            <span class="avg-number">0.0</span>
            <span class="avg-count">@lang('No reviews yet')</span>
          @endif
        </div>
        @if ($driver->reviews_count > 0)
        <div class="reviews-bars">
          @php
            $distribution = collect(range(5, 1))->map(function ($n) use ($reviews) {
                $count = $reviews->where('rating', $n)->count();
                $total = max($reviews->count(), 1);
                return ['stars' => $n, 'count' => $count, 'pct' => round(($count / $total) * 100)];
            });
          @endphp
          @foreach ($distribution as $bar)
            <div class="bar-row">
              <span>{{ $bar['stars'] }} <i class="fas fa-star"></i></span>
              <div class="bar-track"><div class="bar-fill" style="width: {{ $bar['pct'] }}%"></div></div>
              <span class="bar-count">{{ $bar['count'] }}</span>
            </div>
          @endforeach
        </div>
        @endif
      </div>

      <div class="reviews-list" id="reviewsList">
        @forelse ($reviews as $review)
          <div class="review-card">
            <div class="review-top">
              <div class="review-user">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($review->user_name) }}&background={{ $review->avatar_color }}&color=fff&rounded=true" alt="{{ $review->user_name }}">
                <div>
                  <strong>{{ $review->user_name }}</strong>
                  <span class="review-date">{{ $review->created_at->format('M d, Y') }}</span>
                </div>
              </div>
              <div class="review-stars">
                @for ($i = 1; $i <= 5; $i++)
                  <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star"></i>
                @endfor
              </div>
            </div>
            <p class="review-text">{{ $review->comment }}</p>
          </div>
        @empty
          <p class="empty-state">@lang('No reviews yet. Be the first to review :name!', ['name' => $driver->name])</p>
        @endforelse
      </div>
    </div>
  </section>

  <!-- Add Review Modal -->
  <div class="modal" id="reviewModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>@lang('Write a Review')</h3>
        <button class="modal-close" data-modal-close>
          <i class="fas fa-times"></i>
        </button>
      </div>
      <form method="POST" action="{{ lroute('drivers.reviews.store', ['driver' => $driver]) }}" id="reviewForm">
        @csrf
        <div class="form-group">
          <label>@lang('Your Name')</label>
          <input type="text" name="user_name" placeholder="@lang('Enter your name')" required>
        </div>
        <div class="form-group">
          <label>@lang('Phone')</label>
          <input type="text" name="phone" placeholder="@lang('+216 ...')">
        </div>
        <div class="form-group">
          <label>@lang('Email')</label>
          <input type="email" name="email" placeholder="@lang('name@example.com')">
        </div>
        <div class="form-group">
          <label>@lang('Rating')</label>
          <input type="hidden" name="rating" id="ratingInput" value="0">
          <div class="star-rating" id="starRating">
            <i class="far fa-star" data-rating="1"></i>
            <i class="far fa-star" data-rating="2"></i>
            <i class="far fa-star" data-rating="3"></i>
            <i class="far fa-star" data-rating="4"></i>
            <i class="far fa-star" data-rating="5"></i>
          </div>
          @error('rating')<small class="error-text">{{ $message }}</small>@enderror
        </div>
        <div class="form-group">
          <label>@lang('Your Review')</label>
          <textarea name="comment" rows="4" placeholder="@lang('Tell us about your experience...')" required></textarea>
        </div>
        <x-recaptcha::input />
        <button type="submit" class="btn btn-primary btn-block">
          <i class="fas fa-paper-plane"></i> @lang('Submit Review')
        </button>
      </form>
    </div>
  </div>

  @include('partials.flash')

  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <div class="footer-grid">
        <div class="footer-col">
          <a href="{{ lroute('home') }}" class="logo">
            <i class="fas fa-taxi"></i>
            <span class="logo-text">Tozeur VIP Taxi <small>@lang('Premium Service')</small></span>
          </a>
          <p>@lang('Premium taxi service in Tozeur, Tunisia. Safe, comfortable, and reliable rides 24/7.')</p>
        </div>
        <div class="footer-col">
          <h3>@lang('Quick Links')</h3>
          <ul>
            <li><a href="{{ lroute('home') }}#available">@lang('Available Drivers')</a></li>
            <li><a href="{{ lroute('home') }}#services">@lang('Our Services')</a></li>
            <li><a href="{{ lroute('home') }}#about">@lang('About Us')</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h3>@lang('Contact')</h3>
          <ul>
            <li><i class="fas fa-map-marker-alt"></i> @lang('Tozeur, Tunisia')</li>
            <li><i class="fas fa-phone"></i> +216 00 000 000</li>
            <li><i class="fas fa-envelope"></i> info@tozeurtaxi.com</li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; {{ date('Y') }} Tozeur VIP Taxi. @lang('All rights reserved.')</p>
      </div>
    </div>
  </footer>
@endsection

@push('scripts')
  <x-recaptcha::script />
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const mainImg = document.getElementById('vehicleMainImg');
      const thumbs = document.querySelectorAll('.vehicle-thumb');
      if (mainImg && thumbs.length) {
        thumbs.forEach(thumb => {
          thumb.addEventListener('click', () => {
            mainImg.src = thumb.dataset.img;
            thumbs.forEach(t => t.classList.remove('active'));
            thumb.classList.add('active');
          });
        });
      }

      const form = document.getElementById('reviewForm');
      if (form && window.reCaptcha) {
        form.addEventListener('submit', (event) => {
          event.preventDefault();
          window.reCaptcha.render('review', (token) => {
            const input = form.querySelector('input[name="g-recaptcha-response"]');
            if (input) input.value = token;
            form.submit();
          });
        });
      }
    });
  </script>
@endpush
