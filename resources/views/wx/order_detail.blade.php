@extends('_layouts.default_wx')

@section('content')
<div id="infromations" class="content">

    <div class="clear" style="width: 0;height: 0;clear: both;"></div>
    <div class="column applyInfro">
        <img src="{{asset('img/wxkhd/gerenxinxi-tb.png')}}"/>
        <span class="name">个人信息</span>
        <span class="right"><i class="iconfont icon">&#xe600;</i></span>
    </div>
    <ul class="apply">
        <li><span class="leftIn">姓名</span><span class="rightIn">{{$orderInfo->applicant_name}}</span></li>
        <li><span class="leftIn">身份证号</span><span class="rightIn">{{$orderInfo->applicant_id_card}}</span></li>
        <li><span class="leftIn">手机号码</span><span class="rightIn">{{$orderInfo->mobile}}</span></li>
        <li><span class="leftIn">行业</span><span class="rightIn">{{$orderInfo->industry_name}}</span></li>
        <li><span class="leftIn">最高学历</span><span class="rightIn">{{$orderInfo->edu_level}}</span></li>
        <li><span class="leftIn">QQ邮箱</span><span class="rightIn" >{{$orderInfo->qq_email}}</span></li>
        <li><span class="leftIn">直系亲属关系</span><span class="rightIn" >{{$orderInfo->family_relation}}</span></li>
        <li><span class="leftIn">亲属姓名</span><span class="rightIn">{{$orderInfo->family_name}}</span></li>
        <li><span class="leftIn">亲属联系手机</span><span class="rightIn">{{$orderInfo->family_mobile}}</span></li>

        <li><span class="leftIn">现居住地址</span><span class="rightIn" >{{\App\Service\Help::findCity($orderInfo->family_addr1)}}</span></li>
        <li><span class="leftIn">区/县</span><span class="rightIn" >{{$orderInfo->family_addr2}}</span></li>
        <li><span class="leftIn">街道/乡镇</span><span class="rightIn" >{{$orderInfo->family_addr3}}</span></li>
        <li><span class="leftIn">详细地址</span><span class="rightIn" >{{$orderInfo->family_addr4}}</span></li>
        <li><span class="leftIn">门牌号</span><span class="rightIn" >{{$orderInfo->family_addr5}}</span></li>

        @if($orderInfo->no_auth == 1)
        <li><span class="leftIn">其他联系人关系</span><span class="rightIn" >{{$orderInfo->other_contact_relation}}</span></li>
        <li><span class="leftIn">其他联系人姓名</span><span class="rightIn" >{{$orderInfo->other_contact_name}}</span></li>
        <li><span class="leftIn">其他联系人手机</span><span class="rightIn" >{{$orderInfo->other_contact_mobile}}</span></li>
        @endif
        <div class="empty"></div>
    </ul>
    <div class="column personInfro">
        <img src="{{asset('img/wxkhd/workInfo.png')}}"/>
        <span class="name">工作信息</span>
        <span class="right"><i class="iconfont icon">&#xe600;</i></span>
    </div>
    <ul class="person">

        <li><span class="leftIn">工作单位</span><span class="rightIn">{{$orderInfo->work_unit}}</span></li>
        <li><span class="leftIn">单位电话</span><span class="rightIn">{{$orderInfo->work_unit_mobile}}</span></li>

        <li><span class="leftIn">单位住址</span><span class="rightIn" >{{\App\Service\Help::findCity($orderInfo->work_addr1)}}</span></li>
        <li><span class="leftIn">区/县</span><span class="rightIn" >{{$orderInfo->work_addr2}}</span></li>
        <li><span class="leftIn">街道/乡镇</span><span class="rightIn" >{{$orderInfo->work_addr3}}</span></li>
        <li><span class="leftIn">详细地址</span><span class="rightIn" >{{$orderInfo->work_addr4}}</span></li>
        <li><span class="leftIn">门牌号</span><span class="rightIn" >{{$orderInfo->work_addr5}}</span></li>
        <div class="empty"></div>
    </ul>
    <div class="column accountInfro">
        <img src="{{asset('img/wxkhd/zhangdanxinxi_tb.png')}}"/>
        <span class="name">账户信息</span>
        <span class="right"><i class="iconfont icon">&#xe600;</i></span>
    </div>
    <ul class="bank">
        <li><span class="leftIn">代扣还款账号</span><span class="rightIn">{{$orderInfo->work_repayment_account}}</span></li>
        <li><span class="leftIn">代扣开户银行</span><span class="rightIn">{{$orderInfo->work_deposit_bank}}</span></li>
        <li><span class="leftIn">信用卡</span><span class="rightIn">{{$orderInfo->work_credit_card}}</span></li>
        <div class="empty"></div>
    </ul>
    <div class="column installment">
        <img src="{{asset('img/wxkhd/fenqi.png')}}"/>
        <span class="name">分期信息</span>
        <span class="right"><i class="iconfont icon">&#xe600;</i></span>
    </div>
    <ul class="install">
        <li><span class="leftIn">服务类型</span><span class="rightIn">{{$orderInfo->service_type}}</span></li>
        <li><span class="leftIn">分期金额</span><span class="rightIn">￥{{$orderInfo->loan_money}}</span></li>
        <li><span class="leftIn">期数</span><span class="rightIn">{{$orderInfo->periods}}期</span></li>
        <li><span class="leftIn">还款方式</span><span class="rightIn">等本等息</span></li>
        <li><span class="leftIn">每月还款金额</span><span class="rightIn">￥{{$monthly_payment}}</span></li>
        <li><span class="leftIn">每月手续费</span><span class="rightIn">￥{{$service_fees}}</span></li>
        <div class="empty"></div>
    </ul>
    <div class="column photoInfro">
        <img src="{{asset('img/wxkhd/zhaopianxinxi-tb.png')}}"/>
        <span class="name">照片信息</span>
        <span class="right"><i class="iconfont icon">&#xe600;</i></span>
    </div>
    <ul class="photo">
        <li class="idCardFace photoEare">
            <div class="inner">
                <div class="img">
                @if(!empty($orderInfo->cert_face_pic))
                    <img src="{{\App\Util\FileReader::read_storage_image_resize_file($orderInfo->cert_face_pic)}}"/>
                @else
                    <img src=""/>
                @endif
                </div>
            </div>
        </li>
        <li class="idCardFace photoEare">
            <div class="inner">
                <div class="img">
                    @if(!empty($orderInfo->cert_opposite_pic))
                        <img src="{{\App\Util\FileReader::read_storage_image_resize_file($orderInfo->cert_opposite_pic)}}"/>
                    @else
                        <img src=""/>
                    @endif
                </div>
            </div>
        </li>
        <li class="idCardFace photoEare">
            <div class="inner">
                <div class="img">
                    @if(!empty($orderInfo->cert_hand_pic))
                        <img src="{{\App\Util\FileReader::read_storage_image_resize_file($orderInfo->cert_hand_pic)}}"/>
                    @else
                        <img src=""/>
                    @endif
                </div>
            </div>
        </li>
        <li class="idCardBack  photoEare">
            <div class="inner">
                <div class="img">
                    @if(!empty($orderInfo->credit_auth_pic))
                        <img src="{{\App\Util\FileReader::read_storage_image_resize_file($orderInfo->credit_auth_pic)}}"/>
                    @else
                        <img src=""/>
                    @endif
                </div>
            </div>
        </li>

        <li class="idCardBack  photoEare">
            <div class="inner">
                <div class="img">
                    @if(!empty($orderInfo->contract_pic))
                        <img src="{{\App\Util\FileReader::read_storage_image_resize_file($orderInfo->contract_pic)}}"/>
                    @else
                        <img src=""/>
                    @endif
                </div>
            </div>
        </li>
        <li class="idCardFace photoEare">
            <div class="inner">
                <div class="img">
                    @if(!empty($orderInfo->bank_card_pic))
                        <img src="{{\App\Util\FileReader::read_storage_image_resize_file($orderInfo->bank_card_pic)}}"/>
                    @else
                        <img src=""/>
                    @endif
                </div>
            </div>
        </li>
        <li class="idCardFace photoEare">
            <div class="inner">
                <div class="img">
                    @if(!empty($orderInfo->work_pic))
                        <img src="{{\App\Util\FileReader::read_storage_image_resize_file($orderInfo->work_pic)}}"/>
                    @else
                        <img src=""/>
                    @endif
                </div>
            </div>
        </li>
        <div class="empty"></div>
    </ul>
    <div class="column eshopInfro">
        <img src="{{asset('img/wxkhd/diangshangmima-tb.png')}}"/>
        <span class="name">电商信息</span>
        <span class="right"><i class="iconfont icon">&#xe600;</i></span>
    </div>
    <ul class="jdtb">
        <li><span class="leftIn">京东账号</span><span class="rightIn" id="jdAccount">{{$orderInfo->jd_account}}</span></li>
        <li><span class="leftIn">淘宝账号</span><span class="rightIn" id="tbAccount">{{$orderInfo->tb_account}}</span></li>
        <div class="empty"></div>
    </ul>

    {{--@if(!$is_cache)--}}
        {{--<input type="button" name="cancel_btn" id="cancel_btn" value="申请取消订单" class="cancel_btn"/>--}}
    {{--@else--}}
    {{--@endif--}}
</div>

<script src="{{asset('js/wxkhd-js/sliderDown.js')}}" type="text/javascript" charset="utf-8"></script>
<script>

    $("#cancel_btn").click(function(){
        $.ajax({
            type: "POST",
            url: "/wx/order/cancel-contract",
            data: "contractNo="+{{$orderInfo->contract_no}},
            success: function(data){
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
                    btn:['知道了',''],
                    yes:function(){

                    }
                })

                $(".tips_2").html(data.msg);

                $('.layui-layer-btn0').click(function(){
                    window.location.href = '/wx/order/list';
                })
            }
        });
    })

</script>
@endsection

