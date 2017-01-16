
    <div id="box">
        <table width="100%" border="1" bordercolor="black" cellspacing="0">
            <tr>
                <td colspan="19" align="center" id="test">随机码查询<span style="float: right"><a style="margin-right: 10px;text-decoration: none" href="/wx/conlist/page">返回订单列表</a><br /></span><span style="float: right"><a style="margin-right: 10px;text-decoration: none" href="/wx/conlist/page">返回订单列表</a></span></td>
            </tr>
            <tr>
                <th>序号</th>
                <th>订单id</th>
                <th>手机号</th>
                <th>随机码</th>
            </tr>
            @foreach ($data as $k=>$v)
                <tr style="text-align: center">
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
