@extends('layouts.master')
@section('title')
    Contract Details
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Contract Details
        @endslot
        @slot('title')
            Contract
        @endslot
    @endcomponent
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                Contract Details
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Customer</label>
                        <input type="text" class="form-control" value="{{ $contract->customer->name }}" readonly />
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Type</label>
                        <input type="text" class="form-control" value="{{ $contract->type->name }}" readonly />
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Manufacturer</label>
                        <input type="text" class="form-control" value="{{ $contract->manufacturer->name }}" readonly />
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Term</label>
                        <input type="text" class="form-control" value="{{ $contract->term->years }} Year" readonly />
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Start Date</label>
                        <input type="text" class="form-control" value="{{ $contract->start_date }}" readonly />
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">End Date</label>
                        <input type="text" class="form-control" value="{{ $contract->end_date }}" readonly />
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control" value="{{ $contract->location }}" readonly />
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Contract Price (USD)</label>
                        <input type="text" class="form-control" value="{{ $contract->contract_price }}" readonly />
                    </div>
                    @if (Auth::user()->hasRole('admin'))
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Contract Cost (USD)</label>
                            <input type="text" class="form-control" value="{{ $contract->contract_cost }}" readonly />
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Contract Progress (%)</label>
                            <input type="text" class="form-control" value="{{ $contract->contract_progress }}"
                                readonly />
                        </div>
                    @endif
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('contract.index') }}" class="btn btn-sm btn-secondary">Back</a>
                <a href="{{ route('contract.edit', $contract->id) }}" class="btn btn-sm btn-primary">Edit</a>
            </div>
        </div>
    </div>
@endsection
