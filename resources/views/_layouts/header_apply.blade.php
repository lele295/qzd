<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta content="telephone=no" name="format-detection">
<meta content="width=device-width; initial-scale=1.0; maximum-scale=1.0; minimum-scale=1.0; user-scalable=false;" name="viewport">
<meta name="apple-mobile-web-app-capable" content="yes" />
    <title>@if(\App\Util\Kits::isFqg()) 佰仟金融 @else {{\Illuminate\Support\Facades\Config::get('extension.mtitle')}} @endif</title>
    <script src="{{asset("js/jquery.min.js")}}"></script>
    <script type="text/javascript" src="{{asset("js/jquery.validate.min.js")}}"></script>
    <script type="text/javascript" src="{{asset("js/js.js?v=".env('VERSION'))}}"></script>
    <script type="text/javascript" src="{{asset("js/paddy.js?v=".env('VERSION'))}}"></script>
    <script type="text/javascript" src="{{asset("js/plugin/layer/layer2.js?v=".env('VERSION'))}}"></script>
    <link rel="stylesheet" href="{{asset("js/plugin/layer/need/layer.css?v=".env('VERSION'))}}">
    <link rel="stylesheet" href="{{asset("css/css1.css?v=".env('VERSION'))}}">
    <link rel="stylesheet" href="{{asset("css/reset.css?v=".env('VERSION'))}}">
    @section('style')
	@show
@if(\App\Util\Kits::isFqg())
    <style>
        .p-header2{
            background: #364c64 !important;
        }
        .p-bg-color-primary{
            background: #ff6f34 !important;
        }
    </style>
@endif
</head>
<body class="p-body-bgc">


<div class="content">
    @yield('content')
</div>

<div class="footer2">
	@section('footer2')
	@show
</div>

<header class="p-header2">
    {{--<a href="/loan/firm-info" class="p-header2-left-a p-header2-left-a-fh p-header-apply-left">返回</a>--}}
    {{--<a href="" id="applyClose" class="p-header2-right-a p-header-apply-right">--}}
        {{--<img class="p-header2-feedback p-margin-top-15" style="width: 16px;" src="/img/p-header-apply-close.png" alt="">--}}
    {{--</a>--}}
    <h1 class="p-header2-h1">贷款协议</h1>
</header>

@versionfile('/js/plugin/layer/need/layer.css')
@versionfile('/js/plugin/layer/layer2.js')
<style>
    .layermcont{padding:0;text-align: left;}
    .layermbox0 .layermchild{width: 68%;}
    .laymshade{background-color: rgba(0,0,0,.7);}
</style>
<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "//hm.baidu.com/hm.js?4eee259714e24529fe05bcc0192a47be";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
    //退出弹层
    $('#applyClose').on('click', function (event) {
        /*
        event.preventDefault();
        var layerHtml = '<div class="closeLayer">'
               + '<div class="closeLayer-header">请选择退出申请原因</div>'
               + '<ul class="closeLayer-ul">'
               + '<li class="closeLayer-li select" data-id="1">我不会操作</li>'
               + '<li class="closeLayer-li" data-id="2">利息高</li>'
               + '<li class="closeLayer-li" data-id="3">额度低</li>'
               + '<li class="closeLayer-li" data-id="4">期数太长</li>'
               +'<li class="closeLayer-li" data-id="5">放款时间慢</li>'
               +'<li class="closeLayer-li" data-id="6">暂时不需要</li>'
               +'<li class="closeLayer-li" data-id="7">流程复杂</li>'
               +'<li id="last" class="closeLayer-li" data-id="8">更多不爽，必须吐槽'
               +'<div class="closeLayer-textarea-div">'
               +'<textarea id="closeLayer_textarea" class="closeLayer-textarea" name="" id="" cols="30" rows="3">我们将用心倾听您的任何不满~</textarea>'
               +'</div>'
               +'</li>'
               +'</ul>'
               +'<div class="closeLayer-footer">'
               @if(\App\Util\Kits::isFqg())
                +'<a href="javascript:void(0);" id="closeLayer_confirm" class="closeLayer-confirm">反馈</a>'
               @else
                +'<a href="javascript:void(0);" id="closeLayer_confirm" class="closeLayer-confirm">反馈并退出</a>'
                @endif
                +'<a href="javascript:void(0);" id="closeLayer_close" class="closeLayer-close">我点错了</a>'
               +'</div>'
               +'</div>';
        var index=layer.open({
            content:layerHtml,
            success: function () {
                defaultText('#closeLayer_textarea');
                $('#closeLayer_close').on('click', function (event) {
                    //关闭弹层
                    event.preventDefault();
                    layer.close(index);
                });
                selectResult('.closeLayer-li','select');

            }
        });
        */
    });

    var defaultText = function(obj) {
        //文本域默认值设置
        $(obj).focus(function () {
            if ($(this).val() == '我们将用心倾听您的任何不满~') {
                $(obj).val('');
            }
        }).blur(function () {
            if ($(this).val() == '') {
                $(obj).val('我们将用心倾听您的任何不满~');
            }
        })
    };
    var selectResult = function (obj,status) {
        $(obj).on('click', function () {
            $(this).addClass(status).siblings().removeClass(status);
            lastShow('#last','select','#closeLayer_textarea')
        })

    };
    var lastShow = function (clickObj,clickObjStatus,showObj) {
            if ($(clickObj).hasClass(clickObjStatus)) {
                $(showObj).slideDown();
            }else{
                $(showObj).slideUp();
            }
    };
</script>
</body>
<script>
    $(function(){
        $('body').on('click','#closeLayer_confirm',function(){
             var obj = $('.closeLayer-ul li[class~="select"]');
            if(!obj.length){
                layer.open({content: '请选择原因！'});
                return;
            }

            var value = obj.attr('data-id');
            var name = '';
            if(value != '8'){
                name = obj.text();
            }else{
                name = $('#closeLayer_textarea').val();
                if(!name){
                    return layer.open({content: '请填写原因！'});
                }
            }
            {{--var page = "{{$current_step}}";--}}
            $.post('/loan/quit-apply',{value:value,name:name,page:page,_token:'{{ csrf_token() }}'},function(data){
                if(data.status == 1){
                    @if(\App\Util\Kits::isFqg())
                    window.location.reload();
                    @else
                    location.href = data.url;
                    @endif
                }
            },'json');

        });
    });
</script>
<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "//hm.baidu.com/hm.js?4eee259714e24529fe05bcc0192a47be";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>
</html>