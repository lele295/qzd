new function (){
	var _self = this;
	_self.width = 640;//设置默认最大宽度
	_self.fontSize = 100;//默认字体大小
	_self.widthProportion = function(){
        var p = (document.body&&document.body.clientWidth||document.getElementsByTagName("html")[0].offsetWidth)/_self.width;return p<0.5?0.5:(p>0.75?0.75:p);
    };
	
	_self.changePage = function(){
		document.getElementsByTagName("html")[0].setAttribute("style","font-size:"+_self.widthProportion()*_self.fontSize+"px !important");
	}
	_self.changePage();
	window.addEventListener("resize",function(){_self.changePage();},false);
};




/*common layer template*/
var myLayer = function(params){
    var me = {};
    me.params = params?params:{
        'layerCont':'',
        'hasShadowBg':false,
        'shadowClose':false,
        'funcs':{}
    };
    me.init = function(){
        me.layerCont = $(me.params.layerCont).clone();
        me.layerContainer = $('<div class="layer_container"></div>');
        me.layerContWrap = $('<div class="layer_cont_wrap"></div>');
        me.layerBg = $('<div class="bg"></div>');

        me.layerContainer.append(me.layerContWrap.append(me.layerCont.addClass('layer_cont')));

        if(me.params.hasShadowBg){
            me.layerContainer.prepend(me.layerBg);
        }
        if(me.params.shadowClose){
            me.layerBg.on('touchend',function(e){
                e.preventDefault();
                me.destory();
            });
        }
        /*handle func*/
        if(me.params.funcs){
            for(var o in me.params.funcs){
                me.layerContainer.find(o).on('touchend',{'func':me.params.funcs[o]},function(e){
                    e.preventDefault();
                    e.data.func();
                });
            }
        }
        $('body').append(me.layerContainer);
    }
    me.destory = function(){
        me.layerContainer.remove();
        me.layerCont.removeClass('layer_cont');
    }
    me.show = function(){
        if(me.params.layerCont){
            me.init();
        }
    }
    return me;
}

/*提示，弹窗*/
//tips('数据错误','tips_center',1500);
//tips('数据错误','tips_left',1500);
function tips(msg,className,time){
    $('.tips').remove();
    var tipsDiv = $('<div class="tips '+className+'"></div>');
    $('body').append(tipsDiv);
    tipsDiv.html(msg).addClass('tips_show');
    setTimeout(function(){
        tipsDiv.removeClass('tips_show').remove();
    },time);
}
/*获取验证码 1,div*/
function getCodefun(obj,phone,time){
    var me = {};
    me.obj = obj;
    me.phone = phone;
    me.wait= time;
    me.time = function(obj) { 
        if (me.wait == 0) { 
            obj.text("获取验证码"); 
            me.wait = time; 
        }else { 
            obj.text(me.wait + "s"); 
            me.wait--;
            setTimeout(function(){ 
                me.time(obj);
            },1000);
        } 
    } 
    me.getCodeBindEvent = function(){
        obj.on('tap',function(e){
            var p = me.phone;
            var num = (typeof(p) == 'object')?p.val():p;
            if(Number(num)&&(num+'').length==11){
                me.time(obj);
                $(this).off('tap');
            }else{
                console.log('号码为空或格式不对');
                p.focus();
            }
        });
        setTimeout(function(){ 
            me.getCodeBindEvent(obj);
        },me.wait*1000);
    }
    me.getCodeBindEvent(me.obj,me.phone);
    return me;
}
/*获取验证码 2,button*/
var getCode = function(btn,time,fn){
    var me = {};
    me.btn = btn;
    me.wait= time;
    me.callBack = fn;
    me.show = function(obj) {
        $(me.btn).attr("disabled","disabled");//设置button不可用
        me.wait--;
        $(me.btn).val(me.wait+"秒").css('color','#999');
        if(me.wait == -1){
            $(me.btn).removeAttr("disabled");//设置button不可用
            $(me.btn).val("获取").css('color','#0aaefd');
            return ;
        }else if(me.wait == 0){
            $(me.btn).removeAttr("disabled");//设置button不可用
            $(me.btn).val("重新获取").css('color','#0aaefd');
            me.wait = time;
            return ;
        }else if(me.wait>0){
            setTimeout(function(){
                me.show(me.btn);
            }, 1000);
        }
    };
    me.init = function(){
        if($(me.btn).attr("disabled")!="disabled"){//fix button disabled='disabled' 失效
            me.callBack();
            me.show();
        }
    };
    me.reset = function(){
        me.wait = 0;
    };
    me.init();
    return me;
}

