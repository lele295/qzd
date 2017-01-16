@extends('_layouts.backend') @section('content')
<div class="header-title">
	<span>风控数据统计</span>
</div>
<div class="tile-template">
    <form class="s_form">
    <table>
			<tr>
				<td><input class="s_btn" type="submit" value="导出" /></td>
			</tr>
		</table>
	</form>
	<a href='http://www.qzd.com/backend/statistics/risk-control-data-api'>风控数据接口</a>
</div>
@endsection
