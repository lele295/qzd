$("#next_pic").click(function(){
    var edu_level_no = $("#edu_level_no").val();
    var qq_email = $("#qq_email").val();
    var family_relation_no = $("#family_relation_no").val();
    var family_name = $("#family_name").val();
    var family_mobile = $("#family_mobile").val();

    var family_addr1 = $("#family_addr1").val();
    var family_addr2 = $("#family_addr2").val();
    var family_addr3 = $("#family_addr3").val();
    var family_addr4 = $("#family_addr4").val();
    var family_addr5 = $("#family_addr5").val();

    var isQq = /^\d{4,12}$/;
    var isMob = /^(((13[0-9]{1})|(14[0-9]{1})|(15[0-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
    var chinese = /^[\u4e00-\u9fa5]/;

    //最高学历
    if(edu_level_no == 0 || edu_level_no == null || edu_level_no == '' || edu_level_no == undefined){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请选择学历</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了','']
        })
        return false;
    }else{
        var edu_level = $('#edu_level_no option:selected').text();
        $("#edu_level").val(edu_level);
    }

    //qq验证
    if(!isQq.test(qq_email)){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请输入正确的qq</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了','']
        })
        return false;
    }

    //亲属关系
    if(family_relation_no == 0 || family_relation_no == null || family_relation_no == '' || family_relation_no == undefined){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请选择亲属关系</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了','']
        })
        return false;
    }else{
        var family_relation = $('#family_relation_no option:selected').text();
        $("#family_relation").val(family_relation);
    }

   if(!chinese.test(family_name)){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请填写正确的亲属姓名</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了','']
        })
        return false;
    }else{
        $("#family_name").css("color","#000");
    }

    if(!isMob.test(family_mobile)){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请填写正确的亲属手机</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了','']
        })
        return false;
    }else{
        $("#family_mobile").css("color","#000");
    }

    if(!family_addr1){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请填写家庭住址</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了','']
        })
        return false;
    }

    if(!family_addr2){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请填写家庭住址所在区或县</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了','']
        })
        return false;
    }

    if(!family_addr3){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请填写家庭住址所在街道或者乡镇</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了','']
        })
        return false;
    }

    if(!family_addr4){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请填写家庭详细住址</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了','']
        })
        return false;
    }

    if(!family_addr5){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请填写家庭住址门牌号</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了','']
        })
        return false;
    }

    $("#next_pic").attr("disabled",true);
    $(this).val('正在提交数据...');
    $("#next_pic").css("background","#ccc");

    $("form").submit();
})

$("#edu_level_no").change(function(){
    var edu_level_no = $('#edu_level_no option:selected').val();
    if(edu_level_no > 0){
        $("#edu_level_no").css("color","#000");
    }else{
        $("#edu_level_no").css("color","");
    }
})

$("#family_relation_no").change(function(){
    var family_relation_no = $('#family_relation_no option:selected').val();
    if(family_relation_no > 0){
        $("#family_relation_no").css("color","#000");
    }else{
        $("#family_relation_no").css("color","");
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
    var flags = [false,false,false,false,false,false,false,false,false];
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

    //最高学历
    $("#edu_level_no").blur(function () {
        v_names($("#edu_level_no"), 0);
    })
    $("#edu_level_no").bind(bind_name, function () {
        v_names($("#edu_level_no"), 0);
    })

    //qq
    $("#qq_email").blur(function () {
        v_names($("#qq_email"), 1);
    })
    $("#qq_email").bind(bind_name, function () {
        v_names($("#qq_email"), 1);
    })

    //亲属关系
    $("#family_relation_no").blur(function () {
        v_names($("#family_relation_no"), 2);
    })
    $("#family_relation_no").bind(bind_name, function () {
        v_names($("#family_relation_no"), 2);
    })

    //亲属姓名
    $("#family_name").blur(function () {
        v_names($("#family_name"), 3);
    })
    $("#family_name").bind(bind_name, function () {
        v_names($("#family_name"), 3);
    })

    //亲属手机
    $("#family_mobile").blur(function () {
        v_names($("#family_mobile"), 4);
    })
    $("#family_mobile").bind(bind_name, function () {
        v_names($("#family_mobile"), 4);
    })

    //家庭住址
    /*$("#family_addr1").blur(function () {
        console.log($("#family_addr1").val());
        v_names($("#family_addr1"), 5);
    })
    $("#family_addr1").bind(bind_name, function () {
        v_names($("#family_addr1"), 5);
    })*/

    $("#family_addr2").blur(function () {
        v_names($("#family_addr2"), 5);
    })
    $("#family_addr2").bind(bind_name, function () {
        v_names($("#family_addr2"), 5);
    })

    $("#family_addr3").blur(function () {
        v_names($("#family_addr3"), 6);
    })
    $("#family_addr3").bind(bind_name, function () {
        v_names($("#family_addr3"), 6);
    })

    $("#family_addr4").blur(function () {
        v_names($("#family_addr4"), 7);
    })
    $("#family_addr4").bind(bind_name, function () {
        v_names($("#family_addr4"), 7);
    })

    $("#family_addr5").blur(function () {
        v_names($("#family_addr5"), 8);
    })
    $("#family_addr5").bind(bind_name, function () {
        v_names($("#family_addr5"), 8);
    })


    v_names($("#edu_level_no"), 0);
    v_names($("#qq_email"), 1);
    v_names($("#family_relation_no"), 2);
    v_names($("#family_name"), 3);
    v_names($("#family_mobile"), 4);

    v_names($("#family_addr2"), 5);
    v_names($("#family_addr3"), 6);
    v_names($("#family_addr4"), 7);
    v_names($("#family_addr5"), 8);
})
