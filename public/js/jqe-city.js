/**
 * 借钱么城市选择
 * @constructor
 */
function JqeCity(config){
    var defaultConfig = {
        selectorStr:'',
        initCode:0,
        suffix:'',
        callback:function(){

        },
        class:'',
        class_two:'',
        defaultNull:false,
        defaultSecondNull:false,
        setHide:true,
    }
    this.config = $.extend(defaultConfig,config);
    this.cityMap = '';
    this.host = $(this.config.selectorStr);

    if(!this.config.setHide){
        console.log(123);
        //this.host.attr('style','width:0;height:0;position: absolute;');
        //this.host.
    }
    //this.host.attr('type','hidden')

    if(this.host.val()){
        this.initCode = this.host.val();
    }else{
        this.initCode = this.config.initCode;
    }

    this.levelOneDefault = parseInt(parseInt(this.initCode)/10000) * 10000;
    this.levelOneSecond = parseInt(parseInt(this.initCode)/100) * 100;
    this.selectorPIdStr = 'jqm-level-one'+ this.config.suffix;
    this.selectorCIdstr = 'jqm-level-two' + this.config.suffix;
    this.init();
}

JqeCity.prototype.init = function(){
    (function(a){
        $.ajax({
            type:'get',
            url:'/jqmapi/city-code-pc',
            async:false,
            dataType:'json',
            success:function(data){
                a.cityMap = data;
            }
        })
    })(this);

    /**
     * 初始化一级数据
     */
    (function(a){
        var innerHtml = '<select class="'+ a.config.class +'" id="'+ a.selectorPIdStr+'">';
        if(a.config.defaultNull){
            innerHtml += '<option value="">--请选择--</option>';
        }
        $.each(a.cityMap,function(index,obj){
            var selectedFlag = '';
            if(a.levelOneDefault == obj.id){
                selectedFlag = 'selected';
                a.levelOneDefault = false;
            }

            innerHtml += '<option value="'+obj.id+'" ' + selectedFlag+'>'+obj.name+'</option>';

        })
        if(a.config.class_two != ''){
            innerHtml += '</select><div><select class="' + a.config.class_two + '" id="'+ a.selectorCIdstr+'" style="visibility: hidden"></select></div>';
        }else {
            innerHtml += '</select><div><select class="' + a.config.class + '" id="'+ a.selectorCIdstr+'" style="visibility: hidden"></select></div>';
        }
        a.host.after(innerHtml);
    })(this);

    this.addListener();
    $('#' + this.selectorPIdStr ).change();
}

/**
 * 添加监听
 */
JqeCity.prototype.addListener = function(){
    (function(a){
        $('#' + a.selectorPIdStr).change(function(){
            var value = $(this).val();
            var data = [];

            $.each(a.cityMap,function(index,obj){
                if(obj.id == value){
                    data = obj.child;
                    return false;
                }
            })

            if(data.length === 0){
                $('#'+ a.selectorCIdstr).attr('style','visibility:hidden');
                a.config.callback($(this).val(),$(this).find("option:selected").text());
                return;
            }

            //加载第二级
            var innerHtml = '';
            console.log(a.config);
            if(a.config.defaultSecondNull){
                innerHtml += '<option value="">--请选择--</option>';
            }
            $.each(data,function(index,obj){
                var selectedFlag = '';
                if(a.levelOneSecond == obj.id){
                    selectedFlag = 'selected';
                    a.levelOneSecond = false;
                }
                innerHtml += '<option value="'+obj.id+'"'+ selectedFlag +'>'+obj.name+'</option>';
            });
            $('#'+ a.selectorCIdstr).html(innerHtml).attr('style','visibility:visible').change();
        });

        $('#' + a.selectorCIdstr).change(function(){
            /**
             * 获得当前的值和value
             */
            a.config.callback($(this).val(),$('#' + a.selectorPIdStr).find("option:selected").text()+$(this).find("option:selected").text());
        });
    })(this);
}