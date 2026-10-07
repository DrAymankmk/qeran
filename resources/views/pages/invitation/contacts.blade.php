@extends('layouts.app')
@section('extra-css')
<link href="{{asset('admin_assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}"
	id="bootstrap-style" rel="stylesheet" type="text/css" />
<style>
	.contact-qr-grid {
		display: flex;
		flex-wrap: wrap;
		gap: 12px;
	}
	.contact-qr-card {
		border: 1px solid #e9ecef;
		border-radius: 8px;
		padding: 10px;
		text-align: center;
		min-width: 140px;
		background: #fff;
	}
	.contact-qr-card img {
		width: 110px;
		height: 110px;
		object-fit: contain;
	}
	.contact-qr-card .guest-name {
		font-size: 12px;
		margin-top: 6px;
		margin-bottom: 0;
		max-width: 130px;
		word-break: break-word;
	}
</style>
@endsection
@section('content')

<div class="row">
	<div class="col-12">
		<div class="page-title-box d-sm-flex align-items-center justify-content-between">
			<h4 class="mb-sm-0 font-size-18">{{ __('admin.invitation-contacts-qr') }}</h4>
			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin.Dashboard') }}</a></li>
					<li class="breadcrumb-item"><a href="{{ route('invitation.index') }}">{{ __('admin.invitations') }}</a></li>
					<li class="breadcrumb-item active">{{ __('admin.invitation-contacts-qr') }}</li>
				</ol>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				@if(session('error'))
					<div class="alert alert-danger">{{ session('error') }}</div>
				@endif
				@if(session('success'))
					<div class="alert alert-success">{{ session('success') }}</div>
				@endif
				<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
					<div>
						<h5 class="mb-1">{{ $invitation->event_name ?: $invitation->name }}</h5>
						<p class="text-muted mb-0">{{ __('admin.id') }}: {{ $invitation->id }}</p>
					</div>
					<div class="d-flex gap-2">
						<a href="{{ route('invitation.index') }}" class="btn btn-sm btn-secondary">
							<i class="mdi mdi-arrow-left"></i> {{ __('admin.back') }}
						</a>
						<a href="{{ route('invitation.contacts.export.pdf', $invitation) }}"
							class="btn btn-sm btn-danger">
							<i class="mdi mdi-file-pdf"></i> {{ __('admin.invitation-contacts-export-pdf') }}
						</a>
					</div>
				</div>

				@if($contactLogs->isEmpty())
					<div class="alert alert-info mb-0">
						{{ __('admin.invitation-contacts-empty') }}
					</div>
				@else
					<div class="table-responsive">
						<table class="table table-hover align-middle">
							<thead>
								<tr class="tr-colored">
									<th>{{ __('admin.id') }}</th>
									<th>{{ __('admin.name') }}</th>
									<th>{{ __('admin.phone') }}</th>
									<th>{{ __('admin.invitation-count') }}</th>
									<th>{{ __('admin.status') }}</th>
									<th>{{ __('admin.acceptance-status') }}</th>
									<th>{{ __('admin.invitation-contact-qr-codes') }}</th>
									<th>{{ __('admin.created_at') }}</th>
								</tr>
							</thead>
							<tbody>
								@foreach($contactLogs as $log)
									@php
										$sendKey = array_search((int) $log->send_status, \App\Helpers\Constant::INVITATION_CONTACT_SEND_STATUS, true);
										$acceptKey = $log->acceptance_status !== null
											? array_search((int) $log->acceptance_status, \App\Helpers\Constant::ACCEPTANCE_STATUS, true)
											: null;
										$guestCards = $log->guest_qr_cards ?? [];
									@endphp
									<tr>
										<td>{{ $log->id }}</td>
										<td>{{ $log->contact_name }}</td>
										<td>{{ trim(($log->country_code ?? '').' '.($log->phone ?? '')) }}</td>
										<td>{{ max(1, (int) ($log->invitation_count ?? 1)) }}</td>
										<td>
											@if($sendKey !== false)
												{{ __('admin.invitation-contact-send-status-'.$sendKey) }}
											@else
												{{ __('admin.no-data-available') }}
											@endif
										</td>
										<td>
											@if($acceptKey !== false && $acceptKey !== null)
												{{ __('admin.invitation-contact-acceptance-'.$acceptKey) }}
											@else
												{{ __('admin.invitation-contact-acceptance-pending') }}
											@endif
										</td>
										<td>
											@if(count($guestCards) === 0)
												<span class="text-muted">{{ __('admin.ib-preview-qr-missing') }}</span>
											@else
												<div class="contact-qr-grid">
													@foreach($guestCards as $guestQr)
														<div class="contact-qr-card">
															@if(!empty($guestQr['qr_url']))
																<a href="{{ $guestQr['qr_url'] }}" target="_blank" rel="noopener">
																	<img src="{{ $guestQr['qr_url'] }}" alt="{{ __('admin.ib-preview-qr-alt') }}">
																</a>
															@else
																<span class="text-muted">{{ __('admin.ib-preview-qr-missing') }}</span>
															@endif
															<p class="guest-name">{{ $guestQr['name'] ?? '' }}</p>
														</div>
													@endforeach
												</div>
											@endif
										</td>
										<td>
											{{ \Carbon\Carbon::parse($log->created_at)->locale(app()->getLocale())->translatedFormat('Y-m-d G:i') }}
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
					<div class="mt-3">
						{{ $contactLogs->links() }}
					</div>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
