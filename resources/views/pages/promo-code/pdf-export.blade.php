<!DOCTYPE html>
<html lang="{{app()->getLocale()}}" dir="{{app()->getLocale() == 'ar' ? 'rtl' : 'ltr'}}">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{__('admin.invitation-requests')}}</title>
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
	</style>
</head>

<body>
	<h1>{{__('admin.users')}}</h1>

	<div class="header-info">
		<p>{{__('admin.created_at')}}:
			{{Carbon\Carbon::now()->locale(app()->getLocale())->translatedFormat('Y-m-d G:i')}}
		</p>
	</div>

	<table>
		<thead>
			<tr>
				<th scope="col">{{__('admin.id')}}</th>
				<th scope="col">{{__('admin.name')}}
				</th>
				<th scope="col">{{__('admin.code')}}
				</th>
				<th scope="col">{{__('admin.discount')}}
					(%)</th>
				<th scope="col">{{__('admin.package')}}
				</th>
				<th scope="col">
					{{__('admin.valid-date')}}
				</th>
				<th scope="col">
					{{__('admin.expire-date')}}
				</th>
				<th scope="col">{{__('admin.status')}}
				</th>
				<th scope="col">{{__('admin.usage')}}
				</th>
				<th scope="col">
					{{__('admin.created_at')}}
				</th>
			</tr>
		</thead>
		<tbody>
			@foreach($promoCodes as $promoCode)
			<tr>
				<td><a href="javascript: void(0);"
						class="text-body fw-bold">{{$promoCode->id}}</a>
				</td>
				<td>{{$promoCode->name}}</td>
				<td><span class="badge bg-primary">{{$promoCode->code}}</span>
				</td>
				<td>{{$promoCode->discount_percentage}}%
				</td>
				<td>
					@if($promoCode->package_id)
					{{$promoCode->package->id ?? __('admin.package')}}
					#{{$promoCode->package_id}}
					@else
					<span class="badge bg-info">{{__('admin.all-packages')}}</span>
					@endif
				</td>
				<td>{{Carbon\Carbon::parse($promoCode->valid_date)->format('Y-m-d')}}
				</td>
				<td>{{Carbon\Carbon::parse($promoCode->expire_date)->format('Y-m-d')}}
				</td>
				<td>
					@if($promoCode->is_active)
					<span class="badge bg-success">{{__('admin.active')}}</span>
					@else
					<span class="badge bg-danger">{{__('admin.inactive')}}</span>
					@endif
				</td>
				<td>
					@if($promoCode->usage_limit)
					{{$promoCode->used_count}} /
					{{$promoCode->usage_limit}}
					@else
					{{$promoCode->used_count}} /
					{{__('admin.unlimited')}}
					@endif
				</td>
				<td>
					{{Carbon\Carbon::parse($promoCode->created_at)->locale(app()->getLocale())->translatedFormat('l dS F G:i - Y')}}
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</body>

</html>