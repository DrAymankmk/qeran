@php
$contactSection = $homePage->activeSections->where('name', 'contact')->first();
@endphp
@if($contactSection)
<div class="block-table block-table-md">
	<div class="block-table__cell col-md-6">
		<section class="section-form-contact section-form-contact_color_white bg-primary">
			<div class="ui-decor-1"><img
					src="{{ asset('frontend/assets/media/general/ui-decor-1_wh.png') }}"
					alt="decor">
			</div>
			<h2 class="ui-title-block"><span>{{ $contactSection->title }}</span></h2>
			<div class="ui-subtitle-block">{{ $contactSection->subtitle }}</div>
			<div id="success"></div>
			@if(session('success'))
				<div class="alert alert-success alert-dismissible fade show" role="alert">
					{{ session('success') }}
					<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
				</div>
			@endif
			@if($errors->any())
				<div class="alert alert-danger alert-dismissible fade show" role="alert">
					<ul class="mb-0">
						@foreach($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
					<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
				</div>
			@endif
			<form id="contactForm" action="{{ route('contact.store') }}" method="post" class="b-form-contacts ui-form">
				@csrf
				<div class="row">
					<div class="col-md-6">
						<input id="user-name" type="text" name="name"
							placeholder="{{ __('frontend.name') }}"
							required="required" class="form-control @error('name') is-invalid @enderror" 
							value="{{ old('name') }}" />
						@error('name')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
						<input id="user-phone" type="tel" name="phone"
							placeholder="{{ __('frontend.phone') }}"
							required="required" class="form-control @error('phone') is-invalid @enderror"
							value="{{ old('phone') }}" />
						@error('phone')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
						<input type="hidden" name="country_code" value="966">
					</div>
					<div class="col-md-6">
						<input id="user-email" type="email" name="email"
							placeholder="{{ __('frontend.email') }}"
							class="form-control @error('email') is-invalid @enderror"
							value="{{ old('email') }}" />
						@error('email')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
						<input id="user-subject" type="text" name="subject"
							placeholder="{{ __('frontend.subject') }}"
							required="required" class="form-control last-block_mrg-btn_0 @error('subject') is-invalid @enderror"
							value="{{ old('subject') }}" />
						@error('subject')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12">
						<textarea id="user-message" name="message" rows="3"
							placeholder="{{ __('frontend.message') }}"
							required="required"
							class="form-control @error('message') is-invalid @enderror">{{ old('message') }}</textarea>
						@error('message')
							<div class="invalid-feedback">{{ $message }}</div>
						@enderror
						<button type="submit" class="btn btn-default">{{ __('frontend.send') }}</button>
					</div>
				</div>
			</form>
			<!-- end .b-form-contact-->

		</section>
	</div>
	@if($contactSection->image)
	<div class="block-table__cell col-md-6"><img src="{{ $contactSection->image->getUrl() }}" alt="foto"></div>
	@else
	<div class="block-table__cell col-md-6"><img src="{{ asset('frontend/assets/media/content/960x750/2.jpg') }}"
			alt="foto"></div>
	@endif

</div>

@endif
