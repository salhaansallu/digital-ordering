<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#52bbdd">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Digital Ordering System - NMSware Technologies</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('assets/images/brand/favicon3.ico') }}" type="image/x-icon">

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @if (Request::is('customer-copy/*'))
        <!-- CSS -->
        <link rel="stylesheet" href="{{ asset('assets/assets/css/backend-plugin.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/assets/css/backende209.css?v=1.0.0') }}">

        <!-- Scripts -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        @vite(['resources/views/pos/sass/app.scss'])
        <script src="{{ asset('assets/assets/js/backend-bundle.min.js') }}"></script>
        <script src="{{ asset('assets/assets/js/table-treeview.js') }}"></script>
        <script src="{{ asset('assets/assets/js/customizer.js') }}"></script>
        <script async src="{{ asset('assets/assets/js/chart-custom.js') }}"></script>
        <script src="{{ asset('assets/assets/js/app.js') }}"></script>
    @endif
</head>

<body>
    @isset($errormsg)
        <script>
            toastr.error("{{ $errormsg }}", "Error");
        </script>
    @endisset
    <div id="app">

        <main>
            @yield('content')
        </main>
    </div>
</body>

</html>
