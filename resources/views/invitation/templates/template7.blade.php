<style>

.template7-wrapper {
	font-family: 'Cairo', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	display: flex;
	justify-content: center;
	align-items: center;
	min-height: 100vh;
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	padding: 20px;
}

.phone {
	width: 375px;
	max-width: 100%;
	height: 812px;
	max-height: 90vh;
	background: #000;
	border-radius: 40px;
	box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
	overflow: hidden;
	position: relative;
}

.notch {
	width: 180px;
	height: 30px;
	background: #000;
	position: absolute;
	top: 0;
	left: 50%;
	transform: translateX(-50%);
	border-radius: 0 0 20px 20px;
	z-index: 1000;
}

.lockscreen {
	width: 100%;
	height: 100%;
	background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
	position: relative;
	transition: transform 0.3s ease;
}

.lockscreen.hidden {
	transform: translateY(-100%);
}

.time {
	color: white;
	text-align: center;
	padding-top: 80px;
}

.time .hour {
	font-size: 80px;
	font-weight: 300;
	letter-spacing: -2px;
}

.time .date {
	font-size: 18px;
	font-weight: 400;
	margin-top: 5px;
	opacity: 0.9;
}

.notification-area {
	position: absolute;
	top: 200px;
	left: 0;
	right: 0;
	padding: 0 15px;
}

