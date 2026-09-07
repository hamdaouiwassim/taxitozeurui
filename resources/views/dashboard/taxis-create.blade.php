@extends('layouts.dashboard')

@section('title', 'Add Taxi - Tozeur VIP Taxi')

@section('content')
  <div class="dashboard-content">
    <div class="content-header">
      <h2>Add Taxi</h2>
      <a href="{{ route('dashboard.taxis') }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to Taxis</a>
    </div>
    <div class="card settings-card">
      <form method="POST" action="{{ route('dashboard.taxis.store') }}" class="settings-form">
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
          @php $colorValue = preg_match('/^#[0-9a-f]{6}$/i', (string) old('color')) ? old('color') : '#FFFFFF'; @endphp
          <div class="color-input-row">
            <input type="color" name="color" value="{{ $colorValue }}" class="color-picker">
            <span class="color-value"></span>
          </div>
        </div>
        <div class="form-group">
          <label>Capacity</label>
          <input type="text" name="capacity" placeholder="e.g. 4 Passengers" value="{{ old('capacity') }}">
        </div>
        <div class="form-group">
          @include('partials.image-uploader', [
              'field' => 'image',
              'uploadUrl' => route('dashboard.uploads.taxi-image'),
              'label' => 'Main Image',
              'hint' => 'Optional - JPG, PNG, WebP up to 2MB.',
              'multiple' => false,
              'initial' => [],
          ])
        </div>
        <div class="form-group">
          @include('partials.image-uploader', [
              'field' => 'images[]',
              'fileField' => 'images[]',
              'uploadUrl' => route('dashboard.uploads.taxi-gallery'),
              'label' => 'Car Gallery (min 4)',
              'hint' => 'At least 4 images required - JPG, PNG, WebP up to 2MB.',
              'multiple' => true,
              'initial' => [],
          ])
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
    document.querySelectorAll('.color-input-row').forEach(row => {
      const picker = row.querySelector('.color-picker');
      const value = row.querySelector('.color-value');
      if (!picker || !value) return;
      const render = () => { value.textContent = picker.value.toLowerCase(); };
      picker.addEventListener('input', render);
      render();
    });
  </script>
@endpush