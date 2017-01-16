@extends('_layouts.default_wx')

@section('content')
<div>
    <div id="passwords" class="content">
        <form method="post" action="">
            <input type="hidden" id="loadingImg" value="{{asset('img/wx/loading.png')}}">
            <input type="hidden" id="applySuccessImg" value="{{asset('img/wxSecond/applySuccess.png')}}">

            <input type="hidden" id="orderStatus" value="">
            <div class="jdPass pass">
                <div class="jdApprove approve">
                    <span id="rightIcon"><i class="iconfont">&#xe604;</i></span>
                    <span id="left">京东账户认证</span>
                </div>
                <div class="userinfo">
                    <div class="uesername name">
                        <span>用&nbsp;户&nbsp;名：</span>
                        <input type="text" name="jd_account" id="jd_account" maxlength="120" placeholder="请填写您的京东用户名" />
                        <div class="underLine"></div>
                    </div>
                    <div class="password word">
                        <span><i>密&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;码</i>：</span>
                        <input type="password" name="jd_password" id="jd_password" maxlength="120" placeholder="请填写您的密码" />
                        <div class="changeType"></div>
                    </div>
                </div>
            </div>

            <div class="tbPass pass">
                <div class="tbApprove approve">
                    <span class="tbLogo"></span>
                    <span id="left">淘宝账户认证</span>
                </div>
                <div class="userinfo">
                    <div class="uesername name">
                        <span>用&nbsp;户&nbsp;名：</span>
                        <input type="text" name="tb_account" id="tb_account" maxlength="120" placeholder="请填写您的淘宝用户名" />
                        <div class="underLine"></div>
                    </div>
                    <div class="password word">
                        <span><i>密&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;码</i>：</span>
                        <input type="password" name="tb_password" id="tb_password" maxlength="120" placeholder="请填写您的密码" />
                        <div class="changeType"></div>
                    </div>
                </div>
            </div>
            <input type="button" id="comfirm_submit" class="butComfire nextBtn" value="确认提交" style="background: rgb(235,33,107)"/>
        </form>

    </div>
    <script src="{{asset('js/wx/check_order.js')}}" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript">
       /* $(function(){
            var orderStatus = $('#orderStatus').val();

            if(orderStatus == 2){
                pushHistory();
                window.addEventListener("popstate", function(e) {
                    window.location.href = '/wx/loan/mcode';
                }, false);
            }else{
                pushHistory();
                window.addEventListener("popstate", function(e) {
                    window.location.href = '/wx/loan/phone-pwd';
                }, false);
            }

            function pushHistory() {
                var state = {title: "title",  url: "#" };
                window.history.pushState(state, "title", "#");
            }
        })*/
        $('.changeType').click(function() {
            var _type = $(this).prev('input').attr('type');
            if (_type == 'text') {
                $(this).prev('input').attr('type', 'password');
                $(this).css({
                    'background': 'url({{asset('img/wxSecond/openPass.png')}}) center center no-repeat',
                    'background-size': '.36rem .24rem'
                });
            } else if (_type == 'password') {
                $(this).prev('input').attr('type', 'text');
                $(this).css({
                    'background': 'url({{asset('img/wxSecond/closePass.png')}}) center center no-repeat',
                    'background-size': '.36rem .24rem'
                });
            }
        })
    </script>

    <script type="text/javascript">
        var af_swift_number,event1;
        (function(){
            try {
                var win = window,
                        doc = document,
                        br = win["BAIRONG"] = win["BAIRONG"] || {},
                        s = doc.createElement("script"),//创建对象
                        url ='{{ asset("/js/wx/braf.js") }}';
                s.charset = "utf-8";
                s.src = url;
                //正式环境
                //br.client_id = "3000202";
                //测试环境
                br.client_id = "3100071";
                doc.getElementsByTagName("head")[0].appendChild(s);//获得页面每个head标签，像节点添加  最后一个子节点
                br.BAIRONG_INFO = {
                    "app" : "antifraud", //反欺诈   必选
                    "event" : "lend",   //借款事件   必选
                    //登录事件为"event" : "login",    注册事件为"event" : "register",
                    "page_type" : "dft"//当前页面类型，请勿修改    必选
                    //以下为可选参数
                }

                window.GetSwiftNumber=function(data){
                    if(data.response.af_swift_number){
                        if(window.isGetSwiftNumberExec){
                            return;
                        }
                        window.isGetSwiftNumberExec = true;

                        try{
                            af_swift_number = data.response.af_swift_number;
                            event1 = data.response.event;
                        }catch(e){
                            af_swift_number = 'null';
                            event1 = 'null';
                        }

                        $.ajax({
                            type: 'post',
                            url: '/wx/loan/bai-rong',
                            async: true,
                            data: {'af_swift_number': af_swift_number, 'event': event1},
                            dataType: 'json'
                        });
                    }else{
                        $.ajax({
                            type: 'post',
                            url: '/wx/loan/bai-rong',
                            async: true,
                            data: {'af_swift_number': '', 'event': ''},
                            dataType: 'json'
                        });
                    }
                }
            }catch (e){

            }
        })();
    </script>
</div>
@endsection

