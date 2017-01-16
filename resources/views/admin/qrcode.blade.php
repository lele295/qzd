<html>
<head>
    <meta charset="UTF-8">
    <title>生成商户二维码</title>
</head>
<body>
<form action="{{url('admin/qrcode/src')}}" method="post">
    <input name="_token" value="{{Csrf_Token()}}" type="hidden">
    请输入商家代码：<input name="rno" type="text"/><br/>
    <input type="submit" value="生成二维码">

    @if(\Illuminate\Support\Facades\Session::has('result'))
        @if(!session('result')['success'])
            <div style="color: red">{{session('result')['message']}}</div>
        @else
            <img src="{{session('result')['message']}}"/>
        @endif

    @endif
</form>

</body>
</html>