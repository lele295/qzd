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

#add_save {
	background-color: #1d8bd8;
	color: #FFFFFF;
	width: 100px;
	height: 30px;
}

#reset_save {
	background-color: #1d8bd8;
	color: #FFFFFF;
	width: 100px;
	height: 30px;
}

#delete_btn {
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

#cancel_btn {
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

#add_company_form>div {
	padding: 10px 20px;
}

#add_company_form>div>table {
	margin: 0 auto;
}

#add_company_form>div>table input, #add_company_form>div>table select {
	margin: 0;
}

#add_company_form>div>table td {
	padding: 2px 8px;
	vertical-align: middle;
}

#add_company_form>div>table tr:last-child td {
	padding: 8px;
	vertical-align: middle;
	text-align: right;
}

#add_company_form>div>table td:first-child {
	text-align: right;
	width: 180px;
}

#add_company_form>div>table td:last-child {
	text-align: left;
	padding-left: 30px;
}

#add_company_form .formErrors {
	color: #ff3c00;
	font-size: 12px;
}

.radio_label {
	margin: 5px 10px 5px 0;
	float: left;
}

.sub_company {
	margin: 0;
}

.tile-template .table td {
	text-align: right;
	border: 0;
}
</style>
<div class="header-title">
	<span>申请信息</span>
</div>
<div class="tile-template">

	<table style="margin-bottom: 0;" frame=void rules=none class="table">
		<tbody>
			<tr>
				<td width="22%">商户代码：</td>
				<td width="23%" class="txt_left">{{$item->RNO or ''}}</td>
				<td width="22%">商户名称：</td>
				<td width="38%" class="txt_left">{{$item->RNAME or ''}}</td>
			</tr>
			<tr>
				<td>商户类型：</td>
				<td class="txt_left">{{$item->RTYPE?$retail_type[$item->RTYPE]->ITEMNAME:''}}</td>
			</tr>
			<tr>
				<td>商户所在城市：</td>
				<td class="txt_left">{{$item->CITY?$map_city[$item->CITY]:''}}</td>
				<td></td>
				<td class="txt_left"></td>
			</tr>
			<tr>
				<td>法定代表人：</td>
				<td class="txt_left">{{$item->LAWPERSON or ''}}</td>
				<td>法人身份证号码：</td>
				<td class="txt_left">{{$item->LAWPERSONCARDNO or ''}}</td>
			</tr>
			<tr>
				<td>主要联系人：</td>
				<td class="txt_left">{{$item->LINKNAME or ''}}</td>
				<td>主要联系人号码：</td>
				<td class="txt_left">{{$item->LINKTEL or ''}}</td>
			</tr>
			<tr>
				<td>主要联系人邮箱：</td>
				<td class="txt_left">{{$item->LINKEMAIL or ''}}</td>
				<td>财务人员姓名：</td>
				<td class="txt_left">{{$item->FINANCIALNAME or ''}}</td>
			</tr>
			<tr>
				<td>财务人员号码：</td>
				<td class="txt_left">{{$item->FINANCIALTEL or ''}}</td>
				<td>财务人员邮箱：</td>
				<td class="txt_left">{{$item->FINANCIALEMAIL or ''}}</td>
			</tr>
			<tr>
				<td>结算账号开户行所在省市：</td>
				<td class="txt_left">{{$item->ACCOUNTBANKCITY?$map_city[$item->ACCOUNTBANKCITY]:''}}</td>
				<td>账号开户行：</td>
				<td class="txt_left">{{$item->ACCOUNTBANK?App\Model\Base\SyncCodeLibrary::bankNameInfo($item->ACCOUNTBANK)->ITEMNAME:''}}</td>
			</tr>
			<tr>
				<td>账号开户名：</td>
				<td class="txt_left">{{$item->ACCOUNTNAME or ''}}</td>
				<td>账号开户行支行：</td>
				<td class="txt_left">{{$item->BRANCHCODE?App\Model\Base\SyncBankputInfo::getBankName($item->BRANCHCODE)->BANKNAME:''}}</td>
			</tr>
			<tr>
				<td>账号开户账号：</td>
				<td class="txt_left">{{$item->ACCOUNT or ''}}</td>
				<td>商户地址：</td>
				<td class="txt_left">{{$item->ADDRESS or ''}}</td>
			</tr>
			<tr>
				<td>分店数量：</td>
				<td class="txt_left">{{$item->STORENUM or ''}}</td>
				<td></td>
				<td class="txt_left"></td>
			</tr>

		</tbody>
	</table>
</div>
<div class="tile-template">
	<div class="s_expose">
		<a id="import"  href='javascript:history.go(-1);' onclick="">返回上一级</a>
	</div>
</div>

@endsection
