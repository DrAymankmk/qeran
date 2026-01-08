<style>
* {
	margin: 0;
	padding: 0;
	box-sizing: border-box;
}

body {
	font-family: 'Georgia', serif;
	overflow: hidden;
	height: 100vh;
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.container {
	position: relative;
	width: 100%;
	height: 100vh;
	cursor: pointer;
}

.panel {
	position: absolute;
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	transition: transform 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55);
	display: flex;
	align-items: center;
	justify-content: center;
	color: white;
	font-size: 3em;
	font-weight: bold;
	text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.panel-top {
	top: 0;
	left: 0;
	width: 100%;
	height: 50%;
	border-bottom: 2px solid rgba(255, 255, 255, 0.3);
}

.panel-bottom {
	bottom: 0;
	left: 0;
	width: 100%;
	height: 50%;
	border-top: 2px solid rgba(255, 255, 255, 0.3);
}

.panel-left {
	top: 0;
	left: 0;
	width: 50%;
	height: 100%;
	border-right: 2px solid rgba(255, 255, 255, 0.3);
}

.panel-right {
	top: 0;
	right: 0;
	width: 50%;
	height: 100%;
	border-left: 2px solid rgba(255, 255, 255, 0.3);
}

.container.opened .panel-top {
	transform: translateY(-100%);
}

.container.opened .panel-bottom {
	transform: translateY(100%);
}

.container.opened .panel-left {
	transform: translateX(-100%);
}

.container.opened .panel-right {
	transform: translateX(100%);
}

.invitation {
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	text-align: center;
	opacity: 0;
	transition: opacity 0.6s ease 0.4s;
	z-index: 10;
	background: white;
	padding: 60px 80px;
	border-radius: 20px;
	box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
	max-width: 600px;
}

.container.opened .invitation {
	opacity: 1;
}

.invitation h1 {
	font-size: 2.5em;
	color: #667eea;
	margin-bottom: 20px;
	font-weight: normal;
}

.invitation h2 {
	font-size: 1.8em;
	color: #764ba2;
	margin-bottom: 30px;
	font-style: italic;
}

.invitation p {
	font-size: 1.2em;
	color: #555;
	line-height: 1.8;
	margin-bottom: 15px;
}

.invitation .details {
	margin-top: 30px;
	padding-top: 30px;
	border-top: 2px solid #667eea;
}

.invitation .date {
	font-size: 1.4em;
	color: #764ba2;
	font-weight: bold;
	margin-bottom: 10px;
}

.click-hint {
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	color: white;
	font-size: 1.5em;
	text-align: center;
	opacity: 1;
	transition: opacity 0.3s ease;
	z-index: 5;
	pointer-events: none;
	text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);
}

.container.opened .click-hint {
	opacity: 0;
}

@media (max-width: 768px) {
	.invitation {
		padding: 40px 30px;
		max-width: 90%;
	}

	.invitation h1 {
		font-size: 1.8em;
	}

	.invitation h2 {
		font-size: 1.3em;
	}

	.invitation p {
		font-size: 1em;
	}

	.panel {
		font-size: 2em;
	}
}

.response-buttons {
	position: absolute;
	/* bottom: -80px; */
	left: 50%;
	transform: translateX(-50%);
	display: flex;
	gap: 20px;
	z-index: 15;
}

.btn {
	padding: 16px 32px;
	border: none;
	border-radius: 12px;
	font-size: 16px;
	font-weight: 600;
	cursor: pointer;
	transition: all 0.3s ease;
	font-family: 'Georgia', serif;
	min-width: 180px;
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

<div class="container" id="container">
	<div class="panel panel-top"></div>
	<div class="panel panel-bottom"></div>
	<div class="panel panel-left"></div>
	<div class="panel panel-right"></div>

	<div class="click-hint">{{__('admin.click-to-open-the-invitation')}}</div>

	<div class="invitation">
		<h1>{{__('admin.you-are-invited')}}!</h1>
		<h2>{{$invitation->event_name ?? __('admin.an-evening-of-celebration')}}</h2>
		@if($invitation->description)
		<p>{{$invitation->description}}</p>
		@else
		<p>{{__('admin.join-us-for-an-unforgettable-evening')}}</p>
		@endif
		<div class="details">
			@if($invitation->date)
			<p class="date">{{$invitation->date}}</p>
			@endif
			@if($invitation->time)
			<p>{{ \Carbon\Carbon::parse($invitation->time)->format('h:i A') }}</p>
			@endif
			@if($invitation->address)
			<p>{!! nl2br(e($invitation->address)) !!}</p>
			@endif
			@if(isset($host_name) && $host_name)
			<p>{{__('admin.host-name')}}: {{$host_name}}</p>
			@endif
			@if($invitation->groom || $invitation->bride)
			@if($invitation->groom && $invitation->bride)
			<p>{{__('admin.groom')}}: {{$invitation->groom}} & {{__('admin.bride')}}:
				{{$invitation->bride}}</p>
			@elseif($invitation->groom)
			<p>{{__('admin.groom')}}: {{$invitation->groom}}</p>
			@elseif($invitation->bride)
			<p>{{__('admin.bride')}}: {{$invitation->bride}}</p>
			@endif
			@endif

			<div class="response-buttons">
				<button class="btn btn-accept" onclick="acceptInvitation()">✓
					{{__('admin.accept-invitation')}}</button>
				<button class="btn btn-decline" onclick="declineInvitation()">✗
					{{__('admin.refuse-invitation')}}</button>
			</div>
		</div>
	</div>


</div>

<script>
const container = document.getElementById('container');
let isOpened = false;

container.addEventListener('click', function() {
	if (!isOpened) {
		container.classList.add('opened');
		isOpened = true;
	} else {
		container.classList.remove('opened');
		isOpened = false;
	}
});
</script>