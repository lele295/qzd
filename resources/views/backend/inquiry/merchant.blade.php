@extends('_layouts.backend') @section('content')
<style>
/*查看详情*/
#productDetail {
	padding: 20px 0;
}

#productDetail>table {
	margin: 0 auto;
	width: 90%;
}

#productDetail>table tr td {
	padding: 6px 1%;
	width: 30%;
}

#productDetail>table tr td+td {
	width: 70%;
}

#productDetail .sp_button {
	margin: 10px 20px 0 0;
}
</style>
<div class="header-title">
	<span>商户查询</span>
</div>
<div class="tile-template">
	<form class="s_form">
		<table>
			<tr>
				<td class="col-title"><span>商户名称：</span></td>
				<td><input name="search[merchantName]"
					value="{{$cons['merchantName'] or ''}}" type="text" /></td>
				<td class="col-title"><span>商户代码：</span></td>
				<td><input name="search[merchantCode]"
					value="{{$cons['merchantCode'] or ''}}" type="text" /></td>
				<td class="col-title"><span>门店名称：</span></td>
				<td><input name="search[storeName]"
					value="{{$cons['storeName'] or ''}}" type="text" /></td>
				<td class="col-title"><span>门店代码：</span></td>
				<td><input name="search[storeCode]"
					value="{{$cons['storeCode'] or ''}}" type="text" /></td>
			</tr>
			<tr>
				<td class="col-title"><span>销售经理：</span></td>
				<td><input name="search[salesManager]"
					value="{{$cons['salesManager'] or ''}}" type="text" /></td>
				<td class="col-title"><span>区域总监：</span></td>
				<td><input name="search[cityManager]"
					value="{{$cons['cityManager'] or ''}}" type="text" /></td>
				<td class="col-title"><span>门店状态：</span></td>
				<td><select name="search[storeStatus]">
						<option value="">全部</option> @foreach($status_list AS $k=>$v)
						<option @if($cons['storeStatus'] == $k) selected="" @endif
							value="{{$k}}">{{$v->ITEMNAME}}</option> @endforeach
				</select></td>
				<td class="col-title"><span>城市：</span></td>
				<td><input id="city" type="hidden" name="storeCity" value="" /></td>
			</tr>
			<tr>
				<td><input class="s_btn" type="submit" value="查询" /></td>
			</tr>
		</table>
	</form>
	<div style="margin-bottom: 0;" class="box bordered-box blue-border">
		<div class="box-content box-no-padding">
			<div class="">
				<div class="scrollable-area">
					<table style="margin-bottom: 0;"
						class="data-table table table-hover table-bordered table-striped dataTable">
						<thead>
							<th>序号</th>
							<th>商户名称</th>
							<th>商户代码</th>
							<th>门店名称</th>
							<th>门店代码</th>
							<th>门店状态</th>
							<th>销售代表</th>
							<th>销售经理</th>
							<th>区域总监</th>
							<th>绑定产品</th>
							<th>操作</th>
						</thead>
						<tbody>
                                @if($list->count())
                                <?php $no=1?>
                                @foreach($list as $val)
                                    <tr>
								<td>{{$no}}</td>
								<td>{{$val->RNAME or ''}}</td>
								<td>{{$val->RNO or ''}}</td>
								<td>{{$val->SNAME or ''}}</td>
								<td>{{$val->SNO or ''}}</td>
								<td>{{$val->STATUS==''?'':$status_list[$val->STATUS]->ITEMNAME}}</td>
								<td>{{$val->SALESMAN or ''}}</td>
								<td>{{$val->SALESMANAGER==''?'':$user_name_list[$val->SALESMANAGER]['USERNAME']}}</td>
								<td>{{$val->CITYMANAGER==''?'':$user_name_list[$val->CITYMANAGER]['USERNAME']}}</td>
								<td><a href="javascript:;" class="product-detail"
									data_sid="{{$val->SID}}"><font color="#1d8bd8">查看</font></a></td>
								<td>
								@if($val->STATUS==05)
								<a href="javascript:;" class="create-qrcode" data_sno="{{$val->SNO}}"><font color="#1d8bd8">生成二维码</font></a>@endif
										<a
									href='/backend/inquiry-management/merchant-detail?rid={{$val->RID}}'><font
										color="#1d8bd8">查看详情</font></a></td>
							</tr>
                                    <?php $no++?>
                                @endforeach
                                @else
                                <tr>
								<td colspan="10"><img src="/backend/images/no-data.png" alt=""
									style="margin: 20px 0 10px;">
									<p>暂无记录</p></td>
							</tr>
							@endif
						</tbody>
					</table>
					<div style="margin: 0 auto; text-align: center">{!! $pages !!}</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="productDetail" style="display: none;">
	<table id="detailInfo">
		<tr>
			<td>门店代码：<span id=store_code></span></td><tr>
		<tr>
			<td>门店名称：<span id=store_name></span></td><tr>
		<tr>
			<td>绑定产品：<span id=product></span></td><tr>
	</table>
</div>

<div id="qrCode" style="display:none;padding:20px 50px;">
        <div id="qrCodeImg"></div>
    </div>
@endsection @section('script')

<script type="text/javascript" src="/js/city.js?v=20160720"></script>
<script type="text/javascript">

        /**
         * 省市联动下拉列表
         */
        new EbuyfunCity({
            selectorStr: 'input[id="city"]',
            suffix: 'residential',
            callback: function (val, name) {
                $('input[id="city').val(val);
            }
        });
      </script>
<script type="text/javascript">
        $(function() {
            /*查看绑定产品*/
            var productDetailLayer;
            $('.product-detail').on('click',function(){
            	var sid = $(this).attr('data_sid');
            	$('#store_code').text('');
                $('#store_name').text('');
                $('#product').text('');
          	  $.get("/backend/inquiry-management/product?sid="+sid, function(data){
            		if(data){
                        $('#store_code').text(data.no);
                        $('#store_name').text(data.name);
                        $('#product').text(data.product);
                    }
            		else{
                		alert('读取信息失败');
            		}
      		  });
            	productDetailLayer = layer.open({
                    type: 1,
                    title:  ['绑定产品', 'font-size:18px;font-weight：bold;'],
                    closeBtn: 1,
                    offset: '20%',
                    fix: true,
                    area: ['450px', '250px'],
                    shadeClose: true,
                    skin: '',
                    content: $("#productDetail")
                });
            });

            /*二维码页*/
            var QrCodeLayer;
            $('.create-qrcode').on('click',function(){
            	var sno = $(this).attr('data_sno');
            	$("#qrCodeImg").html('<center>二维码生成中...</center>');
          	  $.get("/admin/qrcode/create?sno="+sno, function(data){
            		if(data){
                		if(data.success==false)
                		{
                    		alert(data.message);
                    		return;
                		}
                		else{
                    		html='<img src="'+data.message+'"/>';
                			$("#qrCodeImg").html(html);
                		}
                    }
            		else{
                		alert('读取信息失败');
//                 		break;
            		}
                    });
        		QrCodeLayer = layer.open({
                    type: 1,
                    title:  ['门店二维码', 'font-size:18px;font-weight：bold;'],
                    closeBtn: 1,
                    offset: '20%',
                    fix: true,
                    area: ['350px', '350px'],
                    shadeClose: true,
                    skin: '',
                    content: $("#qrCode")
      		  });

            });
        });
    </script>
@endsection
