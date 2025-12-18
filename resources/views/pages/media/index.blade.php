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
			<h4 class="mb-sm-0 font-size-18">Media Library</h4>

			<div class="page-title-right">
				<ol class="breadcrumb m-0">
					<li class="breadcrumb-item"><a
							href="{{route('admin.dashboard')}}">{{__('admin.Dashboard')}}</a>
					</li>
					<li class="breadcrumb-item active">Media</li>
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
					<div class="col-sm-12">
						<div class="text-sm-start">
							<a href="{{route('media.create')}}"
								class="btn btn-primary btn-rounded waves-effect waves-light mb-2 me-2"><i
									class="mdi mdi-plus me-1"></i>
								Upload New Media </a>
						</div>
					</div>
				</div>

				<!-- Filters -->
				<form method="GET" action="{{route('media.index')}}" class="mb-3">
					<div class="row g-3">
						<div class="col-md-3">
							<input type="text" name="search"
								class="form-control"
								placeholder="Search by name..."
								value="{{request('search')}}">
						</div>
						<div class="col-md-3">
							<select name="file_type" class="form-select">
								<option value="">All Types</option>
								<option value="1"
									{{request('file_type') == '1' ? 'selected' : ''}}>
									Image</option>
								<option value="2"
									{{request('file_type') == '2' ? 'selected' : ''}}>
									Video</option>
								<option value="3"
									{{request('file_type') == '3' ? 'selected' : ''}}>
									Audio</option>
								<option value="4"
									{{request('file_type') == '4' ? 'selected' : ''}}>
									GIF</option>
							</select>
						</div>
						<div class="col-md-3">
							<select name="bucket_name" class="form-select">
								<option value="">All Folders</option>
								@foreach($bucketNames as $bucket)
								<option value="{{$bucket}}"
									{{request('bucket_name') == $bucket ? 'selected' : ''}}>
									{{$bucket}}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-3">
							<button type="submit"
								class="btn btn-primary">Filter</button>
							<a href="{{route('media.index')}}"
								class="btn btn-secondary">Reset</a>
						</div>
					</div>
				</form>

				<div class="table-responsive mt-2">
					<table id="mediaTable" class="table table-hover dt-responsive nowrap"
						style="border-collapse: collapse; border-spacing: 0; width: 100%;">
						<thead>
							<tr class="tr-colored">
								<th scope="col">{{__('admin.id')}}</th>
								<th scope="col">Preview</th>
								<th scope="col">Original Name</th>
								<th scope="col">Type</th>
								<th scope="col">Folder</th>
								<th scope="col">Size</th>
								<th scope="col">Related To</th>
								<th scope="col">
									{{__('admin.created_at')}}
								</th>
								<th scope="col">{{__('admin.actions')}}
								</th>
							</tr>
						</thead>
						<tbody>
							@foreach($media as $item)
							<tr>
								<td><a href="javascript: void(0);"
										class="text-body fw-bold">{{$item->id}}</a>
								</td>
								<td>
									@if($item->file_type == 1)
									{{-- Image --}}
									<a target="_blank"
										href="{{$item->get_path()}}">
										<img class="header-profile-user"
											src="{{$item->get_path()}}"
											alt="Media Preview"
											style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
									</a>
									@elseif($item->file_type == 2)
									{{-- Video --}}
									<i
										class="mdi mdi-video font-size-24 text-danger"></i>
									@elseif($item->file_type == 3)
									{{-- Audio --}}
									<i
										class="mdi mdi-music font-size-24 text-primary"></i>
									@elseif($item->file_type == 4)
									{{-- GIF --}}
									<i
										class="mdi mdi-image font-size-24 text-success"></i>
									@else
									<i
										class="mdi mdi-file font-size-24 text-muted"></i>
									@endif
								</td>
								<td>{{$item->original_name ?? 'N/A'}}
								</td>
								<td>
									@if($item->file_type == 1)
									<span
										class="badge bg-info">Image</span>
									@elseif($item->file_type == 2)
									<span
										class="badge bg-danger">Video</span>
									@elseif($item->file_type == 3)
									<span
										class="badge bg-primary">Audio</span>
									@elseif($item->file_type == 4)
									<span
										class="badge bg-success">GIF</span>
									@else
									<span
										class="badge bg-secondary">Unknown</span>
									@endif
								</td>
								<td><code>{{$item->bucket_name}}</code>
								</td>
								<td>
									@if($item->size)
									{{number_format($item->size / 1024, 2)}}
									KB
									@else
									N/A
									@endif
								</td>
								<td>
									@if($item->morphable_type &&
									$item->morphable_id &&
									$item->morphable_type !=
									'App\Models\Media')
									<small>{{class_basename($item->morphable_type)}}
										#{{$item->morphable_id}}</small>
									@else
									<span
										class="text-muted">Standalone</span>
									@endif
								</td>
								<td>
									{{Carbon\Carbon::parse($item->created_at)->locale(app()->getLocale())->translatedFormat('l dS F G:i - Y')}}
								</td>
								<td>
									<div class="d-flex gap-3">
										<a href="{{$item->get_path()}}"
											target="_blank"
											title="View"
											class="text-info">
											<i
												class="mdi mdi-eye font-size-22"></i>
										</a>
										<a href="{{route('media.edit', $item->id)}}"
											title="{{__('admin.edit')}}"
											class="text-warning">
											<i
												class="mdi mdi-file-edit-outline font-size-22"></i>
										</a>
										<!-- <a onclick="openModalDelete({{$item->id}})"
											title="{{__('admin.delete')}}"
											class="text-danger">
											<i
												class="mdi mdi-trash-can-outline font-size-22"></i>
										</a> -->
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

@include('pages.media.scripts.index-scripts')

@section('modal')
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
