$("#next_pic").click(function(){
    var other_contact_relation_no = $("#other_contact_relation_no").val();
    var other_contact_name = $("#other_contact_name").val();
    var other_contact_mobile = $("#other_contact_mobile").val();

    var isMob = /^(((13[0-9]{1})|(14[0-9]{1})|(15[0-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
    var chinese = /^[\u4e00-\u9fa5]/;

    //联系人关系不能为空
    if(other_contact_relation_no == 0 || other_contact_relation_no == null || other_contact_relation_no == '' || other_contact_relation_no == undefined){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: true,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请选择联系人关系</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了','']
        })
        return false;
    }else{
        var other_contact_relation = $('#other_contact_relation_no option:selected').text();
        $("#other_contact_relation").val(other_contact_relation);
    }

    //联系人姓名必须是汉字
   if(!chinese.test(other_contact_name)){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: true,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请填写正确的联系人姓名</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了','']
        })
        return false;
    }else{
        $("#other_contact_name").css("color","#000");
    }

    //验证联系人手机
    if(!isMob.test(other_contact_mobile)){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: true,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请填写正确的联系人手机号码</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了','']
        })
        return false;
    }else{
        $("#other_contact_mobile").css("color","#000");
    }

    $("#next_pic").attr("disabled",true);
    $(this).val('正在提交数据...');
    $("#next_pic").css("background","#ccc");

    $("form").submit();
})

$("#other_contact_relation_no").change(function(){
    var other_contact_relation_no = $('#other_contact_relation_no option:selected').val();
    if(other_contact_relation_no > 0){
        $("#other_contact_relation_no").css("color","#000");
    }else{
        $("#other_contact_relation_no").css("color","");
    }
})

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

//验证多少项，就有多少个false
    var flags = [false,false,false];
//flags全为true时提交按钮解除禁用
    function v_submitbutton() {
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

    function v_names(obj, i) {
        var name = obj.val();
        if (name == "" || name == 0) {
            flags[i] = false;
            enableSubmit(false);
        } else {
            flags[i] = true;
        }
        v_submitbutton();
    }

    //联系人关系
    $("#other_contact_relation_no").blur(function () {
        v_names($("#other_contact_relation_no"), 0);
    })
    $("#other_contact_relation_no").bind(bind_name, function () {
        v_names($("#other_contact_relation_no"), 0);
    })

    //其他联系人姓名
    $("#other_contact_name").blur(function () {
        v_names($("#other_contact_name"), 1);
    })
    $("#other_contact_name").bind(bind_name, function () {
        v_names($("#other_contact_name"), 1);
    })

    //其他联系人手机
    $("#other_contact_mobile").blur(function () {
        v_names($("#other_contact_mobile"), 2);
    })
    $("#other_contact_mobile").bind(bind_name, function () {
        v_names($("#other_contact_mobile"), 2);
    })


    v_names($("#other_contact_relation_no"), 0);
    v_names($("#other_contact_name"), 1);
    v_names($("#other_contact_mobile"), 2);

})
