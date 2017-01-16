<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>角色列表</title>
    <link href='/backend/css/bootstrap/bootstrap.css' media='all' rel='stylesheet' type='text/css' />
    <link href='/backend/css/light-theme.css' id='color-settings-body-color' media='all' rel='stylesheet' type='text/css' />
    <style>
        table{align-content: center;text-align: center}
    </style>
</head>
<body bgcolor="white">
<form>
    <table width='100%' border="1" style="border: 1px gainsboro solid;border-collapse: collapse;">
        <caption><h1>权限列表</h1></caption>
        <tr>
            <th>角色名称</th>
            <th>拥有权限</th>
            <th>权限描述</th>
            <th>添加时间</th>
            <th>操作</th>
        </tr>
        @foreach($data as $v)
        <tr>
            <td>{{$v->rolename}}</td>
            <td>{{$v->c}}</td>
            <td>{{$v->description}}</td>
            <td>{{date('Y-m-d h:i:s',$v->created_at)}}</td>
            <td><a href="#">编辑</a>|<a href="#">删除</a></td>
        </tr>
            @endforeach
    </table>
</form>
</body>
</html>