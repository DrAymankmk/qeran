<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
<style>
.template6-wrapper {
	min-height: 100vh;
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	display: flex;
	align-items: center;
	justify-content: center;
	font-family: 'Cairo', 'Courier New', monospace;
	padding: 20px;
	overflow-x: hidden;
}

.boarding-pass-container {
	perspective: 1000px;
	width: 100%;
	max-width: 800px;
}

.boarding-pass {
	background: white;
	border-radius: 20px;
	box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
	max-width: 800px;
	width: 100%;
	position: relative;
	overflow: hidden;
	transform: translateX(-150%);
	opacity: 0;
	animation: slideIn 1s ease-out 0.5s forwards;
}

@keyframes slideIn {
	to {
		transform: translateX(0);
		opacity: 1;
	}
}

.scan-line {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 3px;
	background: linear-gradient(90deg, transparent, #667eea, transparent);
	animation: scan 3s ease-in-out 1.5s infinite;
	z-index: 10;
	box-shadow: 0 0 20px #667eea;
}

@keyframes scan {
	0%, 100% {
		top: 0;
		opacity: 0;
	}
	10%, 90% {
		opacity: 1;
	}
	50% {
		top: 100%;
	}
}

.pass-header {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	color: white;
	padding: 20px 30px;
	display: flex;
	justify-content: space-between;
	align-items: center;
}

.airline-name {
	font-size: 24px;
	font-weight: bold;
	letter-spacing: 2px;
	font-family: 'Cairo', sans-serif;
}

.flight-number {
	font-size: 18px;
	opacity: 0.9;
}

.pass-body {
	padding: 30px;
}

.event-media-container {
	width: 100%;
	height: 200px;
	margin-bottom: 25px;
	border-radius: 15px;
	overflow: hidden;
	box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.event-image,
.event-video {
	width: 100%;
	height: 100%;
	object-fit: cover;
	border-radius: 15px;
}

.destination-section {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: 30px;
	position: relative;
}

.location {
	text-align: center;
	flex: 1;
}

.location-code {
	font-size: 48px;
	font-weight: bold;
	color: #667eea;
	animation: highlight 2s ease-in-out 2s infinite;
}

@keyframes highlight {
	0%, 100% {
		transform: scale(1);
		color: #667eea;
	}
	50% {
		transform: scale(1.1);
		color: #764ba2;
	}
}

.location-name {
	font-size: 14px;
	color: #666;
	margin-top: 5px;
	font-family: 'Cairo', sans-serif;
}

.plane-icon {
	font-size: 32px;
	color: #667eea;
	margin: 0 20px;
}

.info-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
	gap: 20px;
	margin-bottom: 30px;
}

.info-item {
	border-left: 3px solid #667eea;
	padding-left: 15px;
	transition: all 0.3s ease;
}

.info-item:hover {
	border-left-color: #764ba2;
	transform: translateX(5px);
}

.info-label {
	font-size: 11px;
	color: #999;
	text-transform: uppercase;
	letter-spacing: 1px;
	margin-bottom: 5px;
	font-family: 'Cairo', sans-serif;
}

.info-value {
	font-size: 18px;
	font-weight: bold;
	color: #333;
	font-family: 'Cairo', sans-serif;
}

.highlight {
	background: linear-gradient(120deg, #ffd70033 0%, #ff69b433 100%);
	padding: 2px 8px;
	border-radius: 4px;
	animation: pulse 2s ease-in-out 2.5s infinite;
}

@keyframes pulse {
	0%, 100% {
		background: linear-gradient(120deg, #ffd70033 0%, #ff69b433 100%);
	}
	50% {
		background: linear-gradient(120deg, #ffd70066 0%, #ff69b466 100%);
	}
}

.barcode {
	height: 80px;
	background: repeating-linear-gradient(90deg,
			#000 0px,
			#000 2px,
			#fff 2px,
			#fff 4px,
			#000 4px,
			#000 5px,
			#fff 5px,
			#fff 8px,
			#000 8px,
			#000 10px,
			#fff 10px,
			#fff 11px);
	margin: 20px 0;
	border-radius: 5px;
}

.barcode-number {
	text-align: center;
	font-size: 12px;
	color: #666;
	margin-top: 5px;
	letter-spacing: 3px;
	font-family: 'Courier New', monospace;
}

.tear-line {
	border-top: 2px dashed #ddd;
	margin: 20px 0;
	position: relative;
}

.tear-line::before,
.tear-line::after {
	content: '';
	position: absolute;
	width: 30px;
	height: 30px;
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	border-radius: 50%;
	top: -15px;
}

.tear-line::before {
	left: -15px;
}

.tear-line::after {
	right: -15px;
}

.footer-note {
	text-align: center;
	color: #999;
	font-size: 12px;
	margin-top: 20px;
	font-style: italic;
	font-family: 'Cairo', sans-serif;
}

.response-buttons {
	display: flex;
	gap: 20px;
	margin-top: 30px;
	width: 100%;
}

.btn {
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

.btn-accept {
	background: linear-gradient(135deg, #4ade80, #22c55e);
	color: white;
	box-shadow: 0 4px 15px rgba(34, 197, 94, 0.4);
}

.btn-accept:hover {
	background: linear-gradient(135deg, #22c55e, #16a34a);
	transform: translateY(-2px);
	box-shadow: 0 6px 20px rgba(34, 197, 94, 0.5);
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
	box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
}

@media (max-width: 576px) {
	.location-code {
		font-size: 36px;
	}

	.info-grid {
		grid-template-columns: 1fr;
	}

	.pass-header {
		flex-direction: column;
		text-align: center;
		gap: 10px;
	}
	
	.event-media-container {
		height: 150px;
	}
}
</style>

<div id="envelopeView" class="invitation-wrapper template6-wrapper">
	<div class="boarding-pass-container">
		<div class="boarding-pass">
			<div class="scan-line"></div>

			<div class="pass-header">
				<div class="airline-name">{{__('admin.project-name')}}</div>
				<div class="flight-number">INV-{{$invitation->code ?? $invitation->id}}</div>
			</div>

			<div class="pass-body">
				@if($invitation->image())
				<div class="event-media-container">
					@php
						$mediaUrl = $invitation->image();
						$extension = strtolower(pathinfo($mediaUrl, PATHINFO_EXTENSION));
						$videoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv', 'm4v', '3gp', 'wmv'];
						$isVideo = in_array($extension, $videoExtensions);
					@endphp
					
					@if($isVideo)
						<a href="{{$mediaUrl}}" target="_blank" rel="noopener noreferrer">
							<video class="event-video" autoplay muted loop playsinline preload="metadata" style="width: 100%; height: 100%; object-fit: cover;">
								<source src="{{$mediaUrl}}" type="video/{{$extension === 'mov' ? 'quicktime' : $extension}}">
							</video>
						</a>
					@else
						<a href="{{$mediaUrl}}" target="_blank" rel="noopener noreferrer">
							<img src="{{$mediaUrl}}" alt="{{$invitation->event_name}}" class="event-image" loading="lazy" />
						</a>
					@endif
				</div>
				@endif

				<div class="destination-section">
					<div class="location">
						<div class="location-code">{{ strtoupper(substr($invitation->event_name ?? 'EVENT', 0, 3)) }}</div>
						<div class="location-name">{{$invitation->event_name}}</div>
					</div>
					<div class="plane-icon">✈</div>
					<div class="location">
						@if($invitation->address)
							@php
								$addressParts = explode(',', $invitation->address);
								$locationCode = strtoupper(substr(trim($addressParts[0] ?? $invitation->address), 0, 3));
								$locationName = trim($addressParts[0] ?? $invitation->address);
							@endphp
							<div class="location-code">{{ $locationCode }}</div>
							<div class="location-name">{{ $locationName }}</div>
						@else
							<div class="location-code">VEN</div>
							<div class="location-name">{{__('admin.venue')}}</div>
						@endif
					</div>
				</div>

				<div class="info-grid">
					<div class="info-item">
						<div class="info-label">{{__('admin.event-name')}}</div>
						<div class="info-value">{{$invitation->event_name}}</div>
					</div>
					@if($invitation->date)
					<div class="info-item">
						<div class="info-label">{{__('admin.date')}}</div>
						<div class="info-value highlight">{{$invitation->date}}</div>
					</div>
					@endif
					@if($invitation->time)
					<div class="info-item">
						<div class="info-label">{{__('admin.time')}}</div>
						<div class="info-value highlight">{{ \Carbon\Carbon::parse($invitation->time)->format('h:i A') }}</div>
					</div>
					@endif
					@if($invitation->address)
					<div class="info-item">
						<div class="info-label">{{__('admin.address')}}</div>
						<div class="info-value">{{$invitation->address}}</div>
					</div>
					@endif
					@if(isset($host_name) && $host_name)
					<div class="info-item">
						<div class="info-label">{{__('admin.host-name')}}</div>
						<div class="info-value">{{$host_name}}</div>
					</div>
					@endif
					@if($invitation->groom || $invitation->bride)
					<div class="info-item">
						<div class="info-label">{{$invitation->groom && $invitation->bride ? __('admin.couple') : ($invitation->groom ? __('admin.groom') : __('admin.bride'))}}</div>
						<div class="info-value">
							@if($invitation->groom && $invitation->bride)
								{{$invitation->groom}} & {{$invitation->bride}}
							@elseif($invitation->groom)
								{{$invitation->groom}}
							@elseif($invitation->bride)
								{{$invitation->bride}}
							@endif
						</div>
					</div>
					@endif
				</div>

				@if($invitation->description)
				<div class="info-item" style="margin-bottom: 20px;">
					<div class="info-label">{{__('admin.description')}}</div>
					<div class="info-value" style="font-size: 16px; font-weight: normal; line-height: 1.6;">{{$invitation->description}}</div>
				</div>
				@endif

				<div class="barcode"></div>
				<div class="barcode-number">{{ strtoupper($invitation->code ?? 'INV-' . $invitation->id) }}</div>

				<div class="tear-line"></div>

				<div class="response-buttons">
					<button class="btn btn-accept" onclick="acceptInvitation()">✓ قبول الدعوة</button>
					<button class="btn btn-decline" onclick="declineInvitation()">✗ رفض الدعوة</button>
				</div>

				
			</div>
		</div>
	</div>
</div>
