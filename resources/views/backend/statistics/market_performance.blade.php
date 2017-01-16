@extends('_layouts.backend') @section('content')
     <style>
section {
	display: none;
	padding: 20px 0 0;
	border-top: 1px solid #ddd;
}

.tab{
	display: none;
}

label {
	display: inline-block;
	margin: 0 0 -1px;
	padding: 15px 25px;
	text-align: center;
	color: #bbb;
	border: 1px solid transparent;
}

label:before {
	font-family: fontawesome;
	font-weight: normal;
	margin-right: 10px;
}

label:hover {
	color: #888;
	cursor: pointer;
}

input:checked+label {
	color: #555;
	border: 1px solid #ddd;
	border-top: 2px solid #1d8bd8;
	border-bottom: 1px solid #fff;
}

#tab1:checked ~ #content1,
#tab2:checked ~ #content2,
#tab3:checked ~ #content3,
#tab4:checked ~ #content4,
#tab5:checked ~ #content5,
#tab6:checked ~ #content6{
  display: block;
}

@media screen and (max-width: 650px) {
	label {
		font-size: 0;
	}
	label:before {
		margin: 0;
		font-size: 18px;
	}
}

@media screen and (max-width: 400px) {
	label {
		padding: 15px;
	}
}
</style>

<div class="header-title">
	<span>销售业绩统计</span>
</div>
<div class="tile-template">
    <form class="s_form" action="/backend/statistics/market-performance-export">
    <table>
			<tr>
			<td class="col-title"><span>申请时间：</span></td>
				<td><input id="d4311" name="s_date" class="Wdate"
					onFocus="WdatePicker({dateFmt:'yyyy-M-d'})"
					value="{{$condition['s_date'] or ''}}" type="text" /></td>
				<td>-</td>
				<td><input id="d4312" class="Wdate"
					onFocus="WdatePicker({dateFmt:'yyyy-M-d'})" name="e_date"
					value="{{$condition['e_date'] or ''}}" type="text" /></td>
				<td><input class="s_btn" type="submit" value="导出" /></td>
			</tr>
		</table>
	</form>
	<a href='/backend/statistics/market-performance-api'>业绩统计接口</a>
	<a href='/backend/statistics/city-rank-api'>城市排名接口</a>
	<a href='/backend/statistics/area-rank-api'>区域排名接口</a>
</div>

<div class="tile-template">
<input id="tab1" type="radio" name="tabs"  class="tab" checked> <label for="tab1" >区域排名</label>
<input id="tab2" type="radio" name="tabs"  class="tab"> <label for="tab2">城市排名</label>
<section id="content1" >
<form class="s_form" action="/backend/statistics/area-rank-export">
    <table>
			<tr>
			<td class="col-title"><span>申请时间：</span></td>
				<td><input id="d4311" name="s_date" class="Wdate"
					onFocus="WdatePicker({dateFmt:'yyyy-M-d'})"
					value="{{$condition['s_date'] or ''}}" type="text" /></td>
				<td>-</td>
				<td><input id="d4312" class="Wdate"
					onFocus="WdatePicker({dateFmt:'yyyy-M-d'})" name="e_date"
					value="{{$condition['e_date'] or ''}}" type="text" /></td>
				<td><input class="s_btn" type="submit" value="导出" /></td>
			</tr>
		</table>
	</form>
</section>
<section id="content2">
<form class="s_form" action="/backend/statistics/city-rank-export">
    <table>
			<tr>
			<td class="col-title"><span>申请时间：</span></td>
				<td><input id="d4311" name="s_date" class="Wdate"
					onFocus="WdatePicker({dateFmt:'yyyy-M-d'})"
					value="{{$condition['s_date'] or ''}}" type="text" /></td>
				<td>-</td>
				<td><input id="d4312" class="Wdate"
					onFocus="WdatePicker({dateFmt:'yyyy-M-d'})" name="e_date"
					value="{{$condition['e_date'] or ''}}" type="text" /></td>
				<td><input class="s_btn" type="submit" value="导出" /></td>
			</tr>
		</table>
	</form>
</section>

</div>
@endsection
