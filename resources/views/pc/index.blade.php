<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>首页</title>
    <link rel="stylesheet" type="text/css" href="{{asset('css/csspc/comomHead&Footer.css')}}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('css/csspc/index.css')}}"/>
</head>
<body>
<!--头部-->
<div id="header">
    <div class="header">
        <img src="{{asset('img/pcImg/logo.png')}}"/>
        <ul>
            <li><a href="{{url('/')}}"><span>首页</span></a></li>
            <li><a href="{{url('pc/pro')}}"><span>产品介绍</span></a></li>
            <li><a href="{{url('pc/alliance')}}"><span>合作加盟</span></a></li>
            <li><a href="{{url('pc/company')}}" class="lastA"><span>公司介绍</span></a></li>
        </ul>
    </div>
</div>
<!--广告牌-->
<div id="banner"></div>
<div id="footer">
    <p>Copyright &copy; 2014 billionscredit.com,All Rights Reserved 深圳市佰仟金融服务有限公司 版权所有 粤ICP备 14012749号</p>
</div>
</body>
</html>
