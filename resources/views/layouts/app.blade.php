<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @yield('css')
</head>

<body cz-shortcut-listen="true">
    <div id="app">
        @include('admin.shared.header')
        <div class="container-fluid">
            <div class="row">
                @auth
                    {{-- @include('admin.shared.sidebar') --}}
                @endauth
                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mt-5">
                    @include('admin.shared.alerts')
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul class="m-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @yield('content')
                </main>
            </div>
        </div>
    </div>
    @yield('javascript')
</body>

</html>
