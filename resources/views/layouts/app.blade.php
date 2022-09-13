<!doctype html>
<html class="bootstrap-layout" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.head')
</head>

<body class="layout-container ls-top-navbar si-l3-md-up">
    @yield('content')

    @include('layouts.body-scripts')

    @yield('scripts')
</body>

</html>