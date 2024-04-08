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
        <form action="{{ isset($renewal) ? route('renewal.update', $renewal->id) : route('renewal.store') }}" method="POST">
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
