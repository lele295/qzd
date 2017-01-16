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

/*调整ifream高度*/
window.onload = function() {
    if(window.parent&&window.parent.setIframeHeight){
        window.parent.setIframeHeight(window.parent.document.getElementById('page-cont'));
    }
}

/*获取验证码*/
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

//weui 校验
var weui_valid = function(objs){
    var me = {};
    me.objs = objs;
    //me.count = 0;
    me.result = true;
    me.warn_tips = $('<div class="weui_toptips weui_warn js_tooltips"></div>');
    me.init = function(){
        me.objs.each(function(){
            if(this.tagName=='INPUT'){
                $(this).on('input',function(){
                    me.valid(this);
                }).on('blur',function(){
                    me.valid(this);
                    //清除按钮点击事件，使输入框失去焦点
                    var that = this;
                    setTimeout(function(){
                        me.clear_btn_remove(that);
                    },500);
                }).on('focus',function(){
                    if(this.value){
                       me.valid(this); 
                    }
                }); 
            }else if(this.tagName=='SELECT'){
                $(this).on('change',function(){
                    if(this.value){
                        me.warn_btn_remove(this);
                    }else{
                        me.warn_btn_show(this);
                    }
                });
            }
        });
        $('body').append(me.warn_tips);
    }
    me.valid = function(obj){
        var pattern = eval($(obj).attr('pat'));
        var maxLen = Number($(obj).attr('maxLen'));
        if(maxLen){
            if(obj.value.length>maxLen){ 
                obj.value = obj.value.substr(0, maxLen);
                me.warn_btn_remove(obj);
                me.clear_btn_show(obj);
            }
        }
        if(pattern){
            if(!pattern.test($(obj).val())){
                me.clear_btn_remove(obj);
                me.warn_btn_show(obj);
            }else{
                me.warn_btn_remove(obj);
                me.clear_btn_show(obj);
            }  
        }else{
            if(obj.value){
                me.warn_btn_remove(obj);
                me.clear_btn_show(obj);
            }else{
                me.clear_btn_remove(obj);
                me.warn_btn_show(obj);
            }
        }
        if($(obj).attr('id')=='idcard'){
            var msg = checkIdcard(obj.value);
            if(msg=='ok'){
                me.warn_btn_remove(obj);
                me.clear_btn_show(obj);
            }else{
                me.clear_btn_remove(obj);
                me.warn_btn_show(obj);
            }
        }
    }
    me.validResult = function(){
        var count = 0;
        me.objs.each(function(){
            var pattern = eval($(this).attr('pat'));
            if(pattern){
                if(pattern.test($(this).val())){
                    count++;
                }else{
                    me.warn_btn_show(this);
                }
            }else{
                if(this.value){
                    if($(this).attr('id')=='idcard'){
                        var msg = checkIdcard(this.value);
                        if(msg=='ok'){
                            count++;
                        }else{
                            me.warn_btn_show(this);
                        }
                    }else{
                        count++;
                    }
                }else{
                    me.warn_btn_show(this);
                }
            }
        });
        if(count==me.objs.length){
            return true;
        }else{
            return false;
        }
    }
    //清楚按钮
    me.clear_btn_show = function(obj){
        var clear_btn = $('<i class="weui_icon weui_icon_clear"></i>');
        if(!$(obj).nextAll('.weui_icon_clear')[0]){
            clear_btn.on('click',function(){
                $(obj).val('');
                me.clear_btn_remove(obj);
                me.warn_btn_show(obj);
            })
            $(obj).after(clear_btn);
        }
        $(clear_btn).addClass('weui_icon_clear_show');
    }
    me.clear_btn_remove = function(obj){
        $(obj).nextAll('.weui_icon_clear').remove();
    }
    //警告按钮
    me.warn_btn_show = function(obj){
        var warn_btn = $('<i class="weui_icon weui_icon_warn"></i>');
        
        if(!$(obj).nextAll('.weui_icon_warn')[0]){
            warn_btn.on('click',function(){
                var msg = '请填写正确信息';
                var pWrapLi = $(this).parents('.con_ul1_li1');
                // msg = pWrapLi.find('.loan1_li1_span').text()+"--不能为空";
                // if(pWrapLi.find('input')[0]&&pWrapLi.find('input').attr('title')){
                //     msg += "、"+pWrapLi.find('input').attr('title');
                // }
                
                if(pWrapLi.find('input')[0]){
                    msg = "请填写正确的"+pWrapLi.find('.loan1_li1_span').text();
                }else if(pWrapLi.find('select')[0]){
                    msg = "请选择"+pWrapLi.find('.loan1_li1_span').text();
                }
                me.warn_tips.text(msg);
                me.warn_tips.addClass('weui_toptips_show');
                setTimeout(function(){
                    me.warn_tips.removeClass('weui_toptips_show');
                },2000);
            })
            $(obj).after(warn_btn);
        }
        $(warn_btn).addClass('weui_icon_warn_show');
    }
    me.warn_btn_remove = function(obj){
        $(obj).nextAll('.weui_icon_warn').remove();
    }
    me.init();
    return me;
}

