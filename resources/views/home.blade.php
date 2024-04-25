@extends('layouts.master')
@section('title')
    @lang('translation.dashboards')
@endsection
@section('css')
    <link href="{{ URL::asset('build/libs/jsvectormap/css/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('build/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')

    <div class="row">
        <div class="col">

            <div class="h-100">
                <div class="row mb-3 pb-1">
                    <div class="col-12">
                        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                            <div class="flex-grow-1">
                                @if (Auth::user()->first_name)
                                    <h4 class="fs-16 mb-1">Hello, {{ Auth::user()->first_name }}!</h4>
                                @endif
                                <p class="text-muted mb-0">Here's what's happening with your
                                    {{ Auth::user()->hasRole('admin') ? 'renewals' : 'contracts' }}:</p>
                            </div>
                        </div><!-- end card header -->
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->
                @if (Auth::user()->hasRole('customer'))
                    <div class="row">
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-primary rounded-4">
                                <div>
                                    <div class="d-flex align-items-center customer-dashboard-card">
                                        <div class="flex-grow-1 ms-3">
                                            <p class="card-text"><span class="fw-medium">Active Contracts</span></p>
                                        </div>
                                        <div class="card-footer py-5">
                                            <div class="text-center px-2">
                                                {{ $active_contracts->count() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-success rounded-4">
                                <div>
                                    <div class="d-flex align-items-center customer-dashboard-card">
                                        <div class="flex-grow-1 ms-3">
                                            <p class="card-text"><span class="fw-medium">Expiring Soon</span></p>
                                        </div>
                                        <div class="card-footer py-5">
                                            <div class="text-center px-2">
                                                {{ $expiring_soon_contracts->count() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-warning rounded-4">
                                <div>
                                    <div class="d-flex align-items-center customer-dashboard-card">
                                        <div class="flex-grow-1 ms-3">
                                            <p class="card-text"><span class="fw-medium">Expired Contracts</span></p>
                                        </div>
                                        <div class="card-footer py-5">
                                            <div class="text-center px-2">
                                                {{ $expired_contracts->count() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-danger rounded-4">
                                <div>
                                    <div class="d-flex align-items-center customer-dashboard-card">
                                        <div class="flex-grow-1 ms-3">
                                            <p class="card-text"><span class="fw-medium">Not Renewing</span></p>
                                        </div>
                                        <div class="card-footer py-5">
                                            <div class="text-center px-2">
                                                {{ $not_renewing_contracts->count() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title mb-0 flex-grow-1">Active Contracts</h4>
                                </div><!-- end card header -->
                                <div class="card-body">
                                    <div class="table-responsive table-card">
                                        <table
                                            class="table table-borderless table-striped table-hover table-nowrap align-middle mb-0">
                                            <thead class="table-light">
                                                <tr class="text-muted">
                                                    <th scope="col" style="width: 20%;">Contract</th>
                                                    <th scope="col">Time Elapsed</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($active_contracts as $item)
                                                    <tr>
                                                        <td class="customer-contract-success">
                                                            <div class="fs-19">{{ $item->manufacturer->name }}
                                                                #{{ $item->mfr_contract_number }}</div>
                                                            <div class="d-flex py-4">
                                                                <div>{{ formatDate($item->start_date) }}</div>
                                                                <i class="px-2 ri-arrow-right-line "></i>
                                                                <div>{{ formatDate($item->end_date) }}</div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @php
                                                                $start_date = new DateTime($item->start_date);
                                                                $end_date = new DateTime($item->end_date);
                                                                $today = new DateTime();
                                                                $elapsed_time = $today->diff($start_date)->days;
                                                                $total_duration = $start_date->diff($end_date)->days;
                                                                $remaining_days = $total_duration - $elapsed_time;
                                                                $elapsed_progress =
                                                                    ($elapsed_time / $total_duration) * 100;
                                                                $elapsed_percentage = number_format(
                                                                    $elapsed_progress,
                                                                    1,
                                                                );
                                                            @endphp
                                                            <div class="time-elapsed-progress progress">
                                                                <div class="progress-bar" role="progressbar"
                                                                    style="width: {{ $elapsed_progress }}%;"
                                                                    aria-valuenow="{{ $elapsed_progress }}"
                                                                    aria-valuemin="0" aria-valuemax="100">
                                                                    {{ $elapsed_percentage }}% ({{ $remaining_days }} days
                                                                    remaining)
                                                                </div>
                                                            </div>
                                                        </td>

                                                    </tr>
                                                @endforeach
                                            </tbody><!-- end tbody -->
                                        </table><!-- end table -->

                                    </div><!-- end table responsive -->
                                </div><!-- end card body -->
                            </div><!-- end card -->
                        </div><!-- end col -->
                    </div>
                @endif

                @if (Auth::user()->hasRole('admin'))
                    <div class="row">
                        <div class="col-xl-4">
                            <div class="card">
                                <div class="card-body targets">
                                    <div class="target-title">
                                        <h5>Target values:</h5>
                                        <ul class="nav nav-pills nav-success mb-3 justify-content-around" role="tablist">
                                            <div class="btn-group mt-2 shadow" role="group" aria-label="Basic example">
                                                <li class="nav-item" role="presentation">
                                                    <a class=" nav-link waves-effect waves-light active "
                                                        data-bs-toggle="tab" href="#revenue" role="tab"
                                                        aria-selected="true">Revenue</a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link waves-effect waves-light " data-bs-toggle="tab"
                                                        href="#count" role="tab" aria-selected="false"
                                                        tabindex="-1">Count</a>
                                                </li>
                                            </div>
                                        </ul>
                                    </div>
                                    <div class="tab-content text-muted">
                                        <div class="tab-pane active show" id="revenue" role="tabpanel">
                                            <div class="row">
                                                <div class="col-6">
                                                    <label for="labelInputFrom" class="form-label">From $</label>
                                                    <input readonly type="number" {{-- value="{{ $revenue_and_count['minContractRevenue'] }}" --}} value="100"
                                                        class="form-control" id="labelInputFrom">

                                                </div>
                                                <div class="col-6">
                                                    <label for="labelInputTo" class="form-label">To $</label>
                                                    <input readonly type="number" {{-- value="{{ $revenue_and_count['totalContractRevenue'] }}" --}} value="1200"
                                                        class="form-control" id="labelInputTo">
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="progress bg-primary-subtle mt-2">
                                                        <div class="progress-bar" role="progressbar" style="width: 43%;"
                                                            aria-valuenow="" aria-valuemin="0" aria-valuemax="100">

                                                            <div>12%</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane show" id="count" role="tabpanel">
                                            <div class="row">
                                                <div class="col-6">
                                                    <label for="labelInputFrom" class="form-label">From</label>
                                                    <input readonly type="number" {{-- value="{{ $revenue_and_count['minContractRevenue'] }}" --}} value="50"
                                                        class="form-control" id="labelInputFrom">

                                                </div>
                                                <div class="col-6">
                                                    <label for="labelInputTo" class="form-label">To</label>
                                                    <input readonly type="number" {{-- value="{{ $revenue_and_count['totalContractRevenue'] }}" --}} value="610"
                                                        class="form-control" id="labelInputTo">
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="progress bg-primary-subtle mt-2">
                                                        <div class="progress-bar" role="progressbar" style="width: 12%;"
                                                            aria-valuenow="" aria-valuemin="0" aria-valuemax="100">
                                                            <div>12%</div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-8">
                            <div class="row">
                                <div class="col-xl-3">
                                    <div class="card card-height-100">
                                        <div class="card-header pt-0">
                                            <div class="flex-shrink-0">
                                                <button type="button" class="btn btn-primary btn-sm w-100"
                                                    style="margin-top: -4px;">
                                                    Open
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="text-center">
                                                <h4 class="fs- mb-0">${{ $open_revenue['sumOpenContracts'] }}</h4>
                                                <h6 class="text-muted mt-2">Revenue</h6>
                                                <hr>
                                                <h6>{{ $open_revenue['countOpenContracts'] }}</h6>
                                                <h6>Count</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3">
                                    <div class="card card-height-100">
                                        <div class="card-header pt-0">
                                            <div class="flex-shrink-0">
                                                <button type="button" class="btn btn-primary btn-sm w-100"
                                                    style="margin-top: -4px;">
                                                    Close Won
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="text-center">
                                                <h4 class="fs- mb-0">${{ $close_won_revenue['sumCloseWonContracts'] }}
                                                </h4>
                                                <h6 class="text-muted mt-2">Revenue</h6>
                                                <hr>
                                                <h6>{{ $close_won_revenue['countCloseWonContracts'] }}</h6>
                                                <h6>Count</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3">
                                    <div class="card card-height-100">
                                        <div class="card-header pt-0">
                                            <div class="flex-shrink-0">
                                                <button type="button" class="btn btn-primary btn-sm w-100"
                                                    style="margin-top: -4px;">
                                                    Close Lost
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="text-center">
                                                <h4 class="fs- mb-0">${{ $close_lost_revenue['sumCloseLostContracts'] }}
                                                </h4>
                                                <h6 class="text-muted mt-2">Revenue</h6>
                                                <hr>
                                                <h6>{{ $close_lost_revenue['countCloseLostContracts'] }}</h6>
                                                <h6>Count</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3">
                                    <div class="card card-height-100">
                                        <div class="card-header pt-0">
                                            <div class="flex-shrink-0">
                                                <button type="button" class="btn btn-primary btn-sm w-100"
                                                    style="margin-top: -4px;">
                                                    Total Revenue EST
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="text-center">
                                                <h4 class="fs- mb-0">
                                                    ${{ $open_revenue['sumOpenContracts'] + $close_won_revenue['sumCloseWonContracts'] }}
                                                </h4>
                                                <h6 class="text-muted mt-2">Revenue</h6>
                                                <hr>
                                                <h6>{{ $open_revenue['countOpenContracts'] + $close_won_revenue['countCloseWonContracts'] }}
                                                </h6>
                                                <h6>Count</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @if ($graph_by_manufacturer['data'])
                            <div class="col-xl-6">
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h4 class="card-title mb-0">Pipeline by Manufacturer </h4>
                                    </div><!-- end card header -->

                                    <div class="card-body">
                                        <div id="pipeline_by_manufacturer" data-colors='["--vz-primary"]'
                                            class="apex-charts" dir="ltr"></div>
                                    </div><!-- end card-body -->
                                </div><!-- end card -->
                            </div>
                            <div class="col-xl-6">
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h4 class="card-title mb-0">Revenue by Manufacturer</h4>
                                    </div><!-- end card header -->

                                    <div class="card-body">
                                        <div id="revenue_by_manufacturer_chart"
                                            data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]'
                                            class="apex-charts" dir="ltr"></div>
                                    </div><!-- end card-body -->
                                </div><!-- end card -->
                            </div>
                        @endif
                    </div>
                    <div class="row">

                        @if (!empty($profit_revenue_monthly))
                            <div class="col-xl-8">
                                <div class="card">
                                    <div class="card-header border-0 align-items-center d-flex">
                                        <h4 class="card-title mb-0 flex-grow-1">Revenue & Profit</h4>
                                        <div class="revenue-profit_buttons">
                                            <button class="active" onclick="updateChart('monthly', this)">Monthly</button>
                                            <button onclick="updateChart('quarterly', this)">Quarterly</button>
                                            <button onclick="updateChart('annually', this)">Annually</button>
                                        </div>
                                    </div>
                                    <div class="card-body p-0 pb-2">
                                        <div class="w-100">
                                            <div id="contracts_charts"
                                                data-colors='["--vz-success", "--vz-primary", "--vz-danger"]'
                                                class="apex-charts" dir="ltr"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($owners_revenue)
                            <div class="col-xl-4">
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h4 class="card-title mb-0">Contracts Revenue</h4>
                                    </div><!-- end card header -->

                                    <div class="card-body">
                                        <div id="contracts_owners_chart"
                                            data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]'
                                            class="apex-charts" dir="ltr"></div>
                                    </div><!-- end card-body -->
                                </div><!-- end card -->
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        @if (isset($nearest_contarcts) && count($nearest_contarcts))
                            <div class="col-xl-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title mb-0">Opportunity Alerts</h4>
                                    </div>
                                    @foreach ($nearest_contarcts as $item)
                                        <div class="card card-body mb-2">
                                            <div class="d-flex justify-content-around">
                                                <div>
                                                    <h6 class="mb-1">{{ $item->customer->name }}</h6>
                                                    <p class="card-text text-muted mb-1">Start Date:
                                                        {{ $item->start_date }}
                                                        End Date {{ $item->end_date }}</p>
                                                    <p class="card-text text-muted mb-1">Renewal Type:
                                                        {{ $item->manufacturer->name }} </p>
                                                    <p class="card-text text-muted mb-1">Quote# {{ $item->number }} </p>
                                                    <p class="card-text text-muted mb-1">Profit:
                                                        {{ $item->contract_revenue }}
                                                    </p>
                                                </div>
                                                <div class="align-content-end">
                                                    <a href="{{ route('contract.edit', $item->id) }}"><i
                                                            class="ri-eye-fill"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

            </div> <!-- end .h-100-->

        </div> <!-- end col -->
    </div>
@endsection
@section('script')
    <!-- apexcharts -->
    <script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/jsvectormap/js/jsvectormap.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/jsvectormap/maps/world-merc.js') }}"></script>
    <script src="{{ URL::asset('build/libs/swiper/swiper-bundle.min.js') }}"></script>
    <!-- dashboard init -->
    <script src="{{ URL::asset('build/js/pages/dashboard-ecommerce.init.js') }}"></script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

    <script>
        let contractComparisonData = {!! json_encode($owners_revenue) !!};

        var contractComparisonDataOptions = {
            chart: {
                width: 380,
                type: 'donut',
            },
            series: [contractComparisonData.sivilityContractsRevenue, contractComparisonData.difference, ],
            labels: ['Sivility Contracts', 'Other Vendor Revenue'],
            colors: ['#204791', '#f46a6a'],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 300
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }],
            chart: {
                height: 333,
                type: "donut",
            },
            legend: {
                position: "bottom",
            },
            stroke: {
                show: false
            },
            dataLabels: {
                dropShadow: {
                    enabled: false,
                },
            },
        }

        var contractComparisonChart = new ApexCharts(document.querySelector("#contracts_owners_chart"),
            contractComparisonDataOptions);

        contractComparisonChart.render();
    </script>

    <script>
        let monthlyProfitRevenueData = {!! json_encode($profit_revenue_monthly) !!};
        let quarterlyProfitRevenueData = {!! json_encode($profit_revenue_quarterly) !!};
        let annuallyProfitRevenueData = {!! json_encode($profit_revenue_annually) !!};

        let graphDates = Object.keys(monthlyProfitRevenueData);
        let graphValuesRevenue = graphDates.map(date => monthlyProfitRevenueData[date]['revenue']);
        let graphValuesProfit = graphDates.map(date => monthlyProfitRevenueData[date]['profit']);


        var linechartcustomerColors = getChartColorsArray("contracts_charts");

        let revenueProfitChart;

        if (linechartcustomerColors) {
            var options = {
                series: [{
                        name: "Revenue",
                        type: "area",
                        data: graphValuesRevenue,
                    },
                    {
                        name: "Profit",
                        type: "area",
                        data: graphValuesProfit,
                    }
                ],
                chart: {
                    height: 600,
                    type: "line",
                    toolbar: {
                        show: false,
                    },
                },
                stroke: {
                    curve: "straight",
                    width: [2, 2],
                },
                fill: {
                    type: "solid",
                    opacity: 0.1,
                    colors: [linechartcustomerColors[0], linechartcustomerColors[2]],
                },
                markers: {
                    size: 0,
                },
                xaxis: {
                    categories: graphDates,
                },
                yaxis: {
                    title: {
                        text: "Amount (USD)",
                    },
                    labels: {
                        formatter: function(val) {
                            return "$" + val.toFixed(2);
                        },
                    },
                },
                grid: {
                    borderColor: "#f1f1f1",
                },
                legend: {
                    position: "top",
                    horizontalAlign: "left",
                    offsetX: 40,
                },
                colors: linechartcustomerColors,
            };

            revenueProfitChart = new ApexCharts(
                document.querySelector("#contracts_charts"),
                options
            );

            revenueProfitChart.render();
        }

        function updateChart(term, button) {
            let data;
            if (term === 'monthly') {
                data = monthlyProfitRevenueData;
            } else if (term === 'quarterly') {
                data = quarterlyProfitRevenueData;
            } else if (term === 'annually') {
                data = annuallyProfitRevenueData;
            }

            let graphDates = Object.keys(data);
            let graphValuesRevenue = graphDates.map(date => data[date]['revenue']);
            let graphValuesProfit = graphDates.map(date => data[date]['profit']);

            revenueProfitChart.updateOptions({
                xaxis: {
                    categories: graphDates,
                }
            });

            revenueProfitChart.updateSeries([{
                name: "Revenue",
                data: graphValuesRevenue,
            }, {
                name: "Profit",
                data: graphValuesProfit,
            }]);

            // Remove active class from all buttons
            document.querySelectorAll('.revenue-profit_buttons button').forEach(btn => btn.classList.remove('active'));

            // Add active class to the clicked button
            button.classList.add('active');
        }


        // pipeline_by_manufacturer

        let graph_by_manufacturer_labels = {!! json_encode($graph_by_manufacturer['labels']) !!};
        let graph_by_manufacturer_data = {!! json_encode($graph_by_manufacturer['data']) !!};
        let revenue_by_manufacturer = {!! json_encode($graph_by_manufacturer['revenue']) !!};

        var chartBarColors = getChartColorsArray("pipeline_by_manufacturer");
        if (chartBarColors) {
            var options = {
                chart: {
                    height: 302,
                    type: 'bar',
                    toolbar: {
                        show: false,
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                    }
                },
                dataLabels: {
                    enabled: false
                },
                series: [{
                    name: 'Contracts',
                    data: graph_by_manufacturer_data
                }],
                colors: chartBarColors,
                grid: {
                    borderColor: '#f1f1f1',
                },
                title: {
                    style: {
                        fontWeight: 500,
                    },
                },
                xaxis: {
                    categories: graph_by_manufacturer_labels,
                },

                fill: {
                    opacity: 1

                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'left',
                    offsetX: 40
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return parseInt(value);
                        }
                    }
                },
                colors: chartBarColors

            }
            var chart = new ApexCharts(document.querySelector("#pipeline_by_manufacturer"), options);
            chart.render();
        }




        var chartDonutBasicColors = getChartColorsArray("revenue_by_manufacturer_chart");

        if (chartDonutBasicColors) {
            var options = {
                series: revenue_by_manufacturer,
                labels: graph_by_manufacturer_labels,
                chart: {
                    height: 333,
                    type: "donut",
                },
                legend: {
                    position: "bottom",
                },
                stroke: {
                    show: false
                },
                dataLabels: {
                    dropShadow: {
                        enabled: false,
                    },
                },
                colors: chartDonutBasicColors,
            };

            var chart = new ApexCharts(
                document.querySelector("#revenue_by_manufacturer_chart"),
                options
            );
            chart.render();
        }
    </script>
@endsection
