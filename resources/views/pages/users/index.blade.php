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
			<h4 class="mb-sm-0 font-size-18">{{__('admin.users')}}</h4>

			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a
							href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a>
					</li>
					<li class="breadcrumb-item active">{{__('admin.users')}}</li>
				</ol>
			</div>

		</div>
	</div>
</div>
<!-- end page title -->

<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">


				<div class="table-responsive mt-2">
					<table id="usersTable" class="table table-hover dt-responsive nowrap"
						style="border-collapse: collapse; border-spacing: 0; width: 100%;">
						<thead>
							<tr class="tr-colored">
								<th scope="col">{{__('admin.id')}}</th>
								<th scope="col">{{__('admin.img')}}</th>

								<th scope="col">{{__('admin.name')}}
								</th>
								<th scope="col">{{__('admin.email')}}
								</th>
								<th scope="col">{{__('admin.phone')}}
								</th>
								<th scope="col">{{__('admin.status')}}
								</th>
								<th scope="col">
									{{__('admin.created_at')}}
								</th>
								<th scope="col">{{__('admin.actions')}}
								</th>

							</tr>
						</thead>
						<tbody>
							@foreach($users as $user)

							<tr @if($user->verified_status==2) style="
								background: #f38e8e;" @endif>
								<td><a href="{{route('users.show',$user->id)}}"
										class="text-body fw-bold">{{$user->id}}</a>
								</td>
								<td>
									<a href="{{$user->image()}}"
										target="_blank"
										class="text-body fw-bold">

										<img class="rounded-circle header-profile-user"
											src="{{$user->image()}}"
											alt="Header Avatar">
									</a>
								</td>

								<td>{{$user->name}}</td>
								<td>{{$user->email??__('admin.no-data-available')}}
								</td>
								<td style="direction: ltr;"><a
										href="tel:{{$user->phone}}{{$user->country_code}}">
										{{$user->country_code}}{{$user->phone}}
									</a></td>
								<td>
									<select class="form-select status-select"
										data-user-id="{{$user->id}}"
										data-url="{{route('users.change-status',$user->id)}}"
										style="min-width: 150px;">
										<option value="1"
											{{$user->verified == 1 ? 'selected' : ''}}>
											{{__('admin.verified')}}
										</option>
										<option value="2"
											{{$user->verified == 2 ? 'selected' : ''}}>
											{{__('admin.not-verified')}}
										</option>
										<option value="3"
											{{$user->verified == 3 ? 'selected' : ''}}>
											{{__('admin.suspended')}}
										</option>
									</select>
								</td>

								<td>
									{{Carbon\Carbon::parse($user->created_at)->locale(app()->getLocale())->translatedFormat('l dS F G:i - Y')}}
								</td>
								<td>
									<div class="d-flex gap-3">


										<a href="{{route('users.show',$user->id)}}"
											title="{{__('admin.show')}}"
											class="text-info"><i
												class="mdi mdi-eye-check font-size-22"></i></a>
										<a href="{{route('users.edit',$user->id)}}"
											title="{{__('admin.edit')}}"
											class="text-warning"><i
												class="mdi mdi-file-edit-outline font-size-22"></i></a>

										<a onclick="openModalDelete({{$user->id}})"
											title="{{__('admin.delete')}}"
											class="text-danger"><i
												class="mdi mdi-trash-can-outline font-size-22"></i></a>

									</div>
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


@include('pages.users.scripts.index-scripts')

@component('layouts.includes.modal')
@slot('modalID')
deleteModal
@endslot
@slot('modalTitle')
{{__('admin.delete-data')}}
@endslot
@slot('modalMethodPutOrDelete')
@method('delete')
@endslot
@slot('modalContent')
<div class="text-center">
	<span class="text-danger font-16">
		{{__('admin.delete-message-confirm')}}
	</span>
</div>
@endslot
@endcomponent
