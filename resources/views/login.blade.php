@extends('layouts.app')

@section('title', __('Tozeur VIP Taxi - Admin Login'))

@push('body-attrs')
  class="login-body"
@endpush

@section('content')
  <div class="login-wrapper">
    <div class="login-left">
      <div class="login-brand">
        <a href="{{ lroute('home') }}" class="logo">
          <i class="fas fa-taxi"></i>
          <span class="logo-text">Tozeur VIP Taxi <small>@lang('Premium Service')</small></span>
        </a>
      </div>
      <div class="login-hero-content">
        <h1>@lang('Welcome back')</h1>
        <p>@lang('Sign in to manage your taxis and drivers.')</p>
      </div>
      <div class="login-bg-icon">
        <i class="fas fa-taxi"></i>
      </div>
    </div>
    <div class="login-right">
      <div class="login-card">
        @include('partials.lang-switcher')
        <div class="login-card-header">
          <h2>@lang('Admin Login')</h2>
          <p>@lang('Enter your credentials to access the dashboard')</p>
        </div>
        <form method="POST" action="{{ lroute('login') }}">
          @csrf
          <div class="login-field">
            <label for="loginEmail">@lang('Email')</label>
            <div class="login-input-wrap">
              <i class="fas fa-envelope"></i>
              <input type="email" id="loginEmail" name="email" placeholder="admin@taxigo.com" value="{{ old('email', 'admin@taxigo.com') }}" required autofocus>
            </div>
          </div>
          <div class="login-field">
            <label for="loginPassword">@lang('Password')</label>
            <div class="login-input-wrap">
              <i class="fas fa-lock"></i>
              <input type="password" id="loginPassword" name="password" placeholder="@lang('Enter your password')" value="admin123" required>
              <button type="button" class="toggle-pass" id="togglePass">
                <i class="fas fa-eye"></i>
              </button>
            </div>
          </div>
          <div class="login-options">
            <label class="login-checkbox">
              <input type="checkbox" name="remember" checked>
              <span>@lang('Remember me')</span>
            </label>
            <a href="#" class="login-forgot">@lang('Forgot password?')</a>
          </div>
          <button type="submit" class="btn btn-primary btn-lg btn-block login-btn">
            <i class="fas fa-sign-in-alt"></i> @lang('Sign In')
          </button>
          <div class="login-error {{ $errors->any() ? 'visible' : '' }}" id="loginError">
            <i class="fas fa-exclamation-circle"></i>
            {{ $errors->any() ? $errors->first('email') : __('Invalid email or password') }}
          </div>
        </form>
        <div class="login-footer">
          <p>&copy; {{ date('Y') }} Tozeur VIP Taxi. @lang('All rights reserved.')</p>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    const togglePass = document.getElementById('togglePass');
    const loginPassword = document.getElementById('loginPassword');
    if (togglePass && loginPassword) {
      togglePass.addEventListener('click', () => {
        const isPassword = loginPassword.type === 'password';
        loginPassword.type = isPassword ? 'text' : 'password';
        togglePass.innerHTML = `<i class="fas fa-eye${isPassword ? '-slash' : ''}"></i>`;
      });
    }
  </script>
@endpush
