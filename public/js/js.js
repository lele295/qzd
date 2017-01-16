(function(){
    $.extend($.fn,{
        mask: function(msg,maskDivClass){
            this.unmask();
            // 参数
            var op = {
                opacity: 0.8,
                z: 99999999,
                bgcolor: '#ccc'
            };
            var original=$(document.body);
            var position={top:0,left:0};
            if(this[0] && this[0]!==window.document){
                original=this;
                position=original.position();
            }
            // 创建一个 Mask 层，追加到对象中
            var maskDiv=$('<div class="maskdivgen">&nbsp;</div>');
            maskDiv.appendTo(original);
            /*
             var maskWidth=original.outerWidth();
             if(!maskWidth){
             maskWidth=original.width();
             }
             var maskHeight=original.outerHeight();
             if(!maskHeight){
             maskHeight=original.height();
             }*/
            maskDiv.css({
                position: 'fixed',
                top: position.top,
                left: position.left,
                'z-index': op.z,
                width: '100%',
                height:'100%',
                'background-color': op.bgcolor,
                opacity: 0
            });
            if(maskDivClass){
                maskDiv.addClass(maskDivClass);
            }
            if(msg){
                var msgDiv=$('<div style="position:fixed;border:#6593cf 1px solid; padding:2px;background:#ccca"><div style="line-height:24px;border:#a3bad9 1px solid;background:white;padding:2px 10px 2px 10px">'+msg+'</div></div>');
                msgDiv.appendTo(maskDiv);
                msgDiv.css({
                    cursor:'wait',
                    top:'47%',
                    left:($("html").width()/2-72)
                });
            }
            maskDiv.fadeIn('fast', function(){
                // 淡入淡出效果
                $(this).fadeTo('slow', op.opacity);
            })
            return maskDiv;
        },
        unmask: function(){
            var original=$(document.body);
            if(this[0] && this[0]!==window.document){
                original=$(this[0]);
            }
            original.find("> div.maskdivgen").fadeOut('fast',0,function(){
                $(this).remove();
            });
        }
    });
})();


function common_ajax(myconfig){
    var defaultConfig = {
        type:'get',
        async:true,
        data:"",
        fun:function(json){
            if(json.url){
                window.location.href = json.url;
                return;
            }
            if(json.status == true){
                window.location.reload();
            }else{
                $(document).unmask();
                alert(json.msg);
            }
        }
    }
    this.config = $.extend(defaultConfig,myconfig);

    if($('input[name=_token]').val()){
        this.config.data._token = $('input[name=_token]').val();
    }

    $(document).mask('验证中，请等待...');
    $.ajax({
        type:this.config.type,
        dataType:'json',
        async:this.config.async,
        url:this.config.url,
        data:this.config.data,
        success:function(json){
            config.fun(json);
        },
        error:function(msg){
            $(document).unmask();
            alert("系统错误请刷新");
        }
    });
}

//选择中后显示
function select_res_show(obj, val, obj_con){
    this.obj = obj;
    this.val = val;
    this.obj_con = obj_con;
}
select_res_show.prototype.start = function(){
    var self = this;
    self.obj.change(function(){
        var val = self.obj.val();
        if(val == self.val){
            self.obj_con.show();
        }else{
            self.obj_con.hide();
        }
    })
    self.obj.trigger("change");
}





