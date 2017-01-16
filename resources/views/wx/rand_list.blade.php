<!doctype html>
<html lang="en">
<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.2/css/bootstrap.min.css">
<head>
    <meta charset="UTF-8">
    <title>仟姿贷</title>
    <style>
        .page {
            text-align: center;
            margin-top: 20px;
            width: auto;
        }
        .page a {
            padding: 0 5px;
            text-align: center;
            border:1px solid #ccc;
            text-decoration: none;
        }
        tr td,tr th{
            text-align: center;
        }
    </style>
    <script src="{{asset('js/jquery-3.1.0.min.js')}}" type="text/javascript" charset="utf-8"></script>
</head>
<body>
    <div id="box">
        <table width="100%" border="1" bordercolor="black" cellspacing="0">
            <tr>
                <td colspan="19" align="center" id="test">随机码查询<span style="float: right"><a style="margin-right: 10px;text-decoration: none" href="/wx/conlist/page">返回订单列表</a></span></td>
            </tr>
            <tr>
                <th>序号</th>
                <th>订单id</th>
                <th>手机号</th>
                <th>随机码</th>
            </tr>
            @foreach ($data as $k=>$v)
            <tr>
                <td>{{ $rev*($page-1)+$k+1 }}</td>
                <td>{{ $v->id }}</td>
                <td>{{ $v->mobile }}</td>
                <td>{{ $v->rand_code }}</td>
            </tr>
            @endforeach

        </table>
        <div class="page">
            <a href="javascript:void(0)" onclick="page(1)">首页</a>
            <a href="javascript:void(0)" onclick="page(<?php echo $prev ?>)">上一页</a>
            @foreach($pp as $key=>$val)
                @if($val == $page)
                    {{$val}}
                @else
                    <a href="javascript:void(0)" onclick="page({{$val}})">{{$val}}</a>
                @endif
            @endforeach
            <a href="javascript:void(0)" onclick="page(<?php echo $next ?>)">下一页</a>
            <a href="javascript:void(0)" onclick="page(<?php echo $sums ?>)">尾页</a><br />
        </div>


    </div>
</body>
</html>
<script type="text/javascript">
    //分页
    function page(page){
        $.ajax({
            type:"get",
            url:"/wx/conlist/rand-code",
            data:{
                page:page,
                type:'page'
            },
            success:function(msg){
                console.log(msg);
                if(msg){
                    $("#box").html(msg)
                }
            }
        })
    }
</script>