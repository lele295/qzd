$("#comfirm_submit").click(function(){

    var jd_account = $("#jd_account").val();
    var jd_password = $("#jd_password").val();
    var tb_account = $("#tb_account").val();
    var tb_password = $("#tb_password").val();
    //要么输入一个完整的京东账号，要么不填写
    if((jd_account && jd_password) || (!jd_account && !jd_password)){

    }else{
        layer.closeAll();
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请输入完整的京东账户名和密码</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了','']
        })
        return false;
    }

    //要么输入一个完整的淘宝账号，要么不填写
    if((tb_account && tb_password) || (!tb_account && !tb_password)){

    }else{
        layer.closeAll();
        layer.open({
            skin: 'oAlterWindow',
            title:'小提示',
            fix: false,
            offset:['120px',''],
            shade: [0.8, '#000'],
            shadeClose: true,
            maxmin: true,
            content: "<p class='tips_1'></p><p class='tips_2'>请输入完整的淘宝账户名和密码</p>",
            bgcolor:'red',
            closeBtn:0,
            btn:['知道了','']
        })
        return false;
    }

    $(this).attr("disabled",true);
    $(this).css("background","#ccc");
    $(this).val('正在提交数据...');

    layer.open({
        skin:"loadWindow",
        title:0,
        shade: [0.8, '#000'],
        offset:['235px',''],
        content:"<p class='tips_1'><img class='loadingImg' src=''/></p><p class='dia_span tips_2'>提交中...</p>",
        closeBtn:0,
        btn:0
    })
    $('.loadingImg').attr('src',$('#loadingImg').val());

    //调用提单接口校验提单
    var ajaxTimeOut = $.ajax({
        url:'/wx/loan/check-order',
        type:'post',
        timeout:30000,//设置请求时间不能超过30秒
        data:{'jd_account':jd_account,'jd_password':jd_password,'tb_account':tb_account,'tb_password':tb_password},
        dataType:'json',
        success:function(data){
            if(data.order_res ==1 && data.order_photo_res == 1){
                layer.closeAll();
                layer.open({
                    skin:'applySuccessAlterWindow',
                    title:0,
                    offset:['150px','14%'],
                    shade: [0.8, '#000'],
                    shadeClose: false,
                    btn:'查看订单',
                    content: "<div class='txt1'><img class='applySuccessImg' src=''/></div><p class='txt2'>恭喜您申请成功！</p><p class='txt3'>预计审核需要20分钟，休息一下...</p>",
                    closeBtn:0,
                    yes:function(){
                        window.location.href = '/wx/order/list';
                    }
                })
                $("#orderStatus").val(2);
                $('.applySuccessImg').attr('src',$('#applySuccessImg').val());
            }else{
                layer.closeAll();
                layer.open({
                    skin: 'oAlterWindow',
                    title:'小提示',
                    fix: false,
                    offset:['120px',''],
                    shade: [0.8, '#000'],
                    shadeClose: true,
                    maxmin: true,
                    content: "<p class='tips_1'></p><p class='tips_2'>订单提交失败</p>",
                    bgcolor:'red',
                    closeBtn:0,
                    btn:['知道了','']
                })
                $("#comfirm_submit").attr("disabled",false);
                $("#comfirm_submit").css("background","rgb(235,33,107)");
                $("#comfirm_submit").val('确认提交');
            }
        },
        complete:function(XMLHttpRequest,status){
            if(status == 'timeout'){
                ajaxTimeOut.abort();
                layer.closeAll();
                layer.open({
                    skin: 'oAlterWindow',
                    title:'小提示',
                    fix: false,
                    offset:['120px',''],
                    shade: [0.8, '#000'],
                    shadeClose: true,
                    maxmin: true,
                    content: "<p class='tips_1'></p><p class='tips_2'>请求超时，请稍后重试</p>",
                    bgcolor:'red',
                    closeBtn:0,
                    btn:['知道了','']
                })
                $("#comfirm_submit").attr("disabled",false);
                $("#comfirm_submit").css("background","rgb(235,33,107)");
                $("#comfirm_submit").val('确认提交');
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
                content: "<p class='tips_1'></p><p class='tips_2'>提单异常</p>",
                bgcolor:'red',
                closeBtn:0,
                btn:['知道了','']
            })
            //$("#comfirm_submit").attr("disabled",false);
            $("#comfirm_submit").val('确认提交');
        }
    });
});

