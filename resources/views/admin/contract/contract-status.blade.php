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
                                        <td><a href="{{ route('contract.show', $item->id) }}">#{{ $item->id }}</a></td>
                                        <td>{{ $item->manufacturer->name }}</td>
                                        <td>{{ $item->type->name }}</td>
                                        <td>{{ $item->serial_number }}</td>
                                        <td>{{ $item->mfr_contract_number }}</td>
                                        <td>{{ $item->term->name == 1 ? $item->term->name . ' Year' : $item->term->name . ' Years' }}
                                        </td>
                                        <td>{{ formatDate($item->start_date) }}</td>
                                        <td>{{ formatDate($item->end_date) }}</td>
                                        <td>${{ $item->contract_price }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->location }}</td>
                                        @php
                                            $openRenewal = \App\Models\Renewal::where('contract_id', $item->id)
                                                ->where('status', 'Open')
                                                ->exists();
                                        @endphp
                                        @if (!Auth::user()->hasRole('admin'))
                                            @if ($openRenewal)
                                                <td>
                                                    <button disabled class="btn btn-disabled">Quote Requested</button>
                                                </td>
                                            @else
                                                <td>
                                                    <button class="btn btn-primary request-renewal" style="width: 140px;"
                                                        data-contract-id="{{ $item->id }}">
                                                        <span class="spinner-border spinner-border-sm d-none" role="status"
                                                            aria-hidden="true"></span>
                                                        <span class="text-hide">Request a Quote</span>
                                                    </button>
                                                </td>
                                            @endif
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div><!-- end table responsive -->
                </div><!-- end card body -->
            </div><!-- end card -->
            <div class="alert alert-secondary alert-dismissible shadow show renewal-alert" role="alert" id="successAlert"
                style="display: none;">
                <strong>Renewal quote requested successfully!</strong>
            </div>
        </div><!-- end col -->

    </div>
@endsection
@section('script')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.request-renewal').click(function(e) {
                e.preventDefault();
                var btn = $(this);
                var contractId = btn.data('contract-id');

                btn.prop('disabled', true);
                btn.find('.spinner-border').removeClass('d-none').addClass('visible');
                btn.find('.text-hide').addClass('visually-hidden');

                axios.post('{{ route('renewal.store.customer') }}', {
                        contract_id: contractId,
                        status: "Open"
                    })
                    .then(function(response) {
                        btn.html(
                            '<span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span> Quote Requested'
                        ).addClass('btn-disabled');
                        $('#successAlert').fadeIn();
                        setTimeout(function() {
                            $('#successAlert').fadeOut();
                        }, 3500);
                    })
                    .catch(function(error) {
                        console.error(error);
                        btn.prop('disabled', false);
                        btn.find('.spinner-border').addClass('d-none').removeClass('visible');
                        btn.find('.text-hide').removeClass('visually-hidden');
                    });
            });
        });
    </script>
@endsection
