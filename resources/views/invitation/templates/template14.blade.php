<style>
* {
	margin: 0;
	padding: 0;
	box-sizing: border-box;
}

body {
	min-height: 100vh;
	display: flex;
	justify-content: center;
	align-items: center;
	background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
	font-family: 'Courier New', monospace;
	padding: 20px;
}

.ticket-container {
	position: relative;
	width: 600px;
	height: 450px;
	cursor: pointer;
	perspective: 1000px;
}

.ticket {
	position: absolute;
	width: 100%;
	height: 100%;
	background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
	border-radius: 12px;
	box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
	display: flex;
	transition: transform 0.3s ease;
}

.ticket:hover {
	transform: translateY(-5px);
	box-shadow: 0 25px 70px rgba(0, 0, 0, 0.6);
}

.ticket-left {
	flex: 2;
	padding: 30px;
	display: flex;
	flex-direction: column;
	justify-content: space-between;
	border-right: 2px dashed rgba(255, 255, 255, 0.3);
	position: relative;
}

.ticket-right {
	flex: 1;
	padding: 30px 20px;
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	text-align: center;
}

.ticket-title {
	font-size: 28px;
	font-weight: bold;
	color: #fff;
	text-transform: uppercase;
	letter-spacing: 2px;
	margin-bottom: 10px;
}

.ticket-subtitle {
	font-size: 14px;
	color: rgba(255, 255, 255, 0.8);
	margin-bottom: 20px;
}

.ticket-info {
	color: #fff;
	font-size: 14px;
	line-height: 1.8;
}

.ticket-info div {
	margin-bottom: 8px;
}

.label {
	color: rgba(255, 255, 255, 0.6);
	font-size: 11px;
	text-transform: uppercase;
	letter-spacing: 1px;
}

.barcode {
	width: 80px;
	height: 60px;
	background: repeating-linear-gradient(90deg, #000 0px, #000 2px, #fff 2px, #fff 4px);
	margin-bottom: 10px;
	border-radius: 4px;
}

.ticket-number {
	color: #fff;
	font-size: 12px;
	letter-spacing: 2px;
}

.tear-effect {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
	border-radius: 12px;
	transform-origin: left center;
	transition: all 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55);
	clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%);
	z-index: 2;
}

.ticket-container.torn .tear-effect {
	transform: translateX(-120%) rotateY(-25deg) rotateZ(-8deg);
	clip-path: polygon(0 0,
			95% 0,
			97% 5%,
			94% 8%,
			96% 12%,
			93% 15%,
			95% 20%,
			92% 25%,
			94% 30%,
			91% 35%,
			93% 40%,
			90% 45%,
			92% 50%,
			89% 55%,
			91% 60%,
			88% 65%,
			90% 70%,
			87% 75%,
			89% 80%,
			86% 85%,
			88% 90%,
			85% 95%,
			87% 100%,
			0 100%);
	box-shadow: -15px 0 40px rgba(0, 0, 0, 0.4);
}

.invitation-details {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
	border-radius: 12px;
	padding: 40px;
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: center;
	color: #fff;
	opacity: 0;
	transition: opacity 0.5s ease 0.3s;
	z-index: 1;
	text-align: center;
}

.ticket-container.torn .invitation-details {
	opacity: 1;
}

.invitation-title {
	font-size: 32px;
	font-weight: bold;
	margin-bottom: 20px;
	color: #f39c12;
	text-transform: uppercase;
	letter-spacing: 3px;
}

.invitation-text {
	font-size: 16px;
	line-height: 1.8;
	margin-bottom: 15px;
	max-width: 450px;
}

.invitation-highlight {
	font-size: 20px;
	color: #3498db;
	font-weight: bold;
	margin: 20px 0;
	padding: 15px 30px;
	border: 2px solid #3498db;
	border-radius: 8px;
	display: inline-block;
}

.click-hint {
	position: absolute;
	bottom: -40px;
	left: 50%;
	transform: translateX(-50%);
	color: rgba(255, 255, 255, 0.6);
	font-size: 14px;
	animation: pulse 2s infinite;
}

.ticket-container.torn .click-hint {
	display: none;
}

