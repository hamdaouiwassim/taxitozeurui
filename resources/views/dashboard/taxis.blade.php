@extends('layouts.dashboard')

@section('title', 'Manage Taxis - Tozeur VIP Taxi')

@section('content')
  <div class="dashboard-content">
    <div class="content-header">
      <h2>Manage Taxis</h2>
      <button class="btn btn-primary" data-modal-open="addTaxiModal"><i class="fas fa-plus"></i> Add Taxi</button>
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
            <td><span class="status {{ $driver->is_active ? 'active' : 'inactive' }}">{{ $driver->is_active ? 'Active' : 'Inactive' }}</span></td>
            <td class="actions">
              <button class="action-btn edit edit-taxi" data-edit-taxi
                data-driver-id="{{ $driver->id }}"
                data-driver-name="{{ $driver->name }}"
                data-name="{{ $driver->taxi?->name }}"
                data-type="{{ $driver->taxi?->type }}"
                data-color="{{ $driver->taxi?->color }}"
                data-capacity="{{ $driver->taxi?->capacity }}"
                title="Edit taxi"><i class="fas fa-pen"></i></button>
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

  <!-- Add Taxi Modal -->
  <div class="modal" id="addTaxiModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Add Taxi</h3>
        <button class="modal-close" data-modal-close><i class="fas fa-times"></i></button>
      </div>
      <form method="POST" action="{{ route('dashboard.taxis.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
          <label>Driver</label>
          <select name="driver_id" required>
            @foreach ($drivers as $driver)
              <option value="{{ $driver->id }}">{{ $driver->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label>Car Name</label>
          <input type="text" name="name" placeholder="e.g. Mercedes Vito" required>
        </div>
        <div class="form-group">
          <label>Vehicle Type</label>
          <select name="type">
            <option value="economy">Economy</option>
            <option value="comfort">Comfort</option>
            <option value="van">Van</option>
            <option value="luxury">Luxury</option>
          </select>
        </div>
        <div class="form-group">
          <label>Color</label>
          <input type="text" name="color" placeholder="e.g. White">
        </div>
        <div class="form-group">
          <label>Capacity</label>
          <input type="text" name="capacity" placeholder="e.g. 4 Passengers">
        </div>
        <div class="form-group">
          <label>Car Image</label>
          <input type="file" name="image" accept="image/*">
        </div>
        <div class="modal-actions">
          <button type="button" class="btn btn-outline" data-modal-close>Cancel</button>
          <button type="submit" class="btn btn-primary">Add Taxi</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Taxi Modal -->
  <div class="modal" id="editTaxiModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Edit Taxi</h3>
        <button class="modal-close" data-modal-close><i class="fas fa-times"></i></button>
      </div>
      <form method="POST" action="" id="editTaxiForm" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-group">
          <label>Driver</label>
          <input type="text" id="edit_taxi_driver" disabled>
          <input type="hidden" name="driver_id" id="edit_taxi_driver_id">
        </div>
        <div class="form-group">
          <label>Car Name</label>
          <input type="text" name="name" id="edit_taxi_name" required>
        </div>
        <div class="form-group">
          <label>Vehicle Type</label>
          <select name="type" id="edit_taxi_type">
            <option value="economy">Economy</option>
            <option value="comfort">Comfort</option>
            <option value="van">Van</option>
            <option value="luxury">Luxury</option>
          </select>
        </div>
        <div class="form-group">
          <label>Color</label>
          <input type="text" name="color" id="edit_taxi_color" placeholder="e.g. White">
        </div>
        <div class="form-group">
          <label>Capacity</label>
          <input type="text" name="capacity" id="edit_taxi_capacity" placeholder="e.g. 4 Passengers">
        </div>
        <div class="form-group">
          <label>Car Image</label>
          <input type="file" name="image" accept="image/*">
        </div>
        <div class="modal-actions">
          <button type="button" class="btn btn-outline" data-modal-close>Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
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

    document.querySelectorAll('[data-edit-taxi]').forEach(btn => {
      btn.addEventListener('click', () => {
        const modal = document.getElementById('editTaxiModal');
        const form = document.getElementById('editTaxiForm');
        form.action = '/dashboard/taxis';
        document.getElementById('edit_taxi_driver').value = btn.dataset.driverName || '';
        document.getElementById('edit_taxi_driver_id').value = btn.dataset.driverId || '';
        document.getElementById('edit_taxi_name').value = btn.dataset.name || '';
        document.getElementById('edit_taxi_type').value = btn.dataset.type || 'economy';
        document.getElementById('edit_taxi_color').value = btn.dataset.color || '';
        document.getElementById('edit_taxi_capacity').value = btn.dataset.capacity || '';
        modal.classList.add('active');
      });
    });
  </script>
@endpush