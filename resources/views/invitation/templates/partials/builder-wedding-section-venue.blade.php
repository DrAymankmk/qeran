  <!-- ⑦ Venue (dynamic) -->
  <div class="wi-venue-bg">
    <section class="wi-section">
      <p class="wi-section-label">{{ $wiVenueLabel }}</p>
      <h2 class="wi-section-title">{{ $wiVenueTitle }}</h2>
      @if(!empty($wiVenueDescription))
      <p class="wi-section-body">{{ $wiVenueDescription }}</p>
      @endif

      @if(!empty($wiHasMap) && !empty($wiMapEmbedUrl))
      <div class="wi-map-embed">
        <iframe
          src="{{ $wiMapEmbedUrl }}"
          title="{{ $wiVenueTitle }}"
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
          allowfullscreen
        ></iframe>
      </div>
      @else
      <div class="wi-map-placeholder">
        <svg class="wi-map-grid" viewBox="0 0 600 220" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
          <defs>
            <pattern id="wi-map-grid" width="30" height="30" patternUnits="userSpaceOnUse">
              <path d="M30 0L0 0 0 30" fill="none" stroke="currentColor" stroke-width="0.4"/>
            </pattern>
          </defs>
          <rect width="600" height="220" fill="url(#wi-map-grid)" opacity="0.35"/>
          <circle cx="300" cy="110" r="36" fill="currentColor" opacity="0.12"/>
          <circle cx="300" cy="110" r="9" fill="currentColor" opacity="0.55"/>
        </svg>
        <div class="wi-map-label">
          <strong>{{ $wiVenueName }}</strong>
          @if(!empty($wiVenueAddressLine))
          <br><span class="wi-map-address">{!! nl2br(e($wiVenueAddressLine)) !!}</span>
          @endif
        </div>
      </div>
      @endif

      @if(!empty($wiVenueAddressLine) && !empty($wiHasMap))
      <p class="wi-venue-address-line">{!! nl2br(e($wiVenueAddressLine)) !!}</p>
      @endif

      @if(!empty($wiMapUrl) && $wiMapUrl !== '#')
      <div class="wi-venue-actions">
        <a href="{{ $wiMapUrl }}" target="_blank" rel="noopener noreferrer" class="wi-venue-btn">الوصول إلى الموقع</a>
        <a href="{{ $wiMapUrl }}" target="_blank" rel="noopener noreferrer" class="wi-venue-btn">عرض على الخريطة</a>
      </div>
      @endif
    </section>
  </div>
