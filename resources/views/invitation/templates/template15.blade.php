<style>
* {
	margin: 0;
	padding: 0;
	box-sizing: border-box;
}

body {
	background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
	min-height: 100vh;
	display: flex;
	justify-content: center;
	align-items: center;
	font-family: 'Courier New', monospace;
	padding: 20px;
}

.typewriter-container {
	background: #f4f1e8;
	padding: 60px 50px;
	border-radius: 10px;
	box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
	max-width: 700px;
	width: 100%;
	position: relative;
}

.paper-texture {
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background-image:
		repeating-linear-gradient(0deg,
			transparent,
			transparent 29px,
			rgba(0, 0, 0, 0.03) 29px,
			rgba(0, 0, 0, 0.03) 30px);
	pointer-events: none;
	border-radius: 10px;
}

.text-content {
	position: relative;
	z-index: 1;
	font-size: 18px;
	line-height: 1.8;
	color: #2c2c2c;
	white-space: pre-wrap;
	word-wrap: break-word;
}

.cursor {
	display: inline-block;
	width: 2px;
	height: 1.2em;
	background-color: #2c2c2c;
	margin-left: 2px;
	animation: blink 0.7s infinite;
	vertical-align: text-bottom;
}

@keyframes blink {

	0%,
	49% {
		opacity: 1;
	}

	50%,
	100% {
		opacity: 0;
	}
}

.controls {
	position: relative;
	z-index: 1;
	margin-top: 30px;
	display: flex;
	gap: 10px;
	justify-content: center;
}

button {
	padding: 12px 24px;
	font-family: 'Courier New', monospace;
	font-size: 14px;
	background: #2c2c2c;
	color: #f4f1e8;
	border: none;
	border-radius: 5px;
	cursor: pointer;
	transition: all 0.3s;
}

button:hover {
	background: #444;
	transform: translateY(-2px);
}

button:active {
	transform: translateY(0);
}

button:disabled {
	background: #999;
	cursor: not-allowed;
	transform: none;
}

.response-buttons {
	display: none;
	flex-direction: column;
	gap: 15px;
	margin-top: 40px;
	justify-content: center;
	align-items: center;
	opacity: 0;
	transform: translateY(20px);
	transition: opacity 0.5s ease, transform 0.5s ease;
}

.response-buttons.show {
	display: flex;
	opacity: 1;
	transform: translateY(0);
}

.btn {
	padding: 12px 24px;
	font-family: 'Courier New', monospace;
	font-size: 14px;
	background: #2c2c2c;
	color: #f4f1e8;
	border: none;
	border-radius: 5px;
	cursor: pointer;
	transition: all 0.3s;
	min-width: 180px;
	text-align: center;
}

.btn-accept {
	background: linear-gradient(135deg, #4ade80, #22c55e);
	color: white;
	box-shadow: 0 4px 15px rgba(34, 197, 94, 0.4);
}

.btn-accept:hover {
	background: linear-gradient(135deg, #22c55e, #16a34a);
	transform: translateY(-2px);
	box-shadow: 0 8px 25px rgba(34, 197, 94, 0.5);
}

.btn-decline {
	background: transparent;
	color: #ef4444;
	border: 2px solid #ef4444;
	box-shadow: 0 4px 15px rgba(239, 68, 68, 0.2);
}

.btn-decline:hover {
	background: #ef4444;
	color: white;
	transform: translateY(-2px);
	box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
}
</style>

<div class="typewriter-container">
	<div class="paper-texture"></div>
	<div class="text-content" id="text"></div>
	<div class="controls">
		<button id="startBtn">{{ __('admin.start-typing') }}</button>
		<button id="resetBtn">{{ __('admin.reset') }}</button>
	</div>
</div>

@php
$invitationText = __('admin.you-are-cordially-invited-to') . "\n\n";
$invitationText .= strtoupper($invitation->event_name ?? __('admin.an-evening-of-enchantment')) . "\n\n";
if($invitation->date) {
$invitationText .= __('admin.date') . ' ' . $invitation->date . "\n";
}
if($invitation->time) {
$invitationText .= __('admin.at') . ' ' . \Carbon\Carbon::parse($invitation->time)->format('h:i A') . "\n\n";
}
if($invitation->address) {
$invitationText .= __('admin.address') . ': ' . $invitation->address . "\n\n";
}
if($invitation->description) {
$invitationText .= __('admin.description') . ': ' . $invitation->description . "\n\n";
}
if(isset($host_name) && $host_name) {
$invitationText .= __('admin.host-name') . ': ' . $host_name . "\n\n";
}
if($invitation->groom || $invitation->bride) {
if($invitation->groom && $invitation->bride) {
$invitationText .= __('admin.groom') . ': ' . $invitation->groom . "\n";
$invitationText .= __('admin.bride') . ': ' . $invitation->bride . "\n\n";
} elseif($invitation->groom) {
$invitationText .= __('admin.groom') . ': ' . $invitation->groom . "\n\n";
} elseif($invitation->bride) {
$invitationText .= __('admin.bride') . ': ' . $invitation->bride . "\n\n";
}
}
$invitationText .= __('admin.we-look-forward-to-your-presence');
$invitationTextJs = addslashes($invitationText);

// Generate response buttons HTML
$responseButtonsHTML = '<div class="response-buttons" id="responseButtons">';
	$responseButtonsHTML .= '<button class="btn btn-accept" onclick="acceptInvitation()">✓ ' .
		__('admin.accept-invitation') . '</button>';
	$responseButtonsHTML .= '<button class="btn btn-decline" onclick="declineInvitation()">✗ ' .
		__('admin.refuse-invitation') . '</button>';
	$responseButtonsHTML .= '</div>';
$responseButtonsHTMLJs = addslashes($responseButtonsHTML);
@endphp
<script>
const invitationText = `{!! $invitationTextJs !!}`;
const responseButtonsHTML = `{!! $responseButtonsHTMLJs !!}`;

const textEl = document.getElementById('text');
const startBtn = document.getElementById('startBtn');
const resetBtn = document.getElementById('resetBtn');

let typeInterval;
let charIndex = 0;
let isTyping = false;

// Create typewriter sound using Web Audio API
const audioContext = new(window.AudioContext || window.webkitAudioContext)();

function playTypeSound() {
	const osc = audioContext.createOscillator();
	const gainNode = audioContext.createGain();

	osc.connect(gainNode);
	gainNode.connect(audioContext.destination);

	// Randomize frequency slightly for variety
	osc.frequency.value = 800 + Math.random() * 200;
	osc.type = 'square';

	gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
	gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.05);

	osc.start(audioContext.currentTime);
	osc.stop(audioContext.currentTime + 0.05);
}

function playCarriageReturn() {
	const osc = audioContext.createOscillator();
	const gainNode = audioContext.createGain();

	osc.connect(gainNode);
	gainNode.connect(audioContext.destination);

	osc.frequency.setValueAtTime(200, audioContext.currentTime);
	osc.frequency.exponentialRampToValueAtTime(150, audioContext.currentTime + 0.1);
	osc.type = 'sawtooth';

	gainNode.gain.setValueAtTime(0.15, audioContext.currentTime);
	gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);

	osc.start(audioContext.currentTime);
	osc.stop(audioContext.currentTime + 0.1);
}

function typeWriter() {
	if (charIndex < invitationText.length) {
		const char = invitationText.charAt(charIndex);

		// Add character
		const currentText = invitationText.substring(0, charIndex + 1);
		textEl.innerHTML = currentText + '<span class="cursor"></span>';

		// Play appropriate sound
		if (char === '\n') {
			playCarriageReturn();
		} else {
			playTypeSound();
		}

		charIndex++;

		// Variable typing speed for more natural feel
		const baseSpeed = 80;
		const variance = Math.random() * 40;
		const pauseAfterPunctuation = char.match(/[.!?,;:]/) ? 300 : 0;
		const nextDelay = baseSpeed + variance + pauseAfterPunctuation;

		typeInterval = setTimeout(typeWriter, nextDelay);
	} else {
		// Typing complete
		isTyping = false;
		startBtn.disabled = false;
		startBtn.textContent = 'Start Typing';

		// Show typed text with cursor
		textEl.innerHTML = invitationText + '<span class="cursor"></span>' + responseButtonsHTML;

		// Show buttons with animation after a short delay
		setTimeout(() => {
			const buttons = textEl.querySelector('.response-buttons');
			if (buttons) {
				buttons.classList.add('show');
			}
		}, 500);
	}
}

function startTyping() {
	if (isTyping) return;

	// Resume audio context if needed (browser autoplay policy)
	if (audioContext.state === 'suspended') {
		audioContext.resume();
	}

	isTyping = true;
	startBtn.disabled = true;
	startBtn.textContent = 'Typing...';

	typeWriter();
}

function reset() {
	clearTimeout(typeInterval);
	charIndex = 0;
	isTyping = false;
	// Reset with cursor only
	textEl.innerHTML = '<span class="cursor"></span>';
	startBtn.disabled = false;
	startBtn.textContent = 'Start Typing';
}

startBtn.addEventListener('click', startTyping);
resetBtn.addEventListener('click', reset);

// Initialize with cursor
reset();
</script>