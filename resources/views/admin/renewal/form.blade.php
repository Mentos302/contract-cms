@extends('layouts.master')
@section('title')
    {{ isset($renewal) ? 'Update Renewal' : 'Add New Renewal' }}
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            {{ isset($renewal) ? 'Update' : 'Add' }}
        @endslot
        @slot('title')
            Create Renewal
        @endslot
    @endcomponent
    <div class="col-md-12">
        <form action="{{ isset($renewal) ? route('renewal.update', $renewal->id) : route('renewal.store') }}" method="POST"
            enctype="multipart/form-data">
            @if (isset($renewal))
                @method('PUT')
            @endif
            @csrf
            <div class="card">
                <div class="card-header">
                    {{ isset($renewal) ? __('Update Renewal') : __('Add New Renewal') }}
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Choose Contract <a href="{{ route('contract.index') }}/create"
                                    class="new-contract-btn btn btn-primary btn-sm">
                                    Add New</a>
                            </label>
                            @if (isset($selectedContract))
                                <input type="hidden" name="contract_id" value="{{ $selectedContract['id'] }}" required>
                                <select class="form-control" name="contract_id" disabled required>
                                    <option value="{{ $selectedContract['id'] }}" selected>
                                        {{ $selectedContract['value'] }}
                                    </option>
                                </select>
                            @else
                                <select class="form-control" name="contract_id" required>
                                    <option value>
                                        Select Contract
                                    </option>
                                    @foreach ($contracts as $key => $item)
                                        <option value="{{ $key }}"
                                            {{ (isset($renewal) && $renewal->contract_id == $key) || old('contract_id') == $key ? "selected='selected'" : '' }}>
                                            {{ $item }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-control text-capitalize" name="status">
                                <option value="Open"> Open</option>
                                @foreach (['Close won', 'Close lost'] as $status)
                                    <option value="{{ $status }}"
                                        {{ (isset($renewal) && $renewal->status == $status) || old('status', 'OPEN') == $status ? "selected='selected'" : '' }}>
                                        {{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="quote_number" class="form-label">Quote Number</label>
                            <input type="text" name="quote_number" class="form-control"
                                value="{{ old('quote_number', isset($renewal) ? $renewal->quote_number : '') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            @if (isset($renewal->quote_file))
                                <label for="quote_file" class="form-label">Update Quote File &#8226; <a
                                        href="{{ asset($renewal->quote_file) }}" target="_blank">View Invoice File</a>
                                </label>
                            @else
                                <label for="quote_file" class="form-label">Quote File
                                    (PDF)
                                </label>
                            @endif
                            <input type="file" name="quote_file" class="form-control" accept="application/pdf">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="purchase_order_number" class="form-label">Purchase Order Number</label>
                            <input type="number" name="purchase_order_number" class="form-control"
                                value="{{ old('purchase_order_number', isset($renewal) ? $renewal->purchase_order_number : '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            @if (isset($renewal->po_file))
                                <label for="po_file" class="form-label">Update PO File &#8226; <a
                                        href="{{ asset($renewal->po_file) }}" target="_blank">View PO File</a>
                                </label>
                            @else
                                <label for="po_file" class="form-label">PO File (PDF)
                                </label>
                            @endif
                            <input type="file" name="po_file" class="form-control" accept="application/pdf">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="invoice_number" class="form-label">Invoice Number</label>
                            <input type="number" name="invoice_number" class="form-control"
                                value="{{ old('invoice_number', isset($renewal) ? $renewal->invoice_number : '') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            @if (isset($renewal->invoice_file))
                                <label for="invoice_file" class="form-label">Update Invoice File &#8226; <a
                                        href="{{ asset($renewal->invoice_file) }}" target="_blank">View Invoice File</a>
                                </label>
                            @else
                                <label for="invoice_file" class="form-label">Invoice File (PDF)
                                </label>
                            @endif
                            <input type="file" name="invoice_file" class="form-control" accept="application/pdf">
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-sm btn-success btn-submit" type="submit">Submit</button>
                    <a href="{{ route('renewal.index') }}" class="btn btn-sm btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
