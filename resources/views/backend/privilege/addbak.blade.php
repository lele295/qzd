<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>添加权限</title>
    <link href='/backend/css/bootstrap/bootstrap.css' media='all' rel='stylesheet' type='text/css' />
    <link href='/backend/css/light-theme.css' id='color-settings-body-color' media='all' rel='stylesheet' type='text/css' />
</head>
<script src="{{url('js/layer/layer.js')}}"></script>
<script src="{{url('js/jquery-1.9.1.min.js')}}"></script>
<style>
    div {vertical-align:middle;}
    td {text-align: left}
    .td {text-align: center}
    span {color: red}
</style>
<body bgcolor="white">
<div align="center">
<form action="{{url('privilege')}}" method="post">
    {{csrf_field()}}
<table border="0">
    <caption><h1>添加权限</h1></caption>
    <tr>
        <td>权限名称:</td>
        <td><input type="text" name="name"></td>
    </tr>
    <tr>
        <td>父级权限:</td><td><select name="pid">
                <option value="0">顶级权限</option>
                @foreach($data as $v)
                <option value="{{$v['id']}}">{{str_repeat('----',$v['level'])}}{{$v['name']}}</option>
                   @endforeach
            </select>
        </td>
    </tr>
    <tr>
        <td>规则(uri):</td><td><input type="text" name="base_uri"></td>
    </tr>
    <tr>
        <td>权限描述:</td><td><input type="text" name="description"></td>
    </tr>
    <tr>
        <td colspan="2" class="td">
            <input type="submit" value="提交">
            <input type="button" value="清除">
        </td>
    </tr>
</table>
    @if (count($errors) > 0)
                @foreach ($errors->all() as $error)
                    <span>{{ $error }}</span>
                @endforeach
    @endif
    @if(session('msg'))
       {{-- <span>{{session('msg')}}</span>--}}
        <span><script>alert('{{session('msg')}}')</script></span>
        @endif
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
