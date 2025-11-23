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
			<h4 class="mb-sm-0 font-size-18">{{__('admin.categories')}}</h4>

			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a
							href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a>
					</li>
					<li class="breadcrumb-item active">{{__('admin.categories')}}</li>
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
				<div class="row mb-2">
					<div class="col-12 col-md 12">
						<a href="{{route('category.create')}}"
							class="btn btn-primary btn-rounded waves-effect waves-light mb-2 me-2"><i
								class="mdi mdi-plus me-1"></i>
							{{__('admin.add-new')}} </a>
					</div>

					<div class="col-sm-8">
						<div class="text-sm-end">


						</div>
					</div>
				</div>
				<div class="table-responsive mt-2">
					<table id="categoriesTable"
						class="table table-hover dt-responsive nowrap"
						style="border-collapse: collapse; border-spacing: 0; width: 100%;">
						<thead>
							<tr class="tr-colored">
								<th scope="col">{{__('admin.id')}}</th>
								<th scope="col">{{__('admin.image')}}
								</th>
								<th scope="col">{{__('admin.name')}}
								</th>
								<th scope="col">
									{{__('admin.created_at')}}
								</th>
								<th scope="col">{{__('admin.actions')}}
								</th>
							</tr>
						</thead>
						<tbody>
							@foreach($categories as $category)
							<tr>
								<td><a href="javascript: void(0);"
										class="text-body fw-bold">{{$category->id}}</a>
								</td>
								<td>
									<a target="_blank"
										href="{{$category->img}}">
										<img class="header-profile-user"
											src="{{$category->image()}}"
											alt="Header Avatar"
											style="width: 50px; height: 50px; object-fit: cover;">
									</a>
								</td>
								<td>{{$category->name}}</td>
								<td>
									{{Carbon\Carbon::parse($category->created_at)->locale(app()->getLocale())->translatedFormat('l dS F G:i - Y')}}
								</td>
								<td>
									<div class="d-flex gap-3">
										<a href="javascript:void(0);"
											onclick="showCategoryDetails({{$category->id}})"
											title="{{__('admin.show')}}"
											class="text-info"><i
												class="mdi mdi-eye font-size-22"></i></a>
										<a href="{{route('category.edit',$category->id)}}"
											title="{{__('admin.edit')}}"
											class="text-warning"><i
												class="mdi mdi-file-edit-outline font-size-22"></i></a>

										<a onclick="openModalDelete({{$category->id}})"
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

@include('pages.category.scripts.index-scripts')


@section('modal')

<!-- Category Details Modal -->
<div class="modal fade" id="categoryDetailsModal" tabindex="-1" aria-labelledby="categoryDetailsModalLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="categoryDetailsModalLabel">
					{{__('admin.category-details')}}</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"
					aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.ar_name')}}:</strong> <span
							id="modal_ar_name"></span>
					</div>
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.en_name')}}:</strong> <span
							id="modal_en_name"></span>
					</div>
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.ar_title')}}:</strong> <span
							id="modal_ar_title"></span>
					</div>
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.en_title')}}:</strong> <span
							id="modal_en_title"></span>
					</div>
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.ar_description')}}:</strong> <span
							id="modal_ar_description"></span>
					</div>
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.en_description')}}:</strong> <span
							id="modal_en_description"></span>
					</div>
					<div class="col-md-12 mb-3">
						<strong>{{__('admin.image')}}:</strong> <span
							id="modal_image"></span>
					</div>
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.created_at')}}:</strong> <span
							id="modal_created_at"></span>
					</div>
					<div class="col-md-6 mb-3">
						<strong>{{__('admin.updated_at')}}:</strong> <span
							id="modal_updated_at"></span>
					</div>

				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary"
					data-bs-dismiss="modal">{{__('admin.close')}}</button>
			</div>
		</div>
	</div>
</div>

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
@endsection
