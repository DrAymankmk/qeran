<script>
function wiSubmitRsvpAccept() {
  if (typeof acceptInvitation === 'function') {
    acceptInvitation();
    return;
  }
  var btn = document.querySelector('.wi-rsvp-submit');
  if (btn) {
    btn.textContent = 'جاري الإرسال…';
    btn.disabled = true;
  }
  fetch(@json($routes['accept'] ?? ''), {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': @json(csrf_token()) },
  })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (data.success) {
        document.getElementById('wiStatusAccepted')?.classList.add('active');
      } else {
        alert(data.message || 'حدث خطأ');
        if (btn) { btn.disabled = false; btn.textContent = 'تأكيد الحضور'; }
      }
    })
    .catch(function () {
      alert('حدث خطأ');
      if (btn) { btn.disabled = false; btn.textContent = 'تأكيد الحضور'; }
    });
}

function setRsvp(val, el) {
  document.querySelectorAll('.wi-rsvp-opt').forEach(function (o) { o.classList.remove('active'); });
  if (el) el.classList.add('active');
  var meal = document.getElementById('mealField');
  if (meal) meal.style.display = val === 'yes' ? '' : 'none';
  if (val === 'no') {
    if (typeof declineInvitation === 'function') {
      declineInvitation();
    } else {
      fetch(@json($routes['decline'] ?? ''), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': @json(csrf_token()) },
      })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          if (data.success) document.getElementById('wiStatusDeclined')?.classList.add('active');
        });
    }
  }
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
</script>
