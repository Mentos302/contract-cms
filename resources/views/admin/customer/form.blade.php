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
            method="POST" autocomplete="off">
            @if (isset($customer))
                @method('PUT')
            @endif
            @csrf
            <div class="card">
                <div class="card-header">
                    {{ isset($customer) ? __('Update customer') : __('Add New Customer') }}
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                name="first_name" placeholder="Enter customer first name"
                                value="{{ isset($customer) ? $customer->first_name : old('first_name') }}" required />
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                name="last_name" placeholder="Enter customer last name"
                                value="{{ isset($customer) ? $customer->last_name : old('last_name') }}" required />
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                autocomplete="off" name="email" placeholder="Enter customer email"
                                value="{{ isset($customer) ? $customer->email : old('email') }}" required
                                autocomplete="off" />
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Job Title</label>
                            <input type="text" class="form-control" name="job_title"
                                placeholder="Enter customer job title"
                                value="{{ isset($customer) ? $customer->job_title : old('job_title') }}" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Department</label>
                            <input type="text" class="form-control" name="department"
                                placeholder="Enter customer department"
                                value="{{ isset($customer) ? $customer->department : old('department') }}" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Company Name</label>
                            <input type="text" class="form-control" name="company_name"
                                placeholder="Enter customer company name"
                                value="{{ isset($customer) ? $customer->company_name : old('company_name') }}" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="phone"
                                placeholder="Enter customer phone number"
                                value="{{ isset($customer) ? $customer->phone : old('phone') }}" />
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
