@extends('_layouts.default_wx')

@section('content')
<div>
    <div id="content" class="content">

        <div class="titleCom installment">
            <p>
                <span class="logoPic iconInstal"></span>
                <span class="title">分期信息</span>
            </p>
        </div>
        <form action="/wx/loan/do-product" method="post" class="myForm" id="product_info">
            <input type="hidden" id="service_type" name="service_type" value="">
            <div class="column serveClass">
                <span><i class="star">*</i>服务类型</span>
                <div class="serveType">
                    <select style="color:#000" name="service_type_no" id="serveClass" class="service_type_no">
                        <option value="0" style="color:#ccc;">请选择服务类型</option>
                        @foreach($pro_types as $pro_type)
                            <option value="{{$pro_type->PRODUCTCTYPEID}}" @if(is_object($product) && $pro_type->PRODUCTCTYPENAME==$product->service_type) selected='selected' @endif>
                                {{$pro_type->PRODUCTCTYPENAME}}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="column loanAmount">
                <span><i class="star">*</i>分期金额</span>
                @if(is_object($product))
                    <input style="color:#000" type="text" id="loanAmount" name="loan_money" value="{{$product->loan_money}}" placeholder="贷款金额必须在1000~50000之间" maxlength="6"  onkeyup='this.value=this.value.replace(/\D/gi,"")' required="required"/>
                @else
                    <input style="color:#000" type="text" id="loanAmount" name="loan_money" value="" placeholder="贷款金额必须在1000~50000之间" maxlength="6"  onkeyup='this.value=this.value.replace(/\D/gi,"")' required="required"/>
                @endif
            </div>
            <div class="column loanTimes">
                <span><i class="star">*</i>分期期数</span>
                <div class="loanNum">
                    <select style="color:#000" name="periods" id="periods">
                        @if(is_object($product))
                            <option value="0">请选择贷款期数</option>
                            <option value="6" @if($product->periods==6) selected='selected' @endif>6期</option>
                            <option value="9" @if($product->periods==9) selected='selected' @endif>9期</option>
                            <option value="12" @if($product->periods==12) selected='selected' @endif>12期</option>
                            <option value="15" @if($product->periods==15) selected='selected' @endif>15期</option>
                            <option value="18" @if($product->periods==18) selected='selected' @endif>18期</option>
                            <option value="24" @if($product->periods==24) selected='selected' @endif>24期</option>
                        @else
                        @endif
                    </select>
                </div>
            </div>
            <div class="column repayMode">
                <span><i class="star">*</i>还款方式</span>
                <select name="pay_type" id="pay_type" style="color:#000;font-family: '微软雅黑'">
                    <option value="1">等本等息</option>
                    {{--<option value="2">一次性付息</option>--}}
                </select>
            </div>
            <div class="column repayEveryMonth">
                <span>&nbsp;每月应还款</span>
                <div id="monthPay" style="color:#000;font-family: '微软雅黑'"></div>
                <input type="button" id="countBtn" value="点击试算" onclick="javascript:check_trial_data()"/>
            </div>
            <div class="column poundage">
                <span>&nbsp;每月手续费</span>
                <input id="serviceFee" type="text"  value="" readonly="readonly" style="color:#000;font-family: '微软雅黑'"/>
            </div>
            <div class="titleCom operation_contract">
                <p>
                    <span class="logoPic iconPic"></span>
                    <span class="title"><i class="star">*</i>手术合同照片</span>
                </p>
            </div>
            <div class="operation_contractPic">
                <div class="add" id="up_pic0" >
                    @if(!empty($contract_pic->contract_pic) && (strpos($contract_pic->contract_pic,'wechat')>0))
                        <img id="contractImg" src="{{\App\Util\FileReader::read_storage_image_resize_file($contract_pic->contract_pic)}}"/>
                    @else
                        <img id="contractImg" src="{{asset('img/wxSecond/addPic.png')}}"/>
                    @endif

                    @if(!empty($contract_pic->contract_pic) && (strpos($contract_pic->contract_pic,'wechat')>0))
                        <input name="contract_pic" id="contract_pic" type="hidden" value="{{$contract_pic->contract_pic}}"/>
                    @else
                        <input name="contract_pic" id="contract_pic" type="hidden" value=""/>
                    @endif
                </div>
            </div>
            <p class="operation_tips">
                需医师签字和医院盖章！
            </p>

            <input type="button" value="下一步" class="nextBtn" disabled="disabled"/>
        </form>
    </div>
    <script src="{{asset('js/wx/proInfo.js')}}" type="text/javascript" charset="utf-8"></script>
    <script src="{{asset("js/jweixin-1.0.0.js")}}"></script>

    <script>
        wx.config({
            debug: false,
            appId: '{{{$signPackage["appId"]}}}',
            timestamp: '{{{$signPackage["timestamp"]}}}',
            nonceStr: '{{{$signPackage["nonceStr"]}}}',
            signature: '{{{$signPackage["signature"]}}}',
            jsApiList: [
                'checkJsApi',
                'chooseImage',
                'previewImage',
                'uploadImage',
                'getLocation',
                'getNetworkType']
        });
        wx.error(function(res){
            layer.open({
                skin:"oAlterWindow",
                title:'小提示',
                offset:['120px',''],
                shadeClose: true,
                content:"<p class='tips_1'></p><p class='dia_span tips_2'>咦，微信掉链子了 O(∩_∩)O~~</p>",
                closeBtn:0,
                btn:['知道了','']
            })

            window.location.href="/wx/loan/file-pic?wxerror="+res.errMsg;
        });
        var images = {
            localId: [],
            serverId: []
        };

        $(function (){
            $('#up_pic0').click(function (e){
                e.stopPropagation();
                up_img('#up_pic0');
            })

        })

        function up_img(e){
            var target = $(e);
            wx.chooseImage({
                sizeType: ['compressed'],
                // 指定来源是相机
                //sourceType: ['camera'],
                success: function (res) {
                    if(res.localIds.length > 1){
                        layer.open({
                            skin:"oAlterWindow",
                            title:'小提示',
                            offset:['120px',''],
                            shadeClose: true,
                            content:"<p class='tips_1'></p><p class='dia_span tips_2'>上传失败，每次请选择1张上传</p>",
                            closeBtn:0,
                            btn:['知道了','']
                        })
                    }else{
                        images.localId = res.localIds;
                        target.find("input").eq(0).val(Date.parse(new Date())/1000);
                        wx.uploadImage({
                            localId: images.localId[0],
                            success: function(res){
                                layer.open({
                                    skin:"loadWindow",
                                    title:0,
                                    shade: [0.8, '#000'],
                                    shadeClose: true,
                                    offset:['235px',''],
                                    content:"<p class='tips_1'><img src='{{asset('img/wx/loading.png')}}'/></p><p class='dia_span tips_2'>加载中...</p>",
                                    closeBtn:0,
                                    btn:0,
                                    time:2000
                                })
                                $.post('/wx/wechat/downwxpic',{'media_id':res.serverId},function(data){
                                    if(data.status){
                                       /* layer.open({
                                            skin:"loadWindow",
                                            title:0,
                                            shade: [0.8, '#000'],
                                            shadeClose: true,
                                            offset:['235px',''],
                                            content:"<p class='tips_1'><img src='{{asset('img/wx/loading.png')}}'/></p><p class='dia_span tips_2'>请稍后...</p>",
                                            closeBtn:0,
                                            btn:0,
                                            time:2000
                                        })*/
                                        target.find("input").eq(0).val(data.path);
                                        //console.log(data.path);
                                        var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                                        target.find('img').attr('src',images.localId[0]);
                                    }else{
                                        layer.open({
                                            skin:"loadWindow",
                                            title:0,
                                            shade: [0.8, '#000'],
                                            shadeClose: true,
                                            offset:['235px',''],
                                            content:"<p class='tips_1'></p><p class='dia_span tips_2'></p>",
                                            closeBtn:0,
                                            btn:0,
                                            time:2000
                                        })
                                        $(".tips_2").html(data.msg);
                                    }
                                },'json');
                            }
                        });
                    }
                }
            });
        }

        $(function(){
            pushHistory();
            window.addEventListener("popstate", function(e) {
                window.location.href = '/wx/loan/mobile';
            }, false);
            function pushHistory() {
                var state = {title: "title",  url: "#" };
                window.history.pushState(state, "title", "#");
            }
        })
    </script>
</div>
@endsection


