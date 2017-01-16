$(".nextBtn").click(function(){
    var serveClass = $("#serveClass").val();
    var loanAmount = parseInt($("#loanAmount").val());
    var periods = $("#periods").val();

    if(serveClass == 0 || serveClass == ""){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请选择服务类型</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了','']
        })
        return false;
    }else{
        var service_type = $('.service_type_no option:selected').text();
        $("#service_type").val(service_type);
    }

    if(!loanAmount){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请填写分期金额</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了','']
        })
        return false;
    }

    if(periods == 0 || periods == "" || periods== null || periods== undefined){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请选择贷款期数</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了','']
        })
        return false;
    }

    //var contract_pic = $("input[name=contract_pic]").val();
    var contractImg = $("#contractImg").attr('src');

    if (contractImg.indexOf('addPic') != -1) {
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请上传手术合同照片</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了','']
        })
        return false;
    }

    $(".nextBtn").attr("disabled",true);
    $(this).val('正在提交数据...');
    $(".nextBtn").css("background","#ccc");

    $("form").submit();
});

$("#loanAmount").focus(function(){
    $(this).val("");
    //$("#periods option[value = '0']").selected = true;
});

$("#serveClass").change(function(){
    var serveClass = $("#serveClass").val();
    if(serveClass == 0){
        $("#serveClass").css("color","#bebebe");
    }else{
        $("#serveClass").css({
            "color":"#000",
            "font-family": "微软雅黑"
        });
    }
});
/*$("#pay_type").blur(function(){
    $("#pay_type").css({
        "color":"#000",
        "font-family": "微软雅黑"
    });
});*/
$("#periods").change(function(){
    var periods = $("#periods").val();
    if(periods == 0){
        $("#periods").css("color","#bebebe");
    }else{
        $("#periods").css({
            "color":"#000",
            "font-family": "微软雅黑"
        });
    }
})

function createSel(arr1,arr2){
    var sel = $("#periods");
    var b= arr1.length;
    for(i=0;i<b;i++){
        $("<option></option>").val(arr1[i]).text(arr2[i]).appendTo(sel);
    }
}
$("#periods").focus(function(){
    var num = parseInt($("#loanAmount").val());
    if(num >= 1000 && num <= 30000){
        $("#periods").empty();
        var arr1 = [0,6,9,12,15,18,24];
        var arr2 = ["请选择还款期数","6期","9期","12期","15期","18期","24期"];
        createSel(arr1,arr2);
    }else if(num>= 30001 && num<=50000){
        $("#periods").empty();
        var arr1 = [0,9,12,15,18,24];
        var arr2 = ["请选择还款期数","9期","12期","15期","18期","24期"];
        createSel(arr1,arr2);
    }else {
        $("#loanAmount").val("");
        $("#periods").empty();
        var arr1 = ["0"];
        var arr2 = ["请选择还款期数"];
        createSel(arr1,arr2);
    }
})
$("#loanAmount").focus(function(){
    $("#periods").empty();
    var arr1 = ["0"];
    var arr2 = ["请选择"];
    createSel(arr1,arr2);
})

function check_trial_data(){
    var loanAmount = parseInt($("#loanAmount").val());
    var periods = $("#periods").val();
    if(!loanAmount || periods == 0 || periods == "" || periods== null || periods== undefined){
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请输入分期金额和分期期数</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了',''],
            zIndex:"19900113"
        })
    }else{
        trial();
    }
}

function trial(){
    var loanAmount = parseInt($("#loanAmount").val());
    var periods = $("#periods").val();
    $.ajax({
        url:'/wx/loan/trial',
        type:'post',
        data:{loanAmount:loanAmount,periods:periods},
        dataType:'json',
        success:function(data){
            //console.log(data);
            $("#monthPay").text(data.monthly_payment+'元');
            $("#serviceFee").val(data.service_fees+'元');
        },
        error:function(){
            layer.open({
                skin: 'oAlterWindow',
                title:'小提示',
                fix: false,
                offset:['120px',''],
                shade: [0.8, '#000'],
                shadeClose: true,
                maxmin: true,
                content: "<p class='tips_1'></p><p class='tips_2'>试算异常</p>",
                bgcolor:'red',
                closeBtn:0,
                btn:['知道了',''],
                zIndex:"19900113"
            })
        }
    })
}

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
    var flags = [false, false, false];
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

    //验证服务类型选择与否
    function v_service_type_no() {
        var serveClass = $("#serveClass").val();
        if (serveClass == 0) {
            flags[0] = false;
            enableSubmit(false);
        } else {
            flags[0] = true;
        }
        v_submitbutton();
    }

    $("#serveClass").blur(function () {
        v_service_type_no()
    })
    $("#serveClass").change(function () {
        v_service_type_no()
    })
    //分期金额
    function v_loanAmount() {
        var loanAmount = $("#loanAmount").val();
        if (loanAmount == "") {
            flags[1] = false;
            enableSubmit(false);
        } else {
            flags[1] = true;
        }
        v_submitbutton();
    }

    $("#loanAmount").blur(function () {
        v_loanAmount()
    })
    $("#loanAmount").change(function () {
        v_loanAmount()
    })
    //分期期数
    function v_periods() {
        var periods = $("#periods").val();
        if (periods == 0 || periods == null) {
            flags[2] = false;
            enableSubmit(false);
        } else {
            flags[2] = true;
        }
        v_submitbutton();
    }

    $("#periods").blur(function () {
        v_periods()
    })
    $("#periods").change(function () {
        v_periods()
    })

    /*function v_contract_pic(){

     if(!contract_pic) {
     flags[0]=false;
     enableSubmit(false);
     }else{
     flags[0] = true;
     }
     v_submitbutton();
     }*/
    v_service_type_no();
    v_loanAmount();
    v_periods();
})
