<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
	<meta charset="utf-8" />
	<title>{{ $invitation->event_name }} — {{ __('messages.invitation_qr_codes_heading') }}</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
	</style>
</head>
<body>
	<div class="page-wrap">
		<h1 class="page-title">{{ $invitation->event_name }}</h1>
		<p class="page-subtitle">{{ __('messages.invitation_qr_codes_page_subtitle') }}</p>
		<p class="page-meta">
			{{ __('messages.invitation_qr_codes_page_count', ['count' => max(1, (int) ($contactLog->invitation_count ?? 1))]) }}
		</p>

		@if(!empty($invitationLink))
			<a class="invitation-link-back" href="{{ $invitationLink }}">{{ __('messages.invitation_qr_codes_view_invitation') }}</a>
		@endif

		@include('invitation.partials.qr-guests-section', [
			'invitation' => $invitation,
			'user' => $user,
			'contactLog' => $contactLog,
			'guestQrCards' => $guestQrCards,
		])
	</div>
</body>
</html>
