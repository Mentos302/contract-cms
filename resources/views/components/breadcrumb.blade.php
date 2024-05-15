<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">{{ $title }}</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">{{ $li_1 }}</a></li>
                    @if (isset($title))
                        <li class="breadcrumb-item active">{{ $title }}</li>
                    @endif
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->
@auth
    @if (is_array(session('errors')) || $errors->any())
        <div class="alert alert-danger">
            <ul>
                @if (is_array(session('errors')) && count(session('errors')) > 0)
                    @foreach (session('errors') as $error)
                        @if (is_array($error))
                            @foreach ($error as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        @else
                            <li>{{ $error }}</li>
                        @endif
                    @endforeach
                @elseif ($errors->any())
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                @endif
            </ul>
        </div>
    @endif
@endauth
@include('admin.shared.alerts')
