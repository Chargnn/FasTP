<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ config('app.name') }} - {{ Route::currentRouteName() }}</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @yield('header')
    </head>
    <body>
        @include('layout.error_notifications')
        @yield('content')
        @yield('footer')
    </body>
</html>