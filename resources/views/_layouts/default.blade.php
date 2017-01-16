<?php
if(!isset($source)){
    $source=1;
    $check_head = \Illuminate\Support\Facades\Auth::check();
    if($check_head){
        $source = \Illuminate\Support\Facades\Auth::user()->source;
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta content="telephone=no" name="format-detection">
<meta content="width=device-width; initial-scale=1.0; maximum-scale=1.0; minimum-scale=1.0; user-scalable=false;" name="viewport">
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="format-detection" content="telephone=no">

  <title>@if($source==3) 佰仟金融 @else 借钱么 @endif</title>
  <script src="{{asset("js/jquery-3.1.0.min.js")}}"></script>
    <script type="text/javascript" src="{{asset("js/jquery-migrate.js")}}"></script>
    <script type="text/javascript" src="{{asset("js/jquery.validate.js")}}"></script>
    <link rel="stylesheet" href="{{asset("css/css.css")}}">
    <script type="text/javascript" src="{{asset("js/layer.m.js")}}"></script>
    <link rel="stylesheet" href="{{asset("css/layer.css")}}">
    <style type="text/css">
        input[type="tel"],input[type="text"],input[type="password"]{border:1px solid #c4c4c4; outline: none; width:81%; padding:0 13% 0 5%; font-size:14px; height:35px; line-height:35px; border-radius:5px;}
        input[type="tel"].error,input[type="text"].error,input[type="password"].error{border:1px solid red;}
        input[type="tel"]:hover,input[type="text"]:hover,input[type="password"]:hover{border:1px solid #ffa2a0;}
        label.error{color:#ff3300; display:block; text-align:left; font-size:12px;}

        .btn_1{width:90%; height:37px;  border:0px; font-size:12px; color:#fff; background:#f1625f; cursor:pointer; margin-left:10px; border-radius:5px;}
        .btn_1:hover{background:#f1625f;}

        .btn_2{width:90%; height:37px; border:0px; font-size:12px; color:#fff; background:#CCC; cursor:default; margin-left:10px; border-radius:5px;}
        .btn_2:hover{background:#CCC;}

    </style>
</head>
<body>

@if($source==3)
    <div class="header">
        <ul class="header_ul">
            <li class="header_li"><a class="header_li_a"><img src="{{asset("images/logo_jieqian.png")}}" class="header_li_a_img"></a></li>
            <li class="header_li header_li2">交叉现金贷办理平台</li>
        </ul>
    </div>
@else
    <div class="header">
        <ul class="header_ul">
            <li class="header_li"><a class="header_li_a"><img src="{{asset("images/logo.png")}}" class="header_li_a_img"></a></li>
            <li class="header_li header_li2">佰仟金融旗下网站</li>
        </ul>
    </div>
@endif

<div class="content">
    @yield('content')
</div>
<div class="footer">
    <p class="footer_p">Copyright © 2015,All Rights Reserved<br/>深圳市佰仟金融服务有限公司 版权所有</p>
</div>
<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "//hm.baidu.com/hm.js?4eee259714e24529fe05bcc0192a47be";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>
</body>
</html>