$("#next_pic").click(function(){
    var companyName = $("#companyName").val();
    var isPhone = /(^([0-9]{3,4}-)?[0-9]{7,8})|(^([0-9]{3,4})?[0-9]{7,8})$/;
    var isMob = /^(((13[0-9]{1})|(14[0-9]{1})|(15[0-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/;
    var isBankNum = /^\d{15,25}$/;
    var work_unit_mobile = $("#work_unit_mobile").val();
    var work_addr1 = $("#work_addr1").val();
    var work_addr2 = $("#work_addr2").val();
    var work_addr3 = $("#work_addr3").val();
    var work_addr4 = $("#work_addr4").val();
    var work_addr5 = $("#work_addr5").val();

    //账户信息
    var accountNum = $("#accountNum").val().replace(/\s/g,"").trim();
    var bankCode = $('#bank option:selected').val();
    var bank = $("#bank").val();
    var bankCity = $("#bankCity").val();
    var bankBranch = $("#bankBranch").val();

    if(!companyName){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请填写工作单位</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了','']
        })
        return false;
    }
    if((!isPhone.test(work_unit_mobile)) && (!isMob.test(work_unit_mobile))){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请填写正确的单位电话</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了','']
        })
        return false;
    } else{
        $("#work_unit_mobile").css("color","#000");
    }

    if(!work_addr1){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请填写单位地址</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了','']
        })
        return false;
    }else{

    }

    if(!work_addr2){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请填写单位地址所在区或县</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了','']
        })
        return false;
    }

    if(!work_addr3){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请填写单位地址所在街道或乡镇</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了','']
        })
        return false;
    }

    if(!work_addr4){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请填写详细单位地址</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了','']
        })
        return false;
    }

    if(!work_addr5){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请填写单位地址门牌号</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了','']
        })
        return false;
    }

    //账户信息不能为空
    if(!isBankNum.test(accountNum)){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请填写正确的代扣还款账号</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了','']
        })
        return false;
    }else{
        var work_deposit_bank = $('#bank option:selected').text();
        $("#work_deposit_bank").val(work_deposit_bank);
    }
    if(bank == 0 || bank == null || bank == '' || bank == undefined){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请选择开户银行</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了','']
        })
        return false;
    }

    if(!bankCity){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请选择开户银行城市</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了','']
        })
        return false;
    };
    /*if(bankBranch == 0 || bankBranch == null || bankBranch == '' || bankBranch == undefined){
        createErro();
        $("#oAlterWindow .oAlter P").html('请选择代扣开户银行支行');
        $("#oAlterWindow").show();
        return false;
    };*/
    var bankBranch = $('#bankBranch option:selected').text();
    $("#work_bank_branch_name").val(bankBranch);

    //var bank_card_pic = $("input[name=bank_card_pic]").val();
    var bankCardImg = $("#bankCardImg").attr('src');

    if (bankCardImg.indexOf('addPic') != -1) {
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请上传银行卡正面照片</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了',''],
            zIndex:"19900113"
        })
        return false;
    }

    $("#next_pic").attr("disabled",true);
    $(this).val('正在提交数据...');
    $("#next_pic").css("background","#ccc");

    //$("form").submit();
    //银行卡信息校验
    $.ajax({
        url:'/wx/loan/check-bank-account',
        type:'post',
        data:{accountNum:accountNum,bankCode:bankCode},
        dataType:'json',
        success:function(data){
            if(data.send_status == 1 && data.query_status ==1){
                $("form").submit();
            }else if(data.query_status == 0 && data.code == 1001){
                layer.open({
                    skin: 'oAlterWindow',
                    title:'小提示',
                    fix: false,
                    offset:['120px',''],
                    shade: [0.8, '#000'],
                    shadeClose: true,
                    maxmin: true,
                    content: "<p class='tips_1'></p><p class='tips_2'>正在验证银行卡，请稍后重试…</p>",
                    bgcolor:'red',
                    closeBtn:0,
                    btn:['知道了','']
                })
                $("#next_pic").attr("disabled",false);
                $("#next_pic").val('确认提交');
                $("#next_pic").css("background","rgb(235,33,107)");
            }else{
                layer.open({
                    skin: 'oAlterWindow',
                    title:'小提示',
                    fix: false,
                    offset:['120px',''],
                    shade: [0.8, '#000'],
                    shadeClose: true,
                    maxmin: true,
                    content: "<p class='tips_1'></p><p class='tips_2'>请确认银行卡信息与持卡人信息是否正确</p>",
                    bgcolor:'red',
                    closeBtn:0,
                    btn:['知道了','']
                })
                $("#next_pic").attr("disabled",false);
                $("#next_pic").val('确认提交');
                $("#next_pic").css("background","rgb(235,33,107)");
            }
        }
    });

})

