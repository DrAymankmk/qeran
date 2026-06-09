
  @import url('https://fonts.bunny.net/css?family=cormorant-garamond:300,400,600|jost:200,300,400');

  * { box-sizing: border-box; margin: 0; padding: 0; }

  .wi-root {
    font-family: 'Jost', sans-serif;
    font-weight: 300;
    background: #faf7f2;
    color: #2c2416;
    overflow-x: hidden;
  }

  /* ── Hero ── */
  .wi-hero {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 60px 24px;
    background: #faf7f2;
    position: relative;
  }
  .wi-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at 50% 40%, rgba(212,185,150,0.18) 0%, transparent 70%);
    pointer-events: none;
  }
  .wi-corner { position: absolute; opacity: 0.25; }
  .wi-corner.tl { top: 20px; left: 20px; }
  .wi-corner.tr { top: 20px; right: 20px; transform: scaleX(-1); }
  .wi-corner.bl { bottom: 20px; left: 20px; transform: scaleY(-1); }
  .wi-corner.br { bottom: 20px; right: 20px; transform: scale(-1); }

  .wi-date-badge {
    font-family: 'Jost', sans-serif;
    font-weight: 200;
    letter-spacing: 0.35em;
    font-size: 11px;
    color: #8a7155;
    text-transform: uppercase;
    margin-bottom: 28px;
  }
  .wi-names {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 300;
    font-size: clamp(54px, 10vw, 86px);
    line-height: 1.05;
    color: #2c2416;
    letter-spacing: -0.01em;
    margin-bottom: 18px;
  }
  .wi-names em { font-style: italic; color: #6b4f2e; }
  .wi-ampersand {
    display: block;
    font-family: 'Cormorant Garamond', serif;
    font-style: italic;
    font-size: 1em;
    color: #b89060;
    line-height: 0.9;
  }
  .wi-subtitle {
    font-size: 12px;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    color: #8a7155;
    font-weight: 300;
    margin-bottom: 36px;
  }
  .wi-divider {
    display: flex;
    align-items: center;
    gap: 14px;
    margin: 0 auto 36px;
    width: 220px;
  }
  .wi-divider::before, .wi-divider::after {
    content: '';
    flex: 1;
    height: 0.5px;
    background: #c8a97a;
  }
  .wi-divider-diamond {
    width: 7px; height: 7px;
    background: #c8a97a;
    transform: rotate(45deg);
    flex-shrink: 0;
  }
  .wi-hero-detail {
    font-size: 13px;
    letter-spacing: 0.15em;
    color: #6b5740;
    line-height: 2;
  }
  .wi-scroll-hint {
    position: absolute;
    bottom: 32px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    color: #b89060;
    font-size: 10px;
    letter-spacing: 0.25em;
    text-transform: uppercase;
    animation: scrollBob 2s ease-in-out infinite;
  }
  @keyframes scrollBob {
    0%,100% { transform: translateX(-50%) translateY(0); opacity:0.6; }
    50% { transform: translateX(-50%) translateY(6px); opacity:1; }
  }

  /* ── Sections shared ── */
  .wi-section {
    padding: 80px 24px;
    text-align: center;
    max-width: 680px;
    margin: 0 auto;
  }
  .wi-section-label {
    font-size: 10px;
    letter-spacing: 0.4em;
    text-transform: uppercase;
    color: #b89060;
    margin-bottom: 16px;
  }
  .wi-section-title {
    font-family: 'Cormorant Garamond', serif;
    font-size: clamp(30px, 5vw, 42px);
    font-weight: 300;
    color: #2c2416;
    margin-bottom: 10px;
    font-style: italic;
  }
  .wi-section-body {
    font-size: 14px;
    line-height: 1.9;
    color: #5a4735;
    font-weight: 300;
  }

  /* ── Countdown ── */
  .wi-countdown-bar {
    background: #2c2416;
    color: #e8dcc8;
    padding: 40px 24px;
    text-align: center;
  }
  .wi-countdown-label {
    font-size: 10px;
    letter-spacing: 0.4em;
    text-transform: uppercase;
    color: #b89060;
    margin-bottom: 24px;
  }
  .wi-countdown-grid {
    display: flex;
    justify-content: center;
    gap: 8px;
    flex-wrap: wrap;
  }
  .wi-count-block {
    background: rgba(255,255,255,0.06);
    border: 0.5px solid rgba(200,169,122,0.3);
    border-radius: 4px;
    padding: 14px 20px;
    min-width: 72px;
  }
  .wi-count-num {
    font-family: 'Cormorant Garamond', serif;
    font-size: 38px;
    font-weight: 300;
    color: #c8a97a;
    line-height: 1;
    display: block;
  }
  .wi-count-unit {
    font-size: 9px;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    color: #8a7a65;
    display: block;
    margin-top: 6px;
  }

  /* ── Our Story ── */
  .wi-story-bg { background: #f4ede2; }
  .wi-timeline {
    margin: 40px auto 0;
    max-width: 500px;
    text-align: left;
    position: relative;
  }
  .wi-timeline::before {
    content: '';
    position: absolute;
    left: 18px;
    top: 8px;
    bottom: 8px;
    width: 0.5px;
    background: #c8a97a;
  }
  .wi-tl-item {
    display: flex;
    gap: 24px;
    margin-bottom: 36px;
    position: relative;
  }
  .wi-tl-dot {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 0.5px solid #c8a97a;
    background: #f4ede2;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    position: relative;
    z-index: 1;
  }
  .wi-tl-dot-inner {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #c8a97a;
  }
  .wi-tl-year {
    font-family: 'Cormorant Garamond', serif;
    font-size: 11px;
    letter-spacing: 0.2em;
    color: #b89060;
    margin-bottom: 4px;
  }
  .wi-tl-text {
    font-size: 14px;
    line-height: 1.7;
    color: #5a4735;
    font-weight: 300;
  }

  /* ── Event Details ── */
  .wi-details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1px;
    background: #d4b896;
    border: 0.5px solid #d4b896;
    margin: 40px 0 0;
    border-radius: 8px;
    overflow: hidden;
  }
  .wi-detail-card {
    background: #faf7f2;
    padding: 32px 20px;
    text-align: center;
  }
  .wi-detail-icon {
    width: 40px;
    height: 40px;
    margin: 0 auto 16px;
    opacity: 0.55;
  }
  .wi-detail-icon-img {
    display: block;
    object-fit: contain;
  }
  .wi-detail-heading {
    font-size: 9px;
    letter-spacing: 0.35em;
    text-transform: uppercase;
    color: #b89060;
    margin-bottom: 10px;
  }
  .wi-detail-main {
    font-family: 'Cormorant Garamond', serif;
    font-size: 22px;
    font-weight: 400;
    color: #2c2416;
    line-height: 1.3;
    margin-bottom: 6px;
  }
  .wi-detail-sub {
    font-size: 12px;
    color: #8a7155;
    line-height: 1.6;
  }

  /* ── Photo Gallery ── */
  .wi-gallery-bg { background: #2c2416; }
  .wi-gallery-bg .wi-section-label { color: #8a7a65; }
  .wi-gallery-bg .wi-section-title { color: #e8dcc8; }
  .wi-gallery {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 6px;
    margin-top: 32px;
  }
  .wi-photo {
    aspect-ratio: 1;
    border-radius: 4px;
    overflow: hidden;
    position: relative;
  }
  .wi-photo-inner {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.35);
    font-family: 'Jost', sans-serif;
  }
  .wi-photo-inner svg { position: absolute; inset: 0; width: 100%; height: 100%; }

  /* ── Schedule ── */
  .wi-schedule {
    margin: 40px 0 0;
    border-top: 0.5px solid #d4b896;
  }
  .wi-schedule-row {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 20px 0;
    border-bottom: 0.5px solid rgba(212,184,150,0.4);
  }
  .wi-sch-time {
    font-family: 'Cormorant Garamond', serif;
    font-size: 15px;
    color: #b89060;
    min-width: 80px;
    text-align: right;
    font-style: italic;
  }
  .wi-sch-dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: #c8a97a;
    flex-shrink: 0;
  }
  .wi-sch-info { text-align: left; }
  .wi-sch-title { font-size: 15px; color: #2c2416; font-weight: 300; }
  .wi-sch-place { font-size: 12px; color: #8a7155; margin-top: 2px; }

  /* ── Venue / Map ── */
  .wi-venue-bg { background: #f4ede2; }
  .wi-map-placeholder {
    margin: 32px 0;
    border-radius: 8px;
    overflow: hidden;
    border: 0.5px solid #d4b896;
    background: #e8dcc8;
    height: 220px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
  }
  .wi-map-grid {
    position: absolute;
    inset: 0;
    opacity: 0.3;
  }
  .wi-map-label {
    font-family: 'Cormorant Garamond', serif;
    font-size: 18px;
    font-style: italic;
    color: #6b4f2e;
    z-index: 1;
    text-align: center;
  }
  .wi-venue-btn {
    display: inline-block;
    border: 0.5px solid #2c2416;
    color: #2c2416;
    font-family: 'Jost', sans-serif;
    font-size: 11px;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    padding: 12px 28px;
    text-decoration: none;
    border-radius: 2px;
    cursor: pointer;
    background: transparent;
    transition: background 0.2s, color 0.2s;
  }
  .wi-venue-btn:hover { background: #2c2416; color: #faf7f2; }

  /* ── Accommodation ── */
  .wi-hotel-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 14px;
    margin-top: 32px;
    text-align: left;
  }
  .wi-hotel-card {
    border: 0.5px solid #d4b896;
    border-radius: 6px;
    padding: 22px 18px;
    background: #faf7f2;
  }
  .wi-hotel-stars { color: #c8a97a; font-size: 11px; margin-bottom: 10px; letter-spacing: 2px; }
  .wi-hotel-name { font-family: 'Cormorant Garamond', serif; font-size: 20px; font-weight: 400; color: #2c2416; margin-bottom: 6px; }
  .wi-hotel-dist { font-size: 11px; color: #8a7155; }

  /* ── Dress Code ── */
  .wi-dress-bg { background: #2c2416; }
  .wi-dress-bg .wi-section-title { color: #e8dcc8; }
  .wi-dress-bg .wi-section-label { color: #8a7a65; }
  .wi-dress-bg .wi-section-body { color: #a0907a; }
  .wi-palette {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin: 32px 0 20px;
    flex-wrap: wrap;
  }
  .wi-swatch {
    text-align: center;
  }
  .wi-swatch-circle {
    width: 50px; height: 50px;
    border-radius: 50%;
    margin: 0 auto 8px;
    border: 0.5px solid rgba(255,255,255,0.1);
  }
  .wi-swatch-name { font-size: 10px; letter-spacing: 0.15em; color: #8a7a65; }

  /* ── Gift Registry ── */
  .wi-gift-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 14px;
    margin-top: 32px;
  }
  .wi-gift-card {
    border: 0.5px solid #d4b896;
    border-radius: 6px;
    padding: 28px 18px;
    text-align: center;
    cursor: pointer;
    transition: border-color 0.2s;
    background: #faf7f2;
  }
  .wi-gift-card:hover { border-color: #8a7155; }
  .wi-gift-icon { font-size: 28px; margin-bottom: 14px; }
  .wi-gift-name { font-size: 14px; color: #2c2416; font-weight: 300; margin-bottom: 4px; }
  .wi-gift-sub { font-size: 11px; color: #8a7155; }

  /* ── RSVP ── */
  .wi-rsvp-bg { background: #6b4f2e; }
  .wi-rsvp-bg .wi-section-label { color: #c8a97a; }
  .wi-rsvp-bg .wi-section-title { color: #f5ede0; }
  .wi-rsvp-bg .wi-section-body { color: #c8a97a; }
  .wi-rsvp-form { margin-top: 36px; text-align: left; }
  .wi-field {
    margin-bottom: 18px;
  }
  .wi-field label {
    display: block;
    font-size: 10px;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    color: #c8a97a;
    margin-bottom: 8px;
  }
  .wi-field input, .wi-field select, .wi-field textarea {
    width: 100%;
    background: rgba(255,255,255,0.08);
    border: 0.5px solid rgba(200,169,122,0.4);
    border-radius: 3px;
    padding: 12px 14px;
    font-family: 'Jost', sans-serif;
    font-size: 13px;
    font-weight: 300;
    color: #f5ede0;
    outline: none;
    transition: border-color 0.2s;
    -webkit-appearance: none;
  }
  .wi-field select option { background: #6b4f2e; color: #f5ede0; }
  .wi-field input::placeholder, .wi-field textarea::placeholder { color: rgba(200,169,122,0.4); }
  .wi-field input:focus, .wi-field select:focus, .wi-field textarea:focus {
    border-color: rgba(200,169,122,0.85);
  }
  .wi-rsvp-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap;
    margin-top: 28px;
  }
  .wi-rsvp-actions.is-hidden { display: none; }
  .wi-rsvp-btn {
    min-width: 140px;
    border: none;
    border-radius: 3px;
    padding: 14px 24px;
    font-family: 'Jost', sans-serif;
    font-size: 11px;
    letter-spacing: 0.25em;
    text-transform: uppercase;
    cursor: pointer;
    transition: background 0.2s, opacity 0.2s;
    font-weight: 500;
  }
  .wi-rsvp-btn:disabled { opacity: 0.65; cursor: wait; }
  .wi-rsvp-btn-accept {
    background: #c8a97a;
    color: #2c2416;
  }
  .wi-rsvp-btn-accept:hover:not(:disabled) { background: #b89060; }
  .wi-rsvp-btn-decline {
    background: transparent;
    color: #f5ede0;
    border: 0.5px solid rgba(200,169,122,0.55);
  }
  .wi-rsvp-btn-decline:hover:not(:disabled) {
    background: rgba(200,169,122,0.12);
  }
  .wi-rsvp-result {
    margin-top: 28px;
    text-align: center;
  }
  .wi-rsvp-result.is-hidden { display: none; }
  .wi-rsvp-result-title {
    font-size: 14px;
    color: #f5ede0;
    margin-bottom: 20px;
    line-height: 1.7;
  }
  .wi-rsvp-qr-wrap {
    display: flex;
    justify-content: center;
  }
  .wi-rsvp-qr.qr-section,
  .wi-rsvp-qr .qr-section {
    background: rgba(255,255,255,0.96);
    padding: 20px;
    border-radius: 12px;
    max-width: 280px;
    margin: 0 auto;
  }
  .wi-rsvp-qr img,
  .wi-rsvp-qr .qr-section img {
    width: 160px;
    height: 160px;
    margin: 0 auto 12px;
    display: block;
  }
  .wi-rsvp-qr p,
  .wi-rsvp-qr .qr-section p {
    color: #2d3748;
    font-size: 0.85rem;
    margin: 0;
  }
  .wi-rsvp-qr .qr-download-button {
    display: inline-block;
    margin-top: 12px;
    padding: 10px 20px;
    border: none;
    border-radius: 20px;
    background: linear-gradient(135deg, #6b4f2e, #2c2416);
    color: #fff;
    font-size: 0.85rem;
    cursor: pointer;
  }

  /* ── Guestbook ── */
  .wi-guestbook-bg { background: #f4ede2; }
  .wi-message-list {
    margin-top: 32px;
    display: flex;
    flex-direction: column;
    gap: 16px;
  }
  .wi-message-card {
    background: #faf7f2;
    border: 0.5px solid #d4b896;
    border-radius: 6px;
    padding: 20px 22px;
    text-align: left;
  }
  .wi-msg-author {
    font-family: 'Cormorant Garamond', serif;
    font-size: 17px;
    font-style: italic;
    color: #2c2416;
    margin-bottom: 8px;
  }
  .wi-msg-text {
    font-size: 13px;
    line-height: 1.8;
    color: #5a4735;
    font-weight: 300;
  }

  /* ── Music Wishlist ── */
  .wi-song-list {
    margin-top: 32px;
    border: 0.5px solid #d4b896;
    border-radius: 6px;
    overflow: hidden;
  }
  .wi-song-row {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 14px 20px;
    border-bottom: 0.5px solid rgba(212,184,150,0.4);
    background: #faf7f2;
  }
  .wi-song-row:last-child { border-bottom: none; }
  .wi-song-num { font-size: 11px; color: #c8a97a; min-width: 22px; }
  .wi-song-info { flex: 1; text-align: left; }
  .wi-song-title { font-size: 14px; color: #2c2416; }
  .wi-song-artist { font-size: 11px; color: #8a7155; margin-top: 2px; }
  .wi-song-heart { color: #c8a97a; font-size: 14px; }

  /* ── Thank you footer ── */
  .wi-footer {
    background: #2c2416;
    padding: 70px 24px 50px;
    text-align: center;
  }
  .wi-footer-names {
    font-family: 'Cormorant Garamond', serif;
    font-style: italic;
    font-size: 42px;
    font-weight: 300;
    color: #c8a97a;
    margin-bottom: 16px;
    line-height: 1.1;
  }
  .wi-footer-msg {
    font-size: 13px;
    color: #8a7a65;
    letter-spacing: 0.15em;
    line-height: 2;
  }
  .wi-footer-date {
    margin-top: 36px;
    font-size: 10px;
    letter-spacing: 0.4em;
    text-transform: uppercase;
    color: #554535;
  }

  /* ── Animations ── */
  .wi-fade-in {
    opacity: 0;
    transform: translateY(20px);
    animation: fadeUp 0.9s ease forwards;
  }
  .wi-fade-in.d1 { animation-delay: 0.1s; }
  .wi-fade-in.d2 { animation-delay: 0.3s; }
  .wi-fade-in.d3 { animation-delay: 0.5s; }
  .wi-fade-in.d4 { animation-delay: 0.7s; }
  .wi-fade-in.d5 { animation-delay: 0.9s; }
  @keyframes fadeUp {
    to { opacity: 1; transform: translateY(0); }
  }
