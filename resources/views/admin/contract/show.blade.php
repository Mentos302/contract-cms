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
        @php
            $start_timestamp = strtotime($contract->start_date);
            $end_timestamp = strtotime($contract->end_date);
            $current_timestamp = time();
            if ($current_timestamp > $end_timestamp) {
                $days_remaining = 'Expired';
            } else {
                $difference = $end_timestamp - $start_timestamp;
                $days_remaining = ceil($difference / (60 * 60 * 24));
            }
        @endphp
        @if ($contract->status)
            @if ($days_remaining == 'Expired')
                <div class="alert alert-secondary alert-dismissible shadow fade show" role="alert">
                    <strong>Contract Expired!</strong> Your contract has
                    expired on <strong>{{ $contract->end_date }}</strong>. Please <b
                        style="cursor: pointer;text-decoration: underline;" data-bs-toggle="modal"
                        data-bs-target="#renewalModal">choose</b> one of the following options.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @elseif ($days_remaining <= 90)
                <div class="alert alert-secondary alert-dismissible shadow fade show" role="alert">
                    <strong>Contract Expiring Soon!</strong> Your contract is
                    expiring in <strong>{{ $days_remaining }}</strong> days on <strong>{{ $contract->end_date }}</strong>.
                    Please <b style="cursor: pointer;text-decoration: underline;" data-bs-toggle="modal"
                        data-bs-target="#renewalModal">choose</b> one of the following options.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        @endif
        <div class="card">
            <div class="card-header">
                <strong>{{ $contract->manufacturer->name }} Contract #{{ $contract->mfr_contract_number }} Details</strong>
            </div>
            <div class="card-body">
                <div class="row">
                    @if (Auth::user()->hasRole('admin'))
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Customer</label>
                            <input type="text" class="form-control"
                                value="{{ $contract->customer->first_name }} {{ $contract->customer->last_name }}"
                                readonly />
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
                        <input type="text" class="form-control" value="{{ formatDate($contract->end_date) }}"
                            readonly />
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
            <div class="card-footer content d-flex justify-content-between">
                <div>
                    <a href="/contracts-status?status=active" class="btn btn-sm btn-secondary">Back</a>
                    @if (isset($contract) &&
                            isset($contract->type->name) &&
                            $contract->type->name === 'Sivility Systems (3rd Party Maintenance)')
                        <a data-bs-toggle="modal" data-bs-target="#createTicketModal" class="btn btn-sm btn-success">Support
                            Ticket</a>
                    @endif
                </div>
                <div>
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
    </div>

    <div class="modal fade" id="createTicketModal" tabindex="-1" role="dialog"
        aria-labelledby="createTicketModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createTicketModalLabel">Create Support Ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('support-tickets.create') }}" method="POST">
                    <div class="modal-body">
                        @csrf
                        <div class="mb-3">
                            <label for="contract" class="form-label">Contract</label>
                            <input type="text" class="form-control" id="contract" name="contract_name"
                                value="{{ $contract->manufacturer->name }} Contract #{{ $contract->mfr_contract_number }}"
                                readonly required>
                            <input type="hidden" name="contract_id" value="{{ $contract->id }}">
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
    @php
        $start_timestamp = strtotime($contract->start_date);
        $end_timestamp = strtotime($contract->end_date);
        $current_timestamp = time();
        if ($current_timestamp > $end_timestamp) {
            $days_remaining = 'Expired';
        } else {
            $difference = $end_timestamp - $start_timestamp;
            $days_remaining = ceil($difference / (60 * 60 * 24));
        }
    @endphp
    @if ($contract->status)
        <div class="modal fade" id="renewalModal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Contract Renewal Options</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($days_remaining == 'Expired')
                            <p>Your contract number <strong>{{ $contract->mfr_contract_number }}</strong> has expired on
                                <strong>{{ $contract->end_date }}</strong>.
                            </p>
                        @else
                            <p>Your contract number <strong>{{ $contract->mfr_contract_number }}</strong> is expiring in
                                <strong>{{ $days_remaining }}</strong> days and must be renewed by
                                <strong>{{ $contract->end_date }}</strong>.
                            </p>
                        @endif
                        <p>Please select one of the following options:</p>

                        <form action="{{ route('contract.update.status', $contract->id) }}" method="POST">
                            @csrf
                            <select name="renewal_option" class="form-select mb-3" required>
                                <option value="quote">Please send me a quote!</option>
                                <option value="not">Not renewing contract</option>
                                <option value="another">Renewing through Another Reseller</option>
                            </select>

                            <div>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    @endif
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const urlParams = new URLSearchParams(window.location.search);

            if (urlParams.has('@')) {
                const myModal = new bootstrap.Modal(document.getElementById('renewalModal'));
                myModal.show();
            }
        });
    </script>
@endsection
