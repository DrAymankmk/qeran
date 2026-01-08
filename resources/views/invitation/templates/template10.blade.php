<style>
* {
	margin: 0;
	padding: 0;
	box-sizing: border-box;
}

body {
	display: flex;
	justify-content: center;
	align-items: center;
	min-height: 100vh;
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	font-family: 'Georgia', serif;
	perspective: 2000px;
}

.book-container {
	position: relative;
	width: 400px;
	height: 500px;
	cursor: pointer;
	transition: transform 0.3s ease;
}

.book-container:hover:not(.open) {
	transform: translateY(-10px);
}

.book {
	position: relative;
	width: 100%;
	height: 100%;
	transform-style: preserve-3d;
	transition: transform 1s ease;
}

.book.opening {
	transform: rotateY(-25deg);
}

/* Book Cover */
.cover {
	position: absolute;
	width: 100%;
	height: 100%;
	background: linear-gradient(135deg, #8b4513 0%, #654321 100%);
	border: 3px solid #5a3a1a;
	border-radius: 0 8px 8px 0;
	transform-origin: left center;
	transform-style: preserve-3d;
	transition: transform 1.2s ease;
	box-shadow: 5px 5px 30px rgba(0, 0, 0, 0.5);
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: center;
	color: #f4e4c1;
	text-align: center;
	padding: 40px;
}

.cover::before {
	content: '';
	position: absolute;
	top: 20px;
	left: 20px;
	right: 20px;
	bottom: 20px;
	border: 2px solid #f4e4c1;
	border-radius: 4px;
}

.cover h1 {
	font-size: 2.5em;
	margin-bottom: 20px;
	z-index: 1;
	text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.cover p {
	font-size: 1.1em;
	z-index: 1;
	font-style: italic;
}

.book.opening .cover {
	transform: rotateY(-180deg);
}

/* Pages */
.page {
	position: absolute;
	width: 100%;
	height: 100%;
	background: #fffef0;
	border: 1px solid #d4c5a9;
	transform-origin: left center;
	transform-style: preserve-3d;
	backface-visibility: hidden;
	display: flex;
	justify-content: center;
	align-items: center;
	padding: 60px 40px;
	text-align: center;
	opacity: 0;
	transition: transform 0.8s ease, opacity 0.3s ease;
}

.page-back {
	position: absolute;
	width: 100%;
	height: 100%;
	background: #f9f8f0;
	border: 1px solid #d4c5a9;
	transform: rotateY(180deg);
	backface-visibility: hidden;
}

.page-content {
	color: #333;
	line-height: 1.8;
}

.page-content h2 {
	font-size: 2em;
	margin-bottom: 30px;
	color: #8b4513;
}

.page-content p {
	font-size: 1.1em;
	margin-bottom: 15px;
}

.page-content .date {
	font-size: 1.3em;
	font-weight: bold;
	color: #654321;
	margin: 25px 0;
}

.page-content .signature {
	margin-top: 40px;
	font-style: italic;
	font-size: 1.2em;
}

/* Page 1 */
.page1 {
	z-index: 3;
}

.book.opening .page1 {
	animation: flipPage1 0.8s ease 1.2s forwards;
}

/* Page 2 */
.page2 {
	z-index: 2;
}

.book.opening .page2 {
	animation: flipPage2 0.8s ease 2s forwards;
}

/* Page 3 - Final invitation */
.page3 {
	z-index: 1;
	background: linear-gradient(135deg, #fff9e6 0%, #fffef0 100%);
}

.book.opening .page3 {
	opacity: 1;
	animation: fadeIn 0.5s ease 2.8s forwards;
}

@keyframes flipPage1 {
	0% {
		transform: rotateY(0deg);
		opacity: 1;
	}

	100% {
		transform: rotateY(-180deg);
		opacity: 1;
	}
}

@keyframes flipPage2 {
	0% {
		transform: rotateY(0deg);
		opacity: 1;
	}

	100% {
		transform: rotateY(-180deg);
		opacity: 1;
	}
}

@keyframes fadeIn {
	from {
		opacity: 0;
	}

	to {
		opacity: 1;
	}
}

.click-hint {
	position: absolute;
	bottom: -50px;
	left: 50%;
	transform: translateX(-50%);
	color: white;
	font-size: 1em;
	opacity: 0.8;
	animation: pulse 2s ease-in-out infinite;
}

.book.opening~.click-hint {
	display: none;
}

@keyframes pulse {

	0%,
	100% {
		opacity: 0.8;
	}

	50% {
		opacity: 0.4;
	}
}

.response-buttons {
	position: absolute;
	/* bottom: -80px; */
	left: 50%;
	transform: translateX(-50%);
	display: flex;
	gap: 20px;
	z-index: 10;
}

.btn {
	padding: 14px 28px;
	border: none;
	border-radius: 8px;
	font-size: 15px;
	font-weight: 600;
	cursor: pointer;
	transition: all 0.3s ease;
	font-family: 'Georgia', serif;
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


<div class="book-container" id="bookContainer">
	<div class="book" id="book">
		<!-- Cover -->
		<div class="cover">
			<h1>{{__('admin.you-are-invited')}}</h1>
			<p>{{__('admin.click-to-open')}}</p>
		</div>

		<!-- Page 1 -->
		<div class="page page1">
			<div class="page-content">
				<h2>{{$invitation->event_name ?? __('admin.special-celebration')}}</h2>
				@if($invitation->description)
				<p>{{$invitation->description}}</p>
				@else
				<p>{{__('admin.we-are-delighted-to-invite-you')}}</p>
				<p>{{__('admin.your-presence-would-make-our-celebration-complete')}}</p>
				@endif
			</div>
			<div class="page-back"></div>
		</div>

		<!-- Page 2 -->
		<div class="page page2">
			<div class="page-content">
				<h2>{{__('admin.event-details')}}</h2>
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
			</div>
			<div class="page-back"></div>
		</div>

		<!-- Page 3 - Final Page -->
		<div class="page page3">
			<div class="page-content">
				<h2>{{__('admin.we-hope-to-see-you-there')}}!</h2>
				@if($invitation->groom || $invitation->bride)
				@if($invitation->groom && $invitation->bride)
				<p>{{__('admin.groom')}}: {{$invitation->groom}}</p>
				<p>{{__('admin.bride')}}: {{$invitation->bride}}</p>
				@elseif($invitation->groom)
				<p>{{__('admin.groom')}}: {{$invitation->groom}}</p>
				@elseif($invitation->bride)
				<p>{{__('admin.bride')}}: {{$invitation->bride}}</p>
				@endif
				@endif
				@if(isset($host_name) && $host_name)
				<p class="signature">{{__('admin.with-warmest-regards')}}<br>{{$host_name}}</p>
				@else
				<p class="signature">{{__('admin.with-warmest-regards')}}</p>
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
	<!-- <div class="click-hint">Click to open the book</div> -->

</div>

<script>
const bookContainer = document.getElementById('bookContainer');
const book = document.getElementById('book');
let isOpen = false;

bookContainer.addEventListener('click', () => {
	if (!isOpen) {
		book.classList.add('opening');
		bookContainer.classList.add('open');
		isOpen = true;
	}
});
</script>
