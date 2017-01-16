@extends('_layouts.default_wx')

@section('content')
<div id="family" class="content">
    <div class="titleCom">
        <p>
            <span class="logoPic iconfamily"></span>
            <span class="title">家庭信息</span>
        </p>
    </div>
    <form action="/wx/loan/do-family" method="post">
        <input type="hidden" id="edu_level" name="edu_level" value="">
        <input type="hidden" id="family_relation" name="family_relation" value="">

        <div class="familyInfo">

            <div class="edu column  selArrow">
                <span><i class="star">*</i>最高学历</span>
                <div class="area">
                    <select style="color:#000" name="edu_level_no" id="edu_level_no" class="select">
                        <option value="0" style="color:#ccc;">请选择学历</option>
                        @foreach($edu_data as $edu)
                            <option value="{{$edu->ITEMNO}}"  @if(is_object($family) && $family->edu_level_no==$edu->ITEMNO) selected='selected' @endif>
                                {{$edu->ITEMNAME}}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="email column">
                <span><i class="star">*</i>QQ邮箱</span>
                @if(is_object($family))
                    <input type="email" name="qq_email" id="qq_email" value="{{\App\Service\Help::qqEmail2qq($family->qq_email)}}"  placeholder="请输入您的QQ" required="required" maxlength="12"/>
                @else
                    <input type="email" name="qq_email" id="qq_email" placeholder="请输入您的QQ" required="required" maxlength="12"/>
                @endif
                <span class="qq_last">@qq.com</span>
            </div>
            <div class="clearfix"></div>
            <div class="immediate column">
                <span><i class="star">*</i>亲属关系</span>
                <div class="area">
                    <select style="color:#000" name="family_relation_no" id="family_relation_no" class="select">
                        <option value="0" style="color:#ccc;">请选择亲属关系</option>
                        @foreach($family_relation as $vo)
                            <option value="{{$vo->ITEMNO}}" @if(is_object($family) && $family->family_relation_no==$vo->ITEMNO) selected='selected' @endif>
                                {{$vo->ITEMNAME}}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="immediate column">
                <span><i class="star">*</i>亲属姓名</span>
                @if(is_object($family))
                    <input type="text" id="family_name" name="family_name" value="{{$family->family_name}}" placeholder="请输入您亲人的姓名" maxlength="40"/>
                @else
                    <input type="text" id="family_name" name="family_name" placeholder="请输入您亲人的姓名" maxlength="40"/>
                @endif
            </div>
            <div class="clearfix"></div>
            <div class="contact column">
                <span><i class="star">*</i>亲属手机</span>
                @if(is_object($family))
                    <input type="text" id="family_mobile" name="family_mobile" value="{{$family->family_mobile}}" placeholder="请输入您亲人的姓名" maxlength="11"/>
                @else
                    <input type="text" id="family_mobile" name="family_mobile" placeholder="请输入您亲人的手机号码" maxlength="11"/>
                @endif
            </div>
            <div class="clearfix"></div>
            <div class="column">
                <span><i class="star">*</i>现居住地址</span>
                <div class="area">
                    @if(is_object($family))
                        <input id="family_addr1" name="family_addr1" value="{{$family->family_addr1}}" type="text"/>
                    @else
                        <input id="family_addr1" name="family_addr1" type="text"/>
                    @endif
                </div>
            </div>
            <div class="column">
                <span><i class="star">*</i>区/县</span>
                @if(is_object($family))
                    <input type="text" id="family_addr2" name="family_addr2" value="{{$family->family_addr2}}" maxlength="20" placeholder="请输入家庭住址所在区或县"/>
                @else
                    <input type="text" id="family_addr2" name="family_addr2" maxlength="20" placeholder="请输入家庭住址所在区或县"/>
                @endif
            </div>
            <div class="clearfix"></div>
            <div class="column">
                <span><i class="star">*</i>街道/乡镇</span>
                @if(is_object($family))
                    <input type="text" id="family_addr3" name="family_addr3" value="{{$family->family_addr3}}" maxlength="20" placeholder="请输入街道/乡镇"/>
                @else
                    <input type="text" id="family_addr3" name="family_addr3"  maxlength="20" placeholder="请输入街道/乡镇"/>
                @endif
            </div>
            <div class="clearfix"></div>
            <div class="column">
                <span><i class="star">*</i>详细住址</span>
                @if(is_object($family))
                    <input type="text" id="family_addr4" name="family_addr4" value="{{$family->family_addr4}}" maxlength="50" placeholder="请输入家庭详细住址"/>
                @else
                    <input type="text" id="family_addr4" name="family_addr4" maxlength="50" placeholder="请输入家庭详细住址"/>
                @endif
            </div>
            <div class="clearfix"></div>
            <div class="column">
                <span><i class="star">*</i>门牌号</span>
                @if(is_object($family))
                    <input type="text" id="family_addr5" name="family_addr5" value="{{$family->family_addr5}}" placeholder="请输入门牌号" maxlength="10"/>
                @else
                    <input type="text" id="family_addr5" name="family_addr5" placeholder="请输入门牌号" maxlength="10"/>
                @endif
            </div>
            <div class="clearfix"></div>
        </div>
        <input type="button" id="next_pic" value="下一步" class="nextBtn"/>
    </form>
</div>
    <script src="{{asset('js/wx/family.js')}}" type="text/javascript" charset="utf-8"></script>
    <script src="{{asset('js/wx/familyCity.js')}}" type="text/javascript" charset="utf-8"></script>

    <script type="text/javascript">
        $(function(){
            pushHistory();
            window.addEventListener("popstate", function(e) {
                window.location.href = '/wx/loan/work';
            }, false);
            function pushHistory() {
                var state = {title: "title",  url: "#" };
                window.history.pushState(state, "title", "#");
            }
        })
    </script>
@endsection
