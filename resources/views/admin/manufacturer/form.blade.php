@extends('layouts.master')
@section('title')
    Manufacturer
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            {{ isset($manufacturer) ? 'Update' : 'Add' }}
        @endslot
        @slot('title')
            Manufacturer
        @endslot
    @endcomponent
    <div class="col-md-12">
        <form
            action="{{ isset($manufacturer) && $manufacturer->id ? route('manufacturer.update', $manufacturer->id) : route('manufacturer.store') }}"
            method="POST">
            @if (isset($manufacturer))
                @method('PUT')
            @endif
            @csrf
            <div class="card">
                <div class="card-header">
                    {{ isset($manufacturer) ? __('Update manufacturer') : __('Add New manufacturer') }}
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" placeholder="manufacturer name"
                                value="{{ isset($manufacturer) ? $manufacturer->name : old('name') }}" />
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-sm btn-success btn-submit" type="submit">Submit</button>
                    <a href="{{ route('manufacturer.index') }}" class="btn btn-sm btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
