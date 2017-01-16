@extends('_layouts.backend')

@section('content')

    <div class="header-title">
        <span>查看管理员</span>
    </div>
<div>
    <form action="#" method="post">
        <table style="margin: auto;text-align: center;">
            <tr>
                <td style="width: 12%">用户名：<input type="text" name="username" placeholder="安硕用户请填USERID" style="width: 60%"></td>&nbsp;
                <td style="width: 12%">城市：<input type="text" name="city" style="width: 60%"></td>&nbsp;
                <td style="width: 12%">手机号：<input type="text" name="phone" style="width: 60%"></td>
                <td style="width: 12%">姓名：<input type="text" name="real_name" style="width: 60%"></td>&nbsp;
                <td style="width: 12%">职位：<input type="text" name="position" style="width: 60%"></td>&nbsp;
            </tr>
            <tr style="text-align: center">
                <td colspan="2" style="text-align: right"><input type="radio" name="type" value="0">安硕管理员</td>&nbsp;&nbsp;
                <td colspan="2" style="text-align: left;"><input type="radio" name="type" value="1" checked="checked" style=" margin-left: 20px;">本地管理员</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="2" style="text-align: center">
                    <input type="button" value="查询" id="check" class="s_btn">
                    <input type="button" value="清除" id="clear" class="s_btn">
                </td>
            </tr>
        </table>
    </form>
</div>
<hr style="border: #0c0c0c silver">
<div style="align-self: center;text-align: center;">

    <form method="post" action="{{url('user/list')}}">
        <table border="1" style="align-content: center;border: 1px gainsboro solid;align-self: center;margin: auto;border-collapse: collapse;width: 600px" class="second">
            <thead>
            <tr>
                <th>id</th>
                <th>用户名</th>
                <th>职位</th>
                <th>角色</th>
                <th>城市</th>
                <th>手机</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </form>
</div>
<div style="height: 20px"></div>
{{--<div style="text-align: center;clear: both;margin: auto">{!! $data->render() !!}</div>--}}



<script src="{{url('js/jquery-1.9.1.min.js')}}"></script>
<script>
    $(function () {
        $('#check').click(function () {
            //获取input框里面的数据
            //alert($(":checked").val());
            $.ajax({
                type:'post',
                url:"{{url('user/list')}}",
                data:{
                    _token:"{{csrf_token()}}",
                    real_name:$("[name='real_name']").val(),
                    city:$("[name='city']").val(),
                    username:$("[name='username']").val(),
                    phone:$("[name='phone']").val(),
                    position:$("[name='position']").val(),
                    type:$(":checked").val()
                },
                success:function (msg) {
                    //组装成数据，放入表格中
                    var data = '';
                    for(var i=0;i<msg.length;i++){
                        data +='<tr>';
                        $.each(msg[i],function (name,value) {
                            //alert(msg[i]['id']);
                            data +='<td>'+value+'</td>';
                        });
                        //alert(typeof(msg[i]['USERID']));
                        if(msg[i]['USERID']){
                            data +=  '<td><a href="#"></a>'
                        }else{
                            data += '<td><a href="#"></a><a href={{url('user/info/edit?id=')}}'+msg[i]['id'];
                            data += '>编辑</a></td></tr>';
                        }
                    }
                    data += '';
                    //追加之前先将div里面的数据清理一下
                    //选中第div,将数据追加
                    $(".second tbody").html('');
                    $('.second').append(data);
                }
            });
        })
    })
</script>
<script>
    $(function () {
        $("#clear").click(function () {
            $("[type='text']").val('');
        });
    })
</script>
    <script>
        //安硕用户不能根据姓名查询，因此将表单的属性disabled改为disabled
        $(function () {
            $("input[value='1']").click(function () {
                $("[name='real_name']").removeAttr('disabled');
            })
            $("input[value='0']").click(function () {
                $("[name='real_name']").attr('disabled','disabled');
            })
        });
    </script>
@endsection
@section('script')
@endsection