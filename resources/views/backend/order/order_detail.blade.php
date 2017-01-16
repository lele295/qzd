@extends('_layouts.backend') @section('content')


<div class="header-title">
	<span>详情</span>
</div>

<div style="margin: 0 50px 0 50px;">
	<!-- 客户基本信息-->
	<div class="row-fluid">

		<div class="span12">
			客户基本信息
			<hr>
			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span3">姓名：</div>
						<div class="span6">{{ $info['applicant_name'] }}</div>
					</div>
				</div>

				<div class="span6">
					<div class="row-fluid">
						<div class="span3">手机号码：</div>
						<div class="span6">{{ $info['mobile'] }}</div>
					</div>
				</div>
			</div>

			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span3">身份证号码：</div>
						<div class="span6">{{ $info['applicant_id_card'] }}</div>
					</div>
				</div>

				<div class="span6">
					<div class="row-fluid">
						<div class="span3">行业：</div>
						<div class="span6">{{ $info['industry_name'] }}</div>
					</div>
				</div>
			</div>

		</div>

	</div>
	<hr>
	<!-- 产品信息-->
	<div class="row-fluid">

		<div class="span12">
			产品信息
			<hr>
			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span3">产品代码：</div>
						<div class="span6">{{ $info['PRODUCTCATEGORYID'] }}</div>
					</div>
				</div>

				<div class="span6">
					<div class="row-fluid">
						<div class="span3">产品名称：</div>
						<div class="span6">{{ $info['PRODUCTCATEGORYNAME'] }}</div>
					</div>
				</div>
			</div>

			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span3">服务类型：</div>
						<div class="span6">{{ $info['service_type'] }}</div>
					</div>
				</div>

				<div class="span6">
					<div class="row-fluid">
						<div class="span3">还款方式：</div>
						<div class="span6">{{ $info['pay_type'] == 1 ? '等本等息' : '一次性付息' }}</div>
					</div>
				</div>
			</div>

			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span3">贷款金额：</div>
						<div class="span6">￥{{ $info['loan_money'] }}</div>
					</div>
				</div>

				<div class="span6">
					<div class="row-fluid">
						<div class="span3">每月还款日：</div>
						<div class="span6">{{ $info['monthly_repay_date'] }}号</div>
					</div>
				</div>
			</div>

			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span3">期数：</div>
						<div class="span6">{{ $info['periods'] }}</div>
					</div>
				</div>

				<div class="span6">
					<div class="row-fluid">
						<div class="span3">手续费：</div>
						<div class="span6">{{ $info['service_cost'] }}</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<hr>
	<!-- 工作信息-->
	<div class="row-fluid">

		<div class="span12">
			工作信息
			<hr>
			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span3">工作单位：</div>
						<div class="span6">{{ $info['work_unit'] }}</div>
					</div>
				</div>

				<div class="span6">
					<div class="row-fluid">
						<div class="span3">单位电话：</div>
						<div class="span6">{{ $info['work_unit_mobile'] }}</div>
					</div>
				</div>
			</div>

			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span3">最高学历：</div>
						<div class="span6">{{ $info['edu_level'] }}</div>
					</div>
				</div>

				<div class="span6">
					<div class="row-fluid">
						<div class="span3">电子邮箱：</div>
						<div class="span6">{{ $info['qq_email'] }}</div>
					</div>
				</div>
			</div>

			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span3">直系亲属姓名：</div>
						<div class="span6">{{ $info['family_name'] }}</div>
					</div>
				</div>

				<div class="span6">
					<div class="row-fluid">
						<div class="span3">直系亲属联系方式：</div>
						<div class="span6">{{ $info['family_mobile'] }}</div>
					</div>
				</div>
			</div>

		</div>
	</div>
	<hr>
	<!-- 还款账户信息信息-->
	<div class="row-fluid">

		<div class="span12">
			还款账户信息
			<hr>
			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span3">还款银行卡账号：</div>
						<div class="span6">{{ $info['work_repayment_account'] }}</div>
					</div>
				</div>

				<div class="span6">
					<div class="row-fluid">
						<div class="span3">还款银行卡开户行：</div>
						<div class="span6">{{ $info['work_deposit_bank'] }}</div>
					</div>
				</div>
			</div>

			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span3">还款银行卡城市：</div>
						<div class="span6">{{ $info['work_bank_city'] }}</div>
					</div>
				</div>

				<div class="span6">
					<div class="row-fluid">
						<div class="span3">还款银行卡支行：</div>
						<div class="span6">{{ $info['work_bank_branch_name'] }}</div>
					</div>
				</div>
			</div>

		</div>
	</div>
	<hr>
	<!-- 销售信息-->
	<div class="row-fluid">

		<div class="span12">
			销售信息
			<hr>
			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span3">商户代码：</div>
						<div class="span6">{{ $info['RNO'] }}</div>
					</div>
				</div>

				<div class="span6">
					<div class="row-fluid">
						<div class="span3">商户名称：</div>
						<div class="span6">{{ $info['RNAME'] }}</div>
					</div>
				</div>
			</div>

			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span3">门店代码：</div>
						<div class="span6">{{ $info['merchant_code'] }}</div>
					</div>
				</div>

				<div class="span6">
					<div class="row-fluid">
						<div class="span3">门店名称：</div>
						<div class="span6">{{ $info['SNAME'] }}</div>
					</div>
				</div>
			</div>

			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span3">销售代表：</div>
						<div class="span6">{{ $info['sales'] }}</div>
					</div>
				</div>

				<div class="span6">
					<div class="row-fluid">
						<div class="span3">销售代表ID：</div>
						<div class="span6">{{ $info['sales_id'] }}</div>
					</div>
				</div>
			</div>

			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span3">销售经理：</div>
						<div class="span6">{{ $info['manager_name'] }}</div>
					</div>
				</div>

				<div class="span6">
					<div class="row-fluid">
						<div class="span3">销售经理ID：</div>
						<div class="span6">{{ $info['SALESMANAGER'] }}</div>
					</div>
				</div>
			</div>

			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span3">城市经理：</div>
						<div class="span6">{{ $info['city_manager_name'] }}</div>
					</div>
				</div>

				<div class="span6">
					<div class="row-fluid">
						<div class="span3">城市经理ID：</div>
						<div class="span6">{{ $info['CITYMANAGER'] }}</div>
					</div>
				</div>
			</div>

			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span3">门店所在城市：</div>
						<div class="span6">{{ $info['CITY'] }}</div>
					</div>
				</div>

			</div>

		</div>
	</div>
	<hr>
	<!-- 手机服务密码-->
	<div class="row-fluid">

		<div class="span12">
			手机服务密码
			<hr>
			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span3">手机号码：</div>
						<div class="span6">{{ $info['mobile'] }}</div>
					</div>
				</div>

				<div class="span6">
					<div class="row-fluid">
						<div class="span3">服务密码：</div>
						<div class="span6"></div>
					</div>
				</div>
			</div>

		</div>
	</div>
	<hr>
	<!-- 电商密码-->
	<div class="row-fluid">

		<div class="span12">
			电商密码
			<hr>
			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span3">京东账户：</div>
						<div class="span6"></div>
					</div>
				</div>

				<div class="span6">
					<div class="row-fluid">
						<div class="span3">京东密码：</div>
						<div class="span6"></div>
					</div>
				</div>
			</div>

			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<div class="span3">淘宝账户：</div>
						<div class="span6"></div>
					</div>
				</div>

				<div class="span6">
					<div class="row-fluid">
						<div class="span3">淘宝密码：</div>
						<div class="span6"></div>
					</div>
				</div>
			</div>

		</div>
	</div>

</div>

<div style="margin:0 0 0 50px">
	<span><a href="javascript:history.back()" class="btn btn-primary">返回主页<a/></span>
</div>

@endsection @section('script')
<script type="text/javascript">

</script>
@endsection
