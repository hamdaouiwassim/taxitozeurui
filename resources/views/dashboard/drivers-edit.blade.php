@extends('layouts.dashboard')

@section('title', 'Edit Driver - Tozeur VIP Taxi')

@section('content')
  <div class="dashboard-content">
    <div class="content-header">
      <h2>Edit Driver</h2>
      <a href="{{ route('dashboard.drivers') }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to Drivers</a>
    </div>
    <div class="card settings-card">
      <form method="POST" action="{{ route('dashboard.drivers.update', $driver) }}" class="settings-form">
        @csrf
        @method('PUT')
        <div class="form-group">
          <label>Driver Name</label>
          <input type="text" name="name" value="{{ old('name', $driver->name) }}" required>
          @error('name') <span class="form-error">{{ $message }}</span> @enderror
        </div>
        <div class="form-group">
          @include('partials.image-uploader', [
              'field' => 'avatar',
              'uploadUrl' => route('dashboard.uploads.avatar'),
              'label' => 'Driver Avatar',
              'hint' => 'Optional - JPG, PNG, WebP up to 2MB.',
              'multiple' => false,
              'initial' => $driver->avatar ? [$driver->avatar] : [],
          ])
        </div>
        <div class="form-group">
          <label>Phone</label>
          <input type="text" name="phone" value="{{ old('phone', $driver->phone) }}" placeholder="+216 ...">
        </div>
        <div class="form-group">
          <label>WhatsApp</label>
          <input type="text" name="whatsapp" value="{{ old('whatsapp', $driver->whatsapp) }}" placeholder="21600000000">
        </div>
        <div class="form-group">
          <label>Location</label>
          <input type="text" name="location" value="{{ old('location', $driver->location) }}" placeholder="Downtown, Tozeur">
        </div>
        <div class="form-group">
          <label>Years Experience</label>
          <input type="number" name="years_experience" min="0" value="{{ old('years_experience', $driver->years_experience) }}">
        </div>
        <div class="form-group">
          <label>Work Start</label>
          <input type="time" name="work_start" value="{{ old('work_start', $driver->work_start) }}">
        </div>
        <div class="form-group">
          <label>Work End</label>
          <input type="time" name="work_end" value="{{ old('work_end', $driver->work_end) }}">
        </div>
        <div class="form-group">
          <label>Working Days (leave empty for 24/7)</label>
          <div class="work-days-row">
            @foreach ([1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat', 7 => 'Sun'] as $day => $label)
              <label class="work-day">
                <input type="checkbox" name="work_days[]" value="{{ $day }}" @checked(in_array($day, old('work_days', $driver->work_days ?? [])))> {{ $label }}
              </label>
            @endforeach
          </div>
        </div>
        <div class="form-group">
          <label class="checkbox-label">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $driver->is_active))>
            <span>Active</span>
          </label>
        </div>
        <div class="form-actions">
          <a href="{{ route('dashboard.drivers') }}" class="btn btn-outline">Cancel</a>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
@endsection