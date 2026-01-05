@extends('layouts.app')
@section('extra-css')
<link href="{{asset('admin_assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css')}}" id="bootstrap-style"
	rel="stylesheet" type="text/css" />
<link href="{{asset('admin_assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css')}}"
	id="bootstrap-style" rel="stylesheet" type="text/css" />
<link href="{{asset('admin_assets/libs/apexcharts/apexcharts.min.js')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')

<!-- start page title -->
<div class="row">
	<div class="col-12">
		<div class="page-title-box d-sm-flex align-items-center justify-content-between">
			<h4 class="mb-sm-0 font-size-18">{{__('admin.monthly-report')}}</h4>

			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a
							href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a>
					</li>
					<li class="breadcrumb-item"><a
							href="{{route('financial.index')}}">{{__('admin.financial-transactions')}}</a>
					</li>
					<li class="breadcrumb-item active">{{__('admin.monthly-report')}}</li>
				</ol>
			</div>

		</div>
	</div>
</div>
<!-- end page title -->

<!-- Statistics Cards -->
<div class="row">
	<div class="col-xl-4 col-md-6">
		<div class="card">
			<div class="card-body">
				<div class="d-flex justify-content-between">
					<div class="flex-1">
						<p class="text-truncate font-size-14 mb-2">{{__('admin.total-amount')}}</p>
						<h4 class="mb-2">{{number_format($totalAmount, 2)}} {{__('admin.currency')}}</h4>
					</div>
					<div class="avatar-sm">
						<span class="avatar-title bg-light text-primary rounded-3">
							<i class="mdi mdi-currency-usd font-size-18"></i>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-4 col-md-6">
		<div class="card">
			<div class="card-body">
				<div class="d-flex justify-content-between">
					<div class="flex-1">
						<p class="text-truncate font-size-14 mb-2">{{__('admin.total-orders')}}</p>
						<h4 class="mb-2">{{number_format($totalOrders)}}</h4>
					</div>
					<div class="avatar-sm">
						<span class="avatar-title bg-light text-success rounded-3">
							<i class="mdi mdi-shopping font-size-18"></i>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xl-4 col-md-6">
		<div class="card">
			<div class="card-body">
				<div class="d-flex justify-content-between">
					<div class="flex-1">
						<p class="text-truncate font-size-14 mb-2">{{__('admin.total-customers')}}</p>
						<h4 class="mb-2">{{number_format($totalCustomers)}}</h4>
					</div>
					<div class="avatar-sm">
						<span class="avatar-title bg-light text-info rounded-3">
							<i class="mdi mdi-account-multiple font-size-18"></i>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Chart -->
<div class="row">
	<div class="col-xl-12">
		<div class="card">
			<div class="card-body">
				<h4 class="card-title mb-4">{{__('admin.daily-breakdown')}}</h4>
				<div id="daily-breakdown-chart" data-colors='["#b49946"]'></div>
			</div>
		</div>
	</div>
</div>

<!-- Month Filter -->
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<form method="GET" action="{{route('financial.monthly-report')}}" class="mb-3">
					<div class="row">
						<div class="col-md-4">
							<label>{{__('admin.status')}}</label>
							<select name="status" class="form-control">
								<option value="all" {{request('status') == 'all' ? 'selected' : ''}}>{{__('admin.all')}}</option>
								<option value="{{App\Helpers\Constant::PAID_STATUS['Paid']}}" {{request('status') == App\Helpers\Constant::PAID_STATUS['Paid'] ? 'selected' : (!request()->has('status') ? 'selected' : '')}}>{{__('admin.paid')}}</option>
								<option value="{{App\Helpers\Constant::PAID_STATUS['Not Paid']}}" {{request('status') == App\Helpers\Constant::PAID_STATUS['Not Paid'] ? 'selected' : ''}}>{{__('admin.not-paid')}}</option>
								<option value="{{App\Helpers\Constant::PAID_STATUS['Pending Admin Payment']}}" {{request('status') == App\Helpers\Constant::PAID_STATUS['Pending Admin Payment'] ? 'selected' : ''}}>{{__('admin.pending-admin-payment')}}</option>
							</select>
						</div>
						<div class="col-md-4">
							<label>{{__('admin.select-month')}}</label>
							<input type="month" name="month" class="form-control" value="{{$month}}">
						</div>
						<div class="col-md-4">
							<label>&nbsp;</label>
							<button type="submit" class="btn btn-primary w-100">{{__('admin.filter')}}</button>
						</div>
					</div>
				</form>

				<div class="table-responsive mt-2">
					<table id="monthlyPaymentsTable"
						class="table table-hover dt-responsive nowrap"
						style="border-collapse: collapse; border-spacing: 0; width: 100%;">
						<thead>
							<tr class="tr-colored">
								<th scope="col">{{__('admin.id')}}</th>
								<th scope="col">{{__('admin.customer-name')}}</th>
								<th scope="col">{{__('admin.amount')}}</th>
								<th scope="col">{{__('admin.count')}}</th>
								<th scope="col">{{__('admin.payment-method')}}</th>
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

@section('extra-js')
<script src="{{asset('admin_assets/libs/apexcharts/apexcharts.min.js')}}"></script>
<script>
function getChartColorsArray(e) {
	if (null !== document.getElementById(e)) {
		var t = document.getElementById(e).getAttribute("data-colors");
		if (t) return (t = JSON.parse(t)).map(function(e) {
			var t = e.replace(" ", "");
			if (-1 === t.indexOf(",")) {
				var r = getComputedStyle(document.documentElement)
					.getPropertyValue(t);
				return r || t
			}
			var a = e.split(",");
			return 2 != a.length ? t : "rgba(" + getComputedStyle(
					document.documentElement)
				.getPropertyValue(a[0]) + "," + a[1] + ")"
		})
	}
}

$(document).ready(function() {
	// Initialize DataTable
	// $('#monthlyPaymentsTable').DataTable({
	// 	order: [[4, 'desc']],
	// 	buttons: ['copy', 'excel', 'pdf', 'print'],
	// 	dom: 'Bfrtip'
	// });

	// Daily Breakdown Chart
	var dailyBreakdownChartColors = getChartColorsArray("daily-breakdown-chart");
	if (dailyBreakdownChartColors) {
		var dailyData = @json($dailyData);
		var dailyBreakdownChartOptions = {
			chart: {
				height: 360,
				type: "bar",
				toolbar: {
					show: false
				}
			},
			plotOptions: {
				bar: {
					horizontal: false,
					columnWidth: "15%",
					endingShape: "rounded"
				}
			},
			dataLabels: {
				enabled: false
			},
			series: [{
				name: "{{__('admin.amount')}}",
				data: dailyData.map(item => item.amount)
			}, {
				name: "{{__('admin.orders')}}",
				data: dailyData.map(item => item.orders)
			}],
			xaxis: {
				categories: dailyData.map(item => item.date)
			},
			colors: dailyBreakdownChartColors,
			legend: {
				position: "bottom"
			},
			fill: {
				opacity: 1
			}
		};

		var dailyBreakdownChart = new ApexCharts(document.querySelector("#daily-breakdown-chart"), dailyBreakdownChartOptions);
		dailyBreakdownChart.render();
	}
});
</script>
@endsection


























































