@extends('layouts.master')
@section('title')
    Contracts
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            List
        @endslot
        @slot('title')
            Contracts
        @endslot
    @endcomponent
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header d-md-flex justify-content-between">
                    <div class="d-flex align-items-baseline">
                        <h4 class="card-title mb-0 flex-grow-1">Contract</h4>
                        <a href="{{ route('contract.create') }}" class="btn btn-outline-secondary mx-2">
                            <span class="icon-on"><i class="ri-add-line align-bottom me-1"></i> Add</span>
                        </a>
                    </div>
                    <form action="{{ route('contract.index') }}" method="GET" role="search">
                        <div class="input-group">
                            <input type="search" class="form-control" name="search"
                                value="{{ request('search') ? request('search') : '' }}" placeholder="Write Here.."> <span
                                class="input-group-btn">
                                <button type="submit" class="btn btn-dark">Search </button>
                            </span>
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    <div class="live-preview">
                        <div class="table-responsive">
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
                                    @foreach ($contracts as $key => $item)
                                        <tr>
                                            <td><a href="{{ route('contract.show', $item->id) }}">#{{ $item->number }}</a>
                                            </td>
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
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    {{ $contracts->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
