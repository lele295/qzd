@extends('_layouts.backend')

@section('content')

    <div class="header-title">
        <span>修改管理员信息</span>
    </div>
    <div class="tile-template" style="width: 25%;margin: 0 auto;">
        <form class="s_form" action="{{url('user/update')}}" method="post">
            {{csrf_field()}}
            <input type="hidden" name="userid" value="{{$id}}">
            <table>
                <tr>
                    <td class="col-title"><span>用户名:</span></td><td><input type="text" name="username" value="{{$data->username}}"></td>
                </tr>
                <tr>
                    <td class="col-title">管理员密码:</td><td><input type="password" name="password" value="{{$data->password}}"></td>
                </tr>
                <tr>
                    <td class="col-title">真实姓名:</td><td><input type="text" name="real_name" value="{{$data->real_name}}"></td>
                </tr>
                <tr>
                    <td class="col-title">邮箱:</td><td><input type="text" name="email" value="{{$data->email}}"></td>
                </tr>
                <tr>
                    <td class="col-title">电话:</td><td><input type="text" name="phone" value="{{$data->phone}}"></td>
                </tr>
                <tr>
                    <td class="col-title">职位:</td><td><input type="text" name="position" value="{{$data->position}}"></td>
                </tr>
                <tr>
                    <td class="col-title">省份:</td><td><input type="text" name="province" value="{{$data->province}}"></td>
                </tr>
                <tr>
                    <td class="col-title">城市:</td><td><input type="text" name="city" value="{{$data->city}}"></td>
                </tr>
                <tr>
                    <td class="col-title">角色:
                    </td><td><select name="id" style="width: 173px">
                            <option value="?">请选择角色</option>
                            @foreach($role_info as $v)
                                <option value="{{$v->id}}"
                                @if($role_id->role_id ==$v->id)
                                    selected="selected"
                                    @endif
                                >{{$v->rolename}}</option>
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
                    {{-- <span>{{session('msg')}}</span>--}}
                    <span><script>alert('{{session('msg')}}')</script></span>
                @endif
                    </td>
                </tr>
            </table>
        </form>
</div>
    <script src="{{url('js/jquery-1.9.1.min.js')}}"></script>
    <script>
        $(function () {
            $("[type='button']").click(function () {
                $("[type='text']").val('');
                $("[type='password']").val('');
            });
        })
    </script>
@endsection
@section('script')
@endsection
