<script>
var wiAcceptUrl = @json($routes['accept'] ?? '');
var wiDeclineUrl = @json($routes['decline'] ?? '');
var wiIsBuilderPreview = @json(!empty($isBuilderPreview));
var wiPreviewQrUrl = @json($previewQrUrl ?? '');

function wiIsPreviewRsvpUrl(url) {
  return wiIsBuilderPreview || !url || url === '#';
}

function wiGetCsrfToken() {
  var meta = document.querySelector('meta[name="csrf-token"]');
  if (meta && meta.content) return meta.content;
  var match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
  return match ? decodeURIComponent(match[1]) : @json(csrf_token());
}

function wiParseJsonResponse(r) {
  var ct = (r.headers.get('content-type') || '').toLowerCase();
  if (!ct.includes('application/json')) {
    return r.text().then(function () {
      throw new Error(r.status === 419 ? 'انتهت صلاحية الجلسة، يرجى تحديث الصفحة' : 'استجابة غير صالحة من الخادم');
    });
  }
  return r.json();
}

function wiPreviewRsvpQrUrl() {
  if (wiPreviewQrUrl) return wiPreviewQrUrl;
  var hiddenImg = document.querySelector('#wiRsvpAccepted img');
  return hiddenImg ? (hiddenImg.getAttribute('src') || '') : '';
}

function wiRsvpPost(url, button) {
  var originalText = button ? button.textContent : '';

  if (wiIsPreviewRsvpUrl(url)) {
    if (button) {
      button.disabled = true;
      button.textContent = '…';
    }
    return new Promise(function (resolve) {
      window.setTimeout(function () {
        if (button) {
          button.disabled = false;
          button.textContent = originalText;
        }
        resolve({ success: true, qr_url: wiPreviewRsvpQrUrl() });
      }, 300);
    });
  }

  if (button) {
    button.disabled = true;
    button.textContent = '…';
  }

  return fetch(url, {
    method: 'POST',
    credentials: 'same-origin',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': wiGetCsrfToken(),
    },
  })
    .then(wiParseJsonResponse)
    .then(function (data) {
      if (!data.success) {
        throw new Error(data.message || 'حدث خطأ');
      }
      return data;
    })
    .catch(function (err) {
      if (button) {
        button.disabled = false;
        button.textContent = originalText;
      }
      throw err;
    });
}

function wiShowRsvpAccepted(qrUrl) {
  var actions = document.getElementById('wiRsvpActions');
  var accepted = document.getElementById('wiRsvpAccepted');
  var declined = document.getElementById('wiRsvpDeclined');
  if (actions) actions.classList.add('is-hidden');
  if (declined) declined.classList.add('is-hidden');
  if (accepted) accepted.classList.remove('is-hidden');
  if (qrUrl) {
    var img = accepted ? accepted.querySelector('img') : null;
    var downloadBtn = accepted ? accepted.querySelector('.qr-download-button') : null;
    if (img) img.src = qrUrl;
    if (downloadBtn) downloadBtn.dataset.qrUrl = qrUrl;
  }
}

function wiShowRsvpDeclined() {
  var actions = document.getElementById('wiRsvpActions');
  var accepted = document.getElementById('wiRsvpAccepted');
  var declined = document.getElementById('wiRsvpDeclined');
  if (actions) actions.classList.add('is-hidden');
  if (accepted) accepted.classList.add('is-hidden');
  if (declined) declined.classList.remove('is-hidden');
}

function wiBindRsvpButtons() {
  var acceptBtn = document.getElementById('wiRsvpAcceptBtn');
  var declineBtn = document.getElementById('wiRsvpDeclineBtn');

  if (acceptBtn && acceptBtn.dataset.bound !== '1') {
    acceptBtn.dataset.bound = '1';
    acceptBtn.addEventListener('click', function () {
      wiRsvpPost(wiAcceptUrl, acceptBtn)
        .then(function (data) {
          wiShowRsvpAccepted(data.qr_url || '');
          if (acceptBtn) acceptBtn.disabled = false;
        })
        .catch(function (err) {
          alert(err.message || 'حدث خطأ أثناء قبول الدعوة');
        });
    });
  }

  if (declineBtn && declineBtn.dataset.bound !== '1') {
    declineBtn.dataset.bound = '1';
    declineBtn.addEventListener('click', function () {
      if (!window.confirm(@json(__('admin.confirm-decline-invitation')))) {
        return;
      }
      wiRsvpPost(wiDeclineUrl, declineBtn)
        .then(function () {
          wiShowRsvpDeclined();
          if (declineBtn) declineBtn.disabled = false;
        })
        .catch(function (err) {
          alert(err.message || 'حدث خطأ أثناء رفض الدعوة');
        });
    });
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', wiBindRsvpButtons);
} else {
  wiBindRsvpButtons();
}

function countdown() {
  var target = new Date(@json($wiCountdownIso));
  var now = new Date();
  var diff = target - now;
  ['cd-days','cd-hours','cd-mins','cd-secs'].forEach(function (id) {
    var el = document.getElementById(id);
    if (!el) return;
    if (diff <= 0) { el.textContent = '00'; return; }
  });
  if (diff <= 0) return;
  var d = Math.floor(diff / 86400000);
  var h = Math.floor((diff % 86400000) / 3600000);
  var m = Math.floor((diff % 3600000) / 60000);
  var s = Math.floor((diff % 60000) / 1000);
  var map = { 'cd-days': d, 'cd-hours': h, 'cd-mins': m, 'cd-secs': s };
  Object.keys(map).forEach(function (id) {
    var el = document.getElementById(id);
    if (el) el.textContent = String(map[id]).padStart(2, '0');
  });
}
if (typeof window.ibStartCountdown === 'function') {
  window.ibStartCountdown();
} else {
  countdown();
  setInterval(countdown, 1000);
}

window.wiEnsureHeroVideosPlay = function () {
  document.querySelectorAll('.wi-hero-video').forEach(function (video) {
    video.muted = true;
    video.setAttribute('playsinline', '');
    video.setAttribute('webkit-playsinline', '');
    var playPromise = video.play();
    if (playPromise && typeof playPromise.catch === 'function') {
      playPromise.catch(function () {});
    }
  });
};

function wiBindHeroVideoPlayback() {
  if (window.wiEnsureHeroVideosPlay) window.wiEnsureHeroVideosPlay();
  document.querySelectorAll('.wi-hero-video').forEach(function (video) {
    if (video.dataset.wiPlayBound === '1') return;
    video.dataset.wiPlayBound = '1';
    video.addEventListener('loadeddata', function () {
      if (window.wiEnsureHeroVideosPlay) window.wiEnsureHeroVideosPlay();
    });
    video.addEventListener('canplay', function () {
      if (window.wiEnsureHeroVideosPlay) window.wiEnsureHeroVideosPlay();
    });
  });
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', wiBindHeroVideoPlayback);
} else {
  wiBindHeroVideoPlayback();
}
</script>
