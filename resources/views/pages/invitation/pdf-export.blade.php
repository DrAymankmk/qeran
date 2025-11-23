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
	<h1>{{__('admin.invitations')}}</h1>

	<div class="header-info">
		<p>{{__('admin.created_at')}}:
			{{Carbon\Carbon::now()->locale(app()->getLocale())->translatedFormat('Y-m-d G:i')}}</p>
	</div>

	<table>
		<thead>
			<tr>
				<th>{{__('admin.id')}}</th>
				<th>{{__('admin.name')}}</th>
				/* category */
				<th>{{__('admin.category')}}</th>
				<th>{{__('admin.invitation-mime-type')}}</th>
				<th>{{__('admin.email')}}</th>
				<th>{{__('admin.invitation-type')}}</th>
				<th>{{__('admin.status')}}</th>
				<th>{{__('admin.host-name')}}</th>
				<th>{{__('admin.date')}}</th>
				<th>{{__('admin.time')}}</th>
				<th>{{__('admin.address')}}</th>
				<th>{{__('admin.groom')}}</th>
				<th>{{__('admin.bride')}}</th>
				<th>{{__('admin.created_at')}}</th>
			</tr>
		</thead>
		<tbody>
			@foreach($invitations as $invitation)
			<tr>
				<td class="text-center">{{$invitation->id}}</td>
				<td>{{$invitation->name}}</td>
				<td>{{$invitation->category?->name}}</td>
				<td>{{__('admin.media-type-'.$invitation->invitation_media_type)}}</td>
				<td>{{$invitation->email}}</td>
				<td>{{__('admin.invitation-type-'.$invitation->invitation_type)}}</td>
				<td>{{__('admin.invitation-status-'.$invitation->status)}}</td>
				<td>{{$invitation->host_name}}</td>
				<td>{{Carbon\Carbon::parse($invitation->date)->locale(app()->getLocale())->translatedFormat('Y-m-d')}}
				</td>
				<td>{{Carbon\Carbon::parse($invitation->time)->locale(app()->getLocale())->translatedFormat('H:i')}}
				</td>
				<td>{{$invitation->address}}</td>
				<td>{{$invitation->groom}}</td>
				<td>{{$invitation->bride}}</td>
				<td>{{Carbon\Carbon::parse($invitation->created_at)->locale(app()->getLocale())->translatedFormat('Y-m-d G:i')}}
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
</body>

</html>