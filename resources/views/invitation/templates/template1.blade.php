<style>
/* Template 1: Classic Envelope — responsive (scales with viewport) */
.invitation-wrapper.template1-wrapper {
	--t1-w: min(600px, 92vw);
	--t1-h: calc(var(--t1-w) * 369.2307692308 / 600);
	--t1-flap-half: calc(var(--t1-w) / 2);
	--t1-flap-h: calc(var(--t1-h) / 2);
	width: var(--t1-w);
	max-width: 92vw;
	margin-left: auto;
	margin-right: auto;
	min-height: calc(var(--t1-h) + 100px);
	box-sizing: border-box;
	overflow-x: hidden;
}

.template1-envelope {
	background: linear-gradient(135deg, #2a2a4a, #3d3d6b, #4a4a7a);
	width: 100%;
	height: var(--t1-h);
	position: relative;
	border-radius: 15px;
	overflow: visible;
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
	border-bottom: 0 solid transparent;
	border-top: var(--t1-h) solid transparent;
	border-right: var(--t1-w) solid #3d3d6b;
	border-radius: 0 15px 0 0;
	z-index: 2;
}

.template1-envelope:after {
	left: 0;
	border-bottom: 0 solid transparent;
	border-top: var(--t1-h) solid transparent;
	border-left: var(--t1-w) solid #4a4a7a;
	border-radius: 0 0 0 15px;
	z-index: 3;
}

.template1-flap {
	border-right: var(--t1-flap-half) solid transparent;
	border-top: var(--t1-flap-h) solid #4a4a7a;
	border-left: var(--t1-flap-half) solid transparent;
	position: absolute;
	left: 0;
	top: 0;
	width: 0;
	height: 0;
	z-index: 4;
	transform-origin: 50% 0%;
	border-radius: 15px 15px 0 0;
	filter: drop-shadow(0 5px 15px rgba(18, 18, 35, 0.5));
}

/* Card content inside template 1 */
.template1-wrapper .mask {
	width: 95%;
	max-width: calc(var(--t1-w) * 0.966);
	margin-left: auto;
	margin-right: auto;
	height: auto;
}

.template1-wrapper .card {
	width: 100%;
	height: auto;
	min-height: clamp(400px, 75vh, 800px);
}

.template1-wrapper .front {
	min-height: clamp(400px, 75vh, 800px);
	padding: clamp(14px, 3vw, 25px);
	box-sizing: border-box;
}

.template1-wrapper .event-media-container {
	width: 100%;
	max-width: 100%;
}

.template1-wrapper .front .event-image,
.template1-wrapper .front .event-video {
	width: 100%;
	max-width: 100%;
	height: clamp(140px, 42vw, 280px);
	object-fit: cover;
}

.template1-wrapper .front .event-name {
	font-size: clamp(1.35rem, 5vw, 2.8rem);
	line-height: 1.25;
	word-wrap: break-word;
	hyphens: auto;
}

.template1-wrapper .response-buttons {
	display: flex;
	flex-wrap: wrap;
	gap: clamp(10px, 2.5vw, 20px);
	width: 100%;
	margin-top: auto;
}

.template1-wrapper .response-buttons .btn {
	flex: 1 1 100%;
	min-width: 0;
	min-height: 48px;
	padding: clamp(12px, 2.5vw, 18px) clamp(16px, 4vw, 30px);
	font-size: clamp(0.95rem, 3.2vw, 1.2rem);
}

.template1-wrapper .open-button {
	top: calc(var(--t1-h) + 20px);
	left: 50%;
	transform: translateX(-50%);
	width: min(100%, calc(var(--t1-w) - 24px));
	max-width: 360px;
	padding: clamp(12px, 3vw, 20px) clamp(20px, 5vw, 50px);
	font-size: clamp(0.95rem, 3.5vw, 1.4rem);
	white-space: normal;
	text-align: center;
	box-sizing: border-box;
}

/* Tablet */
@media screen and (max-width: 1024px) {
	.invitation-wrapper.template1-wrapper {
		--t1-w: min(550px, 88vw);
	}

	.template1-wrapper .card,
	.template1-wrapper .front {
		min-height: clamp(380px, 70vh, 800px);
	}
}

/* Mobile portrait */
@media screen and (max-width: 768px) {
	.invitation-wrapper.template1-wrapper {
		--t1-w: min(400px, 95vw);
	}

	.template1-wrapper .response-buttons {
		flex-direction: column;
	}

	.template1-wrapper .response-buttons .btn {
		flex: 1 1 auto;
		width: 100%;
	}

	.template1-wrapper .card,
	.template1-wrapper .front {
		min-height: clamp(360px, 68vh, 700px);
	}

	.template1-wrapper .front .event-image,
	.template1-wrapper .front .event-video {
		height: clamp(160px, 45vw, 240px);
	}
}

/* Mobile landscape */
@media screen and (max-width: 768px) and (orientation: landscape) {
	.invitation-wrapper.template1-wrapper {
		--t1-w: min(450px, 82vw);
	}

	.template1-wrapper .card,
	.template1-wrapper .front {
		min-height: clamp(280px, 55vh, 650px);
	}

	.template1-wrapper .front .event-image,
	.template1-wrapper .front .event-video {
		height: clamp(120px, 28vh, 220px);
	}

	.template1-wrapper .open-button {
		top: calc(var(--t1-h) + 12px);
	}
}

/* Small phones */
@media screen and (max-width: 480px) {
	.invitation-wrapper.template1-wrapper {
		--t1-w: min(350px, 98vw);
	}

	.template1-envelope {
		border-radius: 12px;
	}

	.template1-envelope:before {
		border-radius: 0 12px 0 0;
	}

	.template1-envelope:after {
		border-radius: 0 0 0 12px;
	}

	.template1-flap {
		border-radius: 12px 12px 0 0;
	}

	.template1-wrapper .card,
	.template1-wrapper .front {
		min-height: clamp(320px, 62vh, 600px);
	}

	.template1-wrapper .front .event-image,
	.template1-wrapper .front .event-video {
		height: clamp(140px, 40vw, 180px);
	}

	.template1-wrapper .open-button {
		top: calc(var(--t1-h) + 16px);
	}
}

/* Very narrow screens */
@media screen and (max-width: 360px) {
	.invitation-wrapper.template1-wrapper {
		--t1-w: 100%;
		max-width: 100vw;
	}

	.template1-wrapper .front .event-name {
		font-size: 1.25rem;
	}
}
</style>

<!-- Template 1: Classic Envelope Invitation View -->
<div id="envelopeView" class="invitation-wrapper template1-wrapper">
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
