<!doctype html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>CSI</title>

    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datetimepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="@yield('css')">

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/moment.js') }}"></script>
    <script src="{{ asset('js/datetimepicker.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="@yield('js')"></script>
</head>

<body>

    @include('layout.header')

    @yield('content')

    @include('layout.footer')

</body>