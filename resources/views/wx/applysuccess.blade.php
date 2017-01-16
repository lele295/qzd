<!DOCTYPE html>
<html style="background:white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>仟姿贷</title>

    <link rel="stylesheet" type="text/css" href="{{asset('css/wx/qzd.css')}}"/>
    <script src="{{asset('js/jquery-3.1.0.min.js')}}" type="text/javascript" charset="utf-8"></script>
    <script src="{{asset('js/wx/fontCom.js')}}" type="text/javascript" charset="utf-8"></script>

</head>
<body onload="loaded()">

<div id="apply" class="content">
    <div class="success">
        <img src="{{asset(url('img/wx/success.png'))}}"/>
        <p class="red">恭喜您申请成功！</p>
        <p class="time">预计审核时间为20分钟，休息一下...</p>
    </div>
    <div class="empty"></div>
    <div class="qrCode">
        <img src="{{asset(url('img/wx/qrCode.jpg'))}}" alt="" />
        <p>欢迎关注千姿贷，<br />及时了解审核进度。</p>
    </div>
</div>

<script type="text/javascript">
    $(function(){
        pushHistory();
        window.addEventListener("popstate", function(e) {
            window.location.href = '/wx/order/list';
        }, false);
        function pushHistory() {
            var state = {title: "title",  url: "#" };
            window.history.pushState(state, "title", "#");
        }
    })
</script>

</body>
</html>

