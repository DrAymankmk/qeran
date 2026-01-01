@php
$contactSection = $homePage->activeSections->where('name', 'contact')->first();
@endphp
@if($contactSection)
<style>
/* Make input text white when entering data */
.section-form-contact .form-control {
	color: #ffffff !important;
}

.section-form-contact .form-control::placeholder {
	color: rgba(255, 255, 255, 0.7) !important;
}

.section-form-contact .form-control:focus {
	color: #ffffff !important;
	background-color: rgba(255, 255, 255, 0.1);
	border-color: rgba(255, 255, 255, 0.3);
}

.section-form-contact textarea.form-control {
	color: #ffffff !important;
}
</style>
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
			<form id="contactForm" action="{{ route('contact.store') }}" method="post"
				class="b-form-contacts ui-form">
				@csrf
				<div class="row">
					<div class="col-md-6">
						<input id="user-name" type="text" name="name"
							placeholder="{{ __('frontend.name') }}"
							required="required"
							class="form-control @error('name') is-invalid @enderror"
							value="{{ old('name') }}" />
						@error('name')
						<div class="invalid-feedback">{{ $message }}</div>
						@enderror
						<input id="user-phone" type="tel" name="phone"
							placeholder="{{ __('frontend.phone') }}"
							required="required"
							class="form-control @error('phone') is-invalid @enderror"
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
							required="required"
							class="form-control last-block_mrg-btn_0 @error('subject') is-invalid @enderror"
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
						<button type="submit"
							class="btn btn-default">{{ __('frontend.send') }}</button>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
	// Handle form submission
	$('#contactForm').on('submit', function(e) {
		e.preventDefault();

		var form = $(this);
		var formData = form.serialize();
		var submitButton = form.find('button[type="submit"]');
		var originalButtonText = submitButton.html();

		// Disable submit button
		submitButton.prop('disabled', true).html(
			'{{__("frontend.sending")}}...');

		$.ajax({
			url: form.attr('action'),
			type: 'POST',
			data: formData,
			headers: {
				'X-Requested-With': 'XMLHttpRequest'
			},
			success: function(response) {
				if (response
					.success
				) {
					// Show success message
					Swal.fire({
						icon: 'success',
						title: '{{__("frontend.success")}}',
						text: response
							.message ||
							'{{__("frontend.contact-stored-success")}}',
						confirmButtonText: '{{__("frontend.ok")}}',
						confirmButtonColor: '#556ee6'
					});

					// Reset form
					form[0]
						.reset();
				} else {
					Swal.fire({
						icon: 'error',
						title: '{{__("frontend.error")}}',
						text: response
							.message ||
							'{{__("frontend.error-occurred")}}',
						confirmButtonText: '{{__("frontend.ok")}}',
						confirmButtonColor: '#f46a6a'
					});
				}
			},
			error: function(xhr) {
				var errorMessage =
					'{{__("frontend.error-occurred")}}';

				if (xhr.responseJSON &&
					xhr
					.responseJSON
					.message
				) {
					errorMessage
						=
						xhr
						.responseJSON
						.message;
				} else if (
					xhr
					.responseJSON &&
					xhr
					.responseJSON
					.errors
				) {
					var
						errors = [];
					$.each(xhr.responseJSON
						.errors,
						function(key,
							value
						) {
							errors.push(value[
								0
							]);
						}
					);
					errorMessage
						=
						errors
						.join(
							'<br>'
						);
				}

				Swal.fire({
					icon: 'error',
					title: '{{__("frontend.error")}}',
					html: errorMessage,
					confirmButtonText: '{{__("frontend.ok")}}',
					confirmButtonColor: '#f46a6a'
				});
			},
			complete: function() {
				// Re-enable submit button
				submitButton
					.prop('disabled',
						false
					)
					.html(
						originalButtonText
					);
			}
		});
	});

	// Handle success message from session (page reload after form submission)
	@if(session('success'))
	var successMessage = @json(session('success'));
	Swal.fire({
		icon: 'success',
		title: '{{__("frontend.success")}}',
		text: successMessage,
		confirmButtonText: '{{__("frontend.ok")}}',
		confirmButtonColor: '#556ee6'
	});
	@endif
});
</script>
@endpush

@endif
