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
	background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
	font-family: 'Georgia', serif;
	padding: 20px;
}

.invitation-container {
	width: 500px;
	max-width: 90%;
	background: white;
	padding: 60px 40px;
	box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
	cursor: pointer;
	position: relative;
	overflow: hidden;
}

.invitation-container::before {
	content: '';
	position: absolute;
	top: 20px;
	left: 20px;
	right: 20px;
	bottom: 20px;
	border: 2px solid #d4af37;
	opacity: 0;
	transition: opacity 2s ease 0.5s;
}

.invitation-container.colored::before {
	opacity: 1;
}

.content {
	position: relative;
	z-index: 1;
	filter: grayscale(100%) contrast(1.2) brightness(0.95);
	transition: filter 2s ease;
}

.invitation-container.colored .content {
	filter: grayscale(0%) contrast(1) brightness(1);
}

.ornament {
	text-align: center;
	font-size: 48px;
	color: #d4af37;
	margin-bottom: 20px;
	opacity: 0.3;
	transition: opacity 1.5s ease 0.3s;
}

.invitation-container.colored .ornament {
	opacity: 1;
}

.header {
	text-align: center;
	margin-bottom: 40px;
}

.subtitle {
	font-size: 14px;
	letter-spacing: 3px;
	text-transform: uppercase;
	color: #8b7355;
	margin-bottom: 15px;
	opacity: 0.5;
	transition: opacity 1.5s ease 0.5s;
}

.invitation-container.colored .subtitle {
	opacity: 1;
}

.names {
	font-size: 42px;
	color: #2c3e50;
	margin: 20px 0;
	font-weight: normal;
	transition: color 2s ease 0.3s;
}

.invitation-container.colored .names {
	color: #1a252f;
}

.ampersand {
	font-size: 36px;
	color: #d4af37;
	font-style: italic;
	margin: 0 10px;
	opacity: 0.4;
	transition: opacity 1.5s ease 0.7s;
}

.invitation-container.colored .ampersand {
	opacity: 1;
}

.details {
	text-align: center;
	margin: 30px 0;
	line-height: 1.8;
}

.date {
	font-size: 18px;
	color: #34495e;
	margin: 10px 0;
	transition: color 1.8s ease 0.5s;
}

.invitation-container.colored .date {
	color: #2c3e50;
}

.time,
.venue {
	font-size: 16px;
	color: #7f8c8d;
	transition: color 1.8s ease 0.8s;
}

.invitation-container.colored .time,
.invitation-container.colored .venue {
	color: #5d6d7e;
}

.divider {
	width: 100px;
	height: 1px;
	background: #d4af37;
	margin: 30px auto;
	opacity: 0.3;
	transition: opacity 1.5s ease 1s;
}

.invitation-container.colored .divider {
	opacity: 0.8;
}

.message {
	text-align: center;
	font-size: 15px;
	font-style: italic;
	color: #95a5a6;
	line-height: 1.6;
	transition: color 1.8s ease 1.2s;
}

.invitation-container.colored .message {
	color: #8b7355;
}

.floral-left,
.floral-right {
	position: absolute;
	font-size: 80px;
	color: #d4af37;
	opacity: 0.15;
	transition: opacity 2s ease 0.8s;
}

.floral-left {
	top: 30px;
	left: 10px;
}

.floral-right {
	bottom: 30px;
	right: 10px;
}

.invitation-container.colored .floral-left,
.invitation-container.colored .floral-right {
	opacity: 0.4;
}

.click-hint {
	text-align: center;
	font-size: 12px;
	color: #95a5a6;
	margin-top: 30px;
	opacity: 1;
	transition: opacity 0.5s ease;
}

.invitation-container.colored .click-hint {
	opacity: 0;
}

.response-buttons {
	display: flex;
	gap: 15px;
	margin-top: 30px;
	justify-content: center;
	opacity: 0;
	transition: opacity 1.5s ease 1.5s;
}

.invitation-container.colored .response-buttons {
	opacity: 1;
}

.btn {
	padding: 12px 28px;
	border: none;
	border-radius: 8px;
	font-size: 14px;
	font-weight: 600;
	cursor: pointer;
	transition: all 0.3s ease;
	font-family: 'Georgia', serif;
	letter-spacing: 1px;
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
<div class="invitation-container" id="invitation">
	<div class="floral-left">❦</div>
	<div class="floral-right">❦</div>

	<div class="content">
		<div class="ornament">✦</div>

		<div class="header">
			@if(isset($host_name) && $host_name)
			<div class="subtitle">{{__('admin.host-name')}}: {{$host_name}}</div>
			@else
			<div class="subtitle">{{__('admin.you-are-invited')}}</div>
			@endif
			<div class="names">
				@if($invitation->groom && $invitation->bride)
				{{$invitation->groom}}
				<span class="ampersand">&</span>
				{{$invitation->bride}}
				@elseif($invitation->groom)
				{{$invitation->groom}}
				@elseif($invitation->bride)
				{{$invitation->bride}}
				@else
				{{$invitation->event_name ?? __('admin.celebration')}}
				@endif
			</div>
		</div>

		<div class="divider"></div>

		<div class="details">
			<div class="subtitle">{{__('admin.you-are-invited')}}</div>
			@if($invitation->event_name)
			<div class="subtitle" style="margin-top: 20px;">{{$invitation->event_name}}</div>
			@endif

			@if($invitation->date)
			<div class="date" style="margin-top: 30px;">{{$invitation->date}}</div>
			@endif

			@if($invitation->time)
			<div class="time" style="margin-top: 20px;">
				{{ \Carbon\Carbon::parse($invitation->time)->format('h:i A') }}</div>
			@endif

			@if($invitation->address)
			<div class="venue" style="margin-top: 10px;">{!! nl2br(e($invitation->address)) !!}</div>
			@endif
		</div>

		<div class="divider"></div>

		@if($invitation->description)
		<div class="message">
			{{$invitation->description}}
		</div>
		@else
		<div class="message">
			{{__('admin.celebration')}}
		</div>
		@endif

		<div class="ornament" style="margin-top: 30px; font-size: 32px;">✦</div>

		<div class="response-buttons">
			<button class="btn btn-accept" onclick="acceptInvitation()">✓
				{{__('admin.accept-invitation')}}</button>
			<button class="btn btn-decline" onclick="declineInvitation()">✗
				{{__('admin.refuse-invitation')}}</button>
		</div>
	</div>

	<div class="click-hint">Click to reveal colors</div>
</div>

<script>
const invitation = document.getElementById('invitation');

invitation.addEventListener('click', function() {
	this.classList.add('colored');
});
</script>
