<style>
* {
	margin: 0;
	padding: 0;
	box-sizing: border-box;
}

.template4-wrapper {
	min-height: 100vh;
	display: flex;
	justify-content: center;
	align-items: center;
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	font-family: 'Cairo', 'Arial', sans-serif;
	overflow: hidden;
	position: relative;
	padding: 20px;
}

.template4-container {
	position: relative;
	cursor: pointer;
	width: 100%;
	max-width: 600px;
	display: flex;
	justify-content: center;
	align-items: center;
}

.gift-box {
	position: relative;
	width: 200px;
	height: 200px;
	transition: transform 0.3s ease;
	z-index: 1;
}

.gift-box:hover:not(.opened) {
	animation: shake 0.5s ease-in-out infinite;
}

@keyframes shake {

	0%,
	100% {
		transform: rotate(0deg);
	}

	25% {
		transform: rotate(-2deg);
	}

	75% {
		transform: rotate(2deg);
	}
}

.box-base {
	position: absolute;
	bottom: 0;
	width: 200px;
	height: 140px;
	background: linear-gradient(145deg, #ff6b9d, #c44569);
	border-radius: 8px;
	box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.ribbon-vertical {
	position: absolute;
	width: 30px;
	height: 140px;
	background: linear-gradient(145deg, #ffd93d, #f6c23e);
	left: 50%;
	transform: translateX(-50%);
	bottom: 0;
}

.ribbon-horizontal {
	position: absolute;
	width: 200px;
	height: 30px;
	background: linear-gradient(145deg, #ffd93d, #f6c23e);
	top: 50%;
	transform: translateY(-50%);
}

.box-lid {
	position: absolute;
	top: 0;
	width: 220px;
	height: 60px;
	background: linear-gradient(145deg, #ff8fab, #d65780);
	border-radius: 8px;
	left: 50%;
	transform: translateX(-50%);
	box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
	transform-origin: bottom center;
	transition: transform 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
	z-index: 10;
}

.gift-box.opened .box-lid {
	transform: translateX(-50%) translateY(-120px) rotateX(-100deg);
}

.bow {
	position: absolute;
	top: -20px;
	left: 50%;
	transform: translateX(-50%);
	width: 80px;
	height: 80px;
}

.bow-loop {
	position: absolute;
	width: 35px;
	height: 35px;
	background: linear-gradient(145deg, #ffd93d, #f6c23e);
	border-radius: 50% 50% 0 50%;
	transform-origin: bottom right;
}

.bow-loop:first-child {
	left: 0;
	transform: rotate(-45deg);
}

.bow-loop:last-child {
	right: 0;
	transform: rotate(45deg) scaleX(-1);
}

.bow-center {
	position: absolute;
	width: 20px;
	height: 20px;
	background: linear-gradient(145deg, #ffed4e, #f6c23e);
	border-radius: 50%;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	z-index: 1;
}

.invitation-card {
	position: absolute;
	width: 100%;
	width: 550px;
	/* min-height: 600px; */
	background: linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(248, 249, 250, 0.95));
	backdrop-filter: blur(20px);
	border-radius: 25px;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%) scale(0.8);
	box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
	padding: 40px;
	text-align: center;
	opacity: 0;
	z-index: 100;
	transition: all 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55);
	pointer-events: none;
	overflow-y: auto;
	max-height: 90vh;
}

.gift-box.opened .invitation-card {
	opacity: 1;
	transform: translate(-50%, -50%) scale(1);
	pointer-events: all;
}

.invitation-card .close-btn {
	position: absolute;
	top: 15px;
	left: 15px;
	width: 40px;
	height: 40px;
	background: rgba(102, 126, 234, 0.2);
	border: 2px solid rgba(102, 126, 234, 0.4);
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
	cursor: pointer;
	transition: all 0.3s ease;
	color: #667eea;
	font-size: 24px;
	font-weight: bold;
	z-index: 10;
}

.invitation-card .close-btn:hover {
	background: rgba(102, 126, 234, 0.3);
	border-color: rgba(102, 126, 234, 0.6);
	transform: rotate(90deg);
}

.invitation-card h2 {
	color: #667eea;
	font-size: 2.5em;
	margin-bottom: 20px;
	font-weight: bold;
	font-family: 'Cairo', sans-serif;
}

.invitation-card .event-media-container {
	width: 100%;
	height: 280px;
	margin-bottom: 25px;
	border-radius: 15px;
	overflow: hidden;
	box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.invitation-card .event-image,
.invitation-card .event-video {
	width: 100%;
	height: 100%;
	object-fit: cover;
	border-radius: 15px;
}

.invitation-card .event-details {
	margin-top: 30px;
	text-align: right;
}

.invitation-card .detail-item {
	margin-bottom: 20px;
	padding: 15px;
	background: rgba(102, 126, 234, 0.1);
	border-radius: 12px;
	border-right: 3px solid #667eea;
}

.invitation-card .detail-label {
	font-weight: bold;
	font-size: 14px;
	margin-bottom: 8px;
	color: #667eea;
}

.invitation-card .detail-value {
	font-size: 16px;
	color: #2d3748;
	line-height: 1.6;
}

.invitation-card .response-buttons {
	display: flex;
	gap: 20px;
	margin-top: 30px;
	width: 100%;
}

.invitation-card .btn {
	flex: 1;
	padding: 18px 30px;
	border-radius: 12px;
	font-size: 1.2em;
	font-weight: 600;
	cursor: pointer;
	transition: all 0.3s ease;
	border: none;
	font-family: 'Cairo', sans-serif;
}

.invitation-card .btn-accept {
	background: linear-gradient(135deg, #4ade80, #22c55e);
	color: white;
	box-shadow: 0 4px 15px rgba(34, 197, 94, 0.4);
}

.invitation-card .btn-accept:hover {
	background: linear-gradient(135deg, #22c55e, #16a34a);
	transform: translateY(-2px);
	box-shadow: 0 6px 20px rgba(34, 197, 94, 0.5);
}

.invitation-card .btn-decline {
	background: transparent;
	color: #ef4444;
	border: 2px solid #ef4444;
	box-shadow: 0 4px 15px rgba(239, 68, 68, 0.2);
}

.invitation-card .btn-decline:hover {
	background: #ef4444;
	color: white;
	transform: translateY(-2px);
	box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
}

.confetti {
	position: fixed;
	width: 10px;
	height: 10px;
	pointer-events: none;
	z-index: 200;
}

@keyframes confettiFall {
	to {
		transform: translateY(100vh) rotate(360deg);
		opacity: 0;
	}
}

.instruction {
	position: absolute;
	bottom: -80px;
	left: 50%;
	transform: translateX(-50%);
	color: white;
	font-size: 18px;
	text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
	white-space: nowrap;
	opacity: 0;
	animation: fadeInOut 3s ease-in-out infinite;
	z-index: 2;
}

@keyframes fadeInOut {

	0%,
	100% {
		opacity: 0;
	}

	50% {
		opacity: 1;
	}
}

.gift-box.opened~.instruction {
	opacity: 0;
	animation: none;
}

@media (max-width: 768px) {
	.invitation-card {
		/* max-width: 95%; */
    width:300px;
		padding: 30px 20px;
		min-height: 500px;
	}

	.invitation-card h2 {
		font-size: 2em;
	}

	.invitation-card .event-media-container {
		height: 200px;
	}
}
</style>

<!-- Template 4: Gift Box Invitation View -->
<div id="envelopeView" class="invitation-wrapper template4-wrapper">
	<div class="template4-container">
		<div class="gift-box" id="giftBox">
			<div class="box-lid">
				<div class="bow">
					<div class="bow-loop"></div>
					<div class="bow-center"></div>
					<div class="bow-loop"></div>
				</div>
			</div>
			<div class="box-base">
				<div class="ribbon-vertical"></div>
				<div class="ribbon-horizontal"></div>
			</div>
			<div class="invitation-card" id="invitationCard">
				<button class="close-btn" onclick="closeTemplate4Card()"
					title="إغلاق">×</button>

				<h2>{{$invitation->event_name}}</h2>

				@if($invitation->image())
				<div class="event-media-container">
					@php
					$mediaUrl = $invitation->image();
					$extension = strtolower(pathinfo($mediaUrl, PATHINFO_EXTENSION));
					$videoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv', 'm4v',
					'3gp', 'wmv'];
					$isVideo = in_array($extension, $videoExtensions);
					@endphp

					@if($isVideo)
					<a href="{{$mediaUrl}}" target="_blank" rel="noopener noreferrer">
						<video class="event-video" autoplay muted loop playsinline
							preload="metadata"
							style="width: 100%; height: 100%; object-fit: cover;">
							<source src="{{$mediaUrl}}"
								type="video/{{$extension === 'mov' ? 'quicktime' : $extension}}">
						</video>
					</a>
					@else
					<a href="{{$mediaUrl}}" target="_blank" rel="noopener noreferrer">
						<img src="{{$mediaUrl}}" alt="{{$invitation->event_name}}"
							class="event-image" loading="lazy" />
					</a>
					@endif
				</div>
				@endif

				<div class="event-details">
					@if($invitation->description)
					<div class="detail-item">
						<div class="detail-label">{{__('admin.description')}}</div>
						<div class="detail-value">{{$invitation->description}}</div>
            
					</div>
					@endif

					@if($invitation->date)
					<div class="detail-item">
						<div class="detail-label">{{__('admin.date')}}</div>
						<div class="detail-value">{{$invitation->date}}</div>

           
					</div>
					@endif

					@if($invitation->time)
					<div class="detail-item">
						<div class="detail-label">{{__('admin.time')}}</div>
						<div class="detail-value">
							{{ \Carbon\Carbon::parse($invitation->time)->format('h:i A') }}
						</div>
					</div>
					@endif

					@if($invitation->address)
					<div class="detail-item">
						<div class="detail-label">{{__('admin.address')}}</div>
						<div class="detail-value">{{$invitation->address}}</div>
					</div>
					@endif

					@if($host_name)
					<div class="detail-item">
						<div class="detail-label">{{__('admin.host-name')}}</div>
						<div class="detail-value">{{$host_name}}</div>
					</div>
					@endif
				</div>

				<div class="response-buttons">
					<button class="btn btn-accept" onclick="acceptInvitation()">
						✓ قبول الدعوة
					</button>
					<button class="btn btn-decline" onclick="declineInvitation()">
						✗ رفض الدعوة
					</button>
				</div>
			</div>
		</div>
		<div class="instruction">اضغط لفتح الصندوق!</div>
	</div>
</div>

<script>
(function() {
	const giftBox = document.getElementById('giftBox');
	const invitationCard = document.getElementById('invitationCard');
	let isOpened = false;

	if (!giftBox || !invitationCard) return;

	giftBox.addEventListener('click', function(e) {
		// Don't open if clicking on the card
		if (e.target.closest('.invitation-card')) {
			return;
		}

		if (!isOpened) {
			isOpened = true;
			giftBox.classList.add('opened');
			createConfetti();
		}
	});

	window.closeTemplate4Card = function() {
		if (isOpened) {
			isOpened = false;
			giftBox.classList.remove('opened');
		}
	};

	function createConfetti() {
		const colors = ['#ff6b9d', '#ffd93d', '#6bcf7f', '#4d9fff', '#b57fff', '#ff9a76',
			'#667eea', '#764ba2'
		];
		const confettiCount = 50;
		const boxRect = giftBox.getBoundingClientRect();
		const centerX = boxRect.left + boxRect.width / 2;
		const centerY = boxRect.top + boxRect.height / 2;

		for (let i = 0; i < confettiCount; i++) {
			setTimeout(() => {
				const confetti = document.createElement('div');
				confetti.className = 'confetti';
				confetti.style.backgroundColor = colors[Math
					.floor(Math.random() * colors
						.length)];
				confetti.style.left = centerX + 'px';
				confetti.style.top = centerY + 'px';

				const angle = (Math.random() * 360) * (Math.PI /
					180);
				const velocity = Math.random() * 200 + 100;
				const tx = Math.cos(angle) * velocity;
				const ty = Math.sin(angle) * velocity - 150;

				confetti.style.transform =
					`translate(${tx}px, ${ty}px)`;

				const duration = Math.random() * 1 + 1.5;
				confetti.style.animation =
					`confettiFall ${duration}s ease-out forwards`;

				document.body.appendChild(confetti);

				setTimeout(() => {
					if (confetti
						.parentNode
						) {
						confetti
					.remove();
					}
				}, duration * 1000);
			}, i * 30);
		}
	}

	// Prevent card clicks from triggering box click
	invitationCard.addEventListener('click', function(e) {
		e.stopPropagation();
	});
})();
</script>