/*数字键盘*/
function enterPsw($wrapper,parms){
    var me = {};
    var parms = parms?parms:{
        title:'支付',
        amount:'0',
        faliedUrl:'',
        pay:function(){
            return;
        }
    }
    
    var $bg = $('<div class="bg-layer"></div>');
    
    var $panel = $('<div class="alert"><div class="a-header"><span class="a-close"></span><span>请输入支付密码</span><span class="a-pw">忘记密码</span></div><div class="a-body"><p class="a-type">'+parms.title+'</p><p class="a-num">￥'+parms.amount+'</p><div class="a-pw-wrap"><span contenteditable="false"><i></i></span><span contenteditable="false"><i></i></span><span contenteditable="false"><i></i></span><span contenteditable="false"><i></i></span><span contenteditable="false"><i></i></span><span contenteditable="false"><i></i></span></div><input type="text" class="psw"></div></div>');
     
    var $keyboard = $('<div class="num-keyboard"><table><tr><td class="num-td">1</td><td class="num-td">2</td><td class="num-td">3</td></tr><tr><td class="num-td">4</td><td class="num-td">5</td><td class="num-td">6</td></tr><tr><td class="num-td">7</td><td class="num-td">8</td><td class="num-td">9</td></tr><tr><td class="grey-bg"></td><td class="num-td">0</td><td class="grey-bg del-td"><img src="../images/del-btn.png" alt=""></td></tr></table></div>');
    //支付失败
    var $payFailed = $('<div class="alert pay-fail-popup"><div class="a-info">支付失败</div><div class="a-btn"><div class="cancel">重试</div><div class="change-card">取消</div></div></div>');

    $wrapper.append($bg).append($panel).append($keyboard).append($payFailed);
    //数字按钮
    $keyboard.find('.num-td').on('tap',function(){
        $(this).addClass('active');
        var _that = this;
        setTimeout(function(){
            $(_that).removeClass('active');
        },100);
        var currNum = $(this).text();
        var psw = $panel.find('.psw').val();
        
        if(psw.length<6){
            $panel.find('.a-pw-wrap>span').eq(psw.length).addClass('active');
            $panel.find('.psw').val(psw+currNum);
        }
        if($panel.find('.psw').val().length==6){
            parms.pay();
            $panel.find('.a-pw-wrap>span').removeClass('active');
            $panel.find('.psw').val('');
        }
    });
    //删除按钮
    $keyboard.find('.del-td').on('tap',function(){
        $(this).addClass('active');
        var _that = this;
        setTimeout(function(){
            $(_that).removeClass('active');
        },100);
        var psw = $panel.find('.psw').val();
        if(psw.length>0){
            $panel.find('.a-pw-wrap>span').eq(psw.length-1).removeClass('active');
            $panel.find('.psw').val(psw.substring(0,psw.length-1));
        }
    });
    //关闭按钮
    $panel.find(".a-close").on("tap",function(e){
        me.close();
    });
    //重试
    $payFailed.find('.cancel').on("tap",function(e){
        me.payFailedclose();
        me.show();
    });
    //取消
    $payFailed.find('.change-card').on("tap",function(e){
        me.payFailedclose();
        //跳转到首页
        window.location = parms.faliedUrl;
    });
    me.close = function(){
        $panel.removeClass('alert-show');
        $keyboard.removeClass('num-keyboard-show');
        //setTimeout(function(){
            $bg.css("display","none");
        //},200);
    }
    me.show = function(){
        $bg.css('display','block');
        $panel.addClass('alert-show');
        $keyboard.addClass('num-keyboard-show');
    }
    me.payFailedshow = function(msg){
        if(msg){
            $payFailed.find('.a-info').text(msg);
        }
        $bg.css('display','block');
        $payFailed.addClass('alert-show');
    }
    me.payFailedclose = function(){
        $bg.css('display','none');
        $payFailed.removeClass('alert-show');
    }
    return me;
}

/*confirm,弹窗*/
function confirmWin($wrapper,parms){
    var me = {};
    var parms = parms?parms:{
        title:'支付',
        leftBtnText:'cancel',
        rightBtnText:'confirm',
        cancel:function(){
            return;
        },
        confirm:function(){
            return;
        }
    }
    var $bg = $('<div class="bg-layer"></div>');
    var $confirmPanel = $('<div class="alert confirm-popup"><div class="a-info"></div><div class="a-btn"><div class="a-btn-left"></div><div class="a-btn-right"></div></div></div>');
    $wrapper.append($bg).append($confirmPanel);
    //left btn
    $confirmPanel.find('.a-btn-left').on("tap",function(e){
        parms.cancel();
    });
    //right btn
    $confirmPanel.find('.a-btn-right').on("tap",function(e){
        parms.confirm();
    });
    me.close = function(){
        $confirmPanel.removeClass('alert-show');
        //setTimeout(function(){
            $bg.css("display","none");
        //},200);
    }
    me.show = function(){
        $bg.css('display','block');
        $confirmPanel.addClass('alert-show');
    }
    me.init = function(){
        $confirmPanel.find('.a-info').text(parms.title);
        $confirmPanel.find('.a-btn-left').text(parms.leftBtnText);
        $confirmPanel.find('.a-btn-right').text(parms.rightBtnText);
    }
    me.init();
    return me;
}

/*图片转Base64*/
function getBase64Image(src) {
    var img = document.createElement('img');
    img.src = src;
    console.log(img);
    var canvas = document.createElement("canvas");
    canvas.width = img.width;
    canvas.height = img.height;
    var ctx = canvas.getContext("2d");
    ctx.drawImage(img, 0, 0, img.width, img.height);
    var dataURL = canvas.toDataURL("image/png");
    console.log(dataURL);
    //return dataURL
    return dataURL.replace("data:image/png;base64,", "");
} 

/*loading*/
var loading = function(param){
    me = {}
    me.param = param?param:{
        'container':document.body,
        'hasBg':true
    };

    me.init = function(){
        me.loader=document.createElement("div"),
        loader_bg=document.createElement("div"),
        span_wrap=document.createElement("div");

        me.loader.className="loader";
        loader_bg.className="loader_bg";
        span_wrap.className="span_wrap";
        for(var i=0;i<5;i++){
            var span=document.createElement("span");
            span_wrap.appendChild(span);   
        }
        if(me.param.hasBg){
           me.loader.appendChild(loader_bg); 
        }
        me.loader.appendChild(span_wrap); 
        me.param.container.appendChild(me.loader);
    }
    me.show = function(){
        me.loader.style.display = 'block';
    }
    me.hide = function(){
        me.loader.style.display = 'none';
    }
    me.destroy = function(){
        document.body.removeChild(me.loader);
    }
    me.init();
    return me;
}