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
                </div>
                <div class="card-body">
                    <div class="table-responsive table-card">
                        <table class="table align-middle table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Quote</th>
                                    <th scope="col">MFR/Soft</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Serial Number</th>
                                    <th scope="col">Contract Number</th>
                                    <th scope="col">Term</th>
                                    <th scope="col">Start date</th>
                                    <th scope="col">End date</th>
                                    <th scope="col">Contract price</th>
                                    <th scope="col">Name (optional)</th>
                                    <th scope="col">Location</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contracts_status as $key => $item)
                                    <tr>
                                        <td><a href="{{ route('contract.show', $item->id) }}">#{{ $item->number }}</a></td>
                                        <td>{{ $item->manufacturer->name }}</td>
                                        <td>{{ $item->type->name }}</td>
                                        <td>{{ $item->serial_number }}</td>
                                        <td>{{ $item->mfr_contract_number }}</td>
                                        <td>{{ $item->term->name }} Year</td>
                                        <td>{{ formatDate($item->start_date) }}</td>
                                        <td>{{ formatDate($item->end_date) }}</td>
                                        <td>${{ $item->contract_price }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->location }}</td>
                                        @if (!Auth::user()->hasRole('admin'))
                                            <td>
                                                <a href="#" class="btn btn-primary">Request a Quote</a>
                                            </td>
                                        @endif
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
