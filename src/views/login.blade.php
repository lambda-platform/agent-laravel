<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{!! csrf_token() !!}">
    <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed:300,400,700|Roboto:300,400,500,700,900&amp;subset=cyrillic-ext" rel="stylesheet">
    <title>{{ config('lambda.title') }}</title>
    <link rel="stylesheet" href="{{ mix('lambda/vendor.css') }}">
    <link rel="stylesheet" href="{{ mix('lambda/auth.css') }}">
</head>
<body>
<noscript>
    This application uses JavaScript. Please enable Javascript then restart again!
</noscript>
<div id="app">
    <div class="loader-page">
        <div class="lds-grid">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
</div>

<script src="/lambda/manifest.js"></script>
<script>
    window.lambda = {!! json_encode(config('lambda')) !!};
</script>
<script src="{{ mix('lambda/vendor.js') }}"></script>
<script src="{{ mix('lambda/auth.js') }}"></script>
</body>
</html>
