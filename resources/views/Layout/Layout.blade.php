<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="_token" content="{{csrf_token()}}">
    <link rel="shortcut icon" href={{ asset('favicon.ico') }} type="image/x-icon">
    <link rel="stylesheet" href={{ asset('css/app.css') }}>
    <title>@yield('title')</title>
    @yield('headers')
</head>
<body>
    @yield('tamplates')
    <div class="wrapper">
        {{-- popup box --}}
        <div id="popup_box" class="hide"></div>
        @include('Layout.Header')
        @yield('body')
        @include('Layout.Footer')
    </div>

    {{-- menu stack is the script for header  --}}
    @stack('checkbox')
    <script>
        const user = @json($user);
    </script>
    <script src="{{ asset('js/app.js') }}"></script>

    @yield('scripts')
</body>
</html>