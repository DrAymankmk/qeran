<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-body">
				<h4 class="card-title mb-4"> {{__('admin.invitation-requests')}} </h4>
				<div class="table-responsive">
					<table class="table align-middle table-nowrap table-check">
						<thead class="table-light">
							<tr>
								<th scope="col">{{__('admin.id')}}</th>
								<th scope="col">{{__('admin.user')}}
								</th>
								<th scope="col">{{__('admin.name')}}
								</th>
								<!-- status -->
								<th scope="col">
									{{__('admin.status')}}
								</th>
								<th scope="col">
									{{__('admin.invitation-mime-type')}}
								</th>
								<!-- <th scope="col">
									{{__('admin.invitation-uploaded-image')}}
								</th>
								<th scope="col">
									{{__('admin.invitation-uploaded-video')}}
								</th>
								<th scope="col">
									{{__('admin.invitation-uploaded-audio')}}
								</th>
								<th scope="col">{{__('admin.desc')}}
								</th>
								<th scope="col">
									{{__('admin.receipt-image')}}
								</th> -->
								<!-- <th scope="col">
									{{__('admin.paid-status')}}
								</th> -->
								<th scope="col">
									{{__('admin.created_at')}}
								</th>
								<th scope="col">{{__('admin.actions')}}
								</th>
							</tr>
						</thead>
						<tbody>
							@foreach($requestInvitations as $requestInvitation)

							<tr>
								<td><a href="javascript: void(0);"
										class="text-body fw-bold">{{$requestInvitation->id}}</a>
								</td>
								<td> <a href="{{route('users.show',$requestInvitation->user_id)}}"
										target="_blank">
										{{$requestInvitation->user?->name}}
									</a></td>

								<td> {{$requestInvitation->name}}</td>
								<td> {{__('admin.invitation-status-'.$requestInvitation->status)}}
								</td>
								<td> {{__('admin.media-type-'.$requestInvitation->invitation_media_type)}}
								</td>
								<!-- <td>
									@if($requestInvitation->designImage())
									<a target="_blank"
										href="{{$requestInvitation->designImage()}}">

										<img class=" header-profile-user"
											src="{{$requestInvitation->designImage()}}"
											alt="Invitation">
									</a>
									@else
									{{__('admin.no-data-available')}}
									@endif
								</td>
								<td>
									@if($requestInvitation->designVideo())

									<video width="150"
										height="150"
										controls>
										<source src="{{$requestInvitation->designVideo()}}"
											type="video/mp4">
										<source src="{{$requestInvitation->designVideo()}}"
											type="video/ogg">
										Your browser does
										not support the
										video tag.
									</video>
									@else
									{{__('admin.no-data-available')}}
									@endif

								</td>
								<td>
									@if($requestInvitation->designAudio())

									<audio controls>
										<source src="{{$requestInvitation->designAudio()}}"
											type="audio/ogg">
										<source src="{{$requestInvitation->designAudio()}}"
											type="audio/mpeg">
										Your browser does
										not support the
										audio element.
									</audio>
									@else
									{{__('admin.no-data-available')}}
									@endif

								</td>
								<td>
									{{$requestInvitation->description}}
								</td>
								<td>
									@if($requestInvitation->receiptImage())
									<a target="_blank"
										href="{{$requestInvitation->receiptImage()}}">

										<img class=" header-profile-user"
											src="{{$requestInvitation->receiptImage()}}"
											alt="Invitation">
									</a>
									@else
									{{__('admin.no-data-available')}}
									@endif
								</td> -->


								{{-- <td>--}}
								{{-- <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">--}}
								{{-- <input class="form-check-input" type="checkbox"--}}
								{{-- onchange="return window.location.href = '{{route('invitations.change-status',$requestInvitation->id)}}'"--}}
								{{-- @if($requestInvitation->paid==1)checked="" @endif>--}}

								{{-- </div>--}}
								{{-- </td>--}}


								<td>
									{{Carbon\Carbon::parse($requestInvitation->created_at)->locale(app()->getLocale())->translatedFormat('l dS F G:i - Y')}}
								</td>
								<td>
									<div class="d-flex gap-3">
										<a href="javascript:void(0);"
											onclick="showInvitationDetails({{$requestInvitation->id}})"
											title="{{__('admin.show')}}"
											class="text-info"><i
												class="mdi mdi-eye font-size-22"></i></a>
										{{-- @can('edit_categories')--}}

										<a href="{{route('invitation.edit',$requestInvitation->id)}}"
											title="{{__('admin.edit')}}"
											class="text-warning"><i
												class="mdi mdi-file-edit-outline font-size-22"></i></a>
										{{-- @endcan--}}
										{{-- @can('delete_categories')--}}

										<a href="{{route('invitations.getPackagesByInvitationId',['invitation_id'=>$requestInvitation->id])}}"
											title="{{__('admin.packages')}}"
											class="text-success"><i
												class="mdi mdi-package font-size-22"></i></a>


										<a onclick="openModalDelete({{$requestInvitation->id}})"
											title="{{__('admin.delete')}}"
											class="text-danger"><i
												class="mdi mdi-trash-can-outline font-size-22"></i></a>
										{{-- @endcan--}}
									</div>
								</td>
							</tr>
							@endforeach


						</tbody>
					</table>
				</div>
				<!-- end table-responsive -->
			</div>
		</div>
	</div>
</div>
