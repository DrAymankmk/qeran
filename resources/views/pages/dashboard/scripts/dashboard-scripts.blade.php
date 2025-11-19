<script src="{{asset('admin_assets/libs/apexcharts/apexcharts.min.js')}}"></script>
<script>
function getChartColorsArray(e) {
	if (null !== document.getElementById(e)) {
		var t = document.getElementById(e).getAttribute("data-colors");
		if (t) return (t = JSON.parse(t)).map(function(e) {
			var t = e.replace(" ", "");
			if (-1 === t.indexOf(",")) {
				var r = getComputedStyle(document.documentElement)
					.getPropertyValue(t);
				return r || t
			}
			var a = e.split(",");
			return 2 != a.length ? t : "rgba(" + getComputedStyle(
					document.documentElement)
				.getPropertyValue(a[0]) + "," + a[1] + ")"
		})
	}
}

setTimeout(function() {
	$("#subscribeModal").modal("show")
}, 2e3);

// Users Statistics Chart
var linechartBasicColors = getChartColorsArray("users-chart");
if (linechartBasicColors) {
	var usersChartOptions = {
		chart: {
			height: 360,
			type: "bar",
			stacked: true,
			toolbar: {
				show: false
			},
			zoom: {
				enabled: true
			}
		},
		plotOptions: {
			bar: {
				horizontal: false,
				columnWidth: "15%",
				endingShape: "rounded"
			}
		},
		dataLabels: {
			enabled: false
		},
		series: [{
			name: "{{__('admin.verified')}}",
			data: @json($verifiedUsers)
		}, {
			name: "{{__('admin.not-verified')}}",
			data: @json($notVerifiedUsers)
		}],
		xaxis: {
			categories: @json($categories)
		},
		colors: linechartBasicColors,
		legend: {
			position: "bottom"
		},
		fill: {
			opacity: 1
		}
	};

	var usersChart = new ApexCharts(document.querySelector("#users-chart"), usersChartOptions);
	usersChart.render();
}

// Invitations Statistics Chart
var invitationsChartColors = getChartColorsArray("invitations-chart");
if (invitationsChartColors) {
	var invitationsChartOptions = {
		chart: {
			height: 360,
			type: "bar",
			stacked: true,
			toolbar: {
				show: false
			},
			zoom: {
				enabled: true
			}
		},
		plotOptions: {
			bar: {
				horizontal: false,
				columnWidth: "15%",
				endingShape: "rounded"
			}
		},
		dataLabels: {
			enabled: false
		},
		series: [{
			name: "{{__('admin.app-design')}}",
			data: @json($invitationsAppDesign)
		}, {
			name: "{{__('admin.contact-design')}}",
			data: @json($invitationsContactDesign)
		}, {
			name: "{{__('admin.user-design')}}",
			data: @json($invitationsUserDesign)
		}],
		xaxis: {
			categories: @json($categories)
		},
		colors: invitationsChartColors,
		legend: {
			position: "bottom"
		},
		fill: {
			opacity: 1
		}
	};

	var invitationsChart = new ApexCharts(document.querySelector("#invitations-chart"), invitationsChartOptions);
	invitationsChart.render();
}

// Categories Chart
var categoriesChartColors = getChartColorsArray("categories-chart");
var categoriesData = @json($categoriesData);
if (categoriesChartColors && categoriesData && categoriesData.length > 0) {
	var categoriesNames = categoriesData.map(function(item) {
		return item.name;
	});
	var categoriesCounts = categoriesData.map(function(item) {
		return item.count;
	});

	var categoriesOptions = {
		chart: {
			height: 360,
			type: "bar",
			toolbar: {
				show: false
			},
			zoom: {
				enabled: true
			}
		},
		plotOptions: {
			bar: {
				horizontal: false,
				columnWidth: "15%",
				endingShape: "rounded"
			}
		},
		dataLabels: {
			enabled: true
		},
		series: [{
			name: "{{__('admin.invitations')}}",
			data: categoriesCounts
		}],
		xaxis: {
			categories: categoriesNames
		},
		colors: categoriesChartColors,
		legend: {
			position: "bottom"
		},
		fill: {
			opacity: 1
		}
	};

	var categoriesChart = new ApexCharts(document.querySelector("#categories-chart"), categoriesOptions);
	categoriesChart.render();
}

// delete invitation modal
function openModalDelete(invitation_id) {
	const form = document.querySelector('#deleteInvitationModal .action_form');
	if (form) {
		form.action = "{{route('invitation.destroy', '')}}" + '/' + invitation_id;
	}
	const modal = new bootstrap.Modal(document.getElementById('deleteInvitationModal'));
	modal.show();
}



document.addEventListener('DOMContentLoaded', function() {

	// Quick filter buttons handler
	const quickFilterBtns = document.querySelectorAll('.quick-filter-btn');
	const filterInput = document.getElementById('filter_input');
	const fromDateInput = document.getElementById('from_date');
	const toDateInput = document.getElementById('to_date');
	const filterForm = document.getElementById('filterForm');

	quickFilterBtns.forEach(function(btn) {
		btn.addEventListener('click', function() {

			// Remove active class
			quickFilterBtns.forEach(b => b
				.classList
				.remove(
					'active'
				)
			);

			// Add active class
			this.classList.add('active');

			// Set filter value
			const filterValue = this
				.getAttribute(
					'data-filter'
				);
			filterInput.value =
				filterValue;

			// Clear dates if quick filter used
			if (filterValue !== 'all') {
				fromDateInput
					.value =
					'';
				toDateInput.value =
					'';
			}

			// Submit form
			filterForm.submit();
		});
	});

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
					'id', 'code',
					'user_name',
					'user_email',
					'invitation_type',
					'name',
					'media_type',
					'description',
					'host_name', 'date',
					'time', 'address',
					'groom', 'bride',
					'event_name',
					'status',
					'created_at',
					'category_name'
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
					designVideoEl.innerHTML = data
						.design_video ?
						`<video width="100%" controls><source src="${data.design_video}" type="video/mp4">Your browser does not support the video tag.</video>` :
						'{{ __("admin.no-data-available") }}';
				}

				const designAudioEl = document
					.getElementById(
						'modal_design_audio'
					);
				if (designAudioEl) {
					designAudioEl.innerHTML = data
						.design_audio ?
						`<audio controls style="width: 100%;"><source src="${data.design_audio}" type="audio/mpeg">Your browser does not support the audio element.</audio>` :
						'{{ __("admin.no-data-available") }}';
				}
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

});
</script>
