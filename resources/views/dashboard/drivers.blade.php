@extends('layouts.dashboard')

@section('title', 'Drivers - Tozeur VIP Taxi')

@section('content')
  <div class="dashboard-content">
    <div class="content-header">
      <h2>Drivers</h2>
      <a href="{{ route('dashboard.drivers.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Driver</a>
    </div>
    <div class="filter-bar">
      <input type="text" placeholder="Search drivers..." id="driverSearchDa">
    </div>
    <table class="data-table full-table">
      <thead>
        <tr>
          <th>Driver</th>
          <th>Phone</th>
          <th>Vehicle</th>
          <th>Working Hours</th>
          <th>Rating</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="driverTableBody">
        @forelse ($drivers as $driver)
          <tr>
            <td class="driver-cell">
              @if ($driver->avatar_url)
                <img class="driver-avatar-thumb" src="{{ $driver->avatar_url }}" alt="{{ $driver->name }}">
              @endif
              <span class="driver-cell-name">{{ $driver->name }}</span>
            </td>
            <td>{{ $driver->phone ?? 'N/A' }}</td>
            <td>{{ $driver->taxi?->name ?? 'N/A' }}</td>
            <td>
              @if ($driver->working_hours)
                {{ $driver->working_hours }}
                @if ($driver->work_days_labels)
                  <small class="block-muted">{{ $driver->work_days_labels }}</small>
                @endif
              @else
                <span class="text-muted">24/7</span>
              @endif
            </td>
            <td><i class="fas fa-star"></i> {{ number_format($driver->rating, 1) }}</td>
            <td><span class="status {{ $driver->is_active ? 'online' : 'offline' }}">{{ $driver->is_active ? 'Active' : 'Inactive' }}</span></td>
            <td class="actions">
              <a href="{{ route('dashboard.drivers.edit', $driver) }}" class="action-btn edit" title="Edit driver"><i class="fas fa-pen"></i></a>
              <button class="action-btn edit" title="Toggle active"
                onclick="document.getElementById('toggle-{{ $driver->id }}').submit()"><i class="fas {{ $driver->is_active ? 'fa-pause' : 'fa-play' }}"></i></button>
              <form id="toggle-{{ $driver->id }}" method="POST" action="{{ route('dashboard.drivers.update', $driver) }}" style="display:none">
                @csrf
                @method('PUT')
                <input type="hidden" name="name" value="{{ $driver->name }}">
                <input type="hidden" name="is_active" value="{{ $driver->is_active ? 0 : 1 }}">
              </form>
              <form method="POST" action="{{ route('dashboard.drivers.destroy', $driver) }}" data-confirm="Delete {{ $driver->name }}?">
                @csrf
                @method('DELETE')
                <button type="submit" class="action-btn delete" title="Delete"><i class="fas fa-trash"></i></button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="8" class="empty-cell">No drivers yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection

@push('scripts')
  <script>
    const driverSearchDa = document.getElementById('driverSearchDa');
    if (driverSearchDa) {
      driverSearchDa.addEventListener('input', () => {
        const q = driverSearchDa.value.toLowerCase();
        document.querySelectorAll('#driverTableBody tr').forEach(row => {
          row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
      });
    }
  </script>
@endpush