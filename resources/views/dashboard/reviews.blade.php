@extends('layouts.dashboard')

@section('title', 'Reviews - Tozeur VIP Taxi')

@section('content')
  <div class="dashboard-content">
    <div class="content-header">
      <h2>Reviews</h2>
    </div>
    <table class="data-table full-table">
      <thead>
        <tr>
          <th>Reviewer</th>
          <th>Driver</th>
          <th>Rating</th>
          <th>Comment</th>
          <th>Date</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($reviews as $review)
          <tr>
            <td>{{ $review->user_name }}</td>
            <td>{{ $review->driver?->name ?? 'N/A' }}</td>
            <td>
              <span class="stars-inline">
                @for ($i = 1; $i <= 5; $i++)
                  <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star"></i>
                @endfor
              </span>
            </td>
            <td>{{ \Illuminate\Support\Str::limit($review->comment, 50) }}</td>
            <td>{{ $review->created_at->format('M d, Y') }}</td>
            <td><span class="status {{ $review->is_visible ? 'active' : 'inactive' }}">{{ $review->is_visible ? 'Visible' : 'Hidden' }}</span></td>
            <td class="actions">
              <form method="POST" action="{{ route('dashboard.reviews.toggle', $review) }}">
                @csrf
                @method('PUT')
                <button type="submit" class="action-btn {{ $review->is_visible ? 'edit' : 'success' }}"
                  title="{{ $review->is_visible ? 'Hide' : 'Show' }}"><i class="fas {{ $review->is_visible ? 'fa-eye-slash' : 'fa-eye' }}"></i></button>
              </form>
              <form method="POST" action="{{ route('dashboard.reviews.destroy', $review) }}" data-confirm="Delete review by {{ $review->user_name }}?">
                @csrf
                @method('DELETE')
                <button type="submit" class="action-btn delete" title="Delete"><i class="fas fa-trash"></i></button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="empty-cell">No reviews yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection