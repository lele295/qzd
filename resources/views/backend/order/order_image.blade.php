@extends('_layouts.backend') @section('content')


<div class="header-title">
	<span>查看影像</span>
</div>

<div style="margin: 0 100px 0 300px;">
	<div class="row-fluid">
		<div class="span10">
			<div class="row-fluid">
				<div class="span3">身份证正面：</div>
				<div class="span6"><img src="{{ $info->cert_face_pic ? \App\Util\FileReader::read_storage_image_resize_file($info->cert_face_pic) : '' }}" class="img-polaroid"></div>
			</div>

			<div class="row-fluid">
				<div class="span3">身份证反面：</div>
				<div class="span6"><img src="{{ $info->cert_opposite_pic ? \App\Util\FileReader::read_storage_image_resize_file($info->cert_opposite_pic) : '' }}" class="img-polaroid"/></div>
			</div>

			<div class="row-fluid">
				<div class="span3">手持身份证：</div>
				<div class="span6"><img src="{{ $info->cert_hand_pic ? \App\Util\FileReader::read_storage_image_resize_file($info->cert_hand_pic) : '' }}" class="img-polaroid"/></div>
			</div>

			<div class="row-fluid">
				<div class="span3">名片或者工牌：</div>
				<div class="span6"><img src="{{ $info->work_pic ? \App\Util\FileReader::read_storage_image_resize_file($info->work_pic) : '' }}" class="img-polaroid"/></div>
			</div>

			<div class="row-fluid">
				<div class="span3">手术合同确认单：</div>
				<div class="span6"><img src="{{ $info->contract_pic ? \App\Util\FileReader::read_storage_image_resize_file($info->contract_pic) : '' }}" class="img-polaroid"/></div>
			</div>

			<div class="row-fluid">
				<div class="span3">征信授权书：</div>
				<div class="span6"><img src="{{ $info->credit_auth_pic ? \App\Util\FileReader::read_storage_image_resize_file($info->credit_auth_pic) : ''}}" class="img-polaroid"/></div>
			</div>

			<div class="row-fluid">
				<div class="span3">银行卡正面：</div>
				<div class="span6"><img src="{{ $info->bank_card_pic ? \App\Util\FileReader::read_storage_image_resize_file($info->bank_card_pic) : '' }}" class="img-polaroid"/></div>
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
