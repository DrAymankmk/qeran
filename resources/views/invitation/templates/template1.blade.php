<style>
/* Template 1: Classic Envelope Design */
.template1-envelope {
	background: linear-gradient(135deg, #2a2a4a, #3d3d6b, #4a4a7a);
	width: 100%;
	height: 369.2307692308px;
	position: relative;
	border-radius: 15px;
	overflow: visible;
	justify-content: center;
	align-items: center;
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	z-index: 4;
	box-shadow: 0 15px 50px rgba(18, 18, 35, 0.8),
		inset 0 1px 0 rgba(255, 255, 255, 0.1), 0 5px 15px rgba(0, 0, 0, 0.4);
	border: 1px solid rgba(255, 255, 255, 0.1);
}

.template1-envelope:before,
.template1-envelope:after {
	content: "";
	position: absolute;
	bottom: 0;
}

.template1-envelope:before {
	right: 0;
	border-bottom: 0px solid transparent;
	border-top: 369.2307692308px solid transparent;
	border-right: 600px solid #3d3d6b;
	border-radius: 0 15px 0 0;
	z-index: 2;
}

.template1-envelope:after {
	left: 0;
	border-bottom: 0px solid transparent;
	border-top: 369.2307692308px solid transparent;
	border-left: 600px solid #4a4a7a;
	border-radius: 0 0 0 15px;
	z-index: 3;
}

.template1-flap {
	border-right: 300px solid transparent;
	border-top: 184.6153846154px solid #4a4a7a;
	border-left: 300px solid transparent;
	position: absolute;
	left: 0;
	top: 0;
	z-index: 4;
	transform-origin: 50% 0%;
	border-radius: 15px 15px 0 0;
	filter: drop-shadow(0 5px 15px rgba(18, 18, 35, 0.5));
}
</style>

<!-- Template 1: Classic Envelope Invitation View -->
<div id="envelopeView" class="invitation-wrapper">
	<div class="envelope template1-envelope">
		<div class="mask">
			<div class="card">
				<div class="face front">
					@if($invitation->image())
					<div class="event-media-container">
						@php
						$mediaUrl = $invitation->image();
						$extension = strtolower(pathinfo($mediaUrl,
						PATHINFO_EXTENSION));
						$videoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'avi',
						'mkv', 'm4v', '3gp', 'wmv'];
						$isVideo = in_array($extension, $videoExtensions);
						@endphp

						@if($isVideo)
						<a href="{{$mediaUrl}}" target="_blank"
							rel="noopener noreferrer">
							<video class="event-image event-video" autoplay
								muted loop playsinline
								preload="metadata"
								onloadstart="this.style.backgroundImage='none'"
								onerror="this.style.backgroundImage='none'; this.style.backgroundColor='rgba(0,0,0,0.3)'">
								<source src="{{$mediaUrl}}"
									type="video/{{$extension === 'mov' ? 'quicktime' : $extension}}">
								<p>Your browser does not support the
									video tag.</p>
							</video>
						</a>
						@else
						<a href="{{$mediaUrl}}" target="_blank"
							rel="noopener noreferrer">
							<img src="{{$mediaUrl}}"
								alt="{{$invitation->event_name}}"
								class="event-image" loading="lazy" />
						</a>
						@endif
					</div>
					@endif
					<h1 class="event-name">{{$invitation->event_name}}</h1>
					<div class="response-buttons">
						<button class="btn btn-primary high-button"
							onclick="openMediaInNewTab()">
							اضغط هنا لعرض الدعوة
						</button>
					</div>

					<div class="response-buttons">
						<button class="btn btn-accept" onclick="acceptInvitation()">
							✓ قبول الدعوة
						</button>
						<button class="btn btn-decline"
							onclick="declineInvitation()">
							✗ رفض الدعوة
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="flap template1-flap"></div>
	<button class="open-button" onclick="openEnvelope()">
		افتح الدعوة
	</button>
</div>