@php
    $config = [
        'appName' => config('app.name'),
        'locale' =>  config('app.locale'),
        'locales' => config('app.locales')

    ];
@endphp
        <!DOCTYPE html>
<html lang="{{ $config['locale'] }}">
<head>
    <meta charset="utf-8">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/favicon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon-16x16.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>{{ $config['appName'] }}</title>

</head>
<body>
<div id="app"></div>

{{-- Global configuration object --}}
<script>
   window.config = @json($config);
</script>

{{-- Load the application scripts --}}
<script src="{{ mix('dist/js/app.js') }}"></script>
</body>
</html>
