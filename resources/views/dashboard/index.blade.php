@extends('layouts.dashboard')

@section('title', 'Dashboard - Tozeur VIP Taxi')

@section('content')
  <div class="dashboard-content">
    <h2>Dashboard Overview</h2>
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-card-icon" style="background: #dbeafe; color: #2563eb;"><i class="fas fa-car"></i></div>
        <div class="stat-card-info">
          <h3>{{ $stats['active_taxis'] }}</h3>
          <p>Active Taxis</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon" style="background: #dcfce7; color: #16a34a;"><i class="fas fa-users"></i></div>
        <div class="stat-card-info">
          <h3>{{ $stats['drivers'] }}</h3>
          <p>Total Drivers</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon" style="background: #e0e7ff; color: #4f46e5;"><i class="fas fa-star"></i></div>
        <div class="stat-card-info">
          <h3>{{ $stats['reviews'] }}</h3>
          <p>Total Reviews</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-card-icon" style="background: #fce7f3; color: #db2777;"><i class="fas fa-taxi"></i></div>
        <div class="stat-card-info">
          <h3>{{ $stats['drivers'] }}</h3>
          <p>Fleet Size</p>
        </div>
      </div>
    </div>

    <div class="dashboard-grid">
      <div class="card">
        <div class="card-header">
          <h3>Recent Drivers</h3>
          <a href="{{ route('dashboard.drivers') }}" class="view-all">View All</a>
        </div>
        <table class="data-table">
          <thead>
            <tr>
              <th>Driver</th>
              <th>Vehicle</th>
              <th>Rating</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($drivers->take(5) as $driver)
              <tr>
                <td class="driver-cell">
                  @if ($driver->avatar_url)
                    <img class="driver-avatar-thumb" src="{{ $driver->avatar_url }}" alt="{{ $driver->name }}">
                  @endif
                  <span class="driver-cell-name">{{ $driver->name }}</span>
                </td>
                <td>{{ $driver->taxi?->name ?? 'N/A' }}</td>
                <td><i class="fas fa-star"></i> {{ number_format($driver->rating, 1) }}</td>
                <td><span class="status {{ $driver->is_active ? 'online' : 'offline' }}">{{ $driver->is_active ? 'Active' : 'Inactive' }}</span></td>
              </tr>
            @empty
              <tr><td colspan="4" class="empty-cell">No drivers yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection