<style>
* {
	margin: 0;
	padding: 0;
	box-sizing: border-box;
}

body {
	font-family: 'Georgia', serif;
	display: flex;
	justify-content: center;
	align-items: center;
	min-height: 100vh;
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	padding: 20px;
}

.container {
	position: relative;
	max-width: 800px;
	width: 100%;
}

.image-wrapper {
	position: relative;
	width: 100%;
	background: white;
	border-radius: 20px;
	box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
	overflow: hidden;
}

.main-image {
	width: 100%;
	height: 500px;
	background: linear-gradient(180deg, #ffecd2 0%, #fcb69f 100%);
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: center;
	position: relative;
	cursor: pointer;
}

.scene {
	width: 100%;
	height: 100%;
	position: relative;
	overflow: hidden;
}

.star {
	position: absolute;
	font-size: 30px;
	animation: twinkle 2s infinite;
	cursor: pointer;
	user-select: none;
}

.moon {
	position: absolute;
	width: 80px;
	height: 80px;
	background: #fff9e6;
	border-radius: 50%;
	top: 50px;
	right: 80px;
	box-shadow: 0 0 40px rgba(255, 249, 230, 0.8);
	cursor: pointer;
}

.tree {
	position: absolute;
	bottom: 0;
	width: 0;
	height: 0;
	border-left: 40px solid transparent;
	border-right: 40px solid transparent;
	border-bottom: 100px solid #2d5016;
	cursor: pointer;
}

.tree:nth-child(2) {
	left: 50px;
}

.tree:nth-child(3) {
	left: 150px;
}

.tree:nth-child(4) {
	right: 50px;
}

.cloud {
	position: absolute;
	background: white;
	width: 100px;
	height: 40px;
	border-radius: 50px;
	cursor: pointer;
	opacity: 0.9;
}

.cloud::before,
.cloud::after {
	content: '';
	position: absolute;
	background: white;
	border-radius: 50%;
}

.cloud::before {
	width: 50px;
	height: 50px;
	top: -25px;
	left: 10px;
}

.cloud::after {
	width: 60px;
	height: 60px;
	top: -30px;
	right: 10px;
}

.hint-text {
	position: absolute;
	bottom: 20px;
	left: 50%;
	transform: translateX(-50%);
	color: #5a3e2b;
	font-size: 18px;
	font-style: italic;
	text-shadow: 2px 2px 4px rgba(255, 255, 255, 0.7);
	z-index: 10;
}

@keyframes twinkle {

	0%,
	100% {
		opacity: 1;
		transform: scale(1);
	}

	50% {
		opacity: 0.5;
		transform: scale(0.8);
	}
}

.invitation-modal {
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background: rgba(0, 0, 0, 0.8);
	display: none;
	justify-content: center;
	align-items: center;
	z-index: 1000;
	animation: fadeIn 0.5s;
}

.invitation-modal.active {
	display: flex;
}

@keyframes fadeIn {
	from {
		opacity: 0;
	}

	to {
		opacity: 1;
	}
}

.invitation-card {
	background: linear-gradient(135deg, #fdfcfb 0%, #e2d1c3 100%);
	padding: 50px;
	border-radius: 20px;
	max-width: 600px;
	width: 100%;
	text-align: center;
	box-shadow: 0 30px 80px rgba(0, 0, 0, 0.5);
	animation: slideUp 0.5s;
	position: relative;
	height: 700px;
}

@keyframes slideUp {
	from {
		transform: translateY(50px);
		opacity: 0;
	}

	to {
		transform: translateY(0);
		opacity: 1;
	}
}

.invitation-card h1 {
	font-size: 42px;
	color: #5a3e2b;
	margin-bottom: 20px;
	font-weight: normal;
}

.invitation-card .subtitle {
	font-size: 24px;
	color: #8b6f47;
	margin-bottom: 30px;
	font-style: italic;
}

.invitation-card .details {
	font-size: 18px;
	color: #5a3e2b;
	line-height: 1.8;
	margin-bottom: 30px;
}

.invitation-card .details strong {
	color: #3d2817;
}

.close-btn {
	background: #667eea;
	color: white;
	border: none;
	padding: 12px 30px;
	border-radius: 25px;
	font-size: 16px;
	cursor: pointer;
	transition: all 0.3s;
}

.close-btn:hover {
	background: #764ba2;
	transform: translateY(-2px);
	box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
}

.attempts {
	position: absolute;
	top: 20px;
	right: 20px;
	background: rgba(255, 255, 255, 0.9);
	padding: 10px 20px;
	border-radius: 20px;
	font-size: 14px;
	color: #5a3e2b;
	z-index: 10;
}

.feedback {
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	background: rgba(255, 255, 255, 0.95);
	padding: 20px 40px;
	border-radius: 15px;
	font-size: 18px;
	color: #d32f2f;
	display: none;
	z-index: 20;
	animation: shake 0.5s;
}

@keyframes shake {

	0%,
	100% {
		transform: translate(-50%, -50%) rotate(0deg);
	}

	25% {
		transform: translate(-50%, -50%) rotate(-5deg);
	}

	75% {
		transform: translate(-50%, -50%) rotate(5deg);
	}
}

.feedback.show {
	display: block;
}

.response-buttons {
	position: absolute;
	/* bottom: -80px; */
	left: 50%;
	transform: translateX(-50%);
	display: flex;
	gap: 20px;
	z-index: 15;
	width: 100%;
	max-width: 500px;
	justify-content: center;
	margin-top: 20px;
}

.btn {
	padding: 14px 28px;
	border: none;
	border-radius: 12px;
	font-size: 15px;
	font-weight: 600;
	cursor: pointer;
	transition: all 0.3s ease;
	min-width: 160px;
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
<div class="container">
	<div class="image-wrapper">
		<div class="attempts">Attempts: <span id="attemptCount">0</span></div>
		<div class="main-image">
			<div class="scene">
				<div class="moon" data-hotspot="correct"></div>
				<div class="star" style="top: 80px; left: 100px;">⭐</div>
				<div class="star" style="top: 120px; left: 250px;">⭐</div>
				<div class="star" style="top: 60px; right: 150px;">⭐</div>
				<div class="star" style="top: 150px; right: 80px;">⭐</div>
				<div class="cloud" style="top: 100px; left: 50px;"></div>
				<div class="cloud" style="top: 150px; right: 100px;"></div>
				<div class="tree"></div>
				<div class="tree"></div>
				<div class="tree"></div>
				<div class="tree"></div>
			</div>
			<div class="hint-text">{{__('admin.find-the-hidden-secret-to-reveal-your-invitation')}}
			</div>
		</div>
		<div class="feedback" id="feedback">{{__('admin.try-again')}}! {{__('admin.keep-searching')}}...
		</div>
	</div>


</div>

<div class="invitation-modal" id="invitationModal">
	<div class="invitation-card">
		<h1>🌙 {{__('admin.you-found-it')}} 🌙</h1>
		<div class="subtitle">{{__('admin.you-are-invited')}}!</div>
		<div class="details">
			<strong>{{$invitation->event_name ?? __('admin.join-us-for-a-magical-evening')}}</strong><br>
			@if($invitation->description)
			{{$invitation->description}}<br><br>
			@endif
			@if($invitation->date)
			<strong>{{__('admin.date')}}:</strong> {{$invitation->date}}<br>
			@endif
			@if($invitation->time)
			<strong>{{__('admin.time')}}:</strong>
			{{ \Carbon\Carbon::parse($invitation->time)->format('h:i A') }}<br>
			@endif
			@if($invitation->address)
			<strong>{{__('admin.address')}}:</strong><br>
			{!! nl2br(e($invitation->address)) !!}<br>
			@endif
			@if(isset($host_name) && $host_name)
			<br><strong>{{__('admin.host-name')}}:</strong> {{$host_name}}<br>
			@endif
			@if($invitation->groom || $invitation->bride)
			@if($invitation->groom && $invitation->bride)
			<br><strong>{{__('admin.groom')}}:</strong> {{$invitation->groom}}<br>
			<strong>{{__('admin.bride')}}:</strong> {{$invitation->bride}}<br>
			@elseif($invitation->groom)
			<br><strong>{{__('admin.groom')}}:</strong> {{$invitation->groom}}<br>
			@elseif($invitation->bride)
			<br><strong>{{__('admin.bride')}}:</strong> {{$invitation->bride}}<br>
			@endif
			@endif


		</div>
		<button class="close-btn" id="closeBtn">{{__('admin.close')}}</button>

		<div class="response-buttons">
			<button class="btn btn-accept" onclick="acceptInvitation()">✓
				{{__('admin.accept-invitation')}}</button>
			<button class="btn btn-decline" onclick="declineInvitation()">✗
				{{__('admin.refuse-invitation')}}</button>
		</div>
	</div>
</div>

<script>
let attempts = 0;
const attemptCount = document.getElementById('attemptCount');
const invitationModal = document.getElementById('invitationModal');
const closeBtn = document.getElementById('closeBtn');
const feedback = document.getElementById('feedback');
const scene = document.querySelector('.scene');

// Add click listeners to all elements in the scene
const clickableElements = scene.querySelectorAll('*');

clickableElements.forEach(el => {
	el.addEventListener('click', (e) => {
		e.stopPropagation();

		if (el.dataset.hotspot === 'correct') {
			// Correct hotspot clicked - show invitation
			invitationModal.classList.add('active');
		} else {
			// Wrong hotspot clicked
			attempts++;
			attemptCount.textContent = attempts;

			// Show feedback
			feedback.classList.add('show');
			setTimeout(() => {
				feedback.classList
					.remove(
						'show'
					);
			}, 1000);
		}
	});

	// Add hover effect
	el.addEventListener('mouseenter', () => {
		el.style.transform = 'scale(1.1)';
		el.style.transition = 'transform 0.2s';
	});

	el.addEventListener('mouseleave', () => {
		el.style.transform = 'scale(1)';
	});
});

// Close modal
closeBtn.addEventListener('click', () => {
	invitationModal.classList.remove('active');
});

// Close modal when clicking outside
invitationModal.addEventListener('click', (e) => {
	if (e.target === invitationModal) {
		invitationModal.classList.remove('active');
	}
});
</script>