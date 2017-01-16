@extends('_layouts.backend')

@section('content')

    <div class="header-title">
        <span>添加权限</span>
    </div>
    <div class="tile-template" style="width: 25%;margin: 0 auto;">
        <form class="s_form" action="{{url('privilege')}}" method="post">
            {{csrf_field()}}
            <table>
                <tr>
                    <td class="col-title">权限名称:</td><td><input type="text" name="name"></td>
                </tr>
                <tr>
                    <td class="col-title">父级权限:</td><td><select name="pid">
                            <option value="0">顶级权限</option>
                            @foreach($data as $v)
                                <option value="{{$v['id']}}">{{str_repeat('----',$v['level'])}}{{$v['name']}}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="col-title">规则(uri):</td><td><input type="text" name="base_uri"></td>
                </tr>
                <tr>
                    <td class="col-title">权限描述:</td><td><input type="text" name="description"></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="1" class="td">
                        <input type="submit" value="提交" class="s_btn">
                        <input type="button" value="清除" class="s_btn">
                    </td>
                </tr>
                <tr>
                    @if (count($errors) > 0)
                        @foreach ($errors->all() as $error)
                            <span style="color: red">{{ $error }} | </span>
                        @endforeach
                    @endif
                    @if(session('msg'))
                        {{-- <span>{{session('msg')}}</span>--}}
                        <span><script>alert('{{session('msg')}}')</script></span>
                    @endif
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
