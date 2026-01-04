<style>
* {
	margin: 0;
	padding: 0;
	box-sizing: border-box;
}

.template3-wrapper {
	min-height: 100vh;
	display: flex;
	justify-content: center;
	align-items: center;
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	font-family: 'Cairo', 'Georgia', serif;
	overflow: hidden;
	position: relative;
	padding: 20px;
}

.container {
	position: relative;
	width: 100%;
	/* max-width: 400px; */
	height: 400px;
	cursor: pointer;
}

@media (max-width: 768px) {
	.container {
		max-width: 375px;
		height: 300px;
	}
}

@media (max-width: 480px) {
	.container {
		width: 375px;
		height: 250px;
	}
}

.flower {
	position: relative;
	width: 100%;
	height: 100%;
	transform-style: preserve-3d;
}

.petal {
	position: absolute;
	width: 120px;
	height: 200px;
	background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
	border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
	top: 50%;
	left: 50%;
	transform-origin: center bottom;
	transition: transform 1.2s cubic-bezier(0.68, -0.55, 0.265, 1.55);
	box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

@media (max-width: 768px) {
	.petal {
		width: 80px;
		height: 140px;
	}
}

@media (max-width: 480px) {
	.petal {
		width: 60px;
		height: 100px;
	}
}

.petal:nth-child(1) {
	background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
	transform: translate(-50%, -100%) rotate(0deg);
}

.petal:nth-child(2) {
	background: linear-gradient(135deg, #ffd89b 0%, #ff9a9e 100%);
	transform: translate(-50%, -100%) rotate(45deg);
}

.petal:nth-child(3) {
	background: linear-gradient(135deg, #fbc2eb 0%, #f093fb 100%);
	transform: translate(-50%, -100%) rotate(90deg);
}

.petal:nth-child(4) {
	background: linear-gradient(135deg, #fccb90 0%, #f9a8d4 100%);
	transform: translate(-50%, -100%) rotate(135deg);
}

.petal:nth-child(5) {
	background: linear-gradient(135deg, #ffeaa7 0%, #fab1a0 100%);
	transform: translate(-50%, -100%) rotate(180deg);
}

.petal:nth-child(6) {
	background: linear-gradient(135deg, #ffd7d7 0%, #ffb3ba 100%);
	transform: translate(-50%, -100%) rotate(225deg);
}

.petal:nth-child(7) {
	background: linear-gradient(135deg, #fff0f0 0%, #ffd1dc 100%);
	transform: translate(-50%, -100%) rotate(270deg);
}

.petal:nth-child(8) {
	background: linear-gradient(135deg, #ffe4e1 0%, #ffc1cc 100%);
	transform: translate(-50%, -100%) rotate(315deg);
}

.flower.bloomed .petal:nth-child(1) {
	transform: translate(-50%, -100%) rotate(0deg) rotateX(-80deg);
}

.flower.bloomed .petal:nth-child(2) {
	transform: translate(-50%, -100%) rotate(45deg) rotateX(-80deg);
}

.flower.bloomed .petal:nth-child(3) {
	transform: translate(-50%, -100%) rotate(90deg) rotateX(-80deg);
}

.flower.bloomed .petal:nth-child(4) {
	transform: translate(-50%, -100%) rotate(135deg) rotateX(-80deg);
}

.flower.bloomed .petal:nth-child(5) {
	transform: translate(-50%, -100%) rotate(180deg) rotateX(-80deg);
}

.flower.bloomed .petal:nth-child(6) {
	transform: translate(-50%, -100%) rotate(225deg) rotateX(-80deg);
}

.flower.bloomed .petal:nth-child(7) {
	transform: translate(-50%, -100%) rotate(270deg) rotateX(-80deg);
}

.flower.bloomed .petal:nth-child(8) {
	transform: translate(-50%, -100%) rotate(315deg) rotateX(-80deg);
}

.center {
	position: absolute;
	width: 140px;
	height: 140px;
	background: radial-gradient(circle, #ffd700, #ffed4e, #ffd700);
	border-radius: 50%;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	box-shadow: 0 0 30px rgba(255, 215, 0, 0.6), inset 0 0 20px rgba(255, 255, 255, 0.3);
	z-index: 10;
	display: flex;
	align-items: center;
	justify-content: center;
}

@media (max-width: 768px) {
	.center {
		width: 100px;
		height: 100px;
	}
}

@media (max-width: 480px) {
	.center {
		width: 70px;
		height: 70px;
	}
}

.click-prompt {
	color: #8b4513;
	font-size: 14px;
	font-weight: bold;
	text-align: center;
	animation: pulse 2s infinite;
	font-family: 'Cairo', sans-serif;
}

@media (max-width: 768px) {
	.click-prompt {
		font-size: 12px;
	}
}

@media (max-width: 480px) {
	.click-prompt {
		font-size: 10px;
	}
}

@keyframes pulse {

	0%,
	100% {
		opacity: 1;
		transform: scale(1);
	}

	50% {
		opacity: 0.7;
		transform: scale(1.05);
	}
}

.invitation-card {
	position: absolute;
	width: 100%;
	width: 500px;
	min-height: 600px;
	background: linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(255, 248, 220, 0.95));
	backdrop-filter: blur(20px);
	border-radius: 25px;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%) scale(0);
	opacity: 0;
	transition: all 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55);
	box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
	z-index: 100;
	text-align: center;
	padding: 40px;
	overflow-y: auto;
	max-height: 90vh;
	pointer-events: none;
}

.invitation-card.show {
	transform: translate(-50%, -50%) scale(1);
	opacity: 1;
	pointer-events: all;
}

.invitation-card .close-btn {
	position: absolute;
	top: 15px;
	left: 15px;
	width: 40px;
	height: 40px;
	background: rgba(118, 75, 162, 0.2);
	border: 2px solid rgba(118, 75, 162, 0.4);
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
	cursor: pointer;
	transition: all 0.3s ease;
	color: #764ba2;
	font-size: 24px;
	font-weight: bold;
	z-index: 10;
}

.invitation-card .close-btn:hover {
	background: rgba(118, 75, 162, 0.3);
	border-color: rgba(118, 75, 162, 0.6);
	transform: rotate(90deg);
}

.invitation-card h1 {
	color: #764ba2;
	font-size: 2.5em;
	margin-bottom: 20px;
	font-weight: bold;
	font-family: 'Cairo', sans-serif;
}

.invitation-card .divider {
	width: 100px;
	height: 2px;
	background: linear-gradient(90deg, transparent, #fcb69f, transparent);
	margin: 20px auto;
}

.invitation-card .event-media-container {
	width: 100%;
	height: 250px;
	margin: 20px auto;
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

.invitation-card p {
	color: #555;
	line-height: 1.8;
	margin: 15px 0;
	font-size: 16px;
	font-family: 'Cairo', sans-serif;
}

.invitation-card .date {
	font-weight: bold;
	color: #764ba2;
	font-size: 1.3em;
	margin: 20px 0;
}

.invitation-card .location {
	font-style: italic;
	color: #666;
}

.invitation-card .event-details {
	margin-top: 25px;
	text-align: right;
}

.invitation-card .detail-item {
	margin: 15px 0;
	padding: 15px;
	background: rgba(118, 75, 162, 0.1);
	border-radius: 12px;
	border-right: 3px solid #764ba2;
}

.invitation-card .detail-label {
	font-weight: bold;
	font-size: 14px;
	margin-bottom: 8px;
	color: #764ba2;
}

.invitation-card .detail-value {
	font-size: 16px;
	color: #2d3748;
	line-height: 1.6;
}

.invitation-card .response-buttons {
	display: flex;
	gap: 15px;
	margin-top: 30px;
	width: 100%;
}

.invitation-card .btn {
	flex: 1;
	padding: 15px 25px;
	border-radius: 25px;
	font-size: 1.1em;
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

.stem {
	position: absolute;
	width: 8px;
	height: 150px;
	background: linear-gradient(180deg, #7cb342 0%, #558b2f 100%);
	bottom: -150px;
	left: 50%;
	transform: translateX(-50%);
	border-radius: 4px;
	z-index: -1;
}

@media (max-width: 768px) {
	.stem {
		width: 6px;
		height: 100px;
		bottom: -100px;
	}
}

@media (max-width: 480px) {
	.stem {
		width: 5px;
		height: 80px;
		bottom: -80px;
	}
}

@media (max-width: 768px) {
	.template3-wrapper {
		padding: 15px;
	}

	.invitation-card {
		width: 95%;
		max-width: 95%;
		padding: 30px 20px;
		min-height: 500px;
	}

	.invitation-card h1 {
		font-size: 2em;
	}

	.invitation-card .event-media-container {
		height: 180px;
	}

	.invitation-card .btn {
		padding: 12px 20px;
		font-size: 1em;
	}
}

@media (max-width: 480px) {
	.template3-wrapper {
		padding: 10px;
	}

	.invitation-card {
		width: 100%;
		max-width: 100%;
		padding: 25px 15px;
		min-height: auto;
		max-height: 85vh;
	}

	.invitation-card h1 {
		font-size: 1.5em;
		margin-bottom: 15px;
	}

	.invitation-card .divider {
		width: 80px;
		margin: 15px auto;
	}

	.invitation-card .event-media-container {
		height: 150px;
		margin: 15px auto;
	}

	.invitation-card p {
		font-size: 14px;
		margin: 10px 0;
	}

	.invitation-card .date {
		font-size: 1.1em;
		margin: 15px 0;
	}

	.invitation-card .event-details {
		margin-top: 20px;
	}

	.invitation-card .detail-item {
		margin: 10px 0;
		padding: 12px;
	}

	.invitation-card .detail-label {
		font-size: 12px;
		margin-bottom: 5px;
	}

	.invitation-card .detail-value {
		font-size: 14px;
	}

	.invitation-card .response-buttons {
		flex-direction: column;
		gap: 10px;
		margin-top: 20px;
	}

	.invitation-card .btn {
		padding: 12px;
		font-size: 0.95em;
		width: 100%;
	}

	.invitation-card .close-btn {
		width: 35px;
		height: 35px;
		font-size: 20px;
		top: 10px;
		left: 10px;
	}
}
</style>

<!-- Template 3: Flower Bloom Invitation View -->
<div id="envelopeView" class="invitation-wrapper template3-wrapper">
	<div class="container" id="flowerContainer">
		<div class="flower" id="flower">
			<div class="petal"></div>
			<div class="petal"></div>
			<div class="petal"></div>
			<div class="petal"></div>
			<div class="petal"></div>
			<div class="petal"></div>
			<div class="petal"></div>
			<div class="petal"></div>
			<div class="center">
				<div class="click-prompt" id="clickPrompt">اضغط للتفتح</div>
			</div>
		</div>
		<div class="stem"></div>
		<div class="invitation-card" id="invitationCard">
			<button class="close-btn" onclick="closeTemplate3Card()" title="إغلاق">×</button>

			<h1>{{$invitation->event_name}}</h1>
			<div class="divider"></div>

			@if($invitation->description)
			<p>{{$invitation->description}}</p>
			@endif

			@if($invitation->image())
			<div class="event-media-container">
				@php
				$mediaUrl = $invitation->image();
				$extension = strtolower(pathinfo($mediaUrl, PATHINFO_EXTENSION));
				$videoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv', 'm4v', '3gp',
				'wmv'];
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

			@if($invitation->date)
			<div class="date">{{$invitation->date}}</div>
			@endif

			@if($invitation->time)
			<p class="location">في الساعة
				{{ \Carbon\Carbon::parse($invitation->time)->format('h:i A') }}</p>
			@endif

			@if($invitation->address)
			<p class="location">{{$invitation->address}}</p>
			@endif

			@if($host_name)
			<p style="margin-top: 15px; font-style: italic;">{{__('admin.host-name')}}: {{$host_name}}
			</p>
			@endif

			<div class="event-details">
				@if($invitation->groom)
				<div class="detail-item">
					<div class="detail-label">{{__('admin.groom')}}</div>
					<div class="detail-value">{{$invitation->groom}}</div>
				</div>
				@endif

				@if($invitation->bride)
				<div class="detail-item">
					<div class="detail-label">{{__('admin.bride')}}</div>
					<div class="detail-value">{{$invitation->bride}}</div>
				</div>
				@endif
			</div>

			<div class="response-buttons">
				<button class="btn btn-accept" onclick="acceptInvitation()">✓ قبول
					الدعوة</button>
				<button class="btn btn-decline" onclick="declineInvitation()">✗ رفض
					الدعوة</button>
			</div>
		</div>
	</div>
</div>

<script>
(function() {
	const flowerContainer = document.getElementById('flowerContainer');
	const flower = document.getElementById('flower');
	const invitationCard = document.getElementById('invitationCard');
	const clickPrompt = document.getElementById('clickPrompt');

	if (!flowerContainer || !flower || !invitationCard) return;

	let hasBloomedOnce = false;

	flowerContainer.addEventListener('click', (e) => {
		// Don't trigger if clicking on the card
		if (e.target.closest('.invitation-card')) {
			return;
		}

		if (!hasBloomedOnce) {
			flower.classList.add('bloomed');
			clickPrompt.style.opacity = '0';

			setTimeout(() => {
				invitationCard.classList.add(
					'show');
			}, 600);

			flowerContainer.style.cursor = 'default';
			hasBloomedOnce = true;
		}
	});

	window.closeTemplate3Card = function() {
		if (hasBloomedOnce) {
			invitationCard.classList.remove('show');
			setTimeout(() => {
				flower.classList.remove('bloomed');
				clickPrompt.style.opacity = '1';
				hasBloomedOnce = false;
				flowerContainer.style.cursor =
				'pointer';
			}, 300);
		}
	};

	// Prevent card clicks from triggering flower click
	invitationCard.addEventListener('click', function(e) {
		e.stopPropagation();
	});
})();
</script>