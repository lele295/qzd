@extends('_layouts.default_wx')

@section('content')
<div id="phonePass" class="content">
    <form action="{{url('wx/loan/do-phone-pwd')}}" method="post" onkeydown="if(event.keyCode==13)return false;" class="passWord column">
        <div class="logoPhoneServe">
            <img src="{{asset('img/wx/phoneServe.png')}}" alt="" />
        </div>
        <ul class="list">
            <li class="phoneNum">
                <div class="left"><i class="iconfont">&#xe603;</i> 手机号码：</div>
                <div class="right" style="color:#000;font-family: '微软雅黑'">
                    @if(isset($mobilePhone))
                        {{$mobilePhone}}
                    @endif
                </div>
            </li>
            <li class="phoneServeMima">
                <div class="left"><i class="iconfont">&#xe602;</i> 服务密码：</div>
                <input type="password" name="mobile_service_password" id="password" placeholder="请输入服务密码" maxlength="11"/>
                <div class="changeType"></div>
            </li>
        </ul>

        <input type="button" value="下一步" id="phoneSub" disabled="disabled"/>
        <p class="alterExp"><a href="javascript:void(0);">忘记密码?</a></p>
    </form>
    <h3>什么是手机服务密码？</h3>
    <p class="serveExp">手机服务密码是你的号码在移动运营公司进行获取服务时需要提供的一个身份证，这个密码是由运营商提供，可以自己修改的。<br />当天只有三次输入密码的机会！超过三次您将无法认证！如果您忘记密码，请拨打手机运营商电话重置密码！</p>

    <div class="alterExpWindow">
        <div class="servewindow">
            <ul>
                <li>
                    <p class="operatorName">移动用户:</p>
                    <p class="methods1">
                        <span class="other">方法一：</span>拨打<i>“10086”到“人工客服”</i>重置密码。
                    </p>
                    <p class="methods2">
                        <span class="other">方法二：</span>本机发送<i>“czmm”到“10086”</i>按提示，输入"身份证号码#新密码#确定密码"。
                    </p>
                    <p>如需帮助可发送"0"到1008611，会有专人为您解答。</p>
                </li>
                <li>
                    <p class="operatorName">联通用户:</p>
                    <p class="methods1">
                        <span class="other">方法一：</span>拨打“10010”，按语音提示，按<i>“411+身份证号码”，密码重置后会发送到本机。</i>重置密码。
                    </p>
                    <p class="methods2">
                        <span class="other">方法二：</span>本机短信回复<i>"405"，按提示，"MMCZ#六位新密码"。</i>
                    </p>
                </li>
                <li>
                    <p class="operatorName">电信用户:</p>
                    <p class="methods1">
                        <span class="other">方法一：</span>拨打<i>“10000”到“人工客服”</i>重置密码。
                    </p>
                    <p class="methods2">
                        <span class="other">方法二：</span>本机发送<i>“6011#新密码”到“10001”</i>，按提示验证重置密码"。
                    </p>
                </li>
            </ul>
            <div class="cancelHide">
                <img src="{{asset('img/wx/shanchu.png')}}"/>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(".alterExp").click(function(){
        $(".alterExpWindow").show();
    })
    $(".alterExpWindow").click(function(){
        $(".alterExpWindow").hide();
    })
    $('.changeType').click(function() {
        var _type = $("#password").attr('type');
        if (_type == 'text') {
            $("#password").attr('type', 'password');
            $('.changeType').css({
                'background': 'url({{asset('img/wxSecond/openPass.png')}}) center center no-repeat',
                'background-size': '.36rem .24rem'
            });
        } else if (_type == 'password') {
            $("#password").attr('type', 'text');
            $('.changeType').css({
                'background': 'url({{asset('img/wxSecond/closePass.png')}}) center center no-repeat',
                'background-size': '.36rem .24rem'
            });
        }
    })
</script>

