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
                                        <th scope="col">#</th>
                                        <th scope="col">Number</th>
                                        <th scope="col">Customer</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Manufacturer</th>
                                        @if (Auth::user()->hasRole('admin'))
                                            <th scope="col">Distributor</th>
                                        @endif
                                        <th scope="col">Term</th>
                                        <th scope="col">Start date</th>
                                        <th scope="col">End date</th>
                                        <th scope="col">Contract price</th>
                                        @if (Auth::user()->hasRole('admin'))
                                            <th scope="col">Contract cost</th>
                                            <th scope="col">Contract revenue</th>
                                        @endif
                                        <th scope="col">Location</th>
                                        @if (Auth::user()->hasRole('admin'))
                                            <th scope="col">Action</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($contracts as $key => $item)
                                        <tr>
                                            <th scope="row">{{ $key + 1 }}</th>
                                            <th>#{{ $item->number }}</th>
                                            <td>{{ $item->customer->name }}</td>
                                            <td>{{ $item->type->name }}</td>
                                            <td>{{ $item->manufacturer->name }}</td>
                                            @if (Auth::user()->hasRole('admin'))
                                                <td>{{ isset($item->distributor) ? $item->distributor->name : '' }}</td>
                                            @endif
                                            <td>{{ $item->term->name }} Year</td>
                                            <td>{{ $item->start_date }}</td>
                                            <td>{{ $item->end_date }}</td>
                                            <td>${{ $item->contract_price }}</td>
                                            @if (Auth::user()->hasRole('admin'))
                                                <td>{{ $item->contract_cost }}</td>
                                                <td>{{ $item->contract_revenue }}</td>
                                            @endif
                                            <td>{{ $item->location }}</td>
                                            @if (Auth::user()->hasRole('admin'))
                                                <td>
                                                    <div class="d-flex">
                                                        <a href="{{ route('contract.edit', $item->id) }}"
                                                            class="btn btn-success btn-icon waves-effect waves-light"><i
                                                                class=" ri-pencil-fill"></i></a>
                                                        {{-- <a style="margin-left: 5px;" href="{{route('contract.show', $item->id)}}" class="btn btn-success btn-icon waves-effect waves-light"><i class=" ri-eye-fill"></i></a> --}}
                                                        <form action="{{ route('contract.destroy', $item->id) }}"
                                                            method="POST">
                                                            @method('DELETE')
                                                            @csrf
                                                            <button onclick="return confirm('Are you sure to delete Item?')"
                                                                class="btn btn-danger btn-icon waves-effect waves-light"
                                                                type="submit" style="margin-left:0.3rem"><i
                                                                    class="ri-delete-bin-5-line"></i></button>
                                                        </form>
                                                    </div>
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
