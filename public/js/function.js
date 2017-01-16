function update_submit(path, params, method) {
    method = method || "post";
    //如果没有特别声明，默认post方式
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);


    for (var key in params) {
        var hiddenField = document.createElement("input");
        hiddenField.setAttribute("type", "hidden");
        hiddenField.setAttribute("name", key);
        hiddenField.setAttribute("value", params[key]);
        form.appendChild(hiddenField);
    }

    document.body.appendChild(form);
    form.submit();
}

/**
 * 写一个按钮类，防止多次触发
 * @constructor
 */
function SubmitButton(config){
    var defaultConfig = {
        prepositionJudge:function(){
            return true;
        },
        callback:function(obj,data){
            if(data.status){
                location.href=obj.config.redirectTo;
            }else{
                alert(data.data);
            }
        },
        error:function(){

        },
        lockTips:function(){}, //被锁定后触发的提示
        url:'',
        data:function(){
            return {}
        },
        selectorStr:'',
        redirectTo:'',
        type:'post'
    }
    this.target = '';
    this.config = $.extend(defaultConfig,config);
    this.lock = false;
    this.selector = $(this.config.selectorStr);
    (function(a){
        a.selector.bind('click',function(){
            if(a.lock === true){
                a.config.lockTips();
                return;
            }else{
                a.target = this;
                a.lock = true;
                a.selector.addClass('submit-dis');
            }
            /*前置判断,如果前置判断返回true,则继续执行，否则中段*/
            if(!a.config.prepositionJudge(a.target)){
                a.lock = false;
                a.selector.removeClass('submit-dis');
                return;
            }

            switch(a.config.type){
                case 'post':
                    $.post(a.config.url, a.config.data(a.target),function(data){
                        /*回调函数*/
                        a.config.callback(a,data);
                        a.lock = false; //更改锁状态
                        a.selector.removeClass('submit-dis');
                    },'json');
                    break;
                case 'get':
                    $.get(a.config.url, a.config.data(a.target),function(data){
                        /*回调函数*/
                        a.config.callback(a,data);
                        a.lock = false; //更改锁状态
                        a.selector.removeClass('submit-dis');
                    },'json');
                    break;
                case 'ajax':
                    $.ajax({
                        type: "post",
                        url: a.config.url,
                        data: a.config.data(a.target),
                        dataType: "json",
                        success: function (data) {
                            a.lock = false; //更改锁状态
                            a.config.callback(a,data);
                            a.selector.removeClass('submit-dis');
                        },
                        error: function () {
                           // a.config.callback(a,data);
                           // alert('网络异常');
                            a.config.error();
                            a.lock = false; //更改锁状态
                            a.selector.removeClass('submit-dis');
                        }
                    });
                    break;
            }
        })
    })(this);
}

/**
 *  最好别用，不兼容IOS
 * @param config
 * @constructor
 */
function SubmitBtn(config){
    var defaultConfig = {
        useDelegate:false, //是否使用代理，默认用body代理
        prepositionJudge:function(){
            return true;
        },
        callback:function(obj,data){
            if(data.status){
                location.href=obj.config.redirectTo;
            }else{
                alert(data.data);
            }
        },
        lockTips:function(){}, //被锁定后触发的提示
        error:function(){
            alert('网络异常！');
        },
        url:'',
        data:function(){
            return {}
        },
        selectorStr:'',
        redirectTo:'',
        type:'post'
    }
    this.target = '';
    this.config = $.extend(defaultConfig,config);
    this.lock = false;
    this.selector = $(this.config.selectorStr);
    (function(a){
        if(a.config.useDelegate){
            $('body').on('click', a.config.selectorStr,function(){
                if(a.lock === true){
                    a.config.lockTips();
                    return;
                }else{
                    a.target = this;
                    a.lock = true;
                    a.selector.addClass('submit-dis');
                }
                /*前置判断,如果前置判断返回true,则继续执行，否则中段*/
                if(!a.config.prepositionJudge(a.target)){
                    a.lock = false;
                    a.selector.removeClass('submit-dis');
                    return;
                }

                $.ajax({
                    url: a.config.url,
                    type: a.config.type,
                    dataType:'json',
                    async:false, //如果是非阻塞的那我就用promise了
                    data: a.config.data(a.target),
                    success:function(data){
                        /*回调函数*/
                        a.config.callback(a,data);
                        a.lock = false; //更改锁状态
                        a.selector.removeClass('submit-dis');
                    },
                    error:function(){
                        a.config.error();
                        a.lock = false; //更改锁状态
                    }
                });
            });
        }else{
            a.selector.bind('click',function(){
                if(a.lock === true){
                    a.config.lockTips();
                    return;
                }else{
                    a.target = this;
                    a.lock = true;
                    a.selector.addClass('submit-dis');
                }
                /*前置判断,如果前置判断返回true,则继续执行，否则中段*/
                if(!a.config.prepositionJudge(a.target)){
                    a.lock = false;
                    a.selector.removeClass('submit-dis');
                    return;
                }

                $.ajax({
                    url: a.config.url,
                    type: a.config.type,
                    dataType:'json',
                    async:false, //如果是非阻塞的那我就用promise了
                    data: a.config.data(a),
                    success:function(data){
                        /*回调函数*/
                        a.config.callback(a,data);
                        a.lock = false; //更改锁状态
                        a.selector.removeClass('submit-dis');
                    },
                    error:function(){
                        a.config.error();
                        a.lock = false; //更改锁状态
                    }
                });
            });
        }
    })(this);
}