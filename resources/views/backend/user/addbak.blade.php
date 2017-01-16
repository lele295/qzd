<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>添加管理员</title>
    <link href='/backend/css/bootstrap/bootstrap.css' media='all' rel='stylesheet' type='text/css' />
    <link href='/backend/css/light-theme.css' id='color-settings-body-color' media='all' rel='stylesheet' type='text/css' />
</head>
<script src="{{url('js/jquery-1.9.1.min.js')}}"></script>
<style>
    div {vertical-align:middle;}
    td {text-align: right}
    .td {text-align: center}
    span {color: red}
</style>
<body bgcolor="white">
<div>
<form action="{{url('user')}}" method="post">
    {{csrf_field()}}
<table align="center">
    <caption><h1>添加管理员</h1></caption>
    <tr>
        <td>用户名:</td><td><input type="text" name="username"></td>
    </tr>
    <tr>
        <td>管理员密码:</td><td><input type="password" name="password"></td>
    </tr>
    <tr>
        <td>真实姓名:</td><td><input type="text" name="real_name"></td>
    </tr>
    <tr>
        <td>邮箱:</td><td><input type="text" name="email"></td>
    </tr>
    <tr>
        <td>电话:</td><td><input type="text" name="phone"></td>
    </tr>
    <tr>
        <td>职位:</td><td><input type="text" name="position"></td>
    </tr>
    <tr>
        <td>省份:</td><td><input type="text" name="province"></td>
    </tr>
    <tr>
        <td>城市:</td><td><input type="text" name="city"></td>
    </tr>
    <tr>
        <td>角色:
        </td><td><select name="id" style="width: 173px">
                <option>请选择角色</option>
                @foreach($data as $v)
                <option value="{{$v->id}}">{{$v->rolename}}</option>
                    @endforeach
            </select>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="td">
            <input type="submit" value="提交" class="s_btn">
            <input type="button" value="清除">
        </td>
    </tr>
    @if (count($errors) > 0)
        @foreach ($errors->all() as $error)
            <span>{{ $error }},</span>
        @endforeach
    @endif
    @if(session('msg'))
        {{-- <span>{{session('msg')}}</span>--}}
        <span><script>alert('{{session('msg')}}')</script></span>
    @endif
</table>
</form>

    </div>
</body>
</html>
<script>
    $(function () {
        $("[type='button']").click(function () {
            $("[type='text']").val('');
        });
    })
</script>