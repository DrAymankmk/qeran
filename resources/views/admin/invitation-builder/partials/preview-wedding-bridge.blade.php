{{-- Persistent preview helpers (survive soft DOM patches without full iframe reload) --}}
<script>
(function () {
	var countdownTimer = null;

	window.wiOpenEnvelope = function () {
		var gate = document.getElementById('wiEnvelopeGate');
		var main = document.getElementById('wiMainContent');
		if (!gate) return;
		gate.classList.add('is-opening');
		if (window.wiPlayOpeningAudio) {
			window.wiPlayOpeningAudio();
		}
		setTimeout(function () {
			gate.classList.add('is-open');
			if (main) {
				main.classList.remove('is-gated');
				main.style.display = 'block';
			}
			if (window.wiEnsureHeroVideosPlay) {
				window.wiEnsureHeroVideosPlay();
			}
		}, 900);
	};

	function runCountdown() {
		var main = document.getElementById('wiMainContent');
		if (!main) return;
		var iso = main.getAttribute('data-wi-countdown');
		if (!iso) return;
		var target = new Date(iso);
		var diff = target - new Date();
		['cd-days', 'cd-hours', 'cd-mins', 'cd-secs'].forEach(function (id) {
			var el = document.getElementById(id);
			if (!el) return;
			if (diff <= 0) {
				el.textContent = '00';
				return;
			}
		});
		if (diff <= 0) return;
		var map = {
			'cd-days': Math.floor(diff / 86400000),
			'cd-hours': Math.floor((diff % 86400000) / 3600000),
			'cd-mins': Math.floor((diff % 3600000) / 60000),
			'cd-secs': Math.floor((diff % 60000) / 1000),
		};
		Object.keys(map).forEach(function (id) {
			var el = document.getElementById(id);
			if (el) el.textContent = String(map[id]).padStart(2, '0');
		});
	}

	window.ibStartCountdown = function () {
		if (countdownTimer) clearInterval(countdownTimer);
		runCountdown();
		countdownTimer = setInterval(runCountdown, 1000);
	};

	window.ibCapturePreviewState = function () {
		var gate = document.getElementById('wiEnvelopeGate');
		var main = document.getElementById('wiMainContent');
		var envelopeOpen = !gate
			|| gate.classList.contains('is-open')
			|| gate.classList.contains('is-opening')
			|| (main && !main.classList.contains('is-gated'));
		return {
			scrollY: window.scrollY || document.documentElement.scrollTop || 0,
			envelopeOpen: envelopeOpen,
		};
	};

	window.ibRestorePreviewState = function (state) {
		if (!state) return;
		if (state.envelopeOpen) {
			var gate = document.getElementById('wiEnvelopeGate');
			var main = document.getElementById('wiMainContent');
			if (gate) {
				gate.classList.add('is-open');
				gate.classList.remove('is-opening');
			}
			if (main) {
				main.classList.remove('is-gated');
				main.style.display = 'block';
			}
		}
		requestAnimationFrame(function () {
			window.scrollTo(0, state.scrollY || 0);
		});
	};

	window.ibAfterPreviewPatch = function () {
		window.ibStartCountdown();
		if (typeof wiBindRsvpButtons === 'function') {
			wiBindRsvpButtons();
		}
		if (window.wiEnsureHeroVideosPlay) {
			window.wiEnsureHeroVideosPlay();
		}
		document.querySelectorAll('.wi-fade-in').forEach(function (el, i) {
			el.style.animationDelay = (i * 0.05) + 's';
		});
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', function () {
			if (window.wiEnsureHeroVideosPlay) {
				window.wiEnsureHeroVideosPlay();
			}
		});
	} else if (window.wiEnsureHeroVideosPlay) {
		window.wiEnsureHeroVideosPlay();
	}
})();
</script>
