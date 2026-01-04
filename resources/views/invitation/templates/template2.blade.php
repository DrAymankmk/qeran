<style>
* {
	margin: 0;
	padding: 0;
	box-sizing: border-box;
}

.template2-wrapper {
	font-family: 'Cairo', 'Georgia', serif;
	background: linear-gradient(135deg, #2c1810 0%, #4a2c1f 100%);
	display: flex;
	justify-content: center;
	align-items: center;
	min-height: 110vh;
	padding: 20px;
	overflow-x: hidden;
}

.scroll-container {
	position: relative;
	width: 90%;
	max-width: 800px;
	height: 500px;
	perspective: 1500px;
	cursor: grab;
	user-select: none;
}

.scroll-container:active {
	cursor: grabbing;
}

.scroll-wrapper {
	position: relative;
	width: 100%;
	height: 100%;
	transform-style: preserve-3d;
}

.scroll-left,
.scroll-right {
	position: absolute;
	top: 0;
	width: 80px;
	height: 100%;
	background: linear-gradient(90deg, #d4b896 0%, #c9a876 50%, #b8965f 100%);
	border-radius: 40px;
	box-shadow:
		inset -5px 0 15px rgba(0, 0, 0, 0.3),
		inset 5px 0 15px rgba(255, 255, 255, 0.2),
		5px 10px 30px rgba(0, 0, 0, 0.5);
	z-index: 10;
	transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.scroll-left {
	left: 0;
	background: linear-gradient(90deg, #b8965f 0%, #c9a876 50%, #d4b896 100%);
	box-shadow:
		inset 5px 0 15px rgba(0, 0, 0, 0.3),
		inset -5px 0 15px rgba(255, 255, 255, 0.2),
		-5px 10px 30px rgba(0, 0, 0, 0.5);
		height: 600px;
}

.scroll-right {
	right: 0;
	height: 600px;
}

.parchment {
	
	position: absolute;
	left: 40px;
	right: 40px;
	top: 0;
	height: 600px;
	background:
		linear-gradient(135deg, rgba(139, 90, 43, 0.1) 0%, transparent 100%),
		linear-gradient(45deg, rgba(205, 170, 125, 0.2) 0%, transparent 100%),
		#f4e8d0;
	background-image:
		repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(139, 90, 43, 0.03) 2px, rgba(139, 90, 43, 0.03) 4px),
		repeating-linear-gradient(90deg, transparent, transparent 2px, rgba(139, 90, 43, 0.03) 2px, rgba(139, 90, 43, 0.03) 4px),
		linear-gradient(135deg, rgba(139, 90, 43, 0.1) 0%, transparent 100%),
		#f4e8d0;
	box-shadow:
		0 15px 40px rgba(0, 0, 0, 0.4),
		inset 0 0 100px rgba(139, 90, 43, 0.1);
	clip-path: inset(0);
	overflow-y: auto;
	transition: clip-path 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.parchment::before {
	content: '';
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background-image:
		radial-gradient(circle at 20% 30%, rgba(139, 90, 43, 0.08) 0%, transparent 50%),
		radial-gradient(circle at 80% 70%, rgba(139, 90, 43, 0.06) 0%, transparent 50%),
		radial-gradient(circle at 40% 80%, rgba(139, 90, 43, 0.05) 0%, transparent 50%);
	pointer-events: none;
}

.content {
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	width: 80%;
	text-align: center;
	opacity: 0;
	transition: opacity 0.8s ease 0.3s;
	color: #3d2817;
	padding: 20px;
}

.scroll-container.unrolled .content {
	opacity: 1;
}

.scroll-container.unrolled .scroll-left {
	transform: translateX(-60px);
}

.scroll-container.unrolled .scroll-right {
	transform: translateX(60px);
}

.scroll-container.unrolled .parchment {
	clip-path: inset(0 -10%);
}

.content h1 {
	font-size: 2.5em;
	margin-bottom: 20px;
	font-weight: normal;
	letter-spacing: 3px;
	text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
	font-family: 'Cairo', serif;
}

.content .divider {
	width: 200px;
	height: 2px;
	background: linear-gradient(90deg, transparent, #8b5a2b, transparent);
	margin: 20px auto;
}

.content p {
	font-size: 1.2em;
	line-height: 1.8;
	margin: 15px 0;
	font-family: 'Cairo', serif;
}

.content .date {
	font-size: 1.4em;
	font-weight: bold;
	margin: 25px 0;
	color: #6b4423;
}

.content .event-media-container {
	width: 100%;
	max-width: 400px;
	height: 250px;
	margin: 20px auto;
	border-radius: 10px;
	overflow: hidden;
	box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
}

.content .event-image,
.content .event-video {
	width: 100%;
	height: 100%;
	object-fit: cover;
	border-radius: 10px;
}

.content .event-details {
	margin-top: 20px;
	text-align: right;
}

.content .detail-item {
	margin: 15px 0;
	padding: 12px;
	background: rgba(139, 90, 43, 0.1);
	border-radius: 8px;
	border-right: 3px solid #8b5a2b;
}

.content .detail-label {
	font-weight: bold;
	font-size: 1em;
	margin-bottom: 5px;
	color: #6b4423;
}

.content .detail-value {
	font-size: 1.1em;
	color: #3d2817;
	line-height: 1.6;
}

.content .response-buttons {
	display: flex;
	gap: 15px;
	margin-top: 30px;
	justify-content: center;
}

.content .btn {
	flex: 1;
	max-width: 200px;
	padding: 15px 25px;
	border-radius: 25px;
	font-size: 1.1em;
	font-weight: 600;
	cursor: pointer;
	transition: all 0.3s ease;
	border: none;
	font-family: 'Cairo', sans-serif;
}

.content .btn-accept {
	background: linear-gradient(135deg, #4ade80, #22c55e);
	color: white;
	box-shadow: 0 4px 15px rgba(34, 197, 94, 0.4);
}

.content .btn-accept:hover {
	background: linear-gradient(135deg, #22c55e, #16a34a);
	transform: translateY(-2px);
	box-shadow: 0 6px 20px rgba(34, 197, 94, 0.5);
}

.content .btn-decline {
	background: transparent;
	color: #ef4444;
	border: 2px solid #ef4444;
	box-shadow: 0 4px 15px rgba(239, 68, 68, 0.2);
}

.content .btn-decline:hover {
	background: #ef4444;
	color: white;
	transform: translateY(-2px);
	box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
}

.instruction {
	position: absolute;
	bottom: -60px;
	left: 50%;
	transform: translateX(-50%);
	color: #d4b896;
	font-size: 0.9em;
	opacity: 1;
	transition: opacity 0.3s ease;
	white-space: nowrap;
	font-family: 'Cairo', sans-serif;
}

.scroll-container.unrolled .instruction {
	opacity: 0;
}

@media (max-width: 768px) {
	.scroll-container {
		height: 400px;
	}

	.content h1 {
		font-size: 1.8em;
	}

	.content p {
		font-size: 1em;
	}

	.content .date {
		font-size: 1.1em;
	}

	.content .event-media-container {
		height: 180px;
	}
}
</style>

<!-- Template 2: Scroll Invitation View -->
<div id="envelopeView" class="invitation-wrapper template2-wrapper">
	<div class="scroll-container" id="scrollContainer">
		<div class="scroll-wrapper">
			<div class="scroll-left"></div>
			<div class="scroll-right"></div>
			<div class="parchment">
				<div class="content">
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

					@if($invitation->date)
					<p class="date">{{$invitation->date}}</p>
					@endif

					@if($invitation->time)
					<p>في الساعة {{ \Carbon\Carbon::parse($invitation->time)->format('h:i A') }}</p>
					@endif

					<div class="divider"></div>

					@if($invitation->address)
					<p>{{$invitation->address}}</p>
					@endif

					@if($host_name)
					<p style="margin-top: 15px; font-style: italic;">{{__('admin.host-name')}}: {{$host_name}}</p>
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
						<button class="btn btn-accept" onclick="acceptInvitation()">✓ قبول الدعوة</button>
						<button class="btn btn-decline" onclick="declineInvitation()">✗ رفض الدعوة</button>
					</div>
				</div>
			</div>
		</div>
		<div class="instruction">اضغط  لفتح المخطوطة</div>
	</div>
</div>

<script>
(function() {
	const container = document.getElementById('scrollContainer');
	if (!container) return;

	let isUnrolled = false;
	let isDragging = false;
	let startX = 0;
	let dragDistance = 0;
	const dragThreshold = 50;

	function unroll() {
		if (!isUnrolled) {
			container.classList.add('unrolled');
			isUnrolled = true;
		}
	}

	function roll() {
		if (isUnrolled) {
			container.classList.remove('unrolled');
			isUnrolled = false;
		}
	}

	// Click to toggle
	container.addEventListener('click', (e) => {
		if (!isDragging) {
			if (isUnrolled) {
				roll();
			} else {
				unroll();
			}
		}
	});

	// Drag functionality
	container.addEventListener('mousedown', (e) => {
		isDragging = false;
		startX = e.clientX;
		dragDistance = 0;
	});

	container.addEventListener('mousemove', (e) => {
		if (startX !== 0) {
			dragDistance = Math.abs(e.clientX - startX);
			if (dragDistance > 5) {
				isDragging = true;
			}
		}
	});

	container.addEventListener('mouseup', (e) => {
		if (isDragging) {
			const deltaX = e.clientX - startX;
			if (Math.abs(deltaX) > dragThreshold) {
				if (deltaX > 0 && !isUnrolled) {
					unroll();
				} else if (deltaX < 0 && isUnrolled) {
					roll();
				}
			}
		}
		startX = 0;
		setTimeout(() => {
			isDragging = false;
		}, 100);
	});

	// Touch support
	container.addEventListener('touchstart', (e) => {
		isDragging = false;
		startX = e.touches[0].clientX;
		dragDistance = 0;
	});

	container.addEventListener('touchmove', (e) => {
		if (startX !== 0) {
			dragDistance = Math.abs(e.touches[0].clientX - startX);
			if (dragDistance > 5) {
				isDragging = true;
			}
		}
	});

	container.addEventListener('touchend', (e) => {
		if (isDragging) {
			const deltaX = e.changedTouches[0].clientX - startX;
			if (Math.abs(deltaX) > dragThreshold) {
				if (deltaX > 0 && !isUnrolled) {
					unroll();
				} else if (deltaX < 0 && isUnrolled) {
					roll();
				}
			}
		}
		startX = 0;
		setTimeout(() => {
			isDragging = false;
		}, 100);
	});
})();
</script>
