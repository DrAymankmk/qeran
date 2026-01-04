<style>
* {
	margin: 0;
	padding: 0;
	box-sizing: border-box;
}

.template5-wrapper {
	font-family: 'Cairo', 'Georgia', serif;
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	min-height: 100vh;
	display: flex;
	align-items: flex-end;
	justify-content: center;
	overflow: hidden;
	padding: 20px;
	position: relative;
}

.container {
	position: relative;
	width: 100%;
	/* max-width: 400px; */
	height: 600px;
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: flex-end;
}

.balloon-group {
	position: absolute;
	bottom: 180px;
	display: flex;
	gap: 30px;
	transition: transform 2s cubic-bezier(0.4, 0, 0.2, 1);
	z-index: 1;
}

.balloon-group.rise {
	transform: translateY(-400px);
}

.balloon {
	position: relative;
	cursor: pointer;
}

.balloon-body {
	width: 60px;
	height: 75px;
	background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
	border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
	position: relative;
	box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
	animation: float 3s ease-in-out infinite;
}

.balloon:nth-child(1) .balloon-body {
	background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
	animation-delay: 0s;
}

.balloon:nth-child(2) .balloon-body {
	background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
	animation-delay: 0.5s;
}

.balloon:nth-child(3) .balloon-body {
	background: linear-gradient(135deg, #ffd93d 0%, #f39c12 100%);
	animation-delay: 1s;
}

.balloon-body::before {
	content: '';
	position: absolute;
	top: 10px;
	left: 15px;
	width: 15px;
	height: 20px;
	background: rgba(255, 255, 255, 0.3);
	border-radius: 50%;
}

.balloon-body::after {
	content: '';
	position: absolute;
	bottom: -10px;
	left: 50%;
	transform: translateX(-50%);
	width: 0;
	height: 0;
	border-left: 5px solid transparent;
	border-right: 5px solid transparent;
	border-top: 10px solid currentColor;
	opacity: 0.8;
}

.balloon:nth-child(1) .balloon-body::after {
	color: #ee5a6f;
}

.balloon:nth-child(2) .balloon-body::after {
	color: #44a08d;
}

.balloon:nth-child(3) .balloon-body::after {
	color: #f39c12;
}

.string {
	position: absolute;
	top: 75px;
	left: 50%;
	transform: translateX(-50%);
	width: 2px;
	height: 120px;
	background: rgba(255, 255, 255, 0.6);
}

@keyframes float {

	0%,
	100% {
		transform: translateY(0) rotate(-2deg);
	}

	50% {
		transform: translateY(-10px) rotate(2deg);
	}
}

.card {
	width: 280px;
	height: 180px;
	background: white;
	border-radius: 15px;
	box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
	position: relative;
	cursor: pointer;
	transition: transform 2s cubic-bezier(0.4, 0, 0.2, 1), opacity 2s, width 2s, height 2s, padding 2s;
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	padding: 30px;
	text-align: center;
	opacity: 0;
	visibility: hidden;
	z-index: 5;
}

.card.rise {
	transform: translateY(-280px);
	opacity: 1;
	visibility: visible;
	width: 100%;
	max-width: 550px;
	min-height: 600px;
	height: auto;
	padding: 40px;
	position: fixed;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	z-index: 100;
	justify-content: flex-start;
	overflow-y: auto;
	max-height: 90vh;
}

.card-content {
	opacity: 0;
	transition: opacity 1s ease-in 1.5s;
	width: 100%;
}

.card.rise .card-content {
	opacity: 1;
}

.card .close-btn {
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
	opacity: 0;
	pointer-events: none;
}

.card.rise .close-btn {
	opacity: 1;
	pointer-events: all;
}

.card .close-btn:hover {
	background: rgba(102, 126, 234, 0.3);
	border-color: rgba(102, 126, 234, 0.6);
	transform: rotate(90deg);
}

.card h1 {
	font-size: 2.5em;
	color: #667eea;
	margin-bottom: 20px;
	font-weight: bold;
	font-family: 'Cairo', sans-serif;
}

.card .event-media-container {
	width: 100%;
	height: 280px;
	margin: 20px auto;
	border-radius: 15px;
	overflow: hidden;
	box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.card .event-image,
.card .event-video {
	width: 100%;
	height: 100%;
	object-fit: cover;
	border-radius: 15px;
}

.card p {
	font-size: 18px;
	color: #555;
	line-height: 1.8;
	margin: 15px 0;
	font-family: 'Cairo', sans-serif;
}

.card .date {
	font-size: 1.3em;
	color: #764ba2;
	font-weight: bold;
	margin: 20px 0;
}

.card .event-details {
	margin-top: 25px;
	text-align: right;
	width: 100%;
}

.card .detail-item {
	margin: 15px 0;
	padding: 15px;
	background: rgba(102, 126, 234, 0.1);
	border-radius: 12px;
	border-right: 3px solid #667eea;
}

.card .detail-label {
	font-weight: bold;
	font-size: 14px;
	margin-bottom: 8px;
	color: #667eea;
}

.card .detail-value {
	font-size: 16px;
	color: #2d3748;
	line-height: 1.6;
}

.card .response-buttons {
	display: flex;
	gap: 20px;
	margin-top: 30px;
	width: 100%;
}

.card .btn {
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

.card .btn-accept {
	background: linear-gradient(135deg, #4ade80, #22c55e);
	color: white;
	box-shadow: 0 4px 15px rgba(34, 197, 94, 0.4);
}

.card .btn-accept:hover {
	background: linear-gradient(135deg, #22c55e, #16a34a);
	transform: translateY(-2px);
	box-shadow: 0 6px 20px rgba(34, 197, 94, 0.5);
}

.card .btn-decline {
	background: transparent;
	color: #ef4444;
	border: 2px solid #ef4444;
	box-shadow: 0 4px 15px rgba(239, 68, 68, 0.2);
}

.card .btn-decline:hover {
	background: #ef4444;
	color: white;
	transform: translateY(-2px);
	box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
}

.instruction {
	position: absolute;
	bottom: 30px;
	color: white;
	font-size: 18px;
	text-align: center;
	animation: pulse 2s ease-in-out infinite;
	transition: opacity 0.5s;
	font-family: 'Cairo', sans-serif;
	z-index: 2;
}

.instruction.hidden {
	opacity: 0;
}

@keyframes pulse {

	0%,
	100% {
		opacity: 1;
	}

	50% {
		opacity: 0.6;
	}
}

@media (max-width: 768px) {
	.card.rise {
		max-width: 95%;
		padding: 30px 20px;
		min-height: 500px;
	}

	.card h1 {
		font-size: 2em;
	}

	.card .event-media-container {
		height: 200px;
	}
}
</style>

<!-- Template 5: Balloon Invitation View -->
<div id="envelopeView" class="invitation-wrapper template5-wrapper">
	<div class="container">
		<div class="balloon-group" id="balloons">
			<div class="balloon">
				<div class="balloon-body"></div>
				<div class="string"></div>
			</div>
			<div class="balloon">
				<div class="balloon-body"></div>
				<div class="string"></div>
			</div>
			<div class="balloon">
				<div class="balloon-body"></div>
				<div class="string"></div>
			</div>
		</div>

		<div class="card" id="card">
			<button class="close-btn" onclick="closeTemplate5Card()" title="إغلاق">×</button>
			<div class="card-content">
				<h1>{{$invitation->event_name ?? 'دعوة'}}</h1>

				@if($invitation->description)
				<p>{{$invitation->description}}</p>
				@endif

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
						<img src="{{$mediaUrl}}"
							alt="{{$invitation->event_name ?? 'دعوة'}}"
							class="event-image" loading="lazy" />
					</a>
					@endif
				</div>
				@endif

				@if($invitation->date)
				<div class="date">{{$invitation->date}}</div>
				@endif

				@if($invitation->time)
				<p>في الساعة {{ \Carbon\Carbon::parse($invitation->time)->format('h:i A') }}</p>
				@endif

				@if($invitation->address)
				<p>{{$invitation->address}}</p>
				@endif

				@if(isset($host_name) && $host_name)
				<p style="margin-top: 15px; font-style: italic;">{{__('admin.host-name')}}:
					{{$host_name}}</p>
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

		<div class="instruction" id="instruction">اضغط على البالونات للفتح ↑</div>
	</div>
</div>

<script>
(function() {
	const card = document.getElementById('card');
	const balloons = document.getElementById('balloons');
	const instruction = document.getElementById('instruction');

	if (!card || !balloons || !instruction) return;

	let isRisen = false;

	function openCard() {
		if (!isRisen) {
			card.classList.add('rise');
			balloons.classList.add('rise');
			instruction.classList.add('hidden');
			isRisen = true;
		}
	}

	function closeCard() {
		if (isRisen) {
			card.classList.remove('rise');
			balloons.classList.remove('rise');
			instruction.classList.remove('hidden');
			isRisen = false;
		}
	}

	window.closeTemplate5Card = function() {
		closeCard();
	};

	// Click on balloons to trigger the animation
	balloons.addEventListener('click', function(e) {
		if (!isRisen) {
			openCard();
		}
	});

	// Prevent card clicks from triggering balloon click
	card.addEventListener('click', function(e) {
		// Only allow clicking on close button or response buttons
		if (e.target.closest('.close-btn') || e.target.closest('.btn')) {
			if (e.target.closest('.close-btn')) {
				closeCard();
			}
			return;
		}
		// Prevent card click from doing anything else
		e.stopPropagation();
	});
})();
</script>