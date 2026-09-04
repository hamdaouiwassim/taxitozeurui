@extends('layouts.dashboard')

@section('title', 'Settings - Tozeur VIP Taxi')

@section('content')
  <div class="dashboard-content">
    <h2>Settings</h2>
    <div class="settings-grid">
      <div class="card settings-card">
        <h3><i class="fas fa-user-cog"></i> General Settings</h3>
        <form method="POST" action="{{ route('dashboard.settings') }}" class="settings-form">
          @csrf
          <div class="form-group">
            <label>Company Name</label>
            <input type="text" name="site_name" value="{{ $settings['site_name'] ?? 'Tozeur VIP Taxi' }}">
          </div>
          <div class="form-group">
            <label>WhatsApp Number</label>
            <input type="text" name="whatsapp_number" value="{{ $settings['whatsapp_number'] ?? '21600000000' }}">
          </div>
          <div class="form-group">
            <label>Phone Number</label>
            <input type="text" name="phone" value="{{ $settings['phone'] ?? '+216 00 000 000' }}">
          </div>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
      </div>
      <div class="card settings-card">
        <h3><i class="fas fa-shield-alt"></i> Security Settings</h3>
        <form method="POST" action="{{ route('dashboard.profile') }}" class="settings-form">
          @csrf
          <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" value="{{ Auth::user()->name }}">
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="{{ Auth::user()->email }}">
          </div>
          <div class="form-group">
            <label>New Password (leave blank to keep)</label>
            <input type="password" name="password" placeholder="Enter new password">
          </div>
          <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
      </div>
    </div>
  </div>
@endsection