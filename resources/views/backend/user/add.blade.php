@extends('_layouts.backend')

@section('content')

    <div class="header-title">
        <span>添加管理员</span>
    </div>
    <div class="tile-template" style="width: 25%;margin: 0 auto;">
        <form class="s_form" action="{{url('user')}}" method="post">
            <table>
                <tr>
                    <td class="col-title"><span>用户名:</span></td><td><input type="text" name="username"></td>
                </tr>
                <tr>
                    <td class="col-title">管理员密码:</td><td><input type="password" name="password"></td>
                </tr>
                <tr>
                    <td class="col-title">真实姓名:</td><td><input type="text" name="real_name"></td>
                </tr>
                <tr>
                    <td class="col-title">邮箱:</td><td><input type="text" name="email"></td>
                </tr>
                <tr>
                    <td class="col-title">电话:</td><td><input type="text" name="phone"></td>
                </tr>
                <tr>
                    <td class="col-title">职位:</td><td><input type="text" name="position"></td>
                </tr>
                <tr>
                    <td class="col-title">省份:</td><td><input type="text" name="province"></td>
                </tr>
                <tr>
                    <td class="col-title">城市:</td><td><input type="text" name="city"></td>
                </tr>
                <tr>
                    <td class="col-title">角色:
                    </td><td><select name="id" style="width: 173px">
                            <option>请选择角色</option>
                            @foreach($data as $key => $v)
                                <option value="{{$key}}">{{$v}}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="1" class="td">
                        <input type="submit" value="提交" class="s_btn">
                        <input type="button" value="清除" class="s_btn">
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                @if (count($errors) > 0)
                    @foreach ($errors->all() as $error)
                        <span style="color: red">{{ $error }} | </span>
                    @endforeach
                @endif
                @if(session('msg'))
                   <span><script>alert('{{session('msg')}}')</script></span>
                   {{--<span><script>
                           layer.open({
                               type: 1,
                               skin: 'layui-layer-demo', //样式类名
                               closeBtn: 0, //不显示关闭按钮
                               anim: 2,
                               shadeClose: true, //开启遮罩关闭
                               content: '{{session('msg')}}'
                           });
                       </script></span>--}}
                @endif
                    </td>
                </tr>
            </table>
        </form>
</div>
    <script src="{{url('js/jquery-1.9.1.min.js')}}"></script>
    <script src="{{url('js/layer/layer.js')}}"></script>
    <script src="{{url('js/layer/mobile/layer.js')}}"></script>
    <script>
        $(function () {
            $("[type='button']").click(function () {
                $("[type='text']").val('');
                //layer.msg('aa');
            });
        })
    </script>
@endsection
@section('script')
@endsection
