@extends('layouts.dashboard')

@section('title', 'Edit Taxi - Tozeur VIP Taxi')

@section('content')
  <div class="dashboard-content">
    <div class="content-header">
      <h2>Edit Taxi</h2>
      <a href="{{ route('dashboard.taxis') }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to Taxis</a>
    </div>
    <div class="card settings-card">
      <form method="POST" action="{{ route('dashboard.taxis.update') }}" enctype="multipart/form-data" class="settings-form">
        @csrf
        @method('PUT')
        <div class="form-group">
          <label>Driver</label>
          <input type="text" value="{{ $driver->name }}" disabled>
          <input type="hidden" name="driver_id" value="{{ $driver->id }}">
        </div>
        <div class="form-group">
          <label>Car Name</label>
          <input type="text" name="name" value="{{ old('name', $driver->taxi?->name) }}" required>
          @error('name') <span class="form-error">{{ $message }}</span> @enderror
        </div>
        <div class="form-group">
          <label>Vehicle Type</label>
          <select name="type">
            <option value="economy" @selected(old('type', $driver->taxi?->type) == 'economy')>Economy</option>
            <option value="comfort" @selected(old('type', $driver->taxi?->type) == 'comfort')>Comfort</option>
            <option value="luxury" @selected(old('type', $driver->taxi?->type) == 'luxury')>Luxury</option>
          </select>
        </div>
        <div class="form-group">
          <label>Color</label>
          <input type="text" name="color" value="{{ old('color', $driver->taxi?->color) }}" placeholder="e.g. White">
        </div>
        <div class="form-group">
          <label>Capacity</label>
          <input type="text" name="capacity" value="{{ old('capacity', $driver->taxi?->capacity) }}" placeholder="e.g. 4 Passengers">
        </div>
        <div class="form-group">
          <label>Car Images (min 4)</label>
          @if ($driver->taxi)
            <div class="image-preview-grid">
              @foreach ($driver->taxi->getImages() as $image)
                <img src="{{ $image }}" alt="Current taxi image">
              @endforeach
            </div>
          @endif
          <input type="file" name="images[]" accept="image/*" multiple>
          <div class="image-preview-grid"></div>
          @error('images') <span class="form-error">{{ $message }}</span> @enderror
        </div>
        <div class="form-group">
          <label class="checkbox-label">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $driver->taxi?->is_active))>
            <span>Available for booking</span>
          </label>
        </div>
        <div class="form-actions">
          <a href="{{ route('dashboard.taxis') }}" class="btn btn-outline">Cancel</a>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    document.querySelectorAll('input[type="file"][name="images[]"]').forEach(input => {
      input.addEventListener('change', () => {
        const grids = input.closest('.form-group').querySelectorAll('.image-preview-grid');
        if (grids.length < 2) return;
        const grid = grids[1];
        grid.innerHTML = '';
        [...input.files].forEach(file => {
          const reader = new FileReader();
          reader.onload = (e) => {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.width = '60px';
            img.style.height = '60px';
            img.style.objectFit = 'cover';
            img.style.borderRadius = '8px';
            grid.appendChild(img);
          };
          reader.readAsDataURL(file);
        });
      });
    });
  </script>
@endpush