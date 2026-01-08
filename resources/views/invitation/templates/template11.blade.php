<style>
* {
	margin: 0;
	padding: 0;
	box-sizing: border-box;
}

body {
	font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
	background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
	min-height: 100vh;
	display: flex;
	align-items: center;
	justify-content: center;
	overflow: hidden;
}

.container {
	text-align: center;
	position: relative;
	width: 500px !important;
	display: block;
}

.scanner-wrapper {
	position: relative;
	width: 280px;
	height: 280px;
	margin: 0 auto 30px;
}

.scanner-frame {
	width: 100%;
	height: 100%;
	background: rgba(255, 255, 255, 0.05);
	border: 3px solid rgba(0, 255, 255, 0.3);
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
	position: relative;
	cursor: pointer;
	transition: all 0.3s ease;
	backdrop-filter: blur(10px);
}

.scanner-frame:hover {
	border-color: rgba(0, 255, 255, 0.6);
	box-shadow: 0 0 30px rgba(0, 255, 255, 0.3);
	transform: scale(1.05);
}

.scanner-frame.scanning {
	border-color: #00ffff;
	box-shadow: 0 0 40px rgba(0, 255, 255, 0.5);
	animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {

	0%,
	100% {
		transform: scale(1);
	}

	50% {
		transform: scale(1.03);
	}
}

.fingerprint {
	width: 150px;
	height: 150px;
	position: relative;
}

.fingerprint svg {
	width: 100%;
	height: 100%;
	fill: none;
	stroke: rgba(0, 255, 255, 0.6);
	stroke-width: 2;
	stroke-linecap: round;
}

.scanner-frame.scanning .fingerprint svg {
	stroke: #00ffff;
	filter: drop-shadow(0 0 10px rgba(0, 255, 255, 0.8));
}

.scan-line {
	position: absolute;
	width: 100%;
	height: 3px;
	background: linear-gradient(90deg,
			transparent 0%,
			rgba(0, 255, 255, 0.3) 20%,
			#00ffff 50%,
			rgba(0, 255, 255, 0.3) 80%,
			transparent 100%);
	top: 0;
	left: 0;
	opacity: 0;
	box-shadow: 0 0 20px rgba(0, 255, 255, 0.8);
}

.scanner-frame.scanning .scan-line {
	animation: scan 2s linear infinite;
	opacity: 1;
}

@keyframes scan {
	0% {
		top: 0;
	}

	100% {
		top: 100%;
	}
}

.status-text {
	color: rgba(255, 255, 255, 0.7);
	font-size: 18px;
	margin-top: 20px;
	transition: all 0.3s ease;
}

.scanner-frame.scanning+.status-text {
	color: #00ffff;
	text-shadow: 0 0 10px rgba(0, 255, 255, 0.5);
}

.progress-bar {
	width: 0;
	height: 4px;
	background: linear-gradient(90deg, #00ffff, #00ff88);
	margin-top: 15px;
	border-radius: 2px;
	transition: width 2s linear;
	box-shadow: 0 0 10px rgba(0, 255, 255, 0.5);
}

.invitation {
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%) scale(0);
	background: linear-gradient(135deg, #0f3460 0%, #16213e 100%);
	padding: 40px 50px;
	border-radius: 20px;
	border: 2px solid #00ffff;
	box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5), 0 0 40px rgba(0, 255, 255, 0.3);
	opacity: 0;
	transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
	max-width: 500px;
	width: 90%;
}

.invitation.unlocked {
	transform: translate(-50%, -50%) scale(1);
	opacity: 1;
}

.invitation h1 {
	color: #00ffff;
	font-size: 32px;
	margin-bottom: 20px;
	text-shadow: 0 0 20px rgba(0, 255, 255, 0.5);
}

.invitation p {
	color: rgba(255, 255, 255, 0.9);
	font-size: 18px;
	line-height: 1.6;
	margin-bottom: 15px;
}

.invitation-details {
	margin-top: 25px;
	padding-top: 25px;
	border-top: 1px solid rgba(0, 255, 255, 0.3);
	color: rgba(255, 255, 255, 0.8);
	font-size: 16px;
}

.checkmark {
	width: 60px;
	height: 60px;
	margin: 0 auto 20px;
	border-radius: 50%;
	border: 3px solid #00ffff;
	display: flex;
	align-items: center;
	justify-content: center;
	animation: checkmark-appear 0.5s ease forwards;
}

@keyframes checkmark-appear {
	from {
		transform: scale(0) rotate(-45deg);
		opacity: 0;
	}

	to {
		transform: scale(1) rotate(0deg);
		opacity: 1;
	}
}

.checkmark svg {
	width: 35px;
	height: 35px;
	stroke: #00ffff;
	stroke-width: 3;
	stroke-linecap: round;
	stroke-linejoin: round;
	fill: none;
	stroke-dasharray: 50;
	stroke-dashoffset: 50;
	animation: checkmark-draw 0.5s 0.3s ease forwards;
}

@keyframes checkmark-draw {
	to {
		stroke-dashoffset: 0;
	}
}

.reset-btn {
	margin-top: 25px;
	padding: 12px 30px;
	background: transparent;
	border: 2px solid #00ffff;
	color: #00ffff;
	border-radius: 25px;
	cursor: pointer;
	font-size: 16px;
	transition: all 0.3s ease;
}

.reset-btn:hover {
	background: #00ffff;
	color: #16213e;
	box-shadow: 0 0 20px rgba(0, 255, 255, 0.5);
}

.response-buttons {
	display: flex;
	gap: 15px;
	margin-top: 30px;
	justify-content: center;
}

.btn {
	padding: 12px 28px;
	border: none;
	border-radius: 25px;
	font-size: 14px;
	font-weight: 600;
	cursor: pointer;
	transition: all 0.3s ease;
	min-width: 150px;
	font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.btn-accept {
	background: linear-gradient(135deg, #00ff88, #00ffff);
	color: #16213e;
	border: 2px solid #00ffff;
	box-shadow: 0 0 20px rgba(0, 255, 255, 0.3);
}

.btn-accept:hover {
	background: linear-gradient(135deg, #00ffff, #00ff88);
	transform: translateY(-2px);
	box-shadow: 0 0 30px rgba(0, 255, 255, 0.5);
}

.btn-decline {
	background: transparent;
	color: #ff6b6b;
	border: 2px solid #ff6b6b;
	box-shadow: 0 0 20px rgba(255, 107, 107, 0.3);
}

.btn-decline:hover {
	background: #ff6b6b;
	color: white;
	transform: translateY(-2px);
	box-shadow: 0 0 30px rgba(255, 107, 107, 0.5);
}
</style>

<div class="container">
	<div class="scanner-wrapper">
		<div class="scanner-frame" id="scanner">
			<div class="fingerprint">
				<svg viewBox="0 0 100 100">
					<path d="M50,20 Q35,20 25,30 Q20,38 20,50 Q20,65 30,75" />
					<path d="M50,25 Q38,25 30,35 Q25,42 25,50 Q25,62 33,70" />
					<path d="M50,30 Q40,30 35,38 Q30,45 30,50 Q30,58 36,65" />
					<path d="M50,35 Q43,35 39,42 Q35,47 35,50 Q35,55 39,60" />
					<path d="M50,20 Q65,20 75,30 Q80,38 80,50 Q80,65 70,75" />
					<path d="M50,25 Q62,25 70,35 Q75,42 75,50 Q75,62 67,70" />
					<path d="M50,30 Q60,30 65,38 Q70,45 70,50 Q70,58 64,65" />
					<path d="M50,35 Q57,35 61,42 Q65,47 65,50 Q65,55 61,60" />
				</svg>
			</div>
			<div class="scan-line"></div>
		</div>
	</div>
	<div class="status-text" id="statusText">Place finger to scan</div>
	<div class="progress-bar" id="progressBar"></div>

	<div class="invitation" id="invitation">
		<div class="checkmark">
			<svg viewBox="0 0 50 50">
				<polyline points="10,25 20,35 40,15" />
			</svg>
		</div>
		<h1>{{__('admin.you-are-invited')}}!</h1>
		<p>{{$invitation->event_name ?? __('admin.you-are-cordially-invited-to')}}</p>
		<div class="invitation-details">
			@if($invitation->event_name)
			<p><strong>{{__('admin.event-name')}}:</strong> {{$invitation->event_name}}</p>
			@endif

			@if($invitation->date)
			<p><strong>{{__('admin.date')}}:</strong> {{$invitation->date}}</p>
			@endif

			@if($invitation->time)
			<p><strong>{{__('admin.time')}}:</strong>
				{{ \Carbon\Carbon::parse($invitation->time)->format('h:i A') }}</p>
			@endif

			@if($invitation->address)
			<p><strong>{{__('admin.address')}}:</strong> {!! nl2br(e($invitation->address)) !!}</p>
			@endif

			@if(isset($host_name) && $host_name)
			<p><strong>{{__('admin.host-name')}}:</strong> {{$host_name}}</p>
			@endif

			@if($invitation->groom || $invitation->bride)
			@if($invitation->groom && $invitation->bride)
			<p><strong>{{__('admin.groom')}}:</strong> {{$invitation->groom}}</p>
			<p><strong>{{__('admin.bride')}}:</strong> {{$invitation->bride}}</p>
			@elseif($invitation->groom)
			<p><strong>{{__('admin.groom')}}:</strong> {{$invitation->groom}}</p>
			@elseif($invitation->bride)
			<p><strong>{{__('admin.bride')}}:</strong> {{$invitation->bride}}</p>
			@endif
			@endif

			@if($invitation->description)
			<p style="margin-top: 15px; font-style: italic;">{{$invitation->description}}</p>
			@endif
		</div>

		<div class="response-buttons">
			<button class="btn btn-accept" onclick="acceptInvitation()">✓
				{{__('admin.accept-invitation')}}</button>
			<button class="btn btn-decline" onclick="declineInvitation()">✗
				{{__('admin.refuse-invitation')}}</button>
		</div>

		<button class="reset-btn" id="resetBtn">Scan Again</button>
	</div>
</div>

<script>
const scanner = document.getElementById('scanner');
const statusText = document.getElementById('statusText');
const progressBar = document.getElementById('progressBar');
const invitation = document.getElementById('invitation');
const resetBtn = document.getElementById('resetBtn');

let isScanning = false;
let scanTimeout;

scanner.addEventListener('click', startScan);

function startScan() {
	if (isScanning) return;

	isScanning = true;
	scanner.classList.add('scanning');
	statusText.textContent = 'Scanning fingerprint...';
	progressBar.style.width = '100%';

	scanTimeout = setTimeout(() => {
		completeScan();
	}, 2000);
}

function completeScan() {
	scanner.classList.remove('scanning');
	statusText.textContent = 'Verification successful!';
	progressBar.style.width = '100%';

	setTimeout(() => {
		invitation.classList.add('unlocked');
	}, 300);
}

resetBtn.addEventListener('click', () => {
	invitation.classList.remove('unlocked');
	scanner.classList.remove('scanning');
	statusText.textContent = 'Place finger to scan';
	progressBar.style.width = '0';
	isScanning = false;
	clearTimeout(scanTimeout);
});
</script>
