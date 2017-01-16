@extends('_layouts.backend')

@section('content')
    <div class="header-title">
        <span>添加角色</span>
    </div>
<div align="center">
<form action="{{url('role')}}" method="post">
    {{csrf_field()}}
<table border="0">
    <tr>
        <td>角色名称:</td><td><input type="text" name="rolename"></td>
    </tr>
    <tr>
        <td>角色描述:</td><td><input type="text" name="description"></td>
    </tr>
    <tr>
        <td class="col-title" style="text-align: right">职位:</td><td><input type="text" name="position" placeholder="如果角色针对安硕用户时必填" /><span style="color: red">本地管理员请不要填</span></td>
    </tr>
    <tr>
        <td colspan="2" align="center">权限名称:</td></tr>
            @foreach($data as $v)
            &nbsp;&nbsp;<tr><td></td><td style="height: 10px"><input type="checkbox" name="base_uri[]" value="{{$v['id']}}">{{str_repeat('----',$v['level'])}}{{$v['name']}}
            @if($v['base_uri'])
                ({{$v['base_uri']}})
                @endif
                @endforeach
        </td></tr>
    <tr>
        <td></td>
        <td colspan="" class="td">
            <input type="submit" value="提交" class="s_btn">
            <input type="button" value="清除" class="s_btn">
        </td>
    </tr>
</table>
    @if (count($errors) > 0)
        @foreach ($errors->all() as $error)
            <span STYLE="color: red">{{ $error }}</span>
        @endforeach
    @endif
    @if(session('msg'))
        {{-- <span>{{session('msg')}}</span>--}}
        <span><script>alert('{{session('msg')}}')</script></span>
    @endif
</form>
    </div>

<script src="{{url('js/jquery-1.9.1.min.js')}}"></script>
<script src="{{url('js/layer/layer.js')}}"></script>
<script>
    $(function () {
        $("[type='button']").click(function () {
            $("[type='text']").val('');
            $("[type='checkbox']").removeAttr("checked");
        });
    })
</script>
<script>
    //当子集权限被选中时，自动勾选顶级权限
   /* $(function () {
        //获取所有的子集权限
        var privilege_son = $()
        if ()
    })*/
</script>
@endsection
@section('script')
@endsection