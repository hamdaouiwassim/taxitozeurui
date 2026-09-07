@php
  $id = 'uploader-'.md5($field);
  $fileFieldName = $fileField ?? ($multiple ? 'images[]' : 'file');
  $displays = collect($initial)->map(function (string $value): string {
      if (preg_match('#^https?://#', $value) || str_starts_with($value, '/')) {
          return $value;
      }

      return \Illuminate\Support\Facades\Storage::url($value);
  })->all();
@endphp
<div class="uploader" id="{{ $id }}" data-upload-url="{{ $uploadUrl }}" data-name="{{ $field }}" data-file-field="{{ $fileFieldName }}" data-multiple="{{ $multiple ? 'true' : 'false' }}">
  <label>{{ $label }}</label>
  @if (! empty($hint))
    <p class="uploader-hint">{{ $hint }}</p>
  @endif
  <label class="uploader-trigger btn btn-outline">
    <i class="fas fa-upload"></i> @lang('Upload')
    <input type="file" accept="image/*" @if ($multiple) multiple @endif>
  </label>
  <div class="image-preview-grid uploader-preview">
    @foreach ($initial as $index => $value)
      <div class="uploader-thumb">
        <img src="{{ $displays[$index] }}" alt="">
        <button type="button" class="uploader-remove" aria-label="@lang('Remove')">&times;</button>
        <input type="hidden" name="{{ $field }}" value="{{ $value }}">
      </div>
    @endforeach
  </div>
  <div class="uploader-error"></div>
  <div class="uploader-status"></div>
</div>

@push('scripts')
  <script>
    if (!window.taxiUploaderInit) {
      window.taxiUploaderInit = true;

      function taxiUploaderThumb(uploader, value, url) {
        const div = document.createElement('div');
        div.className = 'uploader-thumb';

        const img = document.createElement('img');
        img.src = url;
        img.alt = '';

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'uploader-remove';
        btn.setAttribute('aria-label', 'Remove');
        btn.innerHTML = '&times;';
        btn.addEventListener('click', () => div.remove());

        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = uploader.dataset.name;
        hidden.value = value;

        div.append(img, btn, hidden);
        uploader.querySelector('.uploader-preview').appendChild(div);
      }

      async function taxiUploaderUpload(uploader, files) {
        const url = uploader.dataset.uploadUrl;
        const field = uploader.dataset.fileField;
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const status = uploader.querySelector('.uploader-status');
        const errorBox = uploader.querySelector('.uploader-error');

        for (const file of files) {
          errorBox.textContent = '';
          status.textContent = 'Uploading...';

          const fd = new FormData();
          fd.append(field, file);

          try {
            const res = await fetch(url, {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
              },
              body: fd,
            });

            const payload = await res.json().catch(() => ({}));

            if (!res.ok) {
              const errs = payload.errors ? Object.values(payload.errors).flat().join(' ') : (payload.message || 'Upload failed');
              errorBox.textContent = errs;
              return;
            }

            if (uploader.dataset.multiple === 'true') {
              payload.paths.forEach((p, i) => taxiUploaderThumb(uploader, p, payload.urls[i]));
            } else {
              uploader.querySelectorAll('.uploader-thumb').forEach((t) => t.remove());
              taxiUploaderThumb(uploader, payload.path, payload.url);
            }
          } catch (e) {
            errorBox.textContent = 'Upload failed';
          } finally {
            status.textContent = '';
          }
        }
      }

      document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.uploader').forEach((uploader) => {
          const input = uploader.querySelector('input[type="file"]');
          input.addEventListener('change', () => {
            const files = [...input.files];
            if (!files.length) return;
            taxiUploaderUpload(uploader, files);
            input.value = '';
          });
        });
      });
    }
  </script>
@endpush