@keyframes pulse {

	0%,
	100% {
		opacity: 0.6;
	}

	50% {
		opacity: 1;
	}
}

@media (max-width: 650px) {
	.ticket-container {
		width: 100%;
		max-width: 500px;
		height: 240px;
	}

	.ticket-left {
		padding: 20px;
	}

	.ticket-title {
		font-size: 22px;
	}

	.ticket-info {
		font-size: 12px;
	}

	.invitation-title {
		font-size: 24px;
	}

	.invitation-text {
		font-size: 14px;
	}
}
</style>
<div class="ticket-container" id="ticketContainer">
	<div class="tear-effect">
		<div class="ticket">
			<div class="ticket-left">
				<div>
					<div class="ticket-title">
						{{$invitation->event_name ?? __('admin.event-name')}}</div>
					@if(isset($host_name) && $host_name)
					<div class="ticket-subtitle">{{__('admin.host-name')}}: {{$host_name}}
					</div>
					@else
					<div class="ticket-subtitle">{{__('admin.premium-experience')}}</div>
					@endif
				</div>
				<div class="ticket-info">
					@if($invitation->date)
					<div><span class="label">{{__('admin.date')}}:</span>
						{{$invitation->date}}</div>
					@endif
					@if($invitation->time)
					<div><span class="label">{{__('admin.time')}}:</span>
						{{ \Carbon\Carbon::parse($invitation->time)->format('h:i A') }}
					</div>
					@endif
					@if($invitation->address)
					<div><span class="label">{{__('admin.address')}}:</span> {!!
						nl2br(e($invitation->address)) !!}</div>
					@endif
					@if($invitation->groom || $invitation->bride)
					@if($invitation->groom && $invitation->bride)
					<div><span class="label">{{__('admin.couple')}}:</span>
						{{$invitation->groom}} & {{$invitation->bride}}</div>
					@elseif($invitation->groom)
					<div><span class="label">{{__('admin.groom')}}:</span>
						{{$invitation->groom}}</div>
					@elseif($invitation->bride)
					<div><span class="label">{{__('admin.bride')}}:</span>
						{{$invitation->bride}}</div>
					@endif
					@endif
				</div>
			</div>
			<div class="ticket-right">
				<div class="barcode"></div>
				<div class="ticket-number">
					{{ strtoupper($invitation->code ?? 'INV-' . $invitation->id) }}</div>
			</div>
		</div>
	</div>

	<div class="invitation-details">
		<div class="invitation-title">{{__('admin.you-are-invited')}}!</div>
		@if($invitation->description)
		<div class="invitation-text">
			{{$invitation->description}}
		</div>
		@else
		<div class="invitation-text">
			{{__('admin.join-us-for-an-unforgettable-evening')}}
		</div>
		@endif
		<div class="invitation-highlight">
			{{$invitation->event_name ?? __('admin.special-event')}}
		</div>
		<div class="invitation-text">
			@if($invitation->date)
			<strong>{{__('admin.date')}}:</strong> {{$invitation->date}}<br>
			@endif
			@if($invitation->time)
			<strong>{{__('admin.time')}}:</strong>
			{{ \Carbon\Carbon::parse($invitation->time)->format('h:i A') }}<br>
			@endif
			@if($invitation->address)
			<strong>{{__('admin.address')}}:</strong> {!! nl2br(e($invitation->address)) !!}<br>
			@endif
			@if(isset($host_name) && $host_name)
			<strong>{{__('admin.host-name')}}:</strong> {{$host_name}}
			@endif
		</div>
		<div class="response-buttons">
			<button class="btn btn-accept" onclick="acceptInvitation()">✓
				{{__('admin.accept-invitation')}}</button>
			<button class="btn btn-decline" onclick="declineInvitation()">✗
				{{__('admin.refuse-invitation')}}</button>
		</div>
	</div>

	<div class="click-hint">{{__('admin.click-to-open-the-invitation')}}</div>


</div>

<script>
const container = document.getElementById('ticketContainer');
let torn = false;

container.addEventListener('click', function() {
	if (!torn) {
		container.classList.add('torn');
		torn = true;
	} else {
		container.classList.remove('torn');
		torn = false;
	}
});
</script>