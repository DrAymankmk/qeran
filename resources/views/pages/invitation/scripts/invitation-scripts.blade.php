@section('extra-js')
    <script src="{{asset('admin_assets/libs/select2/js/select2.min.js')}}"></script>
    <!-- bootstrap-datepicker js -->
    <script src="{{asset('admin_assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>

@include('pages.global.scripts.datatable-scripts')
@include('pages.global.scripts.datatable-admin-init')

    <!-- init js -->
    <script src="{{asset('admin_assets/js/pages/crypto-orders.init.js')}}"></script>

    <script src="{{asset('admin_assets/js/jquery.printPage.js') }}"></script>

    <script src="{{asset('admin_assets/js/print.js') }}"></script>

    <script>
        $('.btnprn').printPage();
    </script>

    <script>

		$(document).ready(function() {
	// Initialize DataTable with server-side processing
	var table = $('#invitationsTable').DataTable({
		processing: true,
		serverSide: true,
		ajax: {
			url: '{{ route("invitation.index") }}',
			type: 'GET',
			data: function(d) {
				// Add filter parameters
				d.status = $('#statusFilter').val();
				d.date_from = $('#dateFromFilter').val();
				d.date_to = $('#dateToFilter').val();
			},
			headers: {
				'X-Requested-With': 'XMLHttpRequest'
			}
		},
		dom: '<"row mb-3 align-items-center justify-content-between"<"col-auto"l><"col-auto ms-3"f><"col-auto ms-3"B>>' +
			'rt' +
			'<"row"<"col-md-5"i><"col-md-7"p>>',
		buttons: [{
				extend: 'copy',
				className: 'btn btn-sm btn-outline-primary',
				text: '<i class="mdi mdi-content-copy"></i> {{__("admin.copy")}}'
			},
			{
				extend: 'excel',
				className: 'btn btn-sm btn-outline-success',
				text: '<i class="mdi mdi-file-excel"></i> {{__("admin.excel")}}'
			},
			{
				text: '<i class="mdi mdi-file-pdf"></i> {{__("admin.pdf")}}',
				className: 'btn btn-sm btn-outline-danger',
				action: function(e, dt, button, config) {
					window.location.href = '{{route("invitations.export.pdf")}}';
				}
			},
			{
				extend: 'print',
				className: 'btn btn-sm btn-outline-info',
				text: '<i class="mdi mdi-printer"></i> {{__("admin.print")}}'
			},
		],
		responsive: true,
		pageLength: 10,
		lengthMenu: [
			[10, 25, 50, 100],
			[10, 25, 50, 100]
		],
		order: [[0, 'desc']],
		language: {
			@if(app()->getLocale() == 'ar')
			url: "{{asset('admin_assets/ar.json')}}"
			@else
			search: "{{__('admin.search')}}:",
			lengthMenu: "{{__('admin.show')}} _MENU_ {{__('admin.entries')}}",
			info: "{{__('admin.showing')}} _START_ {{__('admin.to')}} _END_ {{__('admin.of')}} _TOTAL_ {{__('admin.entries')}}",
			infoEmpty: "{{__('admin.showing')}} 0 {{__('admin.to')}} 0 {{__('admin.of')}} 0 {{__('admin.entries')}}",
			infoFiltered: "({{__('admin.filtered')}} {{__('admin.from')}} _MAX_ {{__('admin.total')}} {{__('admin.entries')}})",
			paginate: {
				first: "{{__('admin.first')}}",
				last: "{{__('admin.last')}}",
				next: "{{__('admin.next')}}",
				previous: "{{__('admin.previous')}}"
			},
			zeroRecords: "{{__('admin.no-matching-records')}}",
			emptyTable: "{{__('admin.no-data-available')}}",
			processing: "{{__('admin.loading')}}..."
			@endif
		},
		columnDefs: [{
			targets: [1, 4],
			orderable: false,
			searchable: false
		}],
		drawCallback: function() {
			$('.dataTables_length select').addClass('form-select form-select-sm');
		}
	});

	// Style the search box
	$('.dataTables_filter').addClass('mb-0');
	$('.dataTables_filter input').addClass('form-control form-control-sm');
	$('.dataTables_filter input').attr('placeholder', "{{__('admin.search')}}...");

	// Style the length menu (per page selector)
	$('.dataTables_length').addClass('mb-0');
	$('.dataTables_length label').addClass('form-label mb-0 me-2');
	$('.dataTables_length select').addClass('form-select form-select-sm d-inline-block w-auto');

	// Style the buttons container
	$('.dt-buttons').addClass('mb-0');

	// Filter event listeners
	$('#statusFilter, #dateFromFilter, #dateToFilter').on('change', function() {
		table.draw();
	});

	// Reset filters
	$('#resetFilters').on('click', function() {
		$('#statusFilter').val('');
		$('#dateFromFilter').val('');
		$('#dateToFilter').val('');
		table.draw();
	});

	// Highlight row if highlight parameter exists
	const urlParams = new URLSearchParams(window.location.search);
	const highlightId = urlParams.get('highlight');
	if (highlightId) {
		let highlightAttempts = 0;
		const maxAttempts = 10;
		
		// Function to highlight the row
		const highlightRow = function() {
			$('#invitationsTable tbody tr').each(function() {
				const firstCell = $(this).find('td:first');
				const cellText = firstCell.text().trim();
				if (cellText == highlightId) {
					$(this).addClass('table-warning highlight-row');
					$(this).css({
						'background-color': '#fff3cd',
						'border-left': '4px solid #ffc107',
						'animation': 'pulse-highlight 2s ease-in-out'
					});
					
					// Scroll to the row
					$('html, body').animate({
						scrollTop: $(this).offset().top - 100
					}, 500);
					
					// Remove highlight after 5 seconds
					setTimeout(function() {
						$(this).removeClass('table-warning highlight-row');
						$(this).css({
							'background-color': '',
							'border-left': '',
							'animation': ''
						});
					}.bind(this), 5000);
					
					return false;
				}
			});
		};
		
		// Try to highlight after each draw
		table.on('draw', function() {
			highlightAttempts++;
			if (highlightAttempts <= maxAttempts) {
				setTimeout(highlightRow, 300);
			}
		});
		
		// Also try immediately after initialization
		setTimeout(highlightRow, 1000);
	}
});

