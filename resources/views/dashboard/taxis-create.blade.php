@extends('layouts.dashboard')

@section('title', 'Add Taxi - Tozeur VIP Taxi')

@section('content')
  <div class="dashboard-content">
    <div class="content-header">
      <h2>Add Taxi</h2>
      <a href="{{ route('dashboard.taxis') }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to Taxis</a>
    </div>
    <div class="card settings-card">
      <form method="POST" action="{{ route('dashboard.taxis.store') }}" enctype="multipart/form-data" class="settings-form">
        @csrf
        <div class="form-group">
          <label>Driver</label>
          <select name="driver_id" required>
            @foreach ($drivers as $driver)
              <option value="{{ $driver->id }}" @selected(old('driver_id') == $driver->id)>
                {{ $driver->name }}{{ $driver->taxi ? ' (has taxi)' : '' }}
              </option>
            @endforeach
          </select>
          @error('driver_id') <span class="form-error">{{ $message }}</span> @enderror
        </div>
        <div class="form-group">
          <label>Car Name</label>
          <input type="text" name="name" placeholder="e.g. Mercedes Vito" value="{{ old('name') }}" required>
          @error('name') <span class="form-error">{{ $message }}</span> @enderror
        </div>
        <div class="form-group">
          <label>Vehicle Type</label>
          <select name="type">
            <option value="economy" @selected(old('type') == 'economy')>Economy</option>
            <option value="comfort" @selected(old('type') == 'comfort')>Comfort</option>
            <option value="luxury" @selected(old('type') == 'luxury')>Luxury</option>
          </select>
        </div>
        <div class="form-group">
          <label>Color</label>
          <input type="text" name="color" placeholder="e.g. White" value="{{ old('color') }}">
        </div>
        <div class="form-group">
          <label>Capacity</label>
          <input type="text" name="capacity" placeholder="e.g. 4 Passengers" value="{{ old('capacity') }}">
        </div>
        <div class="form-group">
          <label>Car Images (min 4)</label>
          <input type="file" name="images[]" accept="image/*" multiple required>
          <div class="image-preview-grid"></div>
          @error('images') <span class="form-error">{{ $message }}</span> @enderror
        </div>
        <div class="form-group">
          <label class="checkbox-label">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active'))>
            <span>Available for booking</span>
          </label>
        </div>
        <div class="form-actions">
          <a href="{{ route('dashboard.taxis') }}" class="btn btn-outline">Cancel</a>
          <button type="submit" class="btn btn-primary">Add Taxi</button>
        </div>
      </form>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    document.querySelectorAll('input[type="file"][name="images[]"]').forEach(input => {
      input.addEventListener('change', () => {
        const grid = input.closest('.form-group').querySelector('.image-preview-grid');
        if (!grid) return;
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