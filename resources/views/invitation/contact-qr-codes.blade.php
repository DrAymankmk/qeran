<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
	<meta charset="utf-8" />
	<title>{{ $invitation->event_name }} — {{ __('messages.invitation_qr_codes_heading') }}</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<meta name="csrf-token" content="{{ csrf_token() }}" />
	@php app()->setLocale('ar'); @endphp
	<style>
		body {
			margin: 0;
			font-family: "Cairo", sans-serif;
			background: linear-gradient(135deg, #121223 0%, #1a1a3a 50%, #2d2d5f 100%);
			color: #fff;
			min-height: 100vh;
			padding: 24px 16px 48px;
		}
		.page-wrap {
			max-width: 420px;
			margin: 0 auto;
		}
		.page-title {
			text-align: center;
			font-size: 1.35rem;
			margin-bottom: 0.5rem;
		}
		.page-subtitle,
		.page-meta {
			text-align: center;
			opacity: 0.85;
			margin-bottom: 0.75rem;
		}
		.invitation-link-back {
			display: block;
			text-align: center;
			color: #c8a97a;
			margin-bottom: 1.5rem;
			text-decoration: none;
		}
		.response-card {
			border: 1px solid rgba(255, 255, 255, 0.12);
			border-radius: 16px;
			padding: 1.5rem 1.25rem;
			text-align: center;
			background: rgba(255, 255, 255, 0.04);
		}
		.response-card h2 {
			margin: 0 0 0.75rem;
			font-size: 1.2rem;
		}
		.response-card p {
			margin: 0 0 1.25rem;
			opacity: 0.9;
			line-height: 1.7;
		}
		.response-actions {
			display: flex;
			flex-direction: column;
			gap: 0.75rem;
		}
		.btn-accept,
		.btn-decline {
			border: none;
			border-radius: 12px;
			padding: 0.9rem 1rem;
			font-size: 1rem;
			font-weight: 700;
			cursor: pointer;
			font-family: inherit;
		}
		.btn-accept {
			background: #2ecc71;
			color: #fff;
		}
		.btn-accept:disabled,
		.btn-decline:disabled {
			opacity: 0.65;
			cursor: not-allowed;
		}
		.btn-decline {
			background: transparent;
			color: #ff8e8e;
			border: 1px solid rgba(255, 142, 142, 0.45);
		}
		.status-icon {
			width: 64px;
			height: 64px;
			border-radius: 50%;
			display: inline-flex;
			align-items: center;
			justify-content: center;
			font-size: 2rem;
			margin-bottom: 1rem;
		}
		.status-icon.accepted {
			background: rgba(46, 204, 113, 0.2);
			color: #2ecc71;
		}
		.status-icon.declined {
			background: rgba(255, 107, 107, 0.2);
			color: #ff6b6b;
		}
		.error-text {
			color: #ff8e8e;
			margin-top: 0.75rem;
			display: none;
		}
	</style>
</head>
<body>
	<div class="page-wrap">
		<h1 class="page-title">{{ $invitation->event_name }}</h1>

		@if(!empty($invitationLink))
			<a class="invitation-link-back" href="{{ $invitationLink }}">{{ __('messages.invitation_qr_codes_view_invitation') }}</a>
		@endif

		@if($acceptanceView === 'pending')
			<div class="response-card">
				<h2>{{ __('messages.invitation_qr_codes_rsvp_title') }}</h2>
				<p>{{ __('messages.invitation_qr_codes_rsvp_subtitle') }}</p>
				<div class="response-actions">
					<button type="button" class="btn-accept" onclick="respondToInvitation('accept')">
						{{ __('messages.invitation_qr_codes_accept') }}
					</button>
					<button type="button" class="btn-decline" onclick="respondToInvitation('decline')">
						{{ __('messages.invitation_qr_codes_decline') }}
					</button>
				</div>
				<p class="error-text" id="responseError"></p>
			</div>
		@elseif($acceptanceView === 'accepted')
			<p class="page-subtitle">{{ __('messages.invitation_qr_codes_page_subtitle') }}</p>
			<p class="page-meta">
				{{ __('messages.invitation_qr_codes_page_count', ['count' => max(1, (int) ($contactLog->invitation_count ?? 1))]) }}
			</p>

			@include('invitation.partials.qr-guests-section', [
				'invitation' => $invitation,
				'user' => $user,
				'contactLog' => $contactLog,
				'guestQrCards' => $guestQrCards,
			])
		@else
			<div class="response-card">
				<div class="status-icon declined">✗</div>
				<h2>{{ __('messages.invitation_qr_codes_declined_title') }}</h2>
				<p>{{ __('messages.invitation_qr_codes_declined_subtitle', ['event' => $invitation->event_name]) }}</p>
			</div>
		@endif
	</div>

	@if($acceptanceView === 'pending')
	<script>
		async function respondToInvitation(action) {
			const acceptBtn = document.querySelector('.btn-accept');
			const declineBtn = document.querySelector('.btn-decline');
			const errorEl = document.getElementById('responseError');
			const url = action === 'accept'
				? @json($routes['accept'])
				: @json($routes['decline']);

			acceptBtn.disabled = true;
			declineBtn.disabled = true;
			errorEl.style.display = 'none';
			errorEl.textContent = '';

			try {
				const response = await fetch(url, {
					method: 'POST',
					headers: {
						'Accept': 'application/json',
						'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
						'X-Requested-With': 'XMLHttpRequest',
					},
				});

				const data = await response.json();

				if (!response.ok || !data.success) {
					throw new Error(data.message || @json(__('messages.invitation_qr_codes_response_error')));
				}

				window.location.reload();
			} catch (error) {
				errorEl.textContent = error.message || @json(__('messages.invitation_qr_codes_response_error'));
				errorEl.style.display = 'block';
				acceptBtn.disabled = false;
				declineBtn.disabled = false;
			}
		}
	</script>
	@endif
</body>
</html>
