@extends('_layouts.header_apply')
@section('title', '贷款协议')
@section('style')
	<style>
		.dia_span{ display:inline-block;width:100%; height:100%; text-align:center;}
        #clickhere{ color: blue;}
	</style>
@stop
@section('content')
    <link rel="stylesheet" href="{{asset("css/plugin/mobile-select-area.css?v=".env('VERSION'))}}">
    <script type="text/javascript" src="{{asset("js/plugin/dialog.js?v=".env('VERSION'))}}"></script>
    <script type="text/javascript" src="{{asset("js/plugin/mobile-select-area.js?v=".env('VERSION'))}}"></script>


    <div class="p-apply-protocol-txt" style="overflower:scorll">
        @if($flag=='zx')
            @include('mobile.document.fenqi_zx_application')
        @elseif($flag=='zt')
            @include('mobile.document.fenqi_zt_application')
        @else
            don't have application!
        @endif
    </div>
    @if(\Illuminate\Support\Facades\Session::has('message'))
        <div style="width: 100%;text-align: center;">
            <p style="color:red;">{{ \Illuminate\Support\Facades\Session::get('message') }}</p>
        </div>
    @endif
    <form id="checkForm" method="POST" action="">
        <input name="_token" value="{{Csrf_Token()}}" type="hidden">
        <input name="loan_id" value="" type="hidden">
        <div class="p-apply-protocol-code p-clearfix validate">
            <label class="p-apply-protocol-code-label floatLeft" for="code">合同确认码{{Request::old("mobile_code")}}
                <input type="number" name="mobile_code" class="p-code-label-input data" value="" maxlength="6" placeholder="6位数字" >
            </label>
            <div class="p-apply-protocol-code-div floatRight">
                <input id="getverify" class="p-code-btn" type="button" value="点击获取">
            </div>
        </div>
        <p class="p-error"></p>
        <p class="p-protocol-send">已发送至手机号码：<span id="tel"></span>
            {{--<span style="float: right;">没收到短信？<a id="clickhere">点这里</a></span>--}}
        </p>
    

        <p class="p-error"></p>
        <div class="p-input-box" style="padding-bottom: 20px;">
            <input id="button" href="javascript:void(0)" class="p-protocol-sumbit-a p-bg-color-primary p-height-40 sumbit-a" type="submit" value="确认签署"/>
        </div>
    </form>

    <script>
        $(function() {
            //弹出层html
            @if(true === ($brige = \App\Util\AppKits::bridgeCheck()))
            var layerHtml = '<div class="protocol-layer">'+
                            '<img class="protocol-layer-img" src="/img/protocol-layer-img.png" alt="">'+
                            '<p class="protocol-layer-p">协议签署成功，<br>请通知商家为您服务！</p>'+
                            '<div id="confirm" class="protocol-layer-btn">确定</div>'+
                            '</div>';
            @else
            var layerHtml = '<div class="protocol-layer">'+
                            '<img class="protocol-layer-img" src="/img/protocol-layer-img.png" alt="">'+
                            '<p class="protocol-layer-p">贷款申请已提交，<br>通过后需要您认证身份,<br>放款更快！</p>'+
                            '<div id="confirm" class="protocol-layer-btn">确定</div>'+
                            '</div>';
            @endif
            $("#checkForm").validate({
                errorPlacement: function(error, element) {
                    element.closest('.validate').next('.p-error').append(error)
                },
                rules: {
                	mobile_code: {required:true,number:true,rangelength:[6,6]}
                },
                messages: {
                	mobile_code: {required:"请输入合同确认码",number:"请输入6位数字合同确认码",rangelength:"请输入6位数字合同确认码"}
                },
                submitHandler:function() {
                    //验证成功回调
                }
            });

            var wait=120;
            function time(o) {
                if (wait == 0) {
                    o.removeAttribute("disabled");
                    o.style.cssText="color:#fff";
                    o.value="点击获取";
                    wait = 120;
                } else {
                    o.setAttribute("disabled", true);
                    o.style.cssText="color:#fff";
                    o.value="重新获取(" + wait + ")";
                    wait--;
                    setTimeout(function() {
                        time(o)
                    },1000)
                }
            }
            
            var check_code = false, check_submit = false, send_voice = false;
            $("#getverify").click(function(){

            	time(this);
                if(check_code){
                    return;
                }
                check_code = true;
				layer.open({
					style: 'border:none; padding:15px;text-align:center; background-color:#000000; color:#fff;',
					content:'<span class="dia_span">正在获取合同确认码，请等待...</span>'
				})
                $.ajax({
                    type:'post',
                    url:'/sign/protocol-info',
                    async:true,
                    data:$("#checkForm").serialize(),
                    dataType:'json',
                    success:function(data){
                        $(document).unmask();
                        layer.closeAll();
                        check_code = false;
                        if(data.status){
                            console.log(data);
                            $('#tel').html(data.mobile);
                            $('.p-protocol-send').show();
                        }else {
							layer.open({
								style: 'border:none; padding:15px;text-align:center; background-color:#000000; color:#fff;',
								content:'<span class="dia_span">'+data.msg+'</span>',
                                time: 2
							})
                        }
                    },
                    error:function(){
                        layer.closeAll();
                        alert('网络异常！');
                    }
                });
            });
            
            $(".sumbit-a").click(function(){
                if(!$('#checkForm').valid()){
                    return;
                }
                if(check_submit){
                    return;
                }
				layer.open({
					style: 'border:none; padding:15px;text-align:center; background-color:#000000; color:#fff;',
					content:'<span class="dia_span">正在提交，请等待...</span>'
				})
                check_submit = true;
                $.ajax({
                    type:'post',
                    url:'/sign/check-ca',
                    async:true,
                    data:$('#checkForm').serialize(),
                    dataType:'json',
                    success:function(data){
                        //console.log(data);
                        $(document).unmask()
                        check_submit = false;
                        if(data.status){
                        	layer.open({
                                content:layerHtml,
                                success: function () {
                                    $('#confirm').on('click', function () {
                                        window.location.href = '/wx/order/list';
                                    })
                                }
                            })
                        }else{
                            if(data.msg == "系统繁忙") {
                                location.reload();
                            }
                            if(data.msg == "您已签署") {
                                window.location.href = '/wx/order/list';
                            }
                            layer.open({
                                style: 'border:none; padding:15px;text-align:center; background-color:#000000; color:#fff;',
                                content:'<span class="dia_span">'+data.msg+'</span>',
                                time:2
                            });
                        }
                    }
                });
            });

            //语音验证码 这里没用到
//            $('#clickhere').click(function(){
//                if (send_voice){
//                    return;
//                }
//                layer.open({
//                    style: 'border:none; padding:15px;text-align:center; background-color:#000000; color:#fff;',
//                    content:'<span class="dia_span">验证码已发送，请注意接听电话。</span>'
//                })
//                send_voice = true;
//                $.ajax({
//                    type:'post',
//                    url:'/loan/send-voice',
//                    async:true,
//                    data:$('#checkForm').serialize(),
//                    dataType:'json',
//                    success:function(data){
//                        $(document).unmask()
//                        send_voice = false;
//                        if(data.status){
//                            layer.open({
//                                style: 'border:none; padding:15px;text-align:center; background-color:#000000; color:#fff;',
//                                content:'<span class="dia_span">'+data.data.msg+'</span>',
//                                time: 2
//                            })
//                        }else{
//                            if(data.msg == "系统繁忙"){
//                                location.reload();
//                            }
//                            layer.open({
//                                style: 'border:none; padding:15px;text-align:center; background-color:#000000; color:#fff;',
//                                content:'<span class="dia_span">'+data.data.msg+'</span>',
//                                time:2
//                            });
//                        }
//                    }
//                });
//            });
        });

    </script>

@endsection