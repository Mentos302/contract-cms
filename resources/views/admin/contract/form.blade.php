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
            @csrf
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    {{ isset($contract) ? __('Update contract') : __('Add New Contract') }}
                    @if (!isset($contract))
                        <button class="btn btn-sm btn-primary delete-btn" type="button" data-bs-toggle="modal"
                            data-bs-target="#importCSVModal">
                            Import CSV
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row">
                        @if (Auth::user()->hasRole('admin'))
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Customer
                                    <a href="{{ route('customer.index') }}/create"
                                        class="new-contract-btn btn btn-primary btn-sm">Add New</a>

                                </label>
                                <select class="form-control" name="customer_id" required>
                                    <option value=""> Select Customer</option>
                                    @foreach ($customers as $customerId => $customerName)
                                        <option value="{{ $customerId }}"
                                            {{ isset($contract) && ($contract->customer_id == $customerId || old('customer_id') == $customerId) ? 'selected' : '' }}>
                                            {{ $customerName ? $customerName : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <div class="col-md-3 mb-3" style="display: none;">
                                <label class="form-label">Customer</label>
                                <input type="text" readonly class="form-control" value="{{ Auth::user()->id }}" />
                                <input type="hidden" name="customer_id" value="{{ Auth::user()->id }}" />
                            </div>
                        @endif

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Select Type</label>
                            <select class="form-control" name="type_id">
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
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="start_date"
                                value="{{ isset($contract) ? $contract->start_date : old('start_date') }}" required />
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Select Term</label>
                            <select class="form-control" name="term_id" required>
                                <option value=""> Select Term</option>

                                @foreach ($terms as $key => $item)
                                    <option value="{{ $key }}"
                                        {{ (isset($contract) && $contract->term_id == $key) || old('term_id') == $key ? "selected='selected'" : '' }}>
                                        {{ $item == 1 ? $item . ' Year' : $item . ' Years' }}</option>
                                @endforeach
                            </select>
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
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Contract Owner</label>
                            <select class="form-control" id="contract_owner" required>
                                <option value="Sivility Systems"
                                    {{ !isset($contract) || (isset($contract) && $contract->contract_owner === 'Sivility Systems') ? 'selected' : '' }}>
                                    Sivility Systems</option>
                                <option value="Other Partner"
                                    {{ isset($contract) && $contract->contract_owner !== 'Sivility Systems' ? 'selected' : '' }}>
                                    Other Partner</option>
                            </select>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Serial Number</label>
                            <input type="text" class="form-control" name="serial_number"
                                value="{{ isset($contract) ? $contract->serial_number : old('serial_number') }}" />
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">MFR Contract Number</label>
                            <input type="text" class="form-control" name="mfr_contract_number"
                                value="{{ isset($contract) ? $contract->mfr_contract_number : old('mfr_contract_number') }}" />
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Name (Optional)</label>
                            <input type="text" class="form-control" name="name"
                                value="{{ isset($contract) ? $contract->name : old('name') }}" />
                        </div>
                        <div class="col-md-3 mb-3" id="contract_provider_div"
                            style="{{ isset($contract) && $contract->contract_owner !== 'Sivility Systems' ? '' : 'display: none;' }}">
                            <label class="form-label">Contract Provider Name</label>
                            <input type="text" class="form-control" name="contract_owner"
                                value="{{ isset($contract) ? $contract->contract_owner : old('contract_owner') ?? 'Sivility Systems' }}" />
                        </div>
                        @if (Auth::user()->hasRole('admin'))
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Contract Cost (USD)</label>
                                <input type="number" class="form-control" step="any" name="contract_cost"
                                    value="{{ isset($contract) ? $contract->contract_cost : old('contract_cost') }}"
                                    required />
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-sm btn-success btn-submit" type="submit">
                        {{ isset($contract) ? 'Save' : 'Submit' }}
                    </button>
                    <a href="{{ isset($contract) ? route('contract.show', $contract->id) : '/contracts-status?status=active' }}"
                        class="btn btn-sm btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
    <div class="modal fade" id="importCSVModal" tabindex="-1" role="dialog" aria-labelledby="importCSVLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="avatarUploadModalLabel">Contracts import CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('contracts.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="csv_file" id="csvFileInput" class="form-control" accept=".csv"
                            required>
                        <div class="image-modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const contractOwner = $('#contract_owner');
            const contractProviderDiv = $('#contract_provider_div');
            const contractProviderInput = $('input[name="contract_owner"]');

            contractOwner.change(function() {
                if (contractOwner.val() === 'Sivility Systems') {
                    contractProviderInput.val('Sivility Systems');
                    contractProviderDiv.hide();
                } else {
                    contractProviderInput.val('');
                    contractProviderDiv.show();
                }
            });
        });
    </script>
@endsection
