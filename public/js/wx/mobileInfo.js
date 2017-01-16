/*$("#phoneNum").bind('keyup',function(){
    var mobileReg = /^(((13[0-9]{1})|(14[0-9]{1})|(15[0-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
    var mobile = $("#phoneNum").val();
    if(!mobileReg.test(mobile)){
        $("#verify_but").css({
            "background":"",
            "color":"#ccc"
        });
        $("#verify_but").attr("disabled",true);
        $("#mobile_code").attr("readonly",true);
        return false;
    }else {
        $("#verify_but").css({
            "background":"#84dad1",
            "color":"white"
        });

        $("#verify_but").attr("disabled",false);
        $("#mobile_code").attr("readonly",false);
    }
});*/

var bind_name = 'input';
if (navigator.userAgent.indexOf("MSIE") != -1){
    bind_name = 'propertychange';
}
$("#phoneNum").bind(bind_name,function(){
    var mobileReg = /^(((13[0-9]{1})|(14[0-9]{1})|(15[0-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
    var mobile = $(this).val();
    if(!mobileReg.test(mobile)){
        $("#verify_but").css({
            "border":"1px solid #ccc",
            "color":"#ccc"
        });
        $("#verify_but").attr("disabled",true);
        $("#mobile_code").attr("readonly",true);
        return false;
    }else {
        $("#verify_but").css({
            "border":"1px solid #f7d568",
            "color":"#f7d568"
        });

        $("#verify_but").attr("disabled",false);
        $("#mobile_code").attr("readonly",false);
    }
})


$("#verify_but").click(function(){
    countDown($("#verify_but"));
    $.ajax({
        url:'/wx/loan/send-code',
        type:'post',
        data:{'mobile':$('#phoneNum').val()},
        dataType:'json',
        success:function(data){
            if(data.status){
                return true;
            }else{
                return false;
            }
        }
    });
});
// 手机验证码倒计时
var wait=60;
function countDown(obj) {
    if (wait == 0) {
        obj.attr("style", "color:#f7d568;border:1px solid #f7d568;font-size:0.18rem");
        obj.val("点击获取");
        wait = 60;
    } else {
        obj.attr("style","color:#808080;border:1px solid #ccc;background:#ccc;font-size:0.18rem");
        obj.val("重新获取(" + wait + ")");
        wait--;
        setTimeout(function() {
            countDown(obj)
        },1000)
    }
}

$('#nextBtn_pro').click(function(){
    var applicant_name = $("#applicant_name").val();
    var applicant_nameReg = /^([\u4e00-\u9fa5]|[·])+$/;
    var applicant_id_card = $("#applicant_id_card").val();
    var mobile = $("#phoneNum").val();
    var mobile_code = $("#mobile_code").val();
    var mobileReg = /^(((13[0-9]{1})|(14[0-9]{1})|(15[0-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
    //姓名
    if(!applicant_name.match(applicant_nameReg)){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请输入正确的姓名</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了',''],
            zIndex:"19900113"
        })
        return false;
    }

    //身份证正则
    var applicant_id_card = $("#applicant_id_card").val();
    var idCarReg = /^(^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$)|(^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])((\d{4})|\d{3}[Xx])$)$/;
    if(!(idCarReg.test(applicant_id_card)) || applicant_id_card == ""){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请填写正确的身份证</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了',''],
            zIndex:"19900113"
        })
        return false;
    }

    if(!mobileReg.test(mobile)){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请输入正确的手机号</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了',''],
            zIndex:"19900113"
        })
        return false;
    }

    //验证码不能为空
    if(!mobile_code || mobile_code==null || mobile_code == undefined){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请输入验证码</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了',''],
            zIndex:"19900113"
        })
        return false;
    }

    if(!check_mobile_code()){
        return false;
    }

    var profession = $("#professionType").val();
    if(profession == 0){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请选择行业</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了',''],
            zIndex:"19900113"
        })
        return false;
    }else{
        var professionType = $('#professionType option:selected').text();
        $("#industry_name").val(professionType);
    }

    var reference = $("#reference").val();
    if(reference && (!/^[\u4e00-\u9fa5]+$/gi.test(reference))){
        $("#reference").val("");
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请输入正确的推荐人</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了',''],
            zIndex:"19900113"
        })
        return false;
    }
    $(".nextBtn").attr("disabled",true);
    $(".nextBtn").css("background","#ccc");

    //$("form").submit();
    //验证用户是否有效
    $.ajax({
        url:'/wx/loan/check-user',
        type:'post',
        data: {"applicant_name":applicant_name,"applicant_id_card":applicant_id_card},
        dataType:'json',
        success:function(data){
            //console.log(data);return false;
            if(data.status == 1){
                $("form").submit();
            }else{
                layer.open({
                    skin: 'oAlterWindow',
                    title:'小提示',
                    fix: false,
                    offset:['120px',''],
                    shade: [0.8, '#000'],
                    shadeClose: true,
                    maxmin: true,
                    content: "<p class='tips_1'></p><p class='tips_2'>您距离上次在我司办理分期未满3个月，暂时不能再办理分期！</p>",
                    bgcolor:'red',
                    closeBtn:0,
                    btn:['知道了',''],
                    zIndex:"19900113"
                })
                $(".nextBtn").css("background","rgb(235,33,107)");
                $(".nextBtn").attr("disabled",false);
                $(".nextBtn").text('下一步');
            }
        },
        error:function(){
            layer.closeAll();
            layer.open({
                skin: 'oAlterWindow',
                title:'小提示',
                fix: false,
                offset:['120px',''],
                shade: [0.8, '#000'],
                shadeClose: true,
                maxmin: true,
                content: "<p class='tips_1'></p><p class='tips_2'>请求接口异常，请稍后重试</p>",
                bgcolor:'red',
                closeBtn:0,
                btn:['知道了','']
            })
            $(".nextBtn").css("background","rgb(235,33,107)");
            $(".nextBtn").attr("disabled",false);
            $(".nextBtn").text('下一步');
        }
    });
});

