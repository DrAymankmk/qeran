@props([
    'heading' => null,
])

@php
    $headingText = $heading ?? __('admin.validation-errors');
    /** @var \Illuminate\Support\ViewErrorBag $validationErrors */
    $sharedErrors = view()->shared('errors');
    $validationErrors = $sharedErrors instanceof \Illuminate\Support\ViewErrorBag
        ? $sharedErrors
        : new \Illuminate\Support\ViewErrorBag();
@endphp

@if ($validationErrors->any())
    <div {{ $attributes->merge(['class' => 'alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4']) }} role="alert">
        <div class="d-flex align-items-start gap-3">
            <span class="flex-shrink-0 text-danger" aria-hidden="true">
                <i class="mdi mdi-alert-circle-outline fs-3 lh-1 d-inline-block"></i>
            </span>
            <div class="flex-grow-1 min-w-0">
                <h6 class="alert-heading mb-2 fw-semibold">{{ $headingText }}</h6>
                @if ($validationErrors->count() === 1)
                    <p class="mb-0 small">{{ $validationErrors->first() }}</p>
                @else
                    <ul class="mb-0 ps-3 small">
                        @foreach ($validationErrors->all() as $error)
                            <li class="mb-1">{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('admin.close') }}"></button>
    </div>
@endif