// Add CSS for pulse animation
if (document.getElementById('highlight-style') === null) {
	const style = document.createElement('style');
	style.id = 'highlight-style';
	style.textContent = `
		@keyframes pulse-highlight {
			0%, 100% {
				box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7);
			}
			50% {
				box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
			}
		}
		.highlight-row {
			transition: all 0.3s ease;
		}
	`;
	document.head.appendChild(style);
}

    
        
        function change_status(id) {

            axios.get('category/status/' + id)
                .then(function (response) {
                    console.log(response.data);
                })
                .catch(function (error) {
                    console.log(error);
                });
        }

        function featured(id) {

            axios.get('category/featured/' + id)
                .then(function (response) {
                    console.log(response.data);
                })
                .catch(function (error) {
                    console.log(error);
                });
        }

    </script>
    <script>
        function openModalDelete(invitation_id) {
            $('.action_form').attr('action', '{{route('invitation.destroy', '')}}' + '/' + invitation_id);
            $('#deleteModal').modal('show');
        }

        // Show invitation details via AJAX
window.showInvitationDetails = function(invitationId) {
	// Show loading state
	const modalElement = document.getElementById(
		'invitationDetailsModal');
	const modalBody = modalElement.querySelector('.modal-body');
	const originalContent = modalBody.innerHTML;
	modalBody.innerHTML =
		'<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-3">{{__("admin.loading")}}</p></div>';

	// Show modal
	const modal = new bootstrap.Modal(modalElement);
	modal.show();

	// Make AJAX request
	fetch('{{ route("invitations.details", ":id") }}'.replace(':id',
			invitationId), {
			method: 'GET',
			headers: {
				'X-Requested-With': 'XMLHttpRequest',
				'Accept': 'application/json',
				'Content-Type': 'application/json'
			}
		})
		.then(response => {
			if (!response.ok) {
				throw new Error(
					'Network response was not ok'
				);
			}
			return response.json();
		})
		.then(data => {
			// Restore original modal body structure first
			modalBody.innerHTML = originalContent;

			// Fill modal with data
			const fields = [
				'invitation_type',
				'invitation_step',
				'category_name',
				'user_name',
				'user_email',
				'paid',
				'status',
				'host_name',
				'name',
				'slug',
				'date',
				'time',
				'latitude',
				'longitude',
				'address',
				'groom',
				'bride',
				'groom_father',
				'bride_father',
				'event_name',
				'count',
				'price',
				'description',
				'package_id',
				'media_type',
				'code',
				'created_at'
			];

			fields.forEach(field => {
				const element =
					document
					.getElementById(
						`modal_${field}`
					);
				if (
					element
				) {
					element.textContent =
						data[
							field
						] ||
						'{{ __("admin.no-data-available") }}';
				}
			});

			// Handle media
			const designImageEl = document
				.getElementById(
					'modal_design_image'
				);
			if (designImageEl) {
				designImageEl.innerHTML = data
					.design_image ?
					`<a target="_blank" href="${data.design_image}"><img src="${data.design_image}" class="img-fluid" alt="Design Image"></a>` :
					'{{ __("admin.no-data-available") }}';
			}

			const receiptImageEl = document
				.getElementById(
					'modal_receipt_image'
				);
			if (receiptImageEl) {
				receiptImageEl.innerHTML =
					data.receipt_image ?
					`<a target="_blank" href="${data.receipt_image}"><img src="${data.receipt_image}" class="img-fluid" alt="Receipt Image"></a>` :
					'{{ __("admin.no-data-available") }}';
			}

			const designVideoEl = document
				.getElementById(
					'modal_design_video'
				);
			if (designVideoEl) {
				if (data.design_video) {
					// Fix URL - remove double slashes and ensure proper format
					let videoUrl = data.design_video.replace(/([^:]\/)\/+/g, "$1");
					// If URL doesn't start with http, make it absolute
					if (!videoUrl.startsWith('http')) {
						videoUrl = videoUrl.startsWith('/') ? videoUrl : '/' + videoUrl;
					}
					
					// Get file extension to determine MIME type
					const fileExt = videoUrl.split('.').pop().toLowerCase();
					let mimeType = 'video/mp4'; // default
					if (fileExt === 'webm') {
						mimeType = 'video/webm';
					} else if (fileExt === 'ogg' || fileExt === 'ogv') {
						mimeType = 'video/ogg';
					} else if (fileExt === 'mp4' || fileExt === 'mpeg4') {
						mimeType = 'video/mp4';
					}
					
					// Create video element with multiple source types for better compatibility
					designVideoEl.innerHTML = `
						<video width="100%" controls preload="metadata" onerror="this.parentElement.innerHTML='<span class=\\'text-danger\\'>{{ __("admin.error-loading-video") }}</span>'">
							<source src="${videoUrl}" type="${mimeType}">
							<source src="${videoUrl}" type="video/mp4">
							<source src="${videoUrl}" type="video/webm">
							Your browser does not support the video tag.
						</video>
						<div class="mt-2">
							<a href="${videoUrl}" target="_blank" class="btn btn-sm btn-outline-primary">
								<i class="mdi mdi-download"></i> {{__("admin.download-video")}}
							</a>
						</div>
					`;
					
					// Force video element to reload after being inserted into DOM
					setTimeout(function() {
						const video = designVideoEl.querySelector('video');
						if (video) {
							video.load();
						}
					}, 100);
				} else {
					designVideoEl.innerHTML = '{{ __("admin.no-data-available") }}';
				}
			}

			const designAudioEl = document
				.getElementById(
					'modal_design_audio'
				);
			if (designAudioEl) {
				if (data.design_audio) {
					// Fix URL - remove double slashes and ensure proper format
					let audioUrl = data.design_audio.replace(/([^:]\/)\/+/g, "$1");
					
					// Make URL absolute if it's relative
					if (!audioUrl.startsWith('http://') && !audioUrl.startsWith('https://')) {
						// If it starts with /, use current origin
						if (audioUrl.startsWith('/')) {
							audioUrl = window.location.origin + audioUrl;
						} else {
							// Otherwise, prepend with current origin and /
							audioUrl = window.location.origin + '/' + audioUrl;
						}
					}
					
					console.log('Audio URL:', audioUrl); // Debug log
					
					// Get file extension to determine MIME type
					const fileExt = audioUrl.split('.').pop().toLowerCase().split('?')[0]; // Remove query params
					let mimeType = 'audio/mpeg'; // default
					if (fileExt === 'ogg' || fileExt === 'oga') {
						mimeType = 'audio/ogg';
					} else if (fileExt === 'wav') {
						mimeType = 'audio/wav';
					} else if (fileExt === 'mp3' || fileExt === 'mpeg') {
						mimeType = 'audio/mpeg';
					} else if (fileExt === 'aac') {
						mimeType = 'audio/aac';
					} else if (fileExt === 'webm') {
						mimeType = 'audio/webm';
					}
					
					// Check if URL is from different domain (CORS issue)
					const currentDomain = window.location.hostname;
					const audioDomain = new URL(audioUrl).hostname;
					const isCrossOrigin = currentDomain !== audioDomain;
					
					if (isCrossOrigin) {
						// Use proxy for cross-origin files
						const proxyUrl = '{{ route("media.proxy") }}?url=' + encodeURIComponent(audioUrl);
						
						console.log('Loading audio via proxy:', proxyUrl);
						
						// Create audio element with proxy URL
						const audioHtml = `
							<audio 
								controls 
								preload="auto" 
								style="width: 100%;"
								crossorigin="anonymous">
								<source src="${proxyUrl}" type="${mimeType}">
								Your browser does not support the audio element.
							</audio>
							<div class="mt-2">
								<a href="${audioUrl}" target="_blank" class="btn btn-sm btn-outline-primary" download>
									<i class="mdi mdi-download"></i> {{__("admin.download-audio")}}
								</a>
							</div>
							<div id="audio-status" class="mt-2 small text-muted"></div>
						`;
						
						designAudioEl.innerHTML = audioHtml;
						
						// Add event listeners to track audio loading
						setTimeout(function() {
							const audio = designAudioEl.querySelector('audio');
							if (audio) {
								const statusDiv = document.getElementById('audio-status');
								
								audio.addEventListener('loadstart', function() {
									if (statusDiv) statusDiv.textContent = '{{__("admin.loading")}}...';
								});
								
								audio.addEventListener('loadedmetadata', function() {
									if (statusDiv) statusDiv.textContent = '{{__("admin.ready-to-play")}}';
									console.log('Audio metadata loaded:', {
										duration: audio.duration,
										readyState: audio.readyState
									});
								});
								
								audio.addEventListener('canplay', function() {
									if (statusDiv) statusDiv.textContent = '{{__("admin.can-play")}}';
									console.log('Audio can play');
								});
								
								audio.addEventListener('error', function(e) {
									console.error('Audio error:', audio.error);
									if (statusDiv) {
										statusDiv.className = 'mt-2 small text-danger';
										statusDiv.textContent = '{{__("admin.error-loading-audio")}} (Code: ' + (audio.error ? audio.error.code : 'unknown') + ')';
									}
								});
								
								audio.load();
							}
						}, 100);
					} else {
						// Same origin - use direct URL
						const audioHtml = `
							<audio 
								controls 
								preload="metadata" 
								style="width: 100%;">
								<source src="${audioUrl}" type="${mimeType}">
								Your browser does not support the audio element.
							</audio>
							<div class="mt-2">
								<a href="${audioUrl}" target="_blank" class="btn btn-sm btn-outline-primary" download>
									<i class="mdi mdi-download"></i> {{__("admin.download-audio")}}
								</a>
							</div>
						`;
						designAudioEl.innerHTML = audioHtml;
					}
				} else {
					designAudioEl.innerHTML = '{{ __("admin.no-data-available") }}';
				}
			}
			
			// Error handler function
			window.handleAudioError = function(audioElement, url) {
				console.error('Audio failed to load:', url);
				const errorDiv = document.getElementById('audio-error-message');
				if (errorDiv) {
					const errorCode = audioElement.error ? audioElement.error.code : 'unknown';
					let errorMsg = '{{ __("admin.error-loading-audio") }}';
					if (errorCode === 4) {
						errorMsg += ' ({{ __("admin.audio-format-not-supported") }})';
					} else if (errorCode === 3) {
						errorMsg += ' ({{ __("admin.audio-decoding-error") }})';
					} else if (errorCode === 2) {
						errorMsg += ' ({{ __("admin.audio-network-error") }})';
					}
					errorDiv.textContent = errorMsg;
					errorDiv.style.display = 'block';
				}
			};
			
			// Test audio URL function
			window.testAudioUrl = function(url) {
				fetch(url, { method: 'HEAD' })
					.then(response => {
						if (response.ok) {
							alert('{{ __("admin.audio-file-exists") }}: ' + response.status);
						} else {
							alert('{{ __("admin.audio-file-error") }}: ' + response.status);
						}
					})
					.catch(error => {
						alert('{{ __("admin.audio-file-error") }}: ' + error.message);
					});
			};
		})
		.catch(error => {
			console.error('Error:', error);
			modalBody.innerHTML = `<div class="alert alert-danger text-center p-5">
				<h5>{{__("admin.error")}}</h5>
				<p>{{__("admin.invitation-not-found")}}</p>
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__("admin.close")}}</button>
			</div>`;
		});
};
    </script>

@endsection