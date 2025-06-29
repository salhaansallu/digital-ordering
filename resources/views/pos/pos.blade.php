<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Digital Ordering System - POS</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('assets/images/brand/favicon3.ico') }}" type="image/x-icon">

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    @vite(['resources/views/pos/sass/pos.scss', 'resources/js/app.js'])

    <script src="https://printjs-4de6.kxcdn.com/print.min.js"></script>
    <link rel="stylesheet" href="https://printjs-4de6.kxcdn.com/print.min.css">


    <style>
        body {
            background: #f3f3f3;
        }
    </style>
    <script>
        window.addEventListener('offline', () => {
            alert('No internet connection, please check your network');
        });

        window.addEventListener('click', () => {
            if (!window.navigator.onLine) {
                alert('No internet connection, please check your network');
            }
        });
    </script>
</head>

<body>
    <div id="pos-elem">
        <pos v-bind:app_url="'{{ env('APP_ENV')=='production'? '/var/www/image.nmsware.com/' : env('app.url').'/assets/images/' }}'" />
    </div>
</body>

</html>