$("#bank").change(function(){
    var bank = $('#bank option:selected').val();
    if(bank > 0){
        $("#bank").css("color","#000");
    }else{
        $("#bank").css("color","");
    }
})
$("#bankBranch").change(function(){
    var bankBranch = $('#bankBranch option:selected').val();
    if(bankBranch > 0){
        $("#bankBranch").css("color","#000");
    }else{
        $("#bankBranch").css("color","");
    }
})
$("#bankBranch").find('option').css('font-size','0.1rem');

$().ready(function() {
    var oldLength=0;
    var accountNum = $("#accountNum");
    accountNum.bind('change keyup', function() {
        var arr="";
        $this = $(this);
        var oldCode = $this.val();
        var re = /\s/g;
        var code = oldCode.replace(re,"");
        for(var i=0;i<code.length;i=i+4){
            arr += code.substring(i,i+4)+" ";
        }
        accountNum.val("");
        accountNum.val(arr.substring(0,arr.length-1));
    });
});

$().ready(function() {
    var oldLength=0;
    var creditCard = $("#creditCard");
    creditCard.bind('change keyup', function() {
        var arr="";
        $this = $(this);
        var oldCode = $this.val();
        var re = /\s/g;
        var code = oldCode.replace(re,"");
        for(var i=0;i<code.length;i=i+4){
            arr += code.substring(i,i+4)+" ";
        }
        creditCard.val("");
        creditCard.val(arr.substring(0,arr.length-1));
    });
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

//验证多少项，就有多少个false
    var flags = [false,false,false,false,false,false,false,false];
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

//工作单位
    $("#companyName").blur(function () {
        v_names($("#companyName"), 0);
    })
    $("#companyName").bind(bind_name, function () {
        v_names($("#companyName"), 0);
    })
//单位电话
    $("#work_unit_mobile").blur(function () {
        v_names($("#work_unit_mobile"), 1);
    })
    $("#work_unit_mobile").bind(bind_name, function () {
        v_names($("#work_unit_mobile"), 1);
    })
//工作单位地址
   /* $("#work_addr1").blur(function () {
        v_names($("#work_addr1"), 2);
    })
    $("#work_addr1").bind(bind_name, function () {
        v_names($("#work_addr1"), 2);
    })*/

    $("#work_addr2").blur(function () {
        v_names($("#work_addr2"), 2);
    })
    $("#work_addr2").bind(bind_name, function () {
        v_names($("#work_addr2"), 2);
    })

    $("#work_addr3").blur(function () {
        v_names($("#work_addr3"), 3);
    })
    $("#work_addr3").bind(bind_name, function () {
        v_names($("#work_addr3"), 3);
    })

    $("#work_addr4").blur(function () {
        v_names($("#work_addr4"), 4);
    })
    $("#work_addr4").bind(bind_name, function () {
        v_names($("#work_addr4"), 4);
    })

    $("#work_addr5").blur(function () {
        v_names($("#work_addr5"), 5);
    })
    $("#work_addr5").bind(bind_name, function () {
        v_names($("#work_addr5"), 5);
    })


//银行卡号
    $("#accountNum").blur(function () {
        v_names($("#accountNum"), 6);
    })
    $("#accountNum").bind(bind_name, function () {
        v_names($("#accountNum"), 6);
    })
//开户银行
    $("#bank").blur(function () {
        v_names($("#bank"), 7);
    })
    $("#bank").bind(bind_name, function () {
        v_names($("#bank"), 7);
    })
//开户银行城市
  /*  $("#bankCity").blur(function () {
        v_names($("#bankCity"), 8);
    })
    $("#bankCity").bind(bind_name, function () {
        v_names($("#bankCity"), 8);
    })*/

    v_names($("#companyName"), 0);
    v_names($("#work_unit_mobile"), 1);
    //v_names($("#work_addr1"), 2);
    v_names($("#work_addr2"), 2);
    v_names($("#work_addr3"), 3);
    v_names($("#work_addr4"), 4);
    v_names($("#work_addr5"), 5);

    v_names($("#accountNum"), 6);
    v_names($("#bank"), 7);
    //v_names($("#bankCity"), 8);
})

//工作单位提示
$('#companyName').focus(function(){
    $('.companyName_tips').show();
})
$('#companyName').blur(function(){
    $('.companyName_tips').hide();
})

//工作单位提示
$('#companyName').focus(function(){
    $('.companyName_tips').show();
})
$('#companyName').blur(function(){
    $('.companyName_tips').hide();
})
