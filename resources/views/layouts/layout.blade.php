<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @vite(['resources/css/app.scss', 'resources/js/app.js'])
        <title>test</title>
    </head>
    <body>
<div>
    <x-navbar></x-navbar>
    @yield('content')
   
</div>

    </body>
</html>