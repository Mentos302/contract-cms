@extends('layouts.master')
@section('title') Manufacturers @endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') List @endslot
@slot('title') Manufacturers @endslot
@endcomponent
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-md-flex justify-content-between">
                <div class="d-flex align-items-baseline">
                    <h4 class="card-title mb-0 flex-grow-1">Manufacturer</h4>
                    <a href="{{ route('manufacturer.create') }}" class="btn btn-outline-secondary mx-2">
                        <span class="icon-on"><i class="ri-add-line align-bottom me-1"></i> Add</span>
                    </a>
                </div>
                <form action="{{route('manufacturer.index')}}" method="GET" role="search">
                    <div class="input-group">
                        <input type="search" class="form-control" name="search" value="{{request('search')? request('search'):'' }}" placeholder="Write Here.."> <span class="input-group-btn">
                            <button type="submit" class="btn btn-dark">Search </button>
                        </span>
                    </div>
                </form>
            </div>

            <div class="card-body">
                <div class="live-preview">
                    <div class="table-responsive">
                        <table class="table align-middle table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($manufacturers as $key=> $item)
                                    <tr>
                                        <th scope="row">{{$key+1}}</th>
                                        <td>{{ $item->name }}</td>
                                        <td class="d-flex">
                                            <a  href="{{route('manufacturer.edit', $item->id)}}" class="btn btn-success btn-icon waves-effect waves-light"><i class=" ri-pencil-fill"></i></a>
                                            <form action="{{ route('manufacturer.destroy', $item->id) }}" method="POST">
                                                @method('DELETE')
                                                @csrf
                                                <button onclick="return confirm('Are you sure to delete Item?')" class="btn btn-danger btn-icon waves-effect waves-light" type="submit"  style="margin-left:0.3rem"><i class="ri-delete-bin-5-line"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                {{ $manufacturers->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