//异步验证手机验证码
function check_mobile_code(){
    var flag = false;
    $.ajax({
        url:'/wx/loan/check-code',
        type:'post',
        async: false,//设置为同步才能获取flag的值
        data:{'mobile':$("#phoneNum").val(),'mobile_code':$("#mobile_code").val()},
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
                    btn:['知道了',''],
                    zIndex:"19900113"
                })
                $(".tips_2").html(data.msg);
                flag = false;
            }else{
                flag = true;
            }
        }
    });
    return flag;
}


//选择行业以后字体颜色变深
$("#professionType").change(function(){
    var proType = $("#professionType").val();
    if(proType == 0){
        $("#professionType").css("color","#bebebe");
    }else{
        $("#professionType").css({
            "color":"#000",
            "font-family": "微软雅黑"
        });
    }
})
$("#applicant_name").keyup(function(){
    $(this).css("color","#000");
});
$("#applicant_id_card").keyup(function(){
    $(this).css("color","#000");
});
$("#applicant_id_card").focus(function(){
    $(this).val("");
})
$("#applicant_id_card").focus(function(){
    $(this).val("");
});

$(function() {
    //表单必填项内容为非空下一步按钮才可用
    function enableSubmit(bool) {
        if (bool) {
            $(".nextBtn").removeAttr("disabled");
            $(".nextBtn").css('background', '#ea216a')
        }
        else {
            $(".nextBtn").attr("disabled", "disabled");
            $(".nextBtn").css('background', '#ccc')
        }
    }

    //验证多少项，多少个false
    var flags = [false, false, false, false, false];
    //flags全为true时提交按钮解除禁用
    function v_submitbutton() {
        //console.log(flags);
        for (f in flags) {
            if (!flags[f]) {
                enableSubmit(false);
                return;
            }
            enableSubmit(true);
        }
    }

    var bind_name = 'input';
    if (navigator.userAgent.indexOf("MSIE") != -1) {
        bind_name = 'propertychange';
    }
    //姓名不为空
    function v_applicant_name() {
        var applicant_name = $("#applicant_name").val();
        if (applicant_name == "") {
            flags[0] = false;
            enableSubmit(false);
        } else {
            flags[0] = true;
        }
        v_submitbutton();
    }

    $("#applicant_name").blur(function () {
        v_applicant_name();
    })
    $("#applicant_name").bind(bind_name, function () {
        v_applicant_name();
    })

    //身份证不为空
    function v_applicant_id_card() {
        var applicant_id_card = $("#applicant_id_card").val();
        if (applicant_id_card == "") {
            flags[1] = false;
            enableSubmit(false);
        } else {
            flags[1] = true;
        }
        v_submitbutton();
    }

    $("#applicant_id_card").blur(function () {
        v_applicant_id_card();
    })
    $("#applicant_id_card").bind(bind_name, function () {
        v_applicant_id_card();
    })

    //手机号码不为空
    function v_phoneNum() {
        var phoneNum = $("#phoneNum").val();
        if (phoneNum == "") {
            flags[2] = false;
            enableSubmit(false);
        } else {
            flags[2] = true;
        }
        v_submitbutton();
    }

    $("#phoneNum").blur(function () {
        v_phoneNum();
    })
    $("#phoneNum").bind(bind_name, function () {
        v_phoneNum();
    })

    //验证码不为空
    function v_mobile_code() {
        var mobile_code = $("#mobile_code").val();
        if (mobile_code == "") {
            flags[3] = false;
            enableSubmit(false);
        } else {
            flags[3] = true;
        }
        v_submitbutton();
    }

    $("#mobile_code").blur(function () {
        v_mobile_code();
    })
    $("#mobile_code").bind(bind_name, function () {
        v_mobile_code();
    })

    //行业类别不为空
    function v_professionType() {
        var professionType = $("#professionType").val();
        if (professionType == 0) {
            flags[4] = false;
            enableSubmit(false);
        } else {
            flags[4] = true;
        }
        v_submitbutton();
    }

    $("#professionType").blur(function () {
        v_professionType();
    })
    $("#professionType").change(function () {
        v_professionType();
    })

    v_applicant_name();
    v_applicant_id_card();
    v_phoneNum();
    v_mobile_code();
    v_professionType();
})

