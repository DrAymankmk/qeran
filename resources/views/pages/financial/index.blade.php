@extends('layouts.app')
@section('extra-css')
<link href="{{asset('admin_assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}" id="bootstrap-style"
	rel="stylesheet" type="text/css" />
<link href="{{asset('admin_assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css')}}"
	id="bootstrap-style" rel="stylesheet" type="text/css" />
<link href="{{asset('admin_assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css')}}"
	id="bootstrap-style" rel="stylesheet" type="text/css" />
@endsection
@section('content')

<!-- start page title -->
<div class="row">
	<div class="col-12">
		<div class="page-title-box d-sm-flex align-items-center justify-content-between">
			<h4 class="mb-sm-0 font-size-18">{{__('admin.financial-transactions')}}</h4>

			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a
							href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a>
					</li>
					<li class="breadcrumb-item active">{{__('admin.financial-transactions')}}</li>
				</ol>
			</div>

		</div>
	</div>
</div>
<!-- end page title -->

<!-- Statistics Cards -->
<div class="row">
	<div class="col-xl-3 col-md-6">
		<div class="card">
			<div class="card-body">
				<div class="d-flex justify-content-between">
					<div class="flex-1">
						<p class="text-truncate font-size-14 mb-2">{{__('admin.total-visitors')}}</p>
						<h4 class="mb-2">{{number_format($stats['totalVisitors'])}}</h4>
					</div>
					<div class="avatar-sm">
						<span class="avatar-title bg-light text-primary rounded-3">
							<i class="mdi mdi-account-multiple font-size-18"></i>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-md-6">
		<div class="card">
			<div class="card-body">
				<div class="d-flex justify-content-between">
					<div class="flex-1">
						<p class="text-truncate font-size-14 mb-2">{{__('admin.total-customers')}}</p>
						<h4 class="mb-2">{{number_format($stats['totalCustomers'])}}</h4>
					</div>
					<div class="avatar-sm">
						<span class="avatar-title bg-light text-success rounded-3">
							<i class="mdi mdi-account-check font-size-18"></i>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-md-6">
		<div class="card">
			<div class="card-body">
				<div class="d-flex justify-content-between">
					<div class="flex-1">
						<p class="text-truncate font-size-14 mb-2">{{__('admin.conversion-rate')}}</p>
						<h4 class="mb-2">{{number_format($stats['conversionRate'], 2)}}%</h4>
					</div>
					<div class="avatar-sm">
						<span class="avatar-title bg-light text-info rounded-3">
							<i class="mdi mdi-chart-line font-size-18"></i>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-3 col-md-6">
		<div class="card">
			<div class="card-body">
				<div class="d-flex justify-content-between">
					<div class="flex-1">
						<p class="text-truncate font-size-14 mb-2">{{__('admin.total-revenue')}}</p>
						<h4 class="mb-2">{{number_format($payments->sum('price'), 2)}} {{__('admin.currency')}}</h4>
					</div>
					<div class="avatar-sm">
						<span class="avatar-title bg-light text-warning rounded-3">
							<i class="mdi mdi-currency-usd font-size-18"></i>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Charts Row -->
<div class="row">
	<div class="col-xl-6">
		<div class="card">
			<div class="card-body">
				<h4 class="card-title mb-4">{{__('admin.conversion-rate-chart')}}</h4>
				<div id="conversion-chart" data-colors='["#34c38f", "#f46a6a"]'></div>
			</div>
		</div>
	</div>
	<div class="col-xl-6">
		<div class="card">
			<div class="card-body">
				<h4 class="card-title mb-4">{{__('admin.monthly-orders-chart')}}</h4>
				<div id="monthly-orders-chart" data-colors='["#b49946"]'></div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-xl-12">
		<div class="card">
			<div class="card-body">
				<h4 class="card-title mb-4">{{__('admin.daily-orders-chart')}}</h4>
				<div id="daily-orders-chart" data-colors='["#34c38f"]'></div>
			</div>
		</div>
	</div>
</div>

