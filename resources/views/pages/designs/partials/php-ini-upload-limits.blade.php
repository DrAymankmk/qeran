@php
    $iniUploadLimitsForJs = [
        'php_ini_loaded_file' => php_ini_loaded_file() ?: '',
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
        'max_execution_time' => ini_get('max_execution_time'),
        'max_input_time' => ini_get('max_input_time'),
        'memory_limit' => ini_get('memory_limit'),
    ];
@endphp
<details class="small mb-3 border rounded bg-light px-3 py-2">
    <summary class="text-muted" style="cursor: pointer;">{{ __('admin.php-ini-limits-summary') }}</summary>
    <dl class="row mb-0 mt-2">
        @foreach ($iniUploadLimitsForJs as $key => $value)
            @if ($key === 'php_ini_loaded_file')
                <dt class="col-sm-5 col-md-4">{{ __('admin.php-ini-loaded-file') }}</dt>
                <dd class="col-sm-7 col-md-8 mb-1"><code class="small">{{ $value !== '' ? $value : '—' }}</code></dd>
            @else
                <dt class="col-sm-5 col-md-4"><code>{{ $key }}</code></dt>
                <dd class="col-sm-7 col-md-8 mb-1"><code>{{ $value }}</code></dd>
            @endif
        @endforeach
    </dl>
</details>
<script>
(function () {
    var limits = @json($iniUploadLimitsForJs);
    window.__phpIniUploadLimits = limits;
    console.info('[Admin] PHP ini (upload / runtime)', limits);
})();
</script>
