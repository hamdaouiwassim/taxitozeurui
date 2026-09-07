@extends('layouts.dashboard')

@section('title', 'Add Driver - Tozeur VIP Taxi')

@section('content')
  <div class="dashboard-content">
    <div class="content-header">
      <h2>Add Driver</h2>
      <a href="{{ route('dashboard.drivers') }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to Drivers</a>
    </div>
    <div class="card settings-card">
      <form method="POST" action="{{ route('dashboard.drivers.store') }}" class="settings-form">
        @csrf
        <div class="form-group">
          <label>Driver Name</label>
          <input type="text" name="name" placeholder="e.g. John Smith" value="{{ old('name') }}" required>
          @error('name') <span class="form-error">{{ $message }}</span> @enderror
        </div>
        <div class="form-group">
          @include('partials.image-uploader', [
              'field' => 'avatar',
              'uploadUrl' => route('dashboard.uploads.avatar'),
              'label' => 'Driver Avatar',
              'hint' => 'Optional - JPG, PNG, WebP up to 2MB.',
              'multiple' => false,
              'initial' => [],
          ])
        </div>
        <div class="form-group">
          <label>Phone</label>
          <input type="text" name="phone" placeholder="+216 ..." value="{{ old('phone') }}">
        </div>
        <div class="form-group">
          <label>WhatsApp</label>
          <input type="text" name="whatsapp" placeholder="21600000000" value="{{ old('whatsapp') }}">
        </div>
        <div class="form-group">
          <label>Location</label>
          <input type="text" name="location" placeholder="Downtown, Tozeur" value="{{ old('location') }}">
        </div>
        <div class="form-group">
          <label>Years Experience</label>
          <input type="number" name="years_experience" min="0" value="{{ old('years_experience', 0) }}">
        </div>
        <div class="form-group">
          <label>Work Start</label>
          <input type="time" name="work_start" value="{{ old('work_start') }}">
        </div>
        <div class="form-group">
          <label>Work End</label>
          <input type="time" name="work_end" value="{{ old('work_end') }}">
        </div>
        <div class="form-group">
          <label>Working Days (leave empty for 24/7)</label>
          <div class="work-days-row">
            @foreach ([1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat', 7 => 'Sun'] as $day => $label)
              <label class="work-day">
                <input type="checkbox" name="work_days[]" value="{{ $day }}" @checked(in_array($day, old('work_days', [])))> {{ $label }}
              </label>
            @endforeach
          </div>
        </div>
        <div class="form-actions">
          <a href="{{ route('dashboard.drivers') }}" class="btn btn-outline">Cancel</a>
          <button type="submit" class="btn btn-primary">Add Driver</button>
        </div>
      </form>
    </div>
  </div>
@endsection