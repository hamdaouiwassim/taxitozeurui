@extends('layouts.app')

@section('title', $driver->name . ' - Tozeur VIP Taxi')

@section('content')
  <!-- Navbar -->
  <nav class="navbar">
    <div class="container nav-container">
      <a href="{{ route('home') }}" class="logo">
        <i class="fas fa-taxi"></i>
        <span class="logo-text">Tozeur VIP Taxi <small>Premium Service</small></span>
      </a>
      <ul class="nav-links">
        <li><a href="{{ route('home') }}">Home</a></li>
        <li><a href="{{ route('home') }}#services">Our Services</a></li>
        <li><a href="{{ route('home') }}#about">About Us</a></li>
        <li><a href="{{ route('home') }}#contact">Contact Us</a></li>
      </ul>
      <div class="nav-actions">
        <a href="{{ route('home') }}#available" class="btn btn-primary">Book a Taxi</a>
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
              <span class="driver-badge">{{ ucfirst($driver->taxi?->type) }}</span>
            </div>
            <div class="driver-profile-rating">
              <div class="stars">
                @for ($i = 1; $i <= 5; $i++)
                  <i class="fas fa-star{{ $i <= round($driver->rating) ? '' : '-half-alt' }}"></i>
                @endfor
              </div>
              <span class="rating-value">{{ number_format($driver->rating, 1) }}</span>
              <span class="rating-count">({{ $driver->reviews_count }} reviews)</span>
            </div>
            <div class="driver-profile-location">
              <i class="fas fa-map-marker-alt"></i>
              <span>{{ $driver->location }}</span>
            </div>
          </div>
          <div class="driver-profile-actions">
            <a href="https://wa.me/{{ preg_replace('/\D/', '', $driver->whatsapp) }}" target="_blank" class="btn btn-success">
              <i class="fab fa-whatsapp"></i> Chat on WhatsApp
            </a>
            <a href="tel:{{ $driver->phone }}" class="btn btn-outline">
              <i class="fas fa-phone"></i> Call Now
            </a>
          </div>
        </div>
        <div class="driver-profile-stats">
          <div class="stat-item">
            <i class="fas fa-route"></i>
            <div>
              <strong>{{ number_format($driver->trips) }}+</strong>
              <span>Trips Completed</span>
            </div>
          </div>
          <div class="stat-item">
            <i class="fas fa-calendar-check"></i>
            <div>
              <strong>{{ $driver->years_experience }}</strong>
              <span>Years Experience</span>
            </div>
          </div>
          <div class="stat-item">
            <i class="fas fa-shield-alt"></i>
            <div>
              <strong>100%</strong>
              <span>Safety Rating</span>
            </div>
          </div>
          <div class="stat-item">
            <i class="fas fa-clock"></i>
            <div>
              <strong>24/7</strong>
              <span>Availability</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Vehicle Section -->
  <section class="section vehicle-section">
    <div class="container">
      <h2 class="section-title">Vehicle Details</h2>
      <div class="vehicle-details-card">
        <div class="vehicle-image-block">
          <img src="{{ $driver->taxi?->image_url ?: '/asstes/images/47f7a327-5fa8-47db-a183-d4f127272c07.png' }}" alt="{{ $driver->taxi?->name }}">
        </div>
        <div class="vehicle-info-grid">
          <div class="vehicle-info-item">
            <i class="fas fa-car"></i>
            <div>
              <small>Vehicle</small>
              <strong>{{ $driver->taxi?->name ?? 'N/A' }}</strong>
            </div>
          </div>
          <div class="vehicle-info-item">
            <i class="fas fa-calendar-alt"></i>
            <div>
              <small>Year</small>
              <strong>{{ $driver->taxi?->year ?? 'N/A' }}</strong>
            </div>
          </div>
          <div class="vehicle-info-item">
            <i class="fas fa-palette"></i>
            <div>
              <small>Color</small>
              <strong>{{ $driver->taxi?->color ?? 'N/A' }}</strong>
            </div>
          </div>
          <div class="vehicle-info-item">
            <i class="fas fa-users"></i>
            <div>
              <small>Capacity</small>
              <strong>{{ $driver->taxi?->capacity ?? 'N/A' }}</strong>
            </div>
          </div>
          <div class="vehicle-info-item">
            <i class="fas fa-suitcase"></i>
            <div>
              <small>Luggage</small>
              <strong>{{ $driver->taxi?->luggage ?? 'N/A' }}</strong>
            </div>
          </div>
          <div class="vehicle-info-item">
            <i class="fas fa-tag"></i>
            <div>
              <small>Type</small>
              <strong>{{ ucfirst($driver->taxi?->type) }}</strong>
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
        <h2 class="section-title">Reviews</h2>
        <button class="btn btn-primary" data-modal-open="reviewModal">
          <i class="fas fa-plus"></i> Add Review
        </button>
      </div>

      <div class="reviews-summary">
        <div class="reviews-avg">
          <span class="avg-number">{{ number_format($driver->rating, 1) }}</span>
          <div class="avg-stars">
            @for ($i = 1; $i <= 5; $i++)
              <i class="fas fa-star{{ $i <= round($driver->rating) ? '' : '-half-alt' }}"></i>
            @endfor
          </div>
          <span class="avg-count">{{ $driver->reviews_count }} reviews</span>
        </div>
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
          <p class="empty-state">No reviews yet. Be the first to review {{ $driver->name }}!</p>
        @endforelse
      </div>
    </div>
  </section>

  <!-- Add Review Modal -->
  <div class="modal" id="reviewModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Write a Review</h3>
        <button class="modal-close" data-modal-close>
          <i class="fas fa-times"></i>
        </button>
      </div>
      <form method="POST" action="{{ route('drivers.reviews.store', $driver) }}">
        @csrf
        <div class="form-group">
          <label>Your Name</label>
          <input type="text" name="user_name" placeholder="Enter your name" required>
        </div>
        <div class="form-group">
          <label>Phone</label>
          <input type="text" name="phone" placeholder="+216 ...">
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" placeholder="name@example.com">
        </div>
        <div class="form-group">
          <label>Rating</label>
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
          <label>Your Review</label>
          <textarea name="comment" rows="4" placeholder="Tell us about your experience..." required></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-block">
          <i class="fas fa-paper-plane"></i> Submit Review
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
          <a href="{{ route('home') }}" class="logo">
            <i class="fas fa-taxi"></i>
            <span class="logo-text">Tozeur VIP Taxi <small>Premium Service</small></span>
          </a>
          <p>Premium taxi service in Tozeur, Tunisia. Safe, comfortable, and reliable rides 24/7.</p>
        </div>
        <div class="footer-col">
          <h3>Quick Links</h3>
          <ul>
            <li><a href="{{ route('home') }}#available">Available Drivers</a></li>
            <li><a href="{{ route('home') }}#services">Our Services</a></li>
            <li><a href="{{ route('home') }}#about">About Us</a></li>
          </ul>
        </div>
        <div class="footer-col">
          <h3>Contact</h3>
          <ul>
            <li><i class="fas fa-map-marker-alt"></i> Tozeur, Tunisia</li>
            <li><i class="fas fa-phone"></i> +216 00 000 000</li>
            <li><i class="fas fa-envelope"></i> info@tozeurtaxi.com</li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; {{ date('Y') }} Tozeur VIP Taxi. All rights reserved.</p>
      </div>
    </div>
  </footer>
@endsection
