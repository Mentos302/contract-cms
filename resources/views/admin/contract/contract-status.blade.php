@extends('layouts.master')
@section('title')
    Contracts
@endsection
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0 flex-grow-1 text-capitalize">{{ str_replace('-', ' ', request()->status) }}
                    </h4>
                </div><!-- end card header -->
                <div class="card-body">
                    <div class="table-responsive table-card">
                        <table class="table align-middle table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Quote</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Term</th>
                                    <th scope="col">Start date</th>
                                    <th scope="col">End date</th>
                                    <th scope="col">Contract price</th>
                                    @if (Auth::user()->hasRole('admin'))
                                        <th scope="col">Contract cost</th>
                                        <th scope="col">Contract revenue</th>
                                    @endif
                                    <th scope="col">Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contracts_status as $key => $item)
                                    <tr>
                                        <th>#{{ $item->number }}</th>
                                        <td>{{ $item->type->name }}</td>
                                        <td>{{ $item->term->name }} Year</td>
                                        <td>{{ $item->start_date }}</td>
                                        <td>{{ $item->end_date }}</td>
                                        <td>${{ $item->contract_price }}</td>
                                        @if (Auth::user()->hasRole('admin'))
                                            <td>{{ $item->contract_cost }}</td>
                                            <td>{{ $item->contract_revenue }}</td>
                                        @endif
                                        <td>{{ $item->location }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div><!-- end table responsive -->
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
