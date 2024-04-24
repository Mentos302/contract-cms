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
                Contract #{{ $contract->number }} Details
            </div>
            <div class="card-body">
                <div class="row">
                    @if (Auth::user()->hasRole('admin'))
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Customer</label>
                            <input type="text" class="form-control" value="{{ $contract->customer->name }}" readonly />
                        </div>
                    @endif
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
                        <input type="text" class="form-control" value="{{ formatDate($contract->start_date) }}"
                            readonly />
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">End Date</label>
                        <input type="text" class="form-control" value="{{ formatDate($contract->end_date) }}" readonly />
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control" value="{{ $contract->location }}" readonly />
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Contract Price (USD)</label>
                        <input type="text" class="form-control" value="{{ $contract->contract_price }}" readonly />
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Contract Owner</label>
                        <input type="text" class="form-control" value="{{ $contract->contract_owner }}" readonly />
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
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Serial Number</label>
                        <input type="text" class="form-control" value="{{ $contract->serial_number }}" readonly />
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">MFR Contract Number</label>
                        <input type="text" class="form-control" value="{{ $contract->mfr_contract_number }}" readonly />
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Name (Optional)</label>
                        <input type="text" class="form-control" value="{{ $contract->optional_name }}" readonly />
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="/contracts-status?status=active" class="btn btn-sm btn-secondary">Back</a>
                <a href="{{ route('contract.edit', $contract->id) }}" class="btn btn-sm btn-primary">Edit Contract</a>
                @if (isset($contract))
                    <button class="btn btn-sm btn-danger delete-btn" type="button" data-bs-toggle="modal"
                        data-bs-target="#deleteModal">
                        Delete Contract
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Contract</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this contract?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <form action="{{ route('contract.destroy', $contract->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
