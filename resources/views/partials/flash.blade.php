@if (session('success'))
  <div id="flashSuccess" data-message="{{ session('success') }}"></div>
@endif
@if (session('error'))
  <div id="flashError" data-message="{{ session('error') }}"></div>
@endif
@if ($errors->any())
  <div id="flashError" data-message="{{ $errors->first() }}"></div>
@endif
