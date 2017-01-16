@extends('_layouts.backend') @section('content')
<link rel="stylesheet" href="/backend/css/sumoselect.css">
<style>
.star {
	color: red;
}

a {
	cursor: pointer;
}

.sp_button {
	font-size: 14px;
	border: none;
	border-radius: 3px;
	line-height: 25px;
	margin: 0 0 0 20px;
}

.sp_button.confirm_btn {
	margin-top: 30px;
	background-color: #1d8bd8;
	color: #FFFFFF;
	width: 100px;
	height: 30px;
}

#add_cancel {
	width: 100px;
	height: 30px;
}

#reset_cancel {
	width: 100px;
	height: 30px;
}

.sp_button.cancel_btn {
	margin-top: 30px;
	width: 100px;
	height: 30px;
}

#status {
	display: none;
}

#check>div {
	margin-top: 30px;
}

#check table {
	width: 100%;
}

#check table td:first-child {
	width: 34%;
	text-align: right;
	padding: 0 10px 0 0;
}

#check table td:last-child {
	text-align: left;
}

.radio_label {
	margin: 5px 10px 5px 0;
	float: left;
}

.sub_company {
	margin: 0;
}

#returnVisit form {
	padding: 20px 0;
}

#returnVisit>table label, #returnVisit>table input {
	margin: 0;
}

#returnVisit>form {
	margin: 0 auto;
	width: 90%;
}

#returnVisit>form table tr td {
	padding: 6px 1%;
	width: 24%;
}

#returnVisit>form table tr td+td {
	width: 40%;
}

#returnVisit>form table tr td+td+td {
	width: 46%;
}

#returnVisit td>textarea {
	width: 89%;
	margin: 0;
}

#returnVisit .sp_button {
	margin: 10px 20px 0 0;
}
/*设置状态*/
#auditState>form {
	padding: 20px 0;
}

#auditState>form>table {
	margin: 0 auto;
	width: 90%;
}

#auditState>form>table tr td {
	padding: 6px 1%;
	width: 20%;
}

#auditState>form>table tr td+td {
	width: 80%;
}

#auditState>form td>select {
	border-radius: 0;
	margin: 0;
}

#auditState>form td>input {
	margin: 0;
}

#auditState>form td>textarea {
	width: 93.2%;
	margin: 0;
}

#auditState>form .sp_button {
	margin: 10px 20px 0 0;
}
/*查看详情*/
#viewDetail {
	padding: 20px 0;
}

#viewDetail>table {
	margin: 0 auto;
	width: 90%;
}

#viewDetail>table tr td {
	padding: 6px 1%;
	width: 30%;
}

#viewDetail>table tr td+td {
	width: 70%;
}

#viewDetail .sp_button {
	margin: 10px 20px 0 0;
}
</style>

<div class="header-title">
	<span>数据导出</span>
</div>

<div class="tile-template">
	<form class="form-horizontal" id="pushExcelForm" method="get" >
		<div class="row">
			{{--<div class="span4 control-group">--}}
				{{--<label class="control-label" for="inputEmail">区域：</label>--}}
				{{--<div class="controls">--}}
					{{--<select class="span2">--}}
						{{--<option value="1">123</option>--}}
						{{--<option value="2">234</option>--}}
						{{--<option value="3">345</option>--}}
					{{--</select>--}}
				{{--</div>--}}
			{{--</div>--}}
			{{--<div class="span4">--}}
				{{--<label class="control-label" for="inputEmail">城市：</label>--}}
				{{--<div class="controls">--}}
					{{--<select class="span2">--}}
						{{--<option value="1">123</option>--}}
						{{--<option value="2">234</option>--}}
						{{--<option value="3">345</option>--}}
					{{--</select>--}}
				{{--</div>--}}
			{{--</div>--}}
		</div>

		<div class="row">
			<div class="span4 control-group">
				<label class="control-label" for="inputEmail">销售经理：</label>
				<div class="controls">
					<input type="text" name="salesManager" />
				</div>
			</div>

			<div class="span4">
				<label class="control-label" for="inputEmail">销售代表：</label>
				<div class="controls">
					<input type="text" name="sales" />
				</div>
			</div>

		</div>


		<div class="row">
			<div class="span12 control-group">
				<label class="control-label" for="inputEmail">时间：</label>
				<div class="controls">
					<input id="d4312" class="Wdate input-middle" onFocus="WdatePicker({dateFmt:'yyyy-M-d'})" name="s_date" value="{{$condition['e_date'] or ''}}" type="text" />至
					<input id="d4312" class="Wdate input-middle" onFocus="WdatePicker({dateFmt:'yyyy-M-d'})" name="e_date" value="{{$condition['e_date'] or ''}}" type="text" />
				</div>
			</div>


		</div>

		<div class="row">
			<div class="span4">
				<label class="control-label" for="inputEmail">状态：</label>
				<div class="controls">
					<input type="hidden" id="contractStatus" name="contractStatus" />
					<input type="hidden" id="ordersStatus" name="ordersStatus" />
					<select class="testselect2 SumoUnder" id="chooseStatus" data-style="btn-info"  multiple title="请选择" data-selected-text-format="values">
						<option value="1">未提交</option>
						<option value="2">已提交</option>
						@foreach($contractStatusText AS $k=>$v)
							<option value="{{$k}}">
								{{$v}}
							</option>
						@endforeach
					</select>

				</div>
			</div>

			<div class="span6">
				<label class="control-label" for="inputEmail">区域总监：</label>
				<div class="controls">
					<select class="span2" name="city_manager">
						<option value="">全部</option>
						@foreach($cityManagerList AS $k=>$v)
							<option value="{{$v->USERID}}">
								{{$v->USERNAME}}
							</option>
						@endforeach
					</select>
				</div>
			</div>


		</div>

		<button type="button" class="btn btn-primary" id="pushExcel"> 导 出 </button>
	</form>

	<div class="modal fade" id="loadingModal" data-backdrop="static" style="top:50%;left:70%;background-color: transparent;width:50px;height:50px">
		<img src="/backend/images/loading.gif"/>
	</div>

@endsection @section('script')
		<script type="text/javascript" src="/backend/js/bootstrap/bootstrap.min.js"></script>
		<script type="text/javascript" src="/backend/js/jquery.sumoselect.js"></script>
<script type="text/javascript">
$('.testselect2').SumoSelect();
	//监控点击提交操作
	$("#pushExcel").click(function(){
		var contractStatus = '';
		var ordersStatus = '';
		$("#chooseStatus  option:selected").each(function(){
			if($(this).val() == 1 || $(this).val() == 2) {
				//订单状态选中
				ordersStatus += $(this).val()+ ",";
			}else{
				//合同状态选中
				contractStatus += $(this).val()+ ",";
			}
		});
		//职级的多选去掉最后一个逗号
		contractStatus = contractStatus.substring(0,contractStatus.length -1);
		ordersStatus = ordersStatus.substring(0,ordersStatus.length -1);
		$("#contractStatus").val(contractStatus);
		$("#ordersStatus").val(ordersStatus);
//		var sDate = $("input[name='s_date']").val;
//		var eDate = $("input[name='e_date']").val;
		$("#loadingModal").modal('show');
		jQuery.ajax({
			type: "POST",
			url: "/backend/order/download-excel",
			data: $('#pushExcelForm').serialize(),
			dataType: "json",
			success: function(msg){
				if(msg.success) {
					window.location.href = "download-file?path="+msg.url;
				}else {
					alert('导出失败,无满足筛选条件的信息');
				}
				$("#loadingModal").modal('hide');
			}
		});

	});



</script>
@endsection