<!-- Payments Table -->
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<div class="row mb-2">
					<div class="col-12 col-md-12">
						<div class="d-flex gap-2 mb-2">
							<a href="{{route('financial.monthly-report')}}"
								class="btn btn-info btn-rounded waves-effect waves-light">
								<i class="mdi mdi-calendar-month me-1"></i>
								{{__('admin.monthly-report')}}
							</a>
							<a href="{{route('financial.annual-report')}}"
								class="btn btn-success btn-rounded waves-effect waves-light">
								<i class="mdi mdi-calendar-year me-1"></i>
								{{__('admin.annual-report')}}
							</a>
							<a href="{{route('financial.export.pdf', request()->all())}}"
								class="btn btn-danger btn-rounded waves-effect waves-light">
								<i class="mdi mdi-file-pdf me-1"></i>
								{{__('admin.export-pdf')}}
							</a>
						</div>
					</div>
				</div>

				<!-- Filters -->
				<form method="GET" action="{{route('financial.index')}}" class="mb-3">
					<div class="row">
						<div class="col-md-2">
							<label>{{__('admin.status')}}</label>
							<select name="status" class="form-control">
								<option value="all" {{request('status') == 'all' ? 'selected' : ''}}>{{__('admin.all')}}</option>
								<option value="{{App\Helpers\Constant::PAID_STATUS['Paid']}}" {{request('status') == App\Helpers\Constant::PAID_STATUS['Paid'] ? 'selected' : (!request()->has('status') ? 'selected' : '')}}>{{__('admin.paid')}}</option>
								<option value="{{App\Helpers\Constant::PAID_STATUS['Not Paid']}}" {{request('status') == App\Helpers\Constant::PAID_STATUS['Not Paid'] ? 'selected' : ''}}>{{__('admin.not-paid')}}</option>
								<option value="{{App\Helpers\Constant::PAID_STATUS['Pending Admin Payment']}}" {{request('status') == App\Helpers\Constant::PAID_STATUS['Pending Admin Payment'] ? 'selected' : ''}}>{{__('admin.pending-admin-payment')}}</option>
							</select>
						</div>
						<div class="col-md-2">
							<label>{{__('admin.date-from')}}</label>
							<input type="date" name="date_from" class="form-control" value="{{request('date_from')}}">
						</div>
						<div class="col-md-2">
							<label>{{__('admin.date-to')}}</label>
							<input type="date" name="date_to" class="form-control" value="{{request('date_to')}}">
						</div>
						<div class="col-md-3">
							<label>{{__('admin.customer-name')}}</label>
							<input type="text" name="customer_name" class="form-control" value="{{request('customer_name')}}" placeholder="{{__('admin.search')}}">
						</div>
						<div class="col-md-3">
							<label>&nbsp;</label>
							<div class="d-flex gap-2">
								<button type="submit" class="btn btn-primary flex-fill">{{__('admin.filter')}}</button>
								<a href="{{route('financial.index')}}" class="btn btn-secondary">{{__('admin.reset')}}</a>
							</div>
						</div>
					</div>
				</form>

				<div class="table-responsive mt-2">
					<table id="paymentsTable"
						class="table table-hover dt-responsive nowrap"
						style="border-collapse: collapse; border-spacing: 0; width: 100%;">
						<thead>
							<tr class="tr-colored">
								<th scope="col">{{__('admin.id')}}</th>
								<th scope="col">{{__('admin.customer-name')}}</th>
								<th scope="col">{{__('admin.amount')}}</th>
								<th scope="col">{{__('admin.count')}}</th>
								<th scope="col">{{__('admin.payment-method')}}</th>
								<th scope="col">{{__('admin.status')}}</th>
								<th scope="col">{{__('admin.date')}}</th>
							</tr>
						</thead>
						<tbody>
							@foreach($payments as $payment)
							<tr>
								<td><a href="javascript: void(0);"
										class="text-body fw-bold">{{$payment->id}}</a>
								</td>
								<td>{{$payment->invitation->user->name ?? __('admin.no-data-available')}}</td>
								<td>{{number_format($payment->price, 2)}} {{__('admin.currency')}}</td>
								<td>{{$payment->count}}</td>
								<td>{{__('admin.bank-transfer')}}</td>
								<td>
									@if($payment->status == App\Helpers\Constant::PAID_STATUS['Paid'])
										<span class="badge bg-success">{{__('admin.paid')}}</span>
									@elseif($payment->status == App\Helpers\Constant::PAID_STATUS['Not Paid'])
										<span class="badge bg-danger">{{__('admin.not-paid')}}</span>
									@elseif($payment->status == App\Helpers\Constant::PAID_STATUS['Pending Admin Payment'])
										<span class="badge bg-warning">{{__('admin.pending-admin-payment')}}</span>
									@else
										<span class="badge bg-secondary">{{__('admin.unknown')}}</span>
									@endif
								</td>
								<td>
									{{Carbon\Carbon::parse($payment->created_at)->locale(app()->getLocale())->translatedFormat('l dS F G:i - Y')}}
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>

			</div>
		</div>
	</div>
</div>
<!-- end row -->
@endsection

@include('pages.financial.scripts.index-scripts')

