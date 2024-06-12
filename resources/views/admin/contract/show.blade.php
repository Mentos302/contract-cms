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
    @if (session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif
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
            <div class="card-body show">
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
                        <input type="text" class="form-control" value="{{ $contract->name }}" readonly />
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
    <div class="card">
        <div class="card-header">
            <strong>Contract History</strong>
        </div>
        <div class="card-body">
            @if ($contract->renewals->isEmpty())
                <p>No history available for this contract.</p>
            @else
                <table class="table table-bordered renewal-history">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Quote Number</th>
                            <th>Quote File</th>
                            <th>Purchase Order Number</th>
                            <th>PO File</th>
                            <th>Invoice Number</th>
                            <th>Invoice File</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($contract->renewals as $renewal)
                            <tr>
                                <td><b>{{ \Carbon\Carbon::parse($renewal->expiring_date)->format('Y') }}</b>
                                    @if ($renewal->status === 'Open')
                                        <div>Open</div>
                                    @endif
                                </td>
                                <td><i>{{ $renewal->quote_number }}</i></td>
                                <td>
                                    @if ($renewal->quote_file)
                                        <a href="{{ asset('storage/' . $renewal->quote_file) }}" target="_blank">View
                                            PDF</a>
                                    @else
                                        <span class="renewal-na">N/A</span>
                                    @endif
                                </td>
                                <td>{!! $renewal->purchase_order_number ? $renewal->purchase_order_number : '<span class="renewal-na">N/A</span>' !!}</td>
                                <td>
                                    @if ($renewal->po_file)
                                        <a href="{{ asset('storage/' . $renewal->po_file) }}" target="_blank">View
                                            PDF</a>
                                    @else
                                        <span class="renewal-na">N/A</span>
                                    @endif
                                </td>
                                <td>{!! $renewal->invoice_number ?? '<span class="renewal-na">N/A</span>' !!}</td>
                                <td>
                                    @if ($renewal->invoice_file)
                                        <a href="{{ asset('storage/' . $renewal->invoice_file) }}" target="_blank">View
                                            PDF</a>
                                    @else
                                        <span class="renewal-na">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('renewal.edit', $renewal->id) }}">
                                        <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M21.2799 6.40005L11.7399 15.94C10.7899 16.89 7.96987 17.33 7.33987 16.7C6.70987 16.07 7.13987 13.25 8.08987 12.3L17.6399 2.75002C17.8754 2.49308 18.1605 2.28654 18.4781 2.14284C18.7956 1.99914 19.139 1.92124 19.4875 1.9139C19.8359 1.90657 20.1823 1.96991 20.5056 2.10012C20.8289 2.23033 21.1225 2.42473 21.3686 2.67153C21.6147 2.91833 21.8083 3.21243 21.9376 3.53609C22.0669 3.85976 22.1294 4.20626 22.1211 4.55471C22.1128 4.90316 22.0339 5.24635 21.8894 5.5635C21.7448 5.88065 21.5375 6.16524 21.2799 6.40005V6.40005Z"
                                                stroke="#000" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M11 4H6C4.93913 4 3.92178 4.42142 3.17163 5.17157C2.42149 5.92172 2 6.93913 2 8V18C2 19.0609 2.42149 20.0783 3.17163 20.8284C3.92178 21.5786 4.93913 22 6 22H17C19.21 22 20 20.2 20 18V13"
                                                stroke="#000" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('renewal.destroy', $renewal->id) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this renewal entry?');"
                                        style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit">
                                            <svg width="20px" height="20px" viewBox="-0.5 0 25 25" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path d="M3 21.32L21 3.32001" stroke="#000000" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M3 3.32001L21 21.32" stroke="#000000" stroke-width="1.5"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <strong>Add Contract History</strong>
        </div>
        <div class="card-body">
            <form action="{{ route('renewal.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="contract_id" value="{{ $contract->id }}">
                <input type="hidden" name="status" value="Close won">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="year" class="form-label">Year</label>
                        <input type="number" class="form-control" name="year" min="1900" max="2099"
                            step="1" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="quote_number" class="form-label">Quote Number</label>
                        <input type="text" name="quote_number" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="quote_file" class="form-label">Quote File (PDF)</label>
                        <input type="file" name="quote_file" class="form-control" accept="application/pdf">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="purchase_order_number" class="form-label">Purchase Order Number</label>
                        <input type="number" name="purchase_order_number" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="po_file" class="form-label">PO File (PDF)</label>
                        <input type="file" name="po_file" class="form-control" accept="application/pdf">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="invoice_number" class="form-label">Invoice Number</label>
                        <input type="number" name="invoice_number" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="invoice_file" class="form-label">Invoice File (PDF)</label>
                        <input type="file" name="invoice_file" class="form-control" accept="application/pdf">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Add Renewal</button>
            </form>

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
                            <p>Your contract number <strong>{{ $contract->mfr_contract_number }}</strong> has expired
                                on
                                <strong>{{ $contract->end_date }}</strong>.
                            </p>
                        @else
                            <p>Your contract number <strong>{{ $contract->mfr_contract_number }}</strong> is expiring
                                in
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
