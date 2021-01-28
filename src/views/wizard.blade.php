@inject('AgentHelper', 'Lambda\Agent\Helper\AgentHelper')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title or $AgentHelper->projectName }}</title>
    <link href="{{$AgentHelper->favicon}}" rel="icon" />
    <link rel="stylesheet" href="{{ mix('css/vendor.css') }}">
    <link rel="stylesheet" href="/fonts/fira/fira.css">
    <link rel="stylesheet" href="/fonts/icons.css">
    <link rel="stylesheet" href="{{ mix('css/wizard.css') }}">
</head>
<body>
    <div id="wizard"></div>
<script>
    window.init = {
        user: {!! json_encode(Auth::user()) !!}
    }
</script>
<script>window.config = {copyright:"{{$AgentHelper->copyright}}", agentLogo:{!!json_encode($AgentHelper->agentLogo)!!}};</script>
<script src="/js/manifest.js"></script>
{{--<script src="/env.js"></script>--}}
<script src="{{ mix('js/vendor.js') }}"></script>
<script src="{{ mix('js/wizard.js') }}"></script>
</body>
</html>
