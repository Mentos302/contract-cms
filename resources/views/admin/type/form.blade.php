@extends('layouts.master')
@section('title') Type @endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') {{isset($type)? 'Update' : 'Add'}} @endslot
@slot('title') Type @endslot
@endcomponent
    <div class="col-md-12">
      <form action="{{ isset($type) && $type->id ? route('type.update', $type->id) : route('type.store') }}" method="POST">
        @if (isset($type))
            @method('PUT')
        @endif
        @csrf()
        <div class="card">
          <div class="card-header">
            {{isset($type)? __('Update type'):__('Add New type')}}
          </div>
          <div class="card-body">
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label class="form-label">Name</label>
                  <input type="text" class="form-control" name="name" placeholder="type name" value="{{ isset($type) ? $type->name : old('name') }}" />
                </div>
              </div>
          </div>
          <div class="card-footer">
            <button class="btn btn-sm btn-success btn-submit" type="submit">Submit</button>
            <a href="{{ route('type.index') }}" class="btn btn-sm btn-secondary">Cancel</a>
          </div>
        </div>
      </form>
    </div>
@endsection
@section('script')
<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection


