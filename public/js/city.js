function EbuyfunCity(config){
    var defaultConfig = {
        selectorStr:'',
        initCode:0,
		suffix:'',
        callback:function(){

        },
        resourceSrc:'/common/city'
    }
    this.config = $.extend(defaultConfig,config);
    this.cityMap = '';
    this.host = $(this.config.selectorStr);
    this.host.attr('type','hidden');

    if(this.host.val()){
        this.initCode = this.host.val();
    }else{
        this.initCode = this.config.initCode;
    }

    this.levelOneDefault = parseInt(parseInt(this.initCode)/10000) * 10000;
    this.levelOneSecond = parseInt(parseInt(this.initCode)/100) * 100;
	this.selectorPIdStr = this.host.attr('name')+'-ebuycity-level-one'+ this.config.suffix;
    this.selectorCIdstr = this.host.attr('name')+'-ebuycity-level-two' + this.config.suffix;
    this.init();
}

EbuyfunCity.prototype.init = function(){
    (function(a){
        $.ajax({
                    url: a.config.resourceSrc,
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
        var innerHtml = '<select id="'+a.selectorPIdStr+'"><option  value="">请选择</option>';
        $.each(a.cityMap,function(index,obj){
            var selectedFlag = '';
            if(a.levelOneDefault == obj.value){
                selectedFlag = 'selected';
                a.levelOneDefault = false;
            }

            innerHtml += '<option value="'+obj.value+'"' + selectedFlag+'>'+obj.name+'</option>';

        })
        innerHtml += '</select><select id="'+a.selectorCIdstr+'" style="visibility: hidden"></select>';
        a.host.after(innerHtml);
    })(this);

    this.addListener();
    $('#'+this.selectorPIdStr).change();
}

/**
 * 添加监听
 */
EbuyfunCity.prototype.addListener = function(){
    (function(a){
        $('#'+a.selectorPIdStr).change(function(){
            var value = $(this).val();
            var data = [];
            $.each(a.cityMap,function(index,obj){
                if(obj.value == value){
                    data = obj.data;
                    return false;
                }
            })
            //console.log(data);
            if(data.length === 0){
                $('#'+a.selectorCIdstr).attr('style','visibility:hidden');
                a.config.callback($(this).val(),$(this).find("option:selected").text());
                return;
            }

            //加载第二级
            //var innerHtml = '<option value="0">请选择</option>';
            var innerHtml = '<option  value="">请选择</option>';
            $.each(data,function(index,obj){
                var selectedFlag = '';
                if(a.levelOneSecond == obj.val){
                    selectedFlag = 'selected';
                    a.levelOneSecond = false;
                }
                innerHtml += '<option value="'+obj.val+'"'+ selectedFlag +'>'+obj.name+'</option>';
            });
            $('#'+a.selectorCIdstr).html(innerHtml).attr('style','visibility:visible').change();
        });

        $('#'+a.selectorCIdstr).change(function(){
            /**
             * 获得当前的值和value
             */
            a.config.callback($(this).val(),$('#'+a.selectorPIdStr).find("option:selected").text()+$(this).find("option:selected").text());
        });
    })(this);
}
