<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @vite(['resources/css/app.scss', 'resources/js/app.js'])
        <title>test</title>
    </head>
    <body>
<div>
    @yield('content')
    <x-navbar></x-navbar>
</div>

    </body>
</html>