<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Akademik')</title>
    <link href="{{ asset('template/css/login.css')}}" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
</head>
<body>
    @yield('content')
    <script src="{{ asset('template/js/login.js') }}"></script>
    @vite('resources/js/app.js')
</body>
</html>