<script type="text/javascript">
    (function () {
        var submitType = 'SUBMIT_CAPTCHA';
        var _clickEvent =
        $("#phoneSub").click(function () {
            var _submitBtn = this, password = $("#password").val();
            if (!password) {
                layer.open({
                    skin: 'oAlterWindow',
                    title:'小提示',
                    fix: false,
                    offset:['120px',''],
                    shade: [0.8, '#000'],
                    shadeClose: true,
                    maxmin: true,
                    content: "<p class='tips_1'></p><p class='tips_2'>服务密码不能为空</p>",
                    bgcolor:'red',
                    closeBtn:0,
                    btn:['知道了','']
                })
                return false;
            }
            if (!$(this).attr('disabled')) {
                $(this).css("background","#ccc");
                $(this).attr('disabled', 'true');
                $(this).val('正在提交数据...');
                layer.open({
                    skin:"loadWindow",
                    title:0,
                    shade: [0.8, '#000'],
                    offset:['235px',''],
                    content:"<p class='tips_1'><img src='{{asset('img/wx/loading.png')}}'/></p><p class='dia_span tips_2'>请耐心等待...</p>",
                    closeBtn:0,
                    btn:0
                })

                $.post('/wx/loan/check-phone-pwd', getData(), function (data) {
                    showByCode(data, _submitBtn);
                }, 'json')
            }
        });

        function showByCode(data, _submitBtn) {
            switch (data.code) {
                case 0://采集请求超时
                case 101:
                case 10006://短信验证码失效系统已自动重新下发
                case 10017://请用本机发送CXXD至10001获取查询详单的验证码
                case 10018://短信码失效请用本机发送CXXD至10001获取查询详单的验证码
                case 30000://错误信息
                case 65555://运营商正在维护
                case 11111://输入随机码
                    layer.closeAll();
                    window.password  = $("#password").val();
                    window.code  = data.code;
                    window.location.href = "/wx/loan/contacts?password="+password+"&code="+code;
                    break;
                case 10008://成功
                    layer.closeAll();
                    $("form").submit();
                    break;
                case 10001:
                case 10002:
                    layer.closeAll();
                    layer.open({
                    skin:'checkLists',
                    title:0,
                    offset:['150px','14%'],
                    shade: [0.8, '#000'],
                    btn:'确定',
                    maxmin: true,
                    content: "<p class='sendCode'>动态验证码已发送至您的手机上，请注意查收！</p><div class='codeFrom'><div class='code'><span class='codeAble'>验证码：</span><input type='text' name='captcha' id='captcha' class='verCode' placeholder='请输入正确的验证码' maxlength='8'/></div>",
                    closeBtn:0,
                    yes:function(){
                        window.captcha = $("#captcha").val();
                        $("#phoneSub").click();
                    }
                    })
                    submitType = 'SUBMIT_CAPTCHA';
                    reinitBtn(_submitBtn);
                    break;
                case 10022:
                    layer.closeAll();
                    layer.open({
                        skin:'checkLists',
                        title:0,
                        offset:['150px','14%'],
                        shade: [0.8, '#000'],
                        btn:'确定',
                        maxmin: true,
                        content: "<p class='sendCode'>您好！查询密码已发送至您的手机上，请注意查收</p><div class='codeFrom'><div class='code'><span class='codeAble'>查询密码：</span><input type='text' name='queryPwd' id='queryPwd' class='verCode' placeholder='请输入正确的查询密码' maxlength='11'/></div>",
                        closeBtn:0,
                        yes:function(){
                            window.queryPwd = $("#queryPwd").val();
                            $("#phoneSub").click();
                        }
                    })
                    submitType = 'SUBMIT_QUERY_PWD';
                    break;
                default:
                    layer.closeAll();
                    reinitBtn(_submitBtn);
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
                    $(".tips_2").html(data.message);
                    break;
            }
        }

        function reinitBtn(_submitBtn) {
            $(_submitBtn).removeAttrs('disabled');
            $(_submitBtn).css("background","rgb(235,33,107)");
            $(_submitBtn).val('下一步');
        }

        function getData() {
            return {
                'password': $("#password").val(),
                'captcha': window.captcha,
                'queryPwd': window.queryPwd,
                'type': submitType
            };
        }
    })();
    $(function () {
        pushHistory();
        window.addEventListener("popstate", function (e) {
            window.location.href = '/wx/loan/filepic';
        }, false);
        function pushHistory() {
            var state = {title: "title", url: "#"};
            window.history.pushState(state, "title", "#");
        }
    })

    $(function(){
            var password = $('#password').val();
            if(!password){
                $("#phoneSub").attr("disabled",true);
                $("#phoneSub").css("background","#ccc");
            }else {
                $("#phoneSub").attr("disabled",false);
                $("#phoneSub").css("background","rgb(235,33,107)");
            }
    })
    var bind_name = 'input';
    if (navigator.userAgent.indexOf("MSIE") != -1){
        bind_name = 'propertychange';
    }
    $("#password").bind(bind_name,function(){
        var password = $('#password').val();
        if(!password){
            $("#phoneSub").attr("disabled",true);
            $("#phoneSub").css("background","#ccc");
        }else {
            $("#phoneSub").attr("disabled",false);
            $("#phoneSub").css("background","rgb(235,33,107)");
        }
    })

</script>
</div>
</div>
@endsection

