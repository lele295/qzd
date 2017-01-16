<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>仟姿贷</title>

    @if(Request::is('wx/order/list') || Request::is('wx/order/detail'))
        <link rel="stylesheet" type="text/css" href="{{asset('css/wx/qzdClient.css')}}"/>
    @else
        <link rel="stylesheet" type="text/css" href="{{asset('css/wx/qzd.css')}}"/>
    @endif

    <script src="{{asset('js/wx/zepto.js')}}" type="text/javascript" charset="utf-8"></script>
    <script src="{{asset('js/wx/fontCom.js')}}" type="text/javascript" charset="utf-8"></script>
    <script src="{{asset('js/jquery-3.1.0.min.js')}}" type="text/javascript" charset="utf-8"></script>
    <script src="{{asset('js/jquery.validate.min.js')}}" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" src="{{asset("js/layer.js")}}"></script>

</head>
<body onload="loaded()">

<div>
    @yield('content')
</div>

</body>
</html>