.notification {
	background: rgba(30, 30, 46, 0.95);
	backdrop-filter: blur(20px);
	border-radius: 16px;
	padding: 15px;
	margin-bottom: 10px;
	cursor: pointer;
	transform: translateY(-120px);
	opacity: 0;
	animation: slideIn 0.5s ease forwards;
	box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

@keyframes slideIn {
	to {
		transform: translateY(0);
		opacity: 1;
	}
}

.notification:active {
	transform: scale(0.98);
}

.notification-header {
	display: flex;
	align-items: center;
	margin-bottom: 8px;
}

.app-icon {
	width: 24px;
	height: 24px;
	border-radius: 6px;
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	margin-right: 10px;
	display: flex;
	align-items: center;
	justify-content: center;
	font-size: 14px;
}

.app-name {
	color: rgba(255, 255, 255, 0.7);
	font-size: 13px;
	font-weight: 500;
	font-family: 'Cairo', sans-serif;
}

.notification-time {
	margin-left: auto;
	color: rgba(255, 255, 255, 0.5);
	font-size: 12px;
}

.notification-title {
	color: white;
	font-size: 15px;
	font-weight: 600;
	margin-bottom: 4px;
	font-family: 'Cairo', sans-serif;
}

.notification-body {
	color: rgba(255, 255, 255, 0.8);
	font-size: 14px;
	line-height: 1.4;
	font-family: 'Cairo', sans-serif;
}

.unlock-hint {
	position: absolute;
	bottom: 30px;
	left: 0;
	right: 0;
	text-align: center;
	color: rgba(255, 255, 255, 0.6);
	font-size: 14px;
	font-family: 'Cairo', sans-serif;
}

.unlock-bar {
	width: 134px;
	height: 5px;
	background: rgba(255, 255, 255, 0.3);
	border-radius: 3px;
	margin: 10px auto;
}

.content-view {
	width: 100%;
	height: 100%;
	background: white;
	position: absolute;
	top: 0;
	left: 0;
	transform: translateY(100%);
	transition: transform 0.3s ease;
	overflow-y: auto;
}

.content-view.active {
	transform: translateY(0);
}

.content-header {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	color: white;
	padding: 60px 20px 30px;
}

.content-header h2 {
	font-family: 'Cairo', sans-serif;
}

.back-btn {
	background: rgba(255, 255, 255, 0.2);
	border: none;
	color: white;
	padding: 8px 16px;
	border-radius: 8px;
	cursor: pointer;
	font-size: 14px;
	margin-bottom: 20px;
	font-family: 'Cairo', sans-serif;
}

.back-btn:active {
	background: rgba(255, 255, 255, 0.3);
}

.invitation-card {
	background: white;
	border-radius: 16px 16px 0 0;
	padding: 30px 20px;
	margin-top: -10px;
}

.invitation-title {
	font-size: 28px;
	font-weight: 700;
	color: #1a1a2e;
	margin-bottom: 20px;
	font-family: 'Cairo', sans-serif;
}

.event-media-container {
	width: 100%;
	height: 200px;
	margin-bottom: 20px;
	border-radius: 12px;
	overflow: hidden;
	box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
	cursor: pointer;
	position: relative;
}

.event-media-container:hover {
	box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.event-image,
.event-video {
	width: 100%;
	height: 100%;
	object-fit: cover;
	border-radius: 12px;
	display: block;
}

.invitation-details {
	background: #f7f7f7;
	border-radius: 12px;
	padding: 20px;
	margin-bottom: 20px;
}

.detail-row {
	display: flex;
	margin-bottom: 15px;
	align-items: center;
}

.detail-row:last-child {
	margin-bottom: 0;
}

.detail-icon {
	width: 40px;
	height: 40px;
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	border-radius: 10px;
	display: flex;
	align-items: center;
	justify-content: center;
	margin-right: 15px;
	color: white;
	font-size: 18px;
	flex-shrink: 0;
}

.detail-text {
	flex: 1;
	margin-right: 20px;
}

.detail-text h4 {
	font-size: 13px;
	color: #666;
	margin-bottom: 4px;
	font-family: 'Cairo', sans-serif;
}

.detail-text p {
	font-size: 16px;
	color: #1a1a2e;
	font-weight: 500;
	font-family: 'Cairo', sans-serif;
}

.invitation-message {
	color: #555;
	line-height: 1.6;
	margin-bottom: 25px;
	font-family: 'Cairo', sans-serif;
}

.action-buttons {
	display: flex;
	gap: 10px;
}

.btn {
	flex: 1;
	padding: 15px;
	border: none;
	border-radius: 12px;
	font-size: 16px;
	font-weight: 600;
	cursor: pointer;
	font-family: 'Cairo', sans-serif;
	transition: all 0.3s ease;
}

.btn-accept {
	background: linear-gradient(135deg, #4ade80, #22c55e);
	color: white;
}

.btn-accept:hover {
	background: linear-gradient(135deg, #22c55e, #16a34a);
	transform: translateY(-2px);
}

.btn-decline {
	background: #f0f0f0;
	color: #666;
}

.btn-decline:hover {
	background: #e0e0e0;
	transform: translateY(-2px);
}

.pulse {
	animation: pulse 0.5s ease;
}

@keyframes pulse {
	0%, 100% {
		transform: scale(1);
	}
	50% {
		transform: scale(1.05);
	}
}

/* Full Page Modal */
.media-modal {
	display: none;
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background: rgba(0, 0, 0, 0.95);
	z-index: 10000;
	justify-content: center;
	align-items: center;
	animation: fadeIn 0.3s ease;
}

.media-modal.active {
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

.media-modal-content {
	position: relative;
	width: 100%;
	height: 100%;
	display: flex;
	justify-content: center;
	align-items: center;
	padding: 20px;
}

.media-modal-content img,
.media-modal-content video {
	max-width: 100%;
	max-height: 100%;
	object-fit: contain;
	border-radius: 8px;
}

.media-modal-close {
	position: absolute;
	top: 20px;
	right: 20px;
	width: 50px;
	height: 50px;
	background: rgba(255, 255, 255, 0.2);
	backdrop-filter: blur(10px);
	border: 2px solid rgba(255, 255, 255, 0.3);
	border-radius: 50%;
	color: white;
	font-size: 28px;
	cursor: pointer;
	display: flex;
	align-items: center;
	justify-content: center;
	transition: all 0.3s ease;
	z-index: 10001;
	font-family: 'Cairo', sans-serif;
}

.media-modal-close:hover {
	background: rgba(255, 255, 255, 0.3);
	transform: rotate(90deg);
}

@media (max-width: 768px) {
	.template7-wrapper {
		padding: 10px;
	}
	
	.phone {
		width: 100%;
		max-width: 100%;
		height: 100vh;
		max-height: 100vh;
		border-radius: 0;
	}
	
	.time .hour {
		font-size: 60px;
	}
	
	.time .date {
		font-size: 16px;
	}
	
	.invitation-title {
		font-size: 24px;
	}
	
	.event-media-container {
		height: 180px;
	}
	
	.detail-text h4 {
		font-size: 12px;
	}
	
	.detail-text p {
		font-size: 14px;
	}
	
	.btn {
		padding: 12px;
		font-size: 14px;
	}
	
	.media-modal-close {
		top: 10px;
		right: 10px;
		width: 40px;
		height: 40px;
		font-size: 24px;
	}
}

@media (max-width: 480px) {
	.content-header {
		padding: 50px 15px 20px;
	}
	
	.invitation-card {
		padding: 20px 15px;
	}
	
	.invitation-title {
		font-size: 20px;
	}
	
	.event-media-container {
		height: 150px;
	}
	
	.invitation-details {
		padding: 15px;
	}
	
	.detail-icon {
		width: 35px;
		height: 35px;
		font-size: 16px;
		margin-right: 10px;
	}
	
	.action-buttons {
		flex-direction: column;
		gap: 8px;
	}
}
</style>

<div id="envelopeView" class="invitation-wrapper template7-wrapper">
	<div class="phone">
		<div class="notch"></div>

		<div class="lockscreen" id="lockscreen">
			<div class="time">
				<div class="hour" id="currentTime"></div>
				<div class="date" id="currentDate"></div>
			</div>

			<div class="notification-area" id="notificationArea"></div>

			<div class="unlock-hint">
				<div class="unlock-bar"></div>
				اضغط على الإشعار للفتح
			</div>
		</div>

		<div class="content-view" id="contentView">
			<div class="content-header">
				<button class="back-btn" onclick="closeContent()">← العودة</button>
				<h2>دعوة</h2>
			</div>
			<div class="invitation-card">
				<div class="invitation-title">🎉 {{$invitation->event_name}}</div>

				@if($invitation->image())
				<div class="event-media-container" onclick="openMediaModal('{{$invitation->image()}}', '{{strtolower(pathinfo($invitation->image(), PATHINFO_EXTENSION))}}')">
					@php
					$mediaUrl = $invitation->image();
					$extension = strtolower(pathinfo($mediaUrl, PATHINFO_EXTENSION));
					$videoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv', 'm4v', '3gp', 'wmv'];
					$isVideo = in_array($extension, $videoExtensions);
					@endphp

					@if($isVideo)
					<video class="event-video" autoplay muted loop playsinline preload="metadata">
						<source src="{{$mediaUrl}}" type="video/{{$extension === 'mov' ? 'quicktime' : $extension}}">
					</video>
					@else
					<img src="{{$mediaUrl}}" alt="{{$invitation->event_name}}" class="event-image" loading="lazy" />
					@endif
				</div>
				@endif

				<div class="invitation-details">
					@if($invitation->date)
					<div class="detail-row">
						<div class="detail-icon">📅</div>
						<div class="detail-text">
							<h4>{{ __('admin.date') }}</h4>
							<p>{{$invitation->date}}</p>
						</div>
					</div>
					@endif

					@if($invitation->time)
					<div class="detail-row">
						<div class="detail-icon">⏰</div>
						<div class="detail-text">
							<h4>{{ __('admin.time') }}</h4>
							<p>{{ \Carbon\Carbon::parse($invitation->time)->format('h:i A') }}</p>
						</div>
					</div>
					@endif

					@if($invitation->address)
					<div class="detail-row">
						<div class="detail-icon">📍</div>
						<div class="detail-text">
							<h4>{{ __('admin.address') }}</h4>
							<p>{{$invitation->address}}</p>
						</div>
					</div>
					@endif

					@if(isset($host_name) && $host_name)
					<div class="detail-row">
						<div class="detail-icon">👤</div>
						<div class="detail-text">
							<h4>{{ __('admin.host-name') }}</h4>
							<p>{{$host_name}}</p>
						</div>
					</div>
					@endif

					@if($invitation->groom)
					<div class="detail-row">
						<div class="detail-icon">👨</div>
						<div class="detail-text">
							<h4>{{ __('admin.groom') }}</h4>
							<p>{{$invitation->groom}}</p>
						</div>
					</div>
					@endif

					@if($invitation->bride)
					<div class="detail-row">
						<div class="detail-icon">👩</div>
						<div class="detail-text">
							<h4>{{ __('admin.bride') }}</h4>
							<p>{{$invitation->bride}}</p>
						</div>
					</div>
					@endif
				</div>

				@if($invitation->description)
				<div class="invitation-message">
					<p>{{$invitation->description}}</p>
				</div>
				@endif

				<div class="action-buttons">
					<button class="btn btn-accept" onclick="acceptInvitation()">✓ {{ __('admin.accept-invitation') }}</button>
					<button class="btn btn-decline" onclick="declineInvitation()">✗  {{ __('admin.refuse-invitation') }}</button>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Full Page Media Modal -->
	<div class="media-modal" id="mediaModal" onclick="closeMediaModal(event)">
		<div class="media-modal-content" onclick="event.stopPropagation()">
			<button class="media-modal-close" onclick="closeMediaModal()">×</button>
			<div id="mediaModalContent"></div>
		</div>
	</div>
</div>

<script>
function updateTime() {
	const now = new Date();
	const hours = now.getHours().toString().padStart(2, '0');
	const minutes = now.getMinutes().toString().padStart(2, '0');
	document.getElementById('currentTime').textContent = `${hours}:${minutes}`;

	const options = {
		weekday: 'long',
		month: 'long',
		day: 'numeric'
	};
	document.getElementById('currentDate').textContent = now.toLocaleDateString('ar-SA', options);
}

function showNotification() {
	const area = document.getElementById('notificationArea');
	const notification = document.createElement('div');
	notification.className = 'notification';

	@php
	$eventName = $invitation->event_name ?? 'دعوة';
	$eventDate = $invitation->date ?? '';
	$youAreInvited = 'أنت مدعو إلى';
	$on = 'في' ;
	$notificationText = $eventDate ? 
		$youAreInvited . ' ' . $eventName . ' ' . $on . ' ' . $eventDate :
		$youAreInvited . ' ' . $eventName;
	$eventsText ='الأحداث' ;
	$nowText = 'الآن';
	$newInvitationText = 'دعوة حدث جديد';
	@endphp

	notification.innerHTML = `
		<div class="notification-header">
			<div class="app-icon">🎉</div>
			<div class="app-name">{{ $eventsText }}</div>
			<div class="notification-time">{{ $nowText }}</div>
		</div>
		<div class="notification-title">{{ $newInvitationText }}</div>
		<div class="notification-body">{{ $notificationText }}</div>
	`;

	notification.onclick = function() {
		openContent();
	};

	area.appendChild(notification);

	// Add pulse animation
	notification.classList.add('pulse');
	setTimeout(() => notification.classList.remove('pulse'), 500);
}

function openContent() {
	document.getElementById('lockscreen').classList.add('hidden');
	document.getElementById('contentView').classList.add('active');
}

function closeContent() {
	document.getElementById('lockscreen').classList.remove('hidden');
	document.getElementById('contentView').classList.remove('active');
}

function openMediaModal(mediaUrl, extension) {
	const modal = document.getElementById('mediaModal');
	const content = document.getElementById('mediaModalContent');
	const videoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv', 'm4v', '3gp', 'wmv'];
	const isVideo = videoExtensions.includes(extension.toLowerCase());
	
	if (isVideo) {
		content.innerHTML = `
			<video controls autoplay style="max-width: 100%; max-height: 100%; object-fit: contain;">
				<source src="${mediaUrl}" type="video/${extension === 'mov' ? 'quicktime' : extension}">
			</video>
		`;
	} else {
		content.innerHTML = `<img src="${mediaUrl}" alt="Event Media" style="max-width: 100%; max-height: 100%; object-fit: contain;">`;
	}
	
	modal.classList.add('active');
	document.body.style.overflow = 'hidden';
}

function closeMediaModal(event) {
	if (event && event.target !== event.currentTarget && !event.target.closest('.media-modal-close')) {
		return;
	}
	const modal = document.getElementById('mediaModal');
	modal.classList.remove('active');
	document.body.style.overflow = '';
	const content = document.getElementById('mediaModalContent');
	content.innerHTML = '';
}

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
	if (e.key === 'Escape') {
		closeMediaModal();
	}
});

// Initialize
updateTime();
setInterval(updateTime, 1000);

// Show notification after 2 seconds
setTimeout(showNotification, 2000);
</script>
