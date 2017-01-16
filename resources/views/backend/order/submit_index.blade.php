@extends('_layouts.backend') @section('content')
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
	<span>已提交订单</span>
</div>
<div class="tile-template">
	<form class="s_form">
		<table>
			<tr>
				<td class="col-title"><span>客户姓名：</span></td>
				<td>
					<input class="input-medium" name="applicant_name" value="{{$precondition['applicant_name'] or ''}}" type="text" />
				</td>

				<td class="col-title "><span>合同号码：</span></td>
				<td>
					<input name="contract_no" value="{{$precondition['contract_no'] or ''}}" type="text" />
				</td>

				<td class="col-title"><span>客户身份证号码：</span></td>
				<td>
					<input name="applicant_id_card" value="{{$precondition['applicant_id_card'] or ''}}" type="text" />
				</td>


			</tr>
			<tr>

			</tr>

			<tr>
				{{--<td class="col-title"><span>城市：</span></td>--}}
				{{--<td>--}}
					{{--<input name="city" value="{{$precondition['city'] or ''}}" type="text" />--}}
				{{--</td>--}}

				<td class="col-title"><span>城市：</span></td>
				<td><input id="city" type="hidden" name="city" value="" /></td>

				<td class="col-title"><span>提交时间：</span></td>
				<td>
					<input id="d4311" name="order_create_time" class="Wdate " style="width:80%;"
						   onFocus="WdatePicker({dateFmt:'yyyy-M-d'})"
						   value="{{$precondition['order_create_time'] or ''}}" type="text" />
				</td>

				<td class="col-title"><span>状态：</span></td>
				<td>
					<select name="contract_status">
						<option value="">全部</option>
						@foreach($contractStatusText AS $k=>$v)
							<option @if(isset($precondition['contract_status']) && $precondition['contract_status'] === "$k") selected="selected" @endif value="{{$k}}">
								{{$v}}
							</option>
						@endforeach
					</select>
				</td>

				<td><input class="s_btn" type="submit" name="submit" value="查询" /></td>
			</tr>
		</table>
		{{--<div class="s_expose"><a href="/backend/oldplan/export" id="export">导出全部</a></div>--}}
	</form>
	<div style="margin-bottom: 0;" class="box bordered-box blue-border">
		<div class="box-content box-no-padding">
			<div class="">
				<div class="scrollable-area">
					<table style="margin-bottom: 0;"
						class="data-table table table-hover table-bordered table-striped dataTable">
						<thead>
							<th>序号</th>
							<th>合同号</th>
							<th>合同状态</th>
							<th>客户姓名</th>
							<th>随机码</th>
							<th>贷款金额</th>
							<th>期数</th>
							<th>门店名称</th>
							<th>取消原因</th>
							<th>销售代表</th>
							<th>销售经理</th>
							<th>区域总监</th>
							<th>操作</th>
						</thead>
						<tbody>
							@if(count($orderList)) @foreach($orderList as $k=>$item)
							<tr>
								<td>{{ $k+1 }}</td>
								<td>{{$item->contract_no}}</td>
								<td>{{ $contractStatusText[$item->status] }}</td>
								<td>{{$item->applicant_name}}</td>
								<td>{{$item->rand_code}}</td>
								<td>{{$item->loan_money}}</td>
								<td>{{$item->periods}}</td>
								<td>{{$item->SNAME}}</td>
								<td>{{$item->order_remark}}</td>
								<td>{{$item->sales}}</td>
								<td>{{$item->sale_manage}}</td>
								<td>{{$item->city_manage}}</td>
								{{--@if(isset($returnVisits[$item->merchant_no]))--}}
								{{--<td>{{$userType[$returnVisits[$item->merchant_no]['visit_status']]--}}
									{{--or '未回访'}}</td> @else--}}
								{{--<td>未回访</td> @endif--}}
								<td>
										<a href="/backend/order/order-detail-info?order_id={{ $item->id }}" class="return-visit">查看详情</a>
										<a href="/backend/order/order-image?order_id={{ $item->id }}" class="return-visit">查询影像</a>
										<a href="/backend/order/order-protocol?order_id={{ $item->id }}" class="return-visit">查看协议</a>
								</td>
							</tr>
							@endforeach @else
							<tr>
								<td colspan="12"><img src="/backend/images/no-data.png" alt=""
									style="margin: 20px 0 10px;">
									<p>暂无记录</p></td>
							</tr>
							@endif
						</tbody>
					</table>
					{!! $pages !!}
				</div>
			</div>
		</div>
	</div>
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

</script>
@endsection
