<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<title>申请成功</title>
		<script src="{{ asset('js/wx/zepto.js') }}" type="text/javascript" charset="utf-8"></script>
		<script src="{{ asset('js/wx/fontCom.js')}}" type="text/javascript" charset="utf-8"></script>
		<link rel="stylesheet" type="text/css" href="{{asset('css/wx/qzd.css')}}"/>
	</head>
	<body onload="loaded()" style="background: white">
		<div id="apply" class="content">
			<div class="secondHeader">
				<a class="backRound" href ="javascript:history.go(-1);"><i class="iconfont">&#xe65e;</i></a>
				<h2>申请成功</h2>
			</div>
			<div class="success">
				<img src="{{asset('img/wx/success.png')}}"/>
				<p class="red">恭喜您申请成功！</p>
				<p class="time">预计审核时间为20分钟，休息一下...</p>
			</div>
			<div class="empty"></div>
			<div class="qrCode">
				<img src="{{ asset('img/wx/qrCode.jpg') }}" alt="" />
				<p>欢迎关注千姿贷，<br />及时了解审核进度。</p>
			</div>
		</div>
	</body>
</html>
