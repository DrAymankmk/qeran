<script>
    function getChartColorsArray(e) {
        if (null !== document.getElementById(e)) {
            var t = document.getElementById(e).getAttribute("data-colors");
            if (t) return (t = JSON.parse(t)).map(function (e) {
                var t = e.replace(" ", "");
                if (-1 === t.indexOf(",")) {
                    var r = getComputedStyle(document.documentElement).getPropertyValue(t);
                    return r || t
                }
                var a = e.split(",");
                return 2 != a.length ? t : "rgba(" + getComputedStyle(document.documentElement).getPropertyValue(a[0]) + "," + a[1] + ")"
            })
        }
    }
    setTimeout(function () {
        $("#subscribeModal").modal("show")
    }, 2e3);
    var linechartBasicColors = getChartColorsArray("stacked-column-chart");
    linechartBasicColors && (options = {
        chart: {
            height: 360,
            type: "bar",
            stacked: !0,
            toolbar: {
                show: !1
            },
            zoom: {
                enabled: !0
            }
        },
        plotOptions: {
            bar: {
                horizontal: !1,
                columnWidth: "15%",
                endingShape: "rounded"
            }
        },
        dataLabels: {
            enabled: !1
        },
        series: [{
            name: "{{__('admin.pending-orders')}}",
            data: [
                {{$orders1Pending}},
                {{$orders2Pending}},
                {{$orders3Pending}},
                {{$orders4Pending}},
                {{$orders5Pending}},
                {{$orders6Pending}},
                {{$orders7Pending}},
                {{$orders8Pending}},
                {{$orders9Pending}},
                {{$orders10Pending}},
                {{$orders11Pending}},
                {{$orders12Pending}}]
        }, {
            name: "{{__('admin.finished-orders')}}",
            data: [
                {{$orders1Finished}},
                {{$orders2Finished}},
                {{$orders3Finished}},
                {{$orders4Finished}},
                {{$orders5Finished}},
                {{$orders6Finished}},
                {{$orders7Finished}},
                {{$orders8Finished}},
                {{$orders9Finished}},
                {{$orders10Finished}},
                {{$orders11Finished}},
                {{$orders12Finished}}]
        }, {
            name: "{{__('admin.refunded-orders')}}",
            data: [{{$orders1Refunded}}, 17, 15, 15, 21, 14, 11, 18, 17, 12, 20, 18]
        }],
        xaxis: {
            categories: [
                "{{Carbon\Carbon::now()->subMonths(11)->format('F')}}",
                "{{Carbon\Carbon::now()->subMonths(10)->format('F')}}",
                "{{Carbon\Carbon::now()->subMonths(9)->format('F')}}",
                "{{Carbon\Carbon::now()->subMonths(8)->format('F')}}",
                "{{Carbon\Carbon::now()->subMonths(7)->format('F')}}",
                "{{Carbon\Carbon::now()->subMonths(6)->format('F')}}",
                "{{Carbon\Carbon::now()->subMonths(5)->format('F')}}",
                "{{Carbon\Carbon::now()->subMonths(4)->format('F')}}",
                "{{Carbon\Carbon::now()->subMonths(3)->format('F')}}",
                "{{Carbon\Carbon::now()->subMonths(2)->format('F')}}",
                "{{Carbon\Carbon::now()->subMonth()->format('F')}}"
                ,"{{Carbon\Carbon::now()->format('F')}}"]


        },
        colors: linechartBasicColors,
        legend: {
            position: "bottom"
        },
        fill: {
            opacity: 1
        }
    }

        , (chart = new ApexCharts(document.querySelector("#stacked-column-chart"), options)).render());
    var linechartBasicColors = getChartColorsArray("stacked-column-chart2");
    linechartBasicColors && (options = {
        chart: {
            height: 360,
            type: "bar",
            stacked: !0,
            toolbar: {
                show: !1
            },
            zoom: {
                enabled: !0
            }
        },
        plotOptions: {
            bar: {
                horizontal: !1,
                columnWidth: "15%",
                endingShape: "rounded"
            }
        },
        dataLabels: {
            enabled: !1
        },
        series: [{
            name: "{{__('admin.pending')}}",
            data: [{{$orders1Pending}}, 55, 41, 67, 22, 43, 36, 52, 24, 18, 36, 48]
        }, {
            name: "Series B",
            data: [{{$orders1Finished}}, 23, 20, 8, 13, 27, 18, 22, 10, 16, 24, 22]
        }, {
            name: "Series C",
            data: [{{$orders1Refunded}}, 17, 15, 15, 21, 14, 11, 18, 17, 12, 20, 18]
        }],
        xaxis: {
            categories: [
                "{{Carbon\Carbon::now()->subMonths(11)->format('F')}}",
                "{{Carbon\Carbon::now()->subMonths(10)->format('F')}}",
                "{{Carbon\Carbon::now()->subMonths(9)->format('F')}}",
                "{{Carbon\Carbon::now()->subMonths(8)->format('F')}}",
                "{{Carbon\Carbon::now()->subMonths(7)->format('F')}}",
                "{{Carbon\Carbon::now()->subMonths(6)->format('F')}}",
                "{{Carbon\Carbon::now()->subMonths(5)->format('F')}}",
                "{{Carbon\Carbon::now()->subMonths(4)->format('F')}}",
                "{{Carbon\Carbon::now()->subMonths(3)->format('F')}}",
                "{{Carbon\Carbon::now()->subMonths(2)->format('F')}}",
                "{{Carbon\Carbon::now()->subMonth()->format('F')}}"
                ,"{{Carbon\Carbon::now()->format('F')}}"]

        },
        colors: linechartBasicColors,
        legend: {
            position: "bottom"
        },
        fill: {
            opacity: 1
        }
    }

        , (chart = new ApexCharts(document.querySelector("#stacked-column-chart2"), options)).render());
    var options, chart, radialbarColors = getChartColorsArray("radialBar-chart");
    radialbarColors && (options = {
        chart: {
            height: 200,
            type: "radialBar",
            offsetY: -10
        },
        plotOptions: {
            radialBar: {
                startAngle: -135,
                endAngle: 135,
                dataLabels: {
                    name: {
                        fontSize: "13px",
                        color: void 0,
                        offsetY: 60
                    },
                    value: {
                        offsetY: 22,
                        fontSize: "16px",
                        color: void 0,
                        formatter: function (e) {
                            return e + "%"
                        }
                    }
                }
            }
        },
        colors: radialbarColors,
        fill: {
            type: "gradient",
            gradient: {
                shade: "dark",
                shadeIntensity: .15,
                inverseColors: !1,
                opacityFrom: 1,
                opacityTo: 1,
                stops: [0, 50, 65, 91]
            }
        },
        stroke: {
            dashArray: 4
        },
        series: [{{$monthlyProfitsPercentage}}],
        labels: ["معدل الزيادة هذا الشهر"]
    }, (chart = new ApexCharts(document.querySelector("#radialBar-chart"), options)).render());
</script>
