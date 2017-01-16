@extends('_layouts.backend') @section('content')


<div class="header-title">
	<span>查看协议</span>
</div>

<div style="margin: 0 50px 0 50px;">
	{!! $file !!}
</div>

<div style="margin:0 0 0 60px">
	<span><a href="javascript:history.back()" class="btn btn-primary">返回主页<a/></span>
</div>

@endsection @section('script')
<script type="text/javascript">

</script>
@endsection
