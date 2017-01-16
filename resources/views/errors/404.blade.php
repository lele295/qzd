<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="Description" content=""/>
    <meta name="Keywords"  content="">
    <meta content="telephone=no" name="format-detection">
    <meta content="width=device-width; initial-scale=1.0; maximum-scale=1.0; minimum-scale=1.0; user-scalable=false;" name="viewport">
    <meta name="apple-mobile-web-app-capable" content="yes" />

    <title>仟姿贷</title>
    <style>
        * {margin: 0;padding: 0; list-style:none; border:0px}
        html {height: 100%;}
        body {font-family:"微软雅黑","黑体",Arial, Helvetica, sans-serif;font-size: 16px; background:#f0f0f0;}
        a {color: #606060;text-decoration: none;}
        a:hover {text-decoration: none;}
        img{border:none 0;}

        /*头部*/
        .header{width:100%; height:50px; line-height:50px; background:#fff; border:1px solid #d4d3d7; position:fixed; top:0; left:0; z-index:1000;}
        .header_ul{display:block; height:25px; line-height:25px;width:90%; margin:12px auto 0 auto; overflow:hidden;}
        .header_li{display:block; float:left; padding-right:5%; border-right:1px solid #d4d3d7;}
        .header_li2{ padding-left:5%; font-size:14px; color:#534f4c; border-right:0;}
        .header_li_a{ display:inline-block; width:100%;height:25px;}
        .header_li_a_img{height:25px;}

        /*底部2*/
        .footer2{ background:#f0f0f0;width:100%; position:fixed; bottom:0; left:0;}
        .footer2_p{ font-size:12px; width:100%; line-height:23px; text-align:center; padding:10px 0;color:#828282;}
        .footer2_p_span{ font-family:Arial, Helvetica, sans-serif;}

        .error404{width:100%; height:100%; position:fixed; top:45px; background:url({{asset('/img/404_img.png)}}) no-repeat left top; background-size:100%; text-align:center;}
        .error404_img{width:60%; margin:40px 0 20px 0;}
        .error404_p{ width:100%; text-align:center; font-size:13px; color:#828282; margin:0 0 15px 0;}
        .error404_p_span{ font-size:18px;display:inline-block; margin-bottom:7px;}

        .error505_a{ display:inline-block;width:30%; height:40px; line-height:40px; text-align:center; font-size:14px; background:#eb7c69; color:#fff; border-radius:5px;}
        .error505_img{width:80%;margin:40px 0 20px 0}
        .error505_p{ width:100%; text-align:center; font-size:14px; color:#828282; position:relative; top:-100px;}
        .error505_p_span{ font-size:18px; color:#f1625f; display:inline-block; margin-bottom:10px;}
    </style>
</head>
<body>

<div class="header">
    <ul class="header_ul">
        <li class="header_li"><a class="header_li_a"><img src="{{asset('/img/wx/logo.png')}}" class="header_li_a_img"></a></li>
        <li class="header_li header_li2">佰仟金融旗下网站</li>
    </ul>
</div>


<div class="content">
    <div class="error404">
        <img src="{{asset('/img/404_img.png')}}" class="error404_img">
        <p class="error404_p"><span class="error404_p_span">你的页面不存在</span><br/>Your page does not exist</p>
        <a href="javascript:history.go(-1);" class="error505_a">点击返回</a>
    </div>
</div>

<div class="footer2">
    <p class="footer2_p"><span class="footer2_p_span">Copyright © www.qianzidai.cn All rights reserved </span><br/>深圳市佰仟金融服务有限公司 版权所有</p>
</div>

</body>
</html>

<script type="text/javascript">
    $(function (){



    });

</script>