//检测身份证号码正确性
function checkIdcard(idcard) {
    var Errors = ["ok", "身份证号码位数不对!", "身份证号码出生日期超出范围或含有非法字符!", "身份证号码校验错误!", "身份证地区非法!"];
    var area = {11: "北京", 12: "天津", 13: "河北", 14: "山西", 15: "内蒙古", 21: "辽宁", 22: "吉林", 23: "黑龙江", 31: "上海", 32: "江苏", 33: "浙江", 34: "安徽", 35: "福建", 36: "江西", 37: "山东", 41: "河南", 42: "湖北", 43: "湖南", 44: "广东", 45: "广西", 46: "海南", 50: "重庆", 51: "四川", 52: "贵州", 53: "云南", 54: "西藏", 61: "陕西", 62: "甘肃", 63: "青海", 64: "宁夏", 65: "新疆", 71: "台湾", 81: "香港", 82: "澳门", 91: "国外"};
    var idcard, Y, JYM, S, M, idcard_array = [], retflag = false;
    idcard_array = idcard.split("");
    if (area[parseInt(idcard.substr(0, 2))] == null)
        return Errors[4];
    switch (idcard.length) {
        case 15:
            return Errors[2];
            break;
        case 18:
            if ((parseInt(idcard.substr(6, 4)) % 4 == 0 && parseInt(idcard.substr(6, 4)) % 100 != 0) || parseInt(idcard.substr(6, 4)) % 400 == 0) {
                ereg = /^[1-9][0-9]{5}[1|2][0|9][0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|[1-2][0-9]))[0-9]{3}[0-9Xx]$/;
            } else {
                ereg = /^[1-9][0-9]{5}[1|2][0|9][0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|1[0-9]|2[0-8]))[0-9]{3}[0-9Xx]$/;
            }
            if (ereg.test(idcard)) {
                S = (parseInt(idcard_array[0]) + parseInt(idcard_array[10])) * 7 + (parseInt(idcard_array[1]) + parseInt(idcard_array[11])) * 9 + (parseInt(idcard_array[2]) + parseInt(idcard_array[12])) * 10 + (parseInt(idcard_array[3]) + parseInt(idcard_array[13])) * 5 + (parseInt(idcard_array[4]) + parseInt(idcard_array[14])) * 8 + (parseInt(idcard_array[5]) + parseInt(idcard_array[15])) * 4 + (parseInt(idcard_array[6]) + parseInt(idcard_array[16])) * 2 + parseInt(idcard_array[7]) * 1 + parseInt(idcard_array[8]) * 6 + parseInt(idcard_array[9]) * 3;
                Y = S % 11;
                M = "F";
                JYM = "10X98765432";
                M = JYM.substr(Y, 1);
                if (M == idcard_array[17].toUpperCase())
                    return Errors[0];
                else
                    return Errors[3];
            } else
                return Errors[2];
            break;
        default:
            return Errors[1];
            break;
    }
}

function creatSwiper(objs,option){
    var imgSwiperWrap = $('.img-swiper-wrap');
    var imgSwiper=$('<div id="img-swiper" class="swiper-container"></div>');
    var swiperWrapper= $('<div class="swiper-wrapper"></div>');
    var swiperHeader = $('<div class="silde-num"><span class="curr-num"></span>/<span class="silde-sum"></span></div>');
    var exitBtn = $('<div class="exit-btn"></div>');

    for(var i=0;i<objs.length;i++){
        var swiperSlide=$('<div class="swiper-slide"></div>');
        swiperWrapper.append(swiperSlide.append('<img data-src="'+objs[i]+'" src="/images/logo-loading.gif" class="swiper-lazy">'));
    }
    imgSwiperWrap.append(imgSwiper.append(swiperWrapper).append(swiperHeader).append(exitBtn));

    var imgSwiper = new Swiper ('.swiper-container', {
        freeMode : false,
        lazyLoading : true,
        onInit: function(swiper){
            $('#img-swiper .silde-sum').text(swiper.slides.length);
            $('#img-swiper .curr-num').text(swiper.activeIndex+1);
        },
        onSlideChangeEnd: function(swiper){
            $('#img-swiper .curr-num').text(swiper.activeIndex+1);
        }
    });
    swiperWrapper.on('click',function(eve){
        if(option&&option.clickExit){
            imgSwiperWrap.empty();
        }else{
            exitBtn.toggleClass('exit-btn-show');
        }
    });
    exitBtn.on('click',function(eve){
        imgSwiperWrap.empty();
    });
}

/*loading*/
var loading = function(){
    me = {}
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
        me.loader.appendChild(loader_bg); 
        me.loader.appendChild(span_wrap); 
        document.body.appendChild(me.loader);
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
/*提示，弹窗*/
function tips(msg,time){
    var tipsDiv = $('<div class="tips"></div>');
    $('body').append(tipsDiv);
    tipsDiv.text(msg).addClass('tips_show');
    setTimeout(function(){
        tipsDiv.removeClass('tips_show').remove();
    },time);
}