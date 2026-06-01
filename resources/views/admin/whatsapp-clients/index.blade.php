@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-12">
		<div class="page-title-box d-sm-flex align-items-center justify-content-between">
			<h4 class="mb-sm-0 font-size-18">{{ __('admin.whatsapp-clients-title') }}</h4>
			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('admin.dashboard') }}</a></li>
					<li class="breadcrumb-item active">{{ __('admin.whatsapp-clients-title') }}</li>
				</ol>
			</div>
		</div>
	</div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
	{{ session('success') }}
	<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
	{{ session('error') }}
	<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				@if(!$configured)
				<div class="alert alert-warning mb-0">
					{{ __('admin.whatsapp-gateway-not-configured') }}
				</div>
				@else
				<p class="text-muted">{{ __('admin.whatsapp-clients-description') }}</p>
				<p class="mb-3 small">
					<strong>{{ __('admin.whatsapp-gateway-url') }}:</strong>
					<code>{{ $gatewayUrl }}</code>
				</p>

				<ul class="nav nav-tabs nav-tabs-custom mb-3">
					<li class="nav-item">
						<a class="nav-link {{ $filter === 'all' ? 'active' : '' }}"
							href="{{ route('admin.whatsapp-clients.index', ['filter' => 'all']) }}">
							{{ __('admin.whatsapp-clients-filter-all') }}
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link {{ $filter === 'connected' ? 'active' : '' }}"
							href="{{ route('admin.whatsapp-clients.index', ['filter' => 'connected']) }}">
							{{ __('admin.whatsapp-clients-filter-connected') }}
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link {{ $filter === 'disconnected' ? 'active' : '' }}"
							href="{{ route('admin.whatsapp-clients.index', ['filter' => 'disconnected']) }}">
							{{ __('admin.whatsapp-clients-filter-disconnected') }}
						</a>
					</li>
				</ul>

				<div class="table-responsive">
					<table class="table table-bordered table-hover align-middle mb-0">
						<thead class="table-light">
							<tr>
								<th>{{ __('admin.whatsapp-clients-col-user') }}</th>
								<th>{{ __('admin.whatsapp-clients-col-phone') }}</th>
								<th>{{ __('admin.whatsapp-clients-col-session') }}</th>
								<th>{{ __('admin.whatsapp-clients-col-db-status') }}</th>
								<th>{{ __('admin.whatsapp-clients-col-live') }}</th>
								<th>{{ __('admin.whatsapp-clients-col-linked') }}</th>
								<th>{{ __('admin.whatsapp-clients-col-dates') }}</th>
								<th class="text-center">{{ __('admin.whatsapp-clients-col-actions') }}</th>
							</tr>
						</thead>
						<tbody>
							@forelse($sessions as $row)
							@php
								$live = $row['live'];
								$dbBadge = match($row['db_status']) {
									'connected' => 'success',
									'pending_pairing', 'pending_qr', 'starting' => 'warning',
									default => 'secondary',
								};
								$liveBadge = $live['linked'] ? 'success' : ($live['status'] === 'pending_pairing' ? 'warning' : 'secondary');
							@endphp
							<tr>
								<td>
									<div class="fw-medium">{{ $row['user_name'] }}</div>
									@if($row['user_email'])
									<div class="small text-muted">{{ $row['user_email'] }}</div>
									@endif
									<div class="small text-muted">ID: {{ $row['user_id'] }}</div>
								</td>
								<td>{{ $row['phone_display'] }}</td>
								<td><code class="small">{{ $row['session_id'] }}</code></td>
								<td>
									<span class="badge bg-{{ $dbBadge }}">{{ $row['db_status'] }}</span>
								</td>
								<td>
									@if($configured)
									<span class="badge bg-{{ $liveBadge }}">{{ $live['status'] }}</span>
									@if($live['socket_alive'])
									<span class="badge bg-info ms-1">{{ __('admin.whatsapp-clients-socket-on') }}</span>
									@endif
									@else
									<span class="text-muted">—</span>
									@endif
								</td>
								<td>
									@if($live['linked'])
									<span class="badge bg-success">
										<i class="mdi mdi-link-variant me-1"></i>{{ __('admin.whatsapp-clients-linked-yes') }}
									</span>
									@else
									<span class="badge bg-secondary">{{ __('admin.whatsapp-clients-linked-no') }}</span>
									@endif
								</td>
								<td class="small">
									@if($row['connected_at'])
									<div>{{ __('admin.whatsapp-clients-connected-at') }}: {{ $row['connected_at']->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</div>
									@endif
									@if($row['disconnected_at'])
									<div>{{ __('admin.whatsapp-clients-disconnected-at') }}: {{ $row['disconnected_at']->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</div>
									@endif
									@if($row['last_seen_at'])
									<div class="text-muted">{{ __('admin.whatsapp-clients-last-seen') }}: {{ $row['last_seen_at']->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</div>
									@endif
								</td>
								<td class="text-center">
									@if($row['user_id'])
									<form method="post"
										action="{{ route('admin.whatsapp-clients.disconnect', $row['user_id']) }}"
										class="d-inline"
										onsubmit="return confirm(@json(__('admin.whatsapp-client-disconnect-confirm', ['name' => $row['user_name']])));">
										@csrf
										<button type="submit" class="btn btn-sm btn-outline-danger">
											<i class="mdi mdi-link-off"></i>
											{{ __('admin.whatsapp-disconnect') }}
										</button>
									</form>
									@endif
								</td>
							</tr>
							@empty
							<tr>
								<td colspan="8" class="text-center text-muted py-4">
									{{ __('admin.whatsapp-clients-empty') }}
								</td>
							</tr>
							@endforelse
						</tbody>
					</table>
				</div>

				<div class="mt-3">
					{{ $sessions->links() }}
				</div>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection
