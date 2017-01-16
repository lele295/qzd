@extends('_layouts.default_wx')

@section('content')
            <div id="confirm" class="content">

                <form method="POST" action="{{url('wx/loan/do-mcode')}}" id="form_warp">
                    <input name="_token" value="{{Csrf_Token()}}" type="hidden">
                    <input type="hidden" name="sid" value="{{$sess_id}}">

                    <div class="logo" style="background: white;">
                        <div class="logoImg">
                            <img src="{{asset('img/wx/logo.png')}}" />
                        </div>
                        <div class="merchant">
                            <img src="{{asset('img/wx/house.png')}}" class="house"/>
                            @if(is_object($mcode_obj))
                                <input type="text" name="sno" id="merChantNum" value="{{$mcode_obj->merchant_code}}" placeholder="请输入为您服务的商家代码" maxlength="11" />
                            @else
                                <input type="text" name="sno" id="merChantNum" value="" placeholder="请输入为您服务的商家代码" maxlength="11" />
                            @endif
                            <div class="underLine"></div>
                        </div>
                        <p>如您不知道商家号码，请咨询商家工作人员。</p>
                    </div>

                    <input type="button" class="nextBtn" value="下一步" disabled="disabled">
                </form>
            </div>
    <script type="text/javascript">

        $(".nextBtn").click(function(){
            $(".nextBtn").attr("disabled",true);
            $(".nextBtn").css("background","#ccc");
            $.ajax({
                url:'/wx/loan/check-merchantcode',
                //async: false,//设置为同步才能获取flag的值
                type:'post',
                data:{'merchantcode':$('#merChantNum').val()},
                dataType:'json',
                success:function(data){
                    if(data.status == 0){
                        layer.open({
                            skin: 'oAlterWindow',
                            title:'小提示',
                            fix: false,
                            offset:['120px',''],
                            shade: [0.8, '#000'],
                            shadeClose: true,
                            maxmin: true,
                            content: "<p class='tips_1'></p><p class='tips_2'></p>",
                            bgcolor:'red',
                            closeBtn:0,
                            btn:['知道了','']
                        })
                        $(".tips_2").html(data.msg);
                        $(".nextBtn").attr("disabled",false);
                        $(".nextBtn").css("background","rgb(235,33,107)");
                    }else{
                        $("form").submit();
                    }
                }
            });

        });
        $(function(){
            var merChantNum = $('#merChantNum').val();
            if(!merChantNum){
                $(".nextBtn").attr("disabled",true);
                $(".nextBtn").css("background","#ccc");
            }else {
                $(".nextBtn").attr("disabled",false);
                $(".nextBtn").css("background","rgb(235,33,107)");
            }
        })
        var bind_name = 'input';
        if (navigator.userAgent.indexOf("MSIE") != -1){
            bind_name = 'propertychange';
        }
        $("#merChantNum").bind(bind_name,function(){
            var merChantNum = $('#merChantNum').val();
            if(!merChantNum){
                $(".nextBtn").attr("disabled",true);
                $(".nextBtn").css("background","#ccc");
            }else {
                $(".nextBtn").attr("disabled",false);
                $(".nextBtn").css("background","rgb(235,33,107)");
            }
        })

        $(function(){
            var preUrl = document.referrer;

            //如果上级来源是电商页面，则不让返回电商页面
            if(preUrl.indexOf('wx/loan/ecommerce') != -1 || preUrl.indexOf('wx/loan/phone-pwd') != -1){
                pushHistory();
                window.addEventListener("popstate", function(e) {
                    window.location.href = '/wx/order/list';
                }, false);
                function pushHistory() {
                    var state = {title: "title",  url: "#" };
                    window.history.pushState(state, "title", "#");
                }
            }
        })
    </script>
@endsection