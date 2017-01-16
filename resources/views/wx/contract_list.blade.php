<!doctype html>
<html lang="en">
<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.2/css/bootstrap.min.css">
<head>
    <meta charset="UTF-8" http-equiv="refresh" content="90">
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
        span{
            float: right;
            margin-right: 20px;
        }
    </style>
    <script src="{{asset('js/jquery-3.1.0.min.js')}}" type="text/javascript" charset="utf-8"></script>
</head>
<body>
    <div id="box">
        <table width="100%" border="1" bordercolor="black" cellspacing="0">
            <tr>
                <td colspan="24" align="center">
                    <span style="float: left;margin-left: 20px">订单信息列表</span>
                    <span><a href="/wx/conlist/rand-code">查看随机码</a></span>
                    <span><a href="/wx/conlist/lastweek-order-info">查看上周订单信息</a></span>
                    <span><a href="/wx/conlist/form">重新发送ca签署模板</a></span>
                    <span><a href="/wx/conlist/update-token">重新生成token(不要频繁点击)</a></span>
                </td>
            </tr>
            <tr>
                <td colspan="24" align="center">
                    <span>有效订单数：{{ $validCons }}</span>
                    <span>总贷款金额：{{ $sumMoney }} 元</span>
                    <span>今日订单数：{{ $tValidCons }} </span>
                    <span>今日总贷款金额：{{ $tSumMoney }} 元</span>
                    <span>今日取消单总数：{{ $tCancelCons }} </span>
                    <span>今日被否单总数：{{ $tRejectCons }} </span>
                </td>
            </tr>
            <tr>
                <th>序号</th>
                <th>订单日期</th>
                <th>开始录单时间</th>
                <th>提交订单时间</th>
                <th>门店</th>
                <th>服务类型</th>
                <th>身份证</th>
                <th>姓名</th>
                <th>合同号</th>
                <th>合同状态</th>
                <th>贷款金额</th>
                <th>期数</th>
                <th>取消原因</th>
                <th>手机号</th>
                <th>工作单位</th>
                <th>最高学历</th>
                <th>家属联系电话</th>
                <th>家属姓名</th>
                <th>产品类型</th>
                <th>每月还款日</th>
                <th>每月还款额</th>
                <th>销售代表</th>
                <th>销售经理</th>
                <th>城市经理</th>
            </tr>
            @foreach ($data as $k=>$v)
            <tr>
                <td>{{ $rev*($page-1)+$v->order_number }}</td>
                <td>{{ $v->order_time }}</td>
                <td>{{ $v->order_start_time }}</td>
                <td>{{ $v->order_commit_time }}</td>
                <td>{{ $v->SNAME }}</td>
                <td>{{ $v->service_type }}</td>
                <td>{{ $v->applicant_id_card }}</td>
                <td>{{ $v->applicant_name }}</td>
                <td>{{ $v->contract_no }}</td>
                <td>{{ $v->ITEMNAME }}</td>
                <td>{{ $v->loan_money }}</td>
                <td>{{ $v->periods }}</td>
                <td>{{ $v->reason }}</td>
                <td>{{ $v->mobile }}</td>
                <td>{{ $v->work_unit }}</td>
                <td>{{ $v->edu_level }}</td>
                <td>{{ $v->family_mobile }}</td>
                <td>{{ $v->family_name }}</td>
                <td>{{ $v->PNAME }}</td>
                <td>{{ $v->monthly_repay_date }}</td>
                <td>{{ $v->monthly_repay_money }}</td>
                <td>{{ $v->USERNAME }}</td>
                <td>{{ $v->SALESMANAGERNAME }}</td>
                <td>{{ $v->CITYMANAGERNAME }}</td>
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
            url:"/wx/conlist/page",
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