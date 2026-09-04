@extends('layouts.dashboard')

@section('title', 'Drivers - Tozeur VIP Taxi')

@section('content')
  <div class="dashboard-content">
    <div class="content-header">
      <h2>Drivers</h2>
      <button class="btn btn-primary" data-modal-open="addDriverModal"><i class="fas fa-plus"></i> Add Driver</button>
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
          <th>Trips</th>
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
            <td>{{ number_format($driver->trips) }}</td>
            <td><i class="fas fa-star"></i> {{ number_format($driver->rating, 1) }}</td>
            <td><span class="status {{ $driver->is_active ? 'online' : 'offline' }}">{{ $driver->is_active ? 'Active' : 'Inactive' }}</span></td>
            <td class="actions">
              <button class="action-btn edit edit-driver"
                data-edit-driver
                data-id="{{ $driver->id }}"
                data-name="{{ $driver->name }}"
                data-phone="{{ $driver->phone }}"
                data-whatsapp="{{ $driver->whatsapp }}"
                data-location="{{ $driver->location }}"
                data-years="{{ $driver->years_experience }}"
                title="Edit driver"><i class="fas fa-pen"></i></button>
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
          <tr><td colspan="7" class="empty-cell">No drivers yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Add Driver Modal -->
  <div class="modal" id="addDriverModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Add New Driver</h3>
        <button class="modal-close" data-modal-close><i class="fas fa-times"></i></button>
      </div>
      <form method="POST" action="{{ route('dashboard.drivers.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
          <label>Driver Name</label>
          <input type="text" name="name" placeholder="e.g. John Smith" required>
        </div>
        <div class="form-group">
          <label>Driver Avatar</label>
          <input type="file" name="avatar" accept="image/*">
        </div>
        <div class="form-group">
          <label>Phone</label>
          <input type="text" name="phone" placeholder="+216 ...">
        </div>
        <div class="form-group">
          <label>WhatsApp</label>
          <input type="text" name="whatsapp" placeholder="21600000000">
        </div>
        <div class="form-group">
          <label>Location</label>
          <input type="text" name="location" placeholder="Downtown, Tozeur">
        </div>
        <div class="form-group">
          <label>Years Experience</label>
          <input type="number" name="years_experience" min="0" value="0">
        </div>
        <div class="modal-actions">
          <button type="button" class="btn btn-outline" data-modal-close>Cancel</button>
          <button type="submit" class="btn btn-primary">Add Driver</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Driver Modal -->
  <div class="modal" id="editDriverModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Edit Driver</h3>
        <button class="modal-close" data-modal-close><i class="fas fa-times"></i></button>
      </div>
      <form method="POST" action="" id="editDriverForm" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-group">
          <label>Driver Name</label>
          <input type="text" name="name" id="edit_name" required>
        </div>
        <div class="form-group">
          <label>Driver Avatar</label>
          <input type="file" name="avatar" id="edit_avatar" accept="image/*">
        </div>
        <div class="form-group">
          <label>Phone</label>
          <input type="text" name="phone" id="edit_phone" placeholder="+216 ...">
        </div>
        <div class="form-group">
          <label>WhatsApp</label>
          <input type="text" name="whatsapp" id="edit_whatsapp" placeholder="21600000000">
        </div>
        <div class="form-group">
          <label>Location</label>
          <input type="text" name="location" id="edit_location" placeholder="Downtown, Tozeur">
        </div>
        <div class="form-group">
          <label>Years Experience</label>
          <input type="number" name="years_experience" id="edit_years" min="0" value="0">
        </div>
        <input type="hidden" name="is_active" id="edit_is_active" value="1">
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
    const driverSearchDa = document.getElementById('driverSearchDa');
    if (driverSearchDa) {
      driverSearchDa.addEventListener('input', () => {
        const q = driverSearchDa.value.toLowerCase();
        document.querySelectorAll('#driverTableBody tr').forEach(row => {
          row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
      });
    }

    document.querySelectorAll('[data-edit-driver]').forEach(btn => {
      btn.addEventListener('click', () => {
        const modal = document.getElementById('editDriverModal');
        const form = document.getElementById('editDriverForm');
        form.action = '/dashboard/drivers/' + btn.dataset.id;
        document.getElementById('edit_name').value = btn.dataset.name || '';
        document.getElementById('edit_phone').value = btn.dataset.phone || '';
        document.getElementById('edit_whatsapp').value = btn.dataset.whatsapp || '';
        document.getElementById('edit_location').value = btn.dataset.location || '';
        document.getElementById('edit_years').value = btn.dataset.years || 0;
        modal.classList.add('active');
      });
    });
  </script>
@endpush