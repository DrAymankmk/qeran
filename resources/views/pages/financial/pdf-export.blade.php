<!DOCTYPE html>
<html lang="{{app()->getLocale()}}" dir="{{app()->getLocale() == 'ar' ? 'rtl' : 'ltr'}}">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{__('admin.financial-report')}}</title>
	<style>
		@if(app()->getLocale()=='ar') body {
			direction: rtl;
			text-align: right;
		}

		@else body {
			direction: ltr;
			text-align: left;
		}

		@endif h1 {
			text-align: center;
			color: #333;
			margin-bottom: 20px;
			font-size: 24px;
		}

		table {
			width: 100%;
			border-collapse: collapse;
			margin-top: 20px;
		}

		th,
		td {
			border: 1px solid #ddd;
			padding: 8px;
		}

		th {
			background-color: #f2f2f2;
			font-weight: bold;
			color: #333;
		}

		tr:nth-child(even) {
			background-color: #f9f9f9;
		}

		.text-center {
			text-align: center;
		}

		.header-info {
			margin-bottom: 20px;
		}

		.summary {
			margin-top: 20px;
			padding: 15px;
			background-color: #f8f9fa;
			border-radius: 5px;
		}
	</style>
</head>

<body>
	<h1>{{__('admin.financial-report')}}</h1>

	<div class="header-info">
		<p>{{__('admin.generated-at')}}:
			{{Carbon\Carbon::now()->locale(app()->getLocale())->translatedFormat('Y-m-d G:i')}}
		</p>
	</div>

	<table>
		<thead>
			<tr>
				<th>{{__('admin.id')}}</th>
				<th>{{__('admin.customer-name')}}</th>
				<th>{{__('admin.amount')}}</th>
				<th>{{__('admin.count')}}</th>
				<th>{{__('admin.payment-method')}}</th>
				<th>{{__('admin.status')}}</th>
				<th>{{__('admin.date')}}</th>
			</tr>
		</thead>
		<tbody>
			@foreach($payments as $payment)
			<tr>
				<td>{{$payment->id}}</td>
				<td>{{$payment->invitation->user->name ?? __('admin.no-data-available')}}</td>
				<td>{{number_format($payment->price, 2)}} {{__('admin.currency')}}</td>
				<td>{{$payment->count}}</td>
				<td>{{__('admin.bank-transfer')}}</td>
				<td>
					@if($payment->status == App\Helpers\Constant::PAID_STATUS['Paid'])
						{{__('admin.paid')}}
					@elseif($payment->status == App\Helpers\Constant::PAID_STATUS['Not Paid'])
						{{__('admin.not-paid')}}
					@elseif($payment->status == App\Helpers\Constant::PAID_STATUS['Pending Admin Payment'])
						{{__('admin.pending-admin-payment')}}
					@else
						{{__('admin.unknown')}}
					@endif
				</td>
				<td>{{Carbon\Carbon::parse($payment->created_at)->locale(app()->getLocale())->translatedFormat('Y-m-d G:i')}}</td>
			</tr>
			@endforeach
		</tbody>
	</table>

	<div class="summary">
		<h3>{{__('admin.summary')}}</h3>
		<p><strong>{{__('admin.total-amount')}}:</strong> {{number_format($totalAmount, 2)}} {{__('admin.currency')}}</p>
		<p><strong>{{__('admin.total-orders')}}:</strong> {{$totalOrders}}</p>
	</div>
</body>

</html>

