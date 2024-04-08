@extends('layouts.master')
@section('title')
    Customer
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            {{ isset($customer) ? 'Update' : 'Add' }}
        @endslot
        @slot('title')
            Customer
        @endslot
    @endcomponent
    <div class="col-md-12">
        <form
            action="{{ isset($customer) && $customer->id ? route('customer.update', $customer->id) : route('customer.store') }}"
            method="POST">
            @if (isset($customer))
                @method('PUT')
            @endif
            @csrf()
            <div class="card">
                <div class="card-header">
                    {{ isset($customer) ? __('Update customer') : __('Add New Customer') }}
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" autocomplete="off" name="name"
                                placeholder="Enter customer name"
                                value="{{ isset($customer) ? $customer->name : old('name') }}" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" autocomplete="off" name="email"
                                placeholder="Enter customer email"
                                value="{{ isset($customer) ? $customer->email : old('email') }}" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" autocomplete="off" name="password"
                                placeholder="Enter password" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" autocomplete="off" name="password_confirmation"
                                placeholder="Confirm password" />
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-sm btn-success btn-submit" type="submit">Submit</button>
                    <a href="{{ route('customer.index') }}" class="btn btn-sm btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
