@extends('layouts.dashboard')

@section('title', 'Manage Taxis - Tozeur VIP Taxi')

@section('content')
  <div class="dashboard-content">
    <div class="content-header">
      <h2>Manage Taxis</h2>
      <a href="{{ route('dashboard.taxis.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Taxi</a>
    </div>
    <div class="filter-bar">
      <input type="text" placeholder="Search taxis..." id="taxiSearch">
    </div>
    <table class="data-table full-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Vehicle</th>
          <th>Driver</th>
          <th>Type</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="taxiTableBody">
        @forelse ($drivers as $driver)
          <tr>
            <td>T{{ str_pad((string) $driver->id, 3, '0', STR_PAD_LEFT) }}</td>
            <td>
              @if ($driver->taxi?->image_url)
                <img src="{{ $driver->taxi->image_url }}" alt="{{ $driver->taxi->name }}" class="taxi-thumb">
              @endif
              {{ $driver->taxi?->name ?? 'No taxi' }}
            </td>
            <td>{{ $driver->name }}</td>
            <td>{{ ucfirst($driver->taxi?->type) }}</td>
            <td>
              @if ($driver->taxi)
                <span class="status {{ $driver->taxi->is_active ? 'active' : 'inactive' }}">{{ $driver->taxi->is_active ? 'Available' : 'Unavailable' }}</span>
              @else
                <span class="status inactive">No taxi</span>
              @endif
            </td>
            <td class="actions">
              <a href="{{ route('dashboard.taxis.edit', $driver) }}" class="action-btn edit" title="Edit taxi"><i class="fas fa-pen"></i></a>
              @if ($driver->taxi)
              <button class="action-btn edit" title="Toggle availability"
                onclick="document.getElementById('taxi-toggle-{{ $driver->taxi->id }}').submit()"><i class="fas {{ $driver->taxi->is_active ? 'fa-pause' : 'fa-play' }}"></i></button>
              <form id="taxi-toggle-{{ $driver->taxi->id }}" method="POST" action="{{ route('dashboard.taxis.availability', $driver->taxi) }}" style="display:none">
                @csrf
                @method('PUT')
              </form>
              @endif
              <form method="POST" action="{{ route('dashboard.taxis.destroy', $driver) }}" data-confirm="Delete {{ $driver->taxi?->name }}?">
                @csrf
                @method('DELETE')
                <button class="action-btn delete" data-remove title="Delete taxi"><i class="fas fa-trash"></i></button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="empty-cell">No taxis yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection

@push('scripts')
  <script>
    const taxiSearch = document.getElementById('taxiSearch');
    if (taxiSearch) {
      taxiSearch.addEventListener('input', () => {
        const q = taxiSearch.value.toLowerCase();
        document.querySelectorAll('#taxiTableBody tr').forEach(row => {
          row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
      });
    }
  </script>
@endpush