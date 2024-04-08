@extends('layouts.master')
@section('title') Break contracts down by month @endsection
@section('content')
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-md-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-0 flex-grow-1">Break contracts down by month</h4>
                </div>
                <form action="{{route('break.contracts.downby.month')}}" method="GET" class="mx-3" role="search">
                    <div class="row">
                        <div class="col-md-5">
                            <label class="form-label"> Start Date</label>
                            <input type="date" required class="form-control" name="start_date" value="{{request('start_date')? request('start_date'):'' }}" >
                        </div>
                        <div class="col-md-5">
                            <label class="form-label"> End Date</label>
                            <input type="date" required class="form-control" name="end_date" value="{{request('end_date')? request('end_date'):'' }}" >
                        </div>
                        <div class="col-md-1 mt-4">
                            <span class="input-group-btn">
                                <button type="submit" class="btn btn-dark">Search </button>
                            </span>
                        </div>
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
                                    <th scope="col">Month</th>
                                    <th scope="col">Value</th>
                                </tr>
                            </thead>
                            <tbody>
                              @foreach ($monthlyTotals as $key => $item)

                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$key }}</td>
                                    <td>{{$item }}</td>
                                </tr>
                              @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection
