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
				<th>{{__('admin.id')}}</th>
				<th>{{__('admin.name')}}</th>
				<th>{{__('admin.email')}}</th>
				<th>{{__('admin.phone')}}</th>
				<th>{{__('admin.status')}}</th>
				<th>{{__('admin.created_at')}}</th>
			</tr>
		</thead>
		<tbody>
			@foreach($users as $user)
			<tr>
				<td>{{$user->id}}</td>
				<td>{{$user->name}}</td>
				<td>{{$user->email??__('admin.no-data-available')}} </td>

				<td style="direction: ltr;"><a
						href="tel:{{$user->phone}}{{$user->country_code}}">
						{{$user->country_code}}{{$user->phone}} </a></td>
				<td>


					@if($user->verified===1)
					{{__('admin.verified')}}
					@elseif($user->verified===2)
					{{__('admin.not-verified')}}
					@elseif($user->verified===3)
					{{__('admin.suspended')}}
					@else
					{{__('admin.not-verified')}}
					@endif

				</td>

				<td>
					{{Carbon\Carbon::parse($user->created_at)->locale(app()->getLocale())->translatedFormat('l dS F G:i - Y')}}
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</body>

</html>