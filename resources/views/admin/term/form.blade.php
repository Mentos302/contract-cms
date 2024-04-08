@extends('layouts.master')
@section('title') Term @endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') {{isset($term)? 'Update' : 'Add'}} @endslot
@slot('title') Term @endslot
@endcomponent
    <div class="col-md-12">
      <form action="{{ isset($term) && $term->id ? route('term.update', $term->id) : route('term.store') }}" method="POST">
        @if (isset($term))
            @method('PUT')
        @endif
        @csrf()
        <div class="card">
          <div class="card-header">
            {{isset($term)? __('Update term'):__('Add New term')}}
          </div>
          <div class="card-body">
              <div class="row">
                <div class="col-md-4 mb-3">
                  <label class="form-label">Enter Term Year</label>
                  <input type="number" class="form-control" name="name" placeholder="1 yr,2yr..." value="{{ isset($term) ? $term->name : old('name') }}" />
                </div>
              </div>
          </div>
          <div class="card-footer">
            <button class="btn btn-sm btn-success btn-submit" type="submit">Submit</button>
            <a href="{{ route('term.index') }}" class="btn btn-sm btn-secondary">Cancel</a>
          </div>
        </div>
      </form>
    </div>
@endsection
@section('script')
<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection


