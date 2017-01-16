@extends('_layouts.default_wx')

@section('content')
<div>
    <div id="workInfo" class="content">
        <form action="/wx/loan/do-work" method="post" id="work_info">

            <input type="hidden" id="work_deposit_bank" name="work_deposit_bank" value="">
            <input type="hidden" id="work_bank_branch_name" name="work_bank_branch_name" value="">
            <div class="titleCom workInfo">
                <p>
                    <span class="logoPic iconWork"></span>
                    <span class="title">工作信息</span>
                </p>
            </div>
            <div class="worker">
                <div class="companyName column">
                    <span><i class="star">*</i>工作单位</span>
                    @if(is_object($work))
                        <input type="text" id="companyName" name="work_unit" value="{{$work->work_unit}}" placeholder="请填写您营业执照上的公司全称" maxlength="200" required="required"/>
                    @else
                        <input type="text" id="companyName" name="work_unit" placeholder="请填写您营业执照上的公司全称" maxlength="200" required="required"/>
                    @endif
                </div>
                <div class="companyName_tips column" style="width:6rem;height:0.73rem;margin:0 auto;margin-bottom: .16rem;border-bottom: none;background: rgba(250,165,74,.1);display:none ;">
                    <div class="tan_img"><img src="{{asset('img/wxSecond/tan.png')}}" alt="" /></div>
                    <div class="tan_tips">如无正式的名称可以参考下面的格式：店铺招牌+行业或老板名+行业,如爱酷美容美发店,李四服装批发店。</div>
                </div>

                <div class="companyPhone column">
                    <span><i class="star">*</i>单位电话</span>
                    @if(is_object($work))
                        <input type="text" id="work_unit_mobile" name="work_unit_mobile" value="{{$work->work_unit_mobile}}"  placeholder="请输入单位电话" maxlength="20" required="required"/>
                    @else
                        <input type="text" id="work_unit_mobile" name="work_unit_mobile" placeholder="请输入单位电话" maxlength="20" required="required"/>
                    @endif
                </div>

                <div class="column">
                    <span><i class="star">*</i>单位地址</span>
                    <div class="area">
                        @if(is_object($work))
                            <input id="work_addr1" name="work_addr1" value="{{$work->work_addr1}}" type="text"/>
                        @else
                            <input id="work_addr1" name="work_addr1" type="text"/>
                        @endif
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="column">
                    <span><i class="star">*</i>区/县</span>
                    @if(is_object($work))
                        <input type="text" id="work_addr2" name="work_addr2" value="{{$work->work_addr2}}" maxlength="20" placeholder="请输入单位地址所在区或县"/>
                    @else
                        <input type="text" id="work_addr2" name="work_addr2" maxlength="20" placeholder="请输入单位地址所在区或县"/>
                    @endif
                </div>
                <div class="clearfix"></div>
                <div class="column">
                    <span><i class="star">*</i>街道/乡镇</span>
                    @if(is_object($work))
                        <input type="text" id="work_addr3" name="work_addr3" value="{{$work->work_addr3}}" maxlength="20" placeholder="请输入单位地址所在街道或乡镇"/>
                    @else
                        <input type="text" id="work_addr3" name="work_addr3" maxlength="20" placeholder="请输入单位地址所在街道或乡镇"/>
                    @endif
                </div>
                <div class="clearfix"></div>
                <div class="column">
                    <span><i class="star">*</i>详细地址</span>
                    @if(is_object($work))
                        <input type="text" id="work_addr4" name="work_addr4" value="{{$work->work_addr4}}" placeholder="请输入详细地址" required="required" maxlength="50"/>
                    @else
                        <input type="text" id="work_addr4" name="work_addr4" placeholder="请输入详细地址" required="required" maxlength="50"/>
                    @endif
                </div>
                <div class="clearfix"></div>
                <div class="column">
                    <span><i class="star">*</i>门牌号</span>
                    @if(is_object($work))
                        <input type="text" id="work_addr5" name="work_addr5" value="{{$work->work_addr5}}" placeholder="请输入门牌号" required="required" maxlength="10"/>
                    @else
                        <input type="text" id="work_addr5" name="work_addr5" placeholder="请输入门牌号" required="required" maxlength="10"/>
                    @endif
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="titleCom workInfo">
                <p>
                    <span class="logoPic iconBank"></span>
                    <span class="title">还款账户信息</span>
                </p>
            </div>
            <div class="clearfix"></div>
            <div class="account">
                <div class="column accountNum">
                    <span><i class="star">*</i>银行卡号</span>
                    @if(is_object($work))
                        <input type="text" id="accountNum" name="work_repayment_account" value="{{$work->work_repayment_account}}" placeholder="请输入还款账号" required="required" maxlength="25" pattern="\d{15,25}"/>
                    @else
                        <input type="text" id="accountNum" name="work_repayment_account" placeholder="请输入还款账号" required="required" maxlength="25" pattern="\d{15,25}"/>
                    @endif
                </div>
                <div class="clearfix"></div>
                <div class="column bank selArrow">
                    <span><i class="star">*</i>开户银行</span>
                    <div class="area" style="width: 4.2rem">
                        <select style="color:#000;width:4.2rem;" name="work_deposit_bank_no" id="bank"  class="select">
                            <option value='0'>请选择开户行信息</option>
                            @foreach($bank_data as $bank)
                                <option value="{{$bank->ITEMNO}}" @if(is_object($work) && $work->work_deposit_bank_no==$bank->ITEMNO) selected='selected' @endif>
                                    {{$bank->ITEMNAME}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="column bankCity">
                    <span><i class="star">*</i>开户银行城市</span>
                    <div class="area">
                        <div class="content-block">
                            @if(is_object($work))
                                <input id="bankCity" name="work_bank_city" type="text"  value="{{$work->work_bank_city}}"/>
                            @else
                                <input id="bankCity" name="work_bank_city" type="text"  placeholder="" />
                            @endif
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="column branch selArrow">
                    <span>&nbsp;开户支行</span>
                    <div class="area" style="width: 4.2rem">
                        <select style="color:#000;width: 4.2rem" id="bankBranch" name="work_bank_branch_no" class="select">
                            @if(is_object($work))
                                <option value={{$work->work_bank_branch_no}}>{{$work->work_bank_branch_name}}</option>
                            @else
                                <option value='0'>请选择支行信息</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="clearfix"></div>

                <div class="column creditCard">
                    <span>&nbsp;信用卡<i></i></span>
                    @if(is_object($work))
                        <input type="text" id="creditCard" name="work_credit_card" value="{{$work->work_credit_card}}" placeholder="请输入信用卡号"  maxlength="24"/>
                    @else
                        <input type="text" id="creditCard" name="work_credit_card" placeholder="请输入信用卡号"  maxlength="24"/>
                    @endif
                </div>

                <div class="titleCom business_card">
                    <p>
                        <span class="logoPic iconPic"></span>
                        <span class="title"><i class="star">*</i>银行卡正面照片</span>
                    </p>
                </div>
                <div class="business_cardPic">
                    <div class="add" id="up_pic0" >
                        @if(!empty($bank_card_pic->bank_card_pic) && (strpos($bank_card_pic->bank_card_pic,'wechat')>0))
                            <img id="bankCardImg" src="{{\App\Util\FileReader::read_storage_image_resize_file($bank_card_pic->bank_card_pic)}}"/>
                        @else
                            <img id="bankCardImg" src="{{asset('img/wxSecond/addPic.png')}}"/>
                        @endif

                        @if(!empty($bank_card_pic->bank_card_pic) && (strpos($bank_card_pic->bank_card_pic,'wechat')>0))
                            <input name="bank_card_pic" type="hidden" value="{{$bank_card_pic->bank_card_pic}}"/>
                        @else
                            <input name="bank_card_pic" type="hidden" value=""/>
                        @endif
                    </div>
                </div>
            </div>
            <input type="button" id="next_pic" value="下一步" class="nextBtn" disabled="disabled"/>
        </form>
    </div>
    <script src="{{asset('js/wx/workInfo.js')}}" type="text/javascript" charset="utf-8"></script>
    <script src="{{asset('js/wx/bankCity.js')}}" type="text/javascript" charset="utf-8"></script>
    <script src="{{asset('js/wx/workCity.js')}}" type="text/javascript" charset="utf-8"></script>
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
                                    fix:false,
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
                                        target.find("input").eq(0).val(data.path);
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
                window.location.href = '/wx/loan/product';
            }, false);
            function pushHistory() {
                var state = {title: "title",  url: "#" };
                window.history.pushState(state, "title", "#");
            }
        })

    </script>
</div>
@endsection
