@extends('layouts.master')
@section('title') Distributor @endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') {{isset($distributor)? 'Update' : 'Add'}} @endslot
@slot('title') Distributor @endslot
@endcomponent
    <div class="col-md-12">
      <form action="{{ isset($distributor) && $distributor->id ? route('distributor.update', $distributor->id) : route('distributor.store') }}" method="POST">
        @if (isset($distributor))
            @method('PUT')
        @endif
        @csrf()
        <div class="card">
          <div class="card-header">
            {{isset($distributor)? __('Update distributor'):__('Add New distributor')}}
          </div>
          <div class="card-body">
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label class="form-label">Name</label>
                  <input type="text" class="form-control" name="name" placeholder="distributor name" value="{{ isset($distributor) ? $distributor->name : old('name') }}" />
                </div>
              </div>
          </div>
          <div class="card-footer">
            <button class="btn btn-sm btn-success btn-submit" type="submit">Submit</button>
            <a href="{{ route('distributor.index') }}" class="btn btn-sm btn-secondary">Cancel</a>
          </div>
        </div>
      </form>
    </div>
@endsection
@section('script')
<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection

