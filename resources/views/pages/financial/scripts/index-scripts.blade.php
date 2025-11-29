@section('extra-js')
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
	return null;
}

// Wait for both jQuery and ApexCharts to be loaded
$(document).ready(function() {
	// Check if ApexCharts is loaded
	if (typeof ApexCharts === 'undefined') {
		console.error('ApexCharts library is not loaded');
		return;
	}
	


	// Small delay to ensure DOM is fully rendered before initializing charts
	setTimeout(function() {
	// Conversion Rate Chart
	var conversionChartElement = document.querySelector("#conversion-chart");
	if (conversionChartElement) {
		var conversionChartColors = getChartColorsArray("conversion-chart");
		if (conversionChartColors) {
			var conversionChartOptions = {
				chart: {
					height: 360,
					type: "donut",
					toolbar: {
						show: false
					}
				},
				labels: ["{{__('admin.visitors')}}", "{{__('admin.customers')}}"],
				series: [{{$stats['totalVisitors'] ?? 0}}, {{$stats['totalCustomers'] ?? 0}}],
				colors: conversionChartColors,
				legend: {
					position: "bottom"
				},
				dataLabels: {
					enabled: true,
					formatter: function(val) {
						return val.toFixed(2) + "%";
					}
				}
			};

			var conversionChart = new ApexCharts(conversionChartElement, conversionChartOptions);
			conversionChart.render();
		} else {
			console.error('Conversion chart colors not found');
		}
	} else {
		console.error('Conversion chart element not found');
	}

	// Monthly Orders Chart
	var monthlyOrdersChartElement = document.querySelector("#monthly-orders-chart");
	if (monthlyOrdersChartElement) {
		var monthlyOrdersChartColors = getChartColorsArray("monthly-orders-chart");
		if (monthlyOrdersChartColors) {
			var monthlyOrdersData = @json($stats['monthlyOrdersData'] ?? []);
			var monthlyOrdersLabels = @json($stats['monthlyOrdersLabels'] ?? []);
			
			var monthlyOrdersChartOptions = {
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
					enabled: false
				},
				series: [{
					name: "{{__('admin.orders')}}",
					data: monthlyOrdersData
				}],
				xaxis: {
					categories: monthlyOrdersLabels
				},
				colors: monthlyOrdersChartColors,
				legend: {
					position: "bottom"
				},
				fill: {
					opacity: 1
				}
			};

			var monthlyOrdersChart = new ApexCharts(monthlyOrdersChartElement, monthlyOrdersChartOptions);
			monthlyOrdersChart.render();
		} else {
			console.error('Monthly orders chart colors not found');
		}
	} else {
		console.error('Monthly orders chart element not found');
	}

	// Daily Orders Chart
	var dailyOrdersChartElement = document.querySelector("#daily-orders-chart");
	if (dailyOrdersChartElement) {
		var dailyOrdersChartColors = getChartColorsArray("daily-orders-chart");
		if (dailyOrdersChartColors) {
			var dailyOrdersData = @json($stats['dailyOrdersData'] ?? []);
			var dailyOrdersLabels = @json($stats['dailyOrdersLabels'] ?? []);
			
			var dailyOrdersChartOptions = {
				chart: {
					height: 360,
					type: "line",
					toolbar: {
						show: false
					},
					zoom: {
						enabled: true
					}
				},
				stroke: {
					curve: "smooth",
					width: 3
				},
				dataLabels: {
					enabled: false
				},
				series: [{
					name: "{{__('admin.orders')}}",
					data: dailyOrdersData
				}],
				xaxis: {
					categories: dailyOrdersLabels
				},
				colors: dailyOrdersChartColors,
				legend: {
					position: "bottom"
				},
				fill: {
					type: "gradient",
					gradient: {
						shadeIntensity: 1,
						opacityFrom: 0.7,
						opacityTo: 0.9,
						stops: [0, 90, 100]
					}
				}
			};

			var dailyOrdersChart = new ApexCharts(dailyOrdersChartElement, dailyOrdersChartOptions);
			dailyOrdersChart.render();
		} else {
			console.error('Daily orders chart colors not found');
		}
	} else {
		console.error('Daily orders chart element not found');
	}
	}, 300); // 300ms delay to ensure DOM is ready
});
</script>
@endsection

