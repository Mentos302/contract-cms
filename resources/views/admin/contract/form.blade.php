@extends('layouts.master')
@section('title')
    Contract
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            {{ isset($contract) ? 'Update' : 'Add' }}
        @endslot
        @slot('title')
            Contract
        @endslot
    @endcomponent
    <div class="col-md-12">
        <form
            action="{{ isset($contract) && $contract->id ? route('contract.update', $contract->id) : route('contract.store') }}"
            method="POST">
            @if (isset($contract))
                @method('PUT')
            @endif
            @csrf()
            <div class="card">
                <div class="card-header">
                    {{ isset($contract) ? __('Update contract') : __('Add New Contract') }}
                </div>
                <div class="card-body">
                    <div class="row">
                        @if (Auth::user()->hasRole('admin'))
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Select Customer <a href="{{ route('customer.index') }}/create"
                                        class="new-contract-btn btn btn-primary btn-sm">
                                        Add New</a></label>
                                <select class="form-control" name="customer_id" required>
                                    <option value=""> Select Customer</option>
                                    @foreach ($customers as $key => $item)
                                        <option value="{{ $key }}"
                                            {{ (isset($contract) && $contract->customer_id == $key) || old('customer_id') == $key ? "selected='selected'" : '' }}>
                                            {{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <input type="hidden" readonly class="form-control" name="customer_id"
                                value="{{ Auth::user()->id }}" />
                        @endif
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Select Type</label>
                            <select class="form-control" name="type_id" required>
                                <option value=""> Select Type</option>
                                @foreach ($types as $key => $item)
                                    <option value="{{ $key }}"
                                        {{ (isset($contract) && $contract->type_id == $key) || old('type_id') == $key ? "selected='selected'" : '' }}>
                                        {{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Select Manufacturers</label>
                            <select class="form-control" name="manufacturer_id" required>
                                <option value=""> Select Manufacturer</option>
                                @foreach ($manufacturers as $key => $item)
                                    <option value="{{ $key }}"
                                        {{ (isset($contract) && $contract->manufacturer_id == $key) || old('manufacturer_id') == $key ? "selected='selected'" : '' }}>
                                        {{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if (Auth::user()->hasRole('admin'))
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Select Distributor</label>
                                <select class="form-control" name="distributor_id" required>
                                    <option value=""> Select Distributor</option>
                                    @foreach ($distributors as $key => $item)
                                        <option value="{{ $key }}"
                                            {{ (isset($contract) && $contract->distributor_id == $key) || old('distributor_id') == $key ? "selected='selected'" : '' }}>
                                            {{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Select Term</label>
                            <select class="form-control" name="term_id" required>
                                <option value=""> Select Term</option>
                                @foreach ($terms as $key => $item)
                                    <option value="{{ $key }}"
                                        {{ (isset($contract) && $contract->term_id == $key) || old('term_id') == $key ? "selected='selected'" : '' }}>
                                        {{ $item }} Year</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="start_date"
                                value="{{ isset($contract) ? $contract->start_date : old('start_date') }}" required />
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="end_date"
                                value="{{ isset($contract) ? $contract->end_date : old('end_date') }}" required />
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" class="form-control" name="location"
                                value="{{ isset($contract) ? $contract->location : old('location') }}" required />
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Contract Price (USD)</label>
                            <input type="number" class="form-control" step="any" name="contract_price"
                                value="{{ isset($contract) ? $contract->contract_price : old('contract_price') }}"
                                required />
                        </div>
                        @if (Auth::user()->hasRole('admin'))
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Contract Cost (USD)</label>
                                <input type="number" class="form-control" step="any" name="contract_cost"
                                    value="{{ isset($contract) ? $contract->contract_cost : old('contract_cost') }}"
                                    required />
                            </div>
                        @endif
                        @if (Auth::user()->hasRole('admin'))
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Contract Progress (%)</label>
                                <input type="number" min="0" max="100" class="form-control"
                                    name="contract_progress"
                                    value="{{ isset($contract) ? $contract->contract_progress : 0 }}" required />
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-sm btn-success btn-submit" type="submit">Submit</button>
                    <a href="{{ route('contract.index') }}" class="btn btn-sm btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
