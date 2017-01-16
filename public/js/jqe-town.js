/**
 * 借钱么城市选择
 * @constructor
 */
function JqeTown(config){
    var defaultConfig = {
        province:'#province',
        city:'#city',
        town:'#town',
        callback:function(){

        },
        class:'',
        defaultNull:true
    };
    this.config = $.extend(defaultConfig,config);
    this.province = $(this.config.province);
    this.city = $(this.config.city);
    this.town = $(this.config.town);
    this.init();
}

JqeTown.prototype.init = function(){
    (function(a){
        $.ajax({
            type:'get',
            url:'/jqmapi/town-code-pc',
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
        var innerHtml = '';
        if(a.config.defaultNull){
            innerHtml += '<option value="">--请选择--</option>';
        }
        $.each(a.cityMap,function(index,obj){
            var selectedFlag = '';
            if(a.province.attr('data_val') == obj.id){
                selectedFlag = 'selected';
            }
            innerHtml += '<option value="'+obj.id+'" ' + selectedFlag+'>'+obj.name+'</option>';
        });
        a.province.html(innerHtml);
        a.province.change();
    })(this);

    this.addListenerP();
    this.province.change();
};

/**
 * 添加省change监听
 */
JqeTown.prototype.addListenerP = function(){
    (function(a){
        a.province.change(function(){
            var value = $(this).val();
            var data = [];

            $.each(a.cityMap,function(index,obj){
                if(obj.id == value){
                    data = obj.child;
                }
            });

            //加载第二级
            var innerHtml = '';
            $.each(data,function(index,obj){
                var selectedFlag = '';
                if(a.city.attr('data_val') == obj.id){
                    selectedFlag = 'selected';
                }
                innerHtml += '<option value="'+obj.id+'"'+ selectedFlag +'>'+obj.name+'</option>';
            });
            a.city.html(innerHtml);
            a.city.change();
        });
    })(this);
    this.addListenerC();
    this.city.change();
};

/**
 * 添加市change监听
 */
JqeTown.prototype.addListenerC = function(){
    (function(a){
        a.city.change(function(){
            var value = $(this).val();
            var data = [];

            $.each(a.cityMap,function(index,obj){
                $.each(obj.child,function(index,town_arr){
                    if(town_arr.id == value){
                        data = town_arr.child;
                    }
                });
            });

            //加载第二级
            var innerHtml = '';
            $.each(data,function(index,obj){
                var selectedFlag = '';
                if(a.town.attr('data_val') == obj.id){
                    selectedFlag = 'selected';
                }
                innerHtml += '<option value="'+obj.id+'"'+ selectedFlag +'>'+obj.name+'</option>';
            });
            a.town.html(innerHtml);
        });
    })(this);
};