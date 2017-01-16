@extends('_layouts.default_wx')

@section('content')
<div>
    <div id="validate" class="content">
        <div class="titleCom presonInfo">
            <p>
                <span class="logoPic iconPerson"></span>
                <span class="title">个人信息</span>
            </p>
        </div>
        <form name="register" id="register" class="validateIn" action="/wx/loan/do-mobile" method="post">
            <input type="hidden" name="_token" value="{{csrf_token()}}">
            <input type="hidden" name="industry_name" id="industry_name" value="">
            <div class="column name">
                <span><i class="star">*</i>姓名</span>
                @if(is_object($product_obj))
                    <input type="text" id="applicant_name" name="applicant_name" value="{{$product_obj->applicant_name}}" maxlength="120" placeholder="请输入真实姓名"/>
                @else
                    <input type="text" id="applicant_name" name="applicant_name" value="" maxlength="120" placeholder="请输入真实姓名"/>
                @endif
            </div>
            <div class="column idCard">
                <span><i class="star">*</i>身份证号</span>
                @if(is_object($product_obj))
                    <input type="text" id="applicant_id_card" name="applicant_id_card" value="{{$product_obj->applicant_id_card}}" maxlength="120" placeholder="请输入身份证号"/>
                @else
                    <input type="text" id="applicant_id_card" name="applicant_id_card" value="" maxlength="120" placeholder="请输入身份证号"/>
                @endif
            </div>
            <div class="phoneNum column">
                <span><i class="star">*</i>手机号码</span>
                <input type="text" name="mobile" id="phoneNum" placeholder="请输入您的手机号" maxlength="11" />
            </div>
            <div class="column valCode">
                <span><i class="star">*</i>验证码</span>
                <input type="text" name="mobile_code" id="mobile_code" required="required" onkeyup='this.value=this.value.replace(/\D/gi,"")' readonly="readonly" placeholder="请输入6位数字验证码" maxlength="6"/>
                <input type="button" id="verify_but"  value="获取验证码" disabled="disabled"/>
            </div>

            <div class="column profession selArrow">
                <span><i class="star">*</i>行业类别</span>
                <div class="areaPro">
                    <select @if(is_object($mobile_obj) && $mobile_obj->industry_no>0)style="color:#000"@endif name="industry_no" id="professionType" class="selectPro">
                        <option value="0">请选择行业类别</option>
                        @if(is_object($mobile_obj))
                            <option value="3" @if($mobile_obj->industry_no==3) selected='selected' @endif>建筑业</option>
                            <option value="4" @if($mobile_obj->industry_no==4) selected='selected' @endif>文化娱乐</option>
                            <option value="5" @if($mobile_obj->industry_no==5) selected='selected' @endif>教育</option>
                            <option value="6" @if($mobile_obj->industry_no==6) selected='selected' @endif>金融机构</option>
                            <option value="7" @if($mobile_obj->industry_no==7) selected='selected' @endif>政府机构</option>
                            <option value="8" @if($mobile_obj->industry_no==8) selected='selected' @endif>互联网</option>
                            <option value="9" @if($mobile_obj->industry_no==9) selected='selected' @endif>传统制造业</option>
                            <option value="14" @if($mobile_obj->industry_no==14) selected='selected' @endif>房地产</option>
                            <option value="15" @if($mobile_obj->industry_no==15) selected='selected' @endif>个体</option>
                            <option value="18" @if($mobile_obj->industry_no==18) selected='selected' @endif>商业服务</option>
                            <option value="12" @if($mobile_obj->industry_no==12) selected='selected' @endif>其他</option>
                        @else
                            <option value="3">建筑业</option>
                            <option value="4">文化娱乐</option>
                            <option value="5">教育</option>
                            <option value="6">金融机构</option>
                            <option value="7">政府机构</option>
                            <option value="8">互联网</option>
                            <option value="9">传统制造业</option>
                            <option value="14">房地产</option>
                            <option value="15">个体</option>
                            <option value="18">商业服务</option>
                            <option value="12">其他</option>
                        @endif
                    </select>
                </div>
            </div>
            <div class="empty"></div>
            <div class="column referrer">
                <span>推荐人(选填)</span>
                @if(is_object($mobile_obj))
                    <input type="text" id="reference" name="reference" value="{{$mobile_obj->reference}}" maxlength="120" placeholder="请输入推荐人"/>
                @else
                    <input type="text" id="reference" name="reference" maxlength="120" placeholder="请输入推荐人"/>
                @endif
            </div>
            <input type="button" class="nextBtn" id="nextBtn_pro" value="下一步" disabled="disabled">
        </form>
    </div>
    <script src="{{asset('js/wx/mobileInfo.js')}}" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript">
        $(function(){
            pushHistory();
            window.addEventListener("popstate", function(e) {
                window.location.href = '/wx/loan/mcode';
            }, false);
            function pushHistory() {
                var state = {title: "title",  url: "#" };
                window.history.pushState(state, "title", "#");
            }
        })
    </script>
</div>
@endsection
