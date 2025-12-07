<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-body">
				<h4 class="card-title mb-4"> {{__('admin.invitations')}} </h4>
				<div class="table-responsive">
					<table class="table align-middle table-nowrap table-check">
						<thead class="table-light">
							<tr>
								<th scope="col">{{__('admin.id')}}</th>
								<th scope="col">{{__('admin.user')}}
								</th>
								<th scope="col">
									{{__('admin.status')}}
								</th>
								<th scope="col">
									{{__('admin.invitation-type')}}
								</th>
								<th scope="col">{{__('admin.name')}}
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
								{{-- <th scope="col">{{__('admin.paid-status')}}
								</th>--}}
								<th scope="col">
									{{__('admin.created_at')}}
								</th>
								<th scope="col">{{__('admin.actions')}}
								</th>
							</tr>
						</thead>
						<tbody>
							@foreach($invitations as $invitation)

							<tr>
								<td><a href="javascript: void(0);"
										class="text-body fw-bold">{{$invitation->id}}</a>
								</td>
								<td><a href="{{route('users.show',$invitation->user_id)}}"
										target="_blank">
										{{$invitation->user?->name}}
									</a></td>
								<td> {{__('admin.invitation-status-'.$invitation->status)}}
								</td>
								<td> {{__('admin.invitation-type-'.$invitation->invitation_type)}}
								</td>

								<td> {{$invitation->name}}</td>
								<td> {{__('admin.media-type-'.$invitation->invitation_media_type)}}
								</td>
								<!-- <td>
									@if($invitation->designImage())
									<a target="_blank"
										href="{{$invitation->designImage()}}">

										<img class=" header-profile-user"
											src="{{$invitation->designImage()}}"
											alt="Invitation">
									</a>
									@else
									{{__('admin.no-data-available')}}
									@endif
								</td>
								<td>
									@if($invitation->designVideo())

									<video width="150"
										height="150"
										controls>
										<source src="{{$invitation->designVideo()}}"
											type="video/mp4">
										<source src="{{$invitation->designVideo()}}"
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
									@if($invitation->designAudio())

									<audio controls>
										<source src="{{$invitation->designAudio()}}"
											type="audio/ogg">
										<source src="{{$invitation->designAudio()}}"
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
									{{$invitation->description}}
								</td>
								<td>
									@if($invitation->receiptImage())
									<a target="_blank"
										href="{{$invitation->receiptImage()}}">

										<img class=" header-profile-user"
											src="{{$invitation->receiptImage()}}"
											alt="Invitation">
									</a>
									@else
									{{__('admin.no-data-available')}}
									@endif
								</td> -->

								{{-- <td>--}}
								{{-- <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">--}}
								{{-- <input class="form-check-input" type="checkbox"--}}
								{{-- onchange="return window.location.href = '{{route('invitations.change-status',$invitation->id)}}'"--}}
								{{-- @if($invitation->paid==1)checked="" @endif>--}}

								{{-- </div>--}}
								{{-- </td>--}}


								<td>
									{{Carbon\Carbon::parse($invitation->created_at)->locale(app()->getLocale())->translatedFormat('l dS F G:i - Y')}}
								</td>
								<td>
									<div class="d-flex gap-3">
										<a href="javascript:void(0);"
											onclick="showInvitationDetails({{$invitation->id}})"
											title="{{__('admin.show')}}"
											class="text-info"><i
												class="mdi mdi-eye font-size-22"></i></a>
										<a href="{{route('invitation.edit',$invitation->id)}}"
											title="{{__('admin.edit')}}"
											class="text-warning"><i
												class="mdi mdi-file-edit-outline font-size-22"></i></a>

										{{-- <a href="{{route('package.index',['invitation_id'=>$invitation->id])}}"--}}
										{{-- title="{{__('admin.packages')}}"
										class="text-success">
										<i--}} {{--                                                    class="mdi mdi-package font-size-18"></i></a>--}}
											<a
											href="{{route('invitations.getPackagesByInvitationId',['invitation_id'=>$invitation->id])}}"
											title="{{__('admin.packages')}}"
											class="text-success">
											<i
												class="mdi mdi-package font-size-22"></i></a>


											<a onclick="openModalDelete({{$invitation->id}})"
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
				<!-- end table-responsive -->
			</div>
		</div>
	</div>
</div>