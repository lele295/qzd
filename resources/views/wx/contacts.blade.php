@extends('_layouts.default_wx')

@section('content')
<div id="family_people" class="content">
    <div class="failure">
        <div class="failure_img">
            <img src="{{asset('img/wxSecond/failure.png')}}" alt="" />
        </div>
        <p class="failure_title">授权失败</p>
        <p class="failure_con">请提供一个您同事或者朋友的联系方式！</p>
    </div>

    <form action="/wx/loan/do-contacts" method="post">
        <input type="hidden" id="other_contact_relation" name="other_contact_relation" value="">

        <div class="titleCom">
            <p>
                <span class="logoPic iconfamily_people"></span>
                <span class="title">其他联系人</span>
            </p>
        </div>
        <div class="immediate column">
            <span><i class="star">*</i>联系人关系</span>
            <div class="area">
                <select style="color:#000" name="other_contact_relation_no" id="other_contact_relation_no" class="select">
                    <option value="0" style="color:#ccc;">请选择联系人关系</option>
                    @foreach($other_contact_relation as $vo)
                        <option value="{{$vo->ITEMNO}}"  @if(is_object($other_contact_obj) && $other_contact_obj->other_contact_relation_no==$vo->ITEMNO) selected='selected' @endif>
                            {{$vo->ITEMNAME}}
                        </option>
                   @endforeach
                </select>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="immediate column">
            <span><i class="star">*</i>联系人姓名</span>
            @if(is_object($other_contact_obj))
                <input type="text" id="other_contact_name" name="other_contact_name" value="{{$other_contact_obj->other_contact_name}}" placeholder="请输入联系人的姓名" maxlength="20"/>
            @else
                <input type="text" id="other_contact_name" name="other_contact_name" placeholder="请输入联系人的姓名" maxlength="20"/>
            @endif
        </div>
        <div class="clearfix"></div>
        <div class="contact column">
            <span><i class="star">*</i>联系人手机</span>
            @if(is_object($other_contact_obj))
                <input type="text" id="other_contact_mobile" name="other_contact_mobile" value="{{$other_contact_obj->other_contact_mobile}}" placeholder="请输入联系人手机" maxlength="11"/>
            @else
                <input type="text" id="other_contact_mobile" name="other_contact_mobile" placeholder="请输入联系人手机" maxlength="11"/>
            @endif
        </div>
        <div class="clearfix"></div>
        <input type="button" id="next_pic" value="下一步" class="nextBtn"/>
    </form>
</div>

<script type="text/javascript" src="{{asset('js/wx/contacts.js')}}"></script>
<script type="text/javascript">
    $(function(){
        pushHistory();
        window.addEventListener("popstate", function(e) {
            window.location.href = '/wx/loan/phone-pwd';
        }, false);
        function pushHistory() {
            var state = {title: "title",  url: "#" };
            window.history.pushState(state, "title", "#");
        }
    })
</script>
@endsection
