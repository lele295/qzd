/**
 * 易佰分城市原则
 * @constructor
 */
    function workCity(config){
    var defaultConfig = {
        selectorStr:'',
        initCode:0,
        suffix:'',
        callback:function(){

        },
        resourceSrc:'/api/user/wcall'
    }
    console.log(this)
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
    this.selectorPIdStr = 'workCity-level-one'+ this.config.suffix;
    this.selectorCIdstr = 'workCity-level-two' + this.config.suffix;
    this.init();
}

workCity.prototype.init = function(){
    (function(a){
        a.cityMap =
            [{"name":"北京市","value":"110000","data":[]},
                {"name":"天津市","value":"120000","data":[]},
                {"name":"河北省","value":"130000","data":
                    [{"name":"石家庄市","val":"130100"},
                        {"name":"唐山市","val":"130200"},
                        {"name":"秦皇岛市","val":"130300"},
                        {"name":"邯郸市","val":"130400"},
                        {"name":"邢台市","val":"130500"},
                        {"name":"保定市","val":"130600"},
                        {"name":"张家口市","val":"130700"},
                        {"name":"承德市","val":"130800"},
                        {"name":"沧州市","val":"130900"},
                        {"name":"廊坊市","val":"131000"},
                        {"name":"衡水市","val":"131100"}]},
                {"name":"山西省","value":"140000","data":
                    [{"name":"太原市","val":"140100"},
                        {"name":"大同市","val":"140200"},
                        {"name":"阳泉市","val":"140300"},
                        {"name":"长治市","val":"140400"},
                        {"name":"晋城市","val":"140500"},
                        {"name":"朔州市","val":"140600"},
                        {"name":"晋中市","val":"140700"},
                        {"name":"运城市","val":"140800"},
                        {"name":"忻州市","val":"140900"},
                        {"name":"临汾市","val":"141000"},
                        {"name":"吕梁市","val":"141100"}]},
                {"name":"内蒙古自治区","value":"150000","data":[{"name":"呼和浩特市","val":"150100"},{"name":"包头市","val":"150200"},{"name":"乌海市","val":"150300"},{"name":"赤峰市","val":"150400"},{"name":"通辽市","val":"150500"},{"name":"鄂尔多斯市","val":"150600"},{"name":"呼伦贝尔市","val":"150700"},{"name":"巴彦淖尔市","val":"150800"},{"name":"乌兰察布市","val":"150900"},{"name":"兴安盟","val":"152200"},{"name":"锡林郭勒盟","val":"152500"},{"name":"阿拉善盟","val":"152900"}]},{"name":"辽宁省","value":"210000","data":[{"name":"沈阳市","val":"210100"},{"name":"大连市","val":"210200"},{"name":"鞍山市","val":"210300"},{"name":"抚顺市","val":"210400"},{"name":"本溪市","val":"210500"},{"name":"丹东市","val":"210600"},{"name":"锦州市","val":"210700"},{"name":"营口市","val":"210800"},{"name":"阜新市","val":"210900"},{"name":"辽阳市","val":"211000"},{"name":"盘锦市","val":"211100"},{"name":"铁岭市","val":"211200"},{"name":"朝阳市","val":"211300"},{"name":"葫芦岛市","val":"211400"}]},{"name":"吉林省","value":"220000","data":[{"name":"长春市","val":"220100"},{"name":"吉林市","val":"220200"},{"name":"四平市","val":"220300"},{"name":"辽源市","val":"220400"},{"name":"通化市","val":"220500"},{"name":"白山市","val":"220600"},{"name":"松原市","val":"220700"},{"name":"白城市","val":"220800"},{"name":"延边朝鲜族自治州","val":"222400"}]},{"name":"黑龙江省","value":"230000","data":[{"name":"哈尔滨市","val":"230100"},{"name":"齐齐哈尔市","val":"230200"},{"name":"鸡西市","val":"230300"},{"name":"鹤岗市","val":"230400"},{"name":"双鸭山市","val":"230500"},{"name":"大庆市","val":"230600"},{"name":"伊春市","val":"230700"},{"name":"佳木斯市","val":"230800"},{"name":"七台河市","val":"230900"},{"name":"牡丹江市","val":"231000"},{"name":"黑河市","val":"231100"},{"name":"绥化市","val":"231200"},{"name":"大兴安岭地区","val":"232700"}]},{"name":"上海市","value":"310000","data":[]},{"name":"江苏省","value":"320000","data":[{"name":"南京市","val":"320100"},{"name":"无锡市","val":"320200"},{"name":"徐州市","val":"320300"},{"name":"常州市","val":"320400"},{"name":"苏州市","val":"320500"},{"name":"南通市","val":"320600"},{"name":"连云港市","val":"320700"},{"name":"淮安市","val":"320800"},{"name":"盐城市","val":"320900"},{"name":"扬州市","val":"321000"},{"name":"镇江市","val":"321100"},{"name":"泰州市","val":"321200"},{"name":"宿迁市","val":"321300"}]},{"name":"浙江省","value":"330000","data":[{"name":"杭州市","val":"330100"},{"name":"宁波市","val":"330200"},{"name":"温州市","val":"330300"},{"name":"嘉兴市","val":"330400"},{"name":"湖州市","val":"330500"},{"name":"绍兴市","val":"330600"},{"name":"金华市","val":"330700"},{"name":"衢州市","val":"330800"},{"name":"舟山市","val":"330900"},{"name":"台州市","val":"331000"},{"name":"丽水市","val":"331100"}]},{"name":"安徽省","value":"340000","data":[{"name":"合肥市","val":"340100"},{"name":"芜湖市","val":"340200"},{"name":"蚌埠市","val":"340300"},{"name":"淮南市","val":"340400"},{"name":"马鞍山市","val":"340500"},{"name":"淮北市","val":"340600"},{"name":"铜陵市","val":"340700"},{"name":"安庆市","val":"340800"},{"name":"黄山市","val":"341000"},{"name":"滁州市","val":"341100"},{"name":"阜阳市","val":"341200"},{"name":"宿州市","val":"341300"},{"name":"六安市","val":"341500"},{"name":"亳州市","val":"341600"},{"name":"池州市","val":"341700"},{"name":"宣城市","val":"341800"}]},{"name":"福建省","value":"350000","data":[{"name":"福州市","val":"350100"},{"name":"厦门市","val":"350200"},{"name":"莆田市","val":"350300"},{"name":"三明市","val":"350400"},{"name":"泉州市","val":"350500"},{"name":"漳州市","val":"350600"},{"name":"南平市","val":"350700"},{"name":"龙岩市","val":"350800"},{"name":"宁德市","val":"350900"}]},{"name":"江西省","value":"360000","data":[{"name":"南昌市","val":"360100"},{"name":"景德镇市","val":"360200"},{"name":"萍乡市","val":"360300"},{"name":"九江市","val":"360400"},{"name":"新余市","val":"360500"},{"name":"鹰潭市","val":"360600"},{"name":"赣州市","val":"360700"},{"name":"吉安市","val":"360800"},{"name":"宜春市","val":"360900"},{"name":"抚州市","val":"361000"},{"name":"上饶市","val":"361100"}]},{"name":"山东省","value":"370000","data":[{"name":"济南市","val":"370100"},{"name":"青岛市","val":"370200"},{"name":"淄博市","val":"370300"},{"name":"枣庄市","val":"370400"},{"name":"东营市","val":"370500"},{"name":"烟台市","val":"370600"},{"name":"潍坊市","val":"370700"},{"name":"济宁市","val":"370800"},{"name":"泰安市","val":"370900"},{"name":"威海市","val":"371000"},{"name":"日照市","val":"371100"},{"name":"莱芜市","val":"371200"},{"name":"临沂市","val":"371300"},{"name":"德州市","val":"371400"},{"name":"聊城市","val":"371500"},{"name":"滨州市","val":"371600"},{"name":"荷泽市","val":"371700"}]},{"name":"河南省","value":"410000","data":[{"name":"郑州市","val":"410100"},{"name":"开封市","val":"410200"},{"name":"洛阳市","val":"410300"},{"name":"平顶山市","val":"410400"},{"name":"安阳市","val":"410500"},{"name":"鹤壁市","val":"410600"},{"name":"新乡市","val":"410700"},{"name":"焦作市","val":"410800"},{"name":"濮阳市","val":"410900"},{"name":"许昌市","val":"411000"},{"name":"漯河市","val":"411100"},{"name":"三门峡市","val":"411200"},{"name":"南阳市","val":"411300"},{"name":"商丘市","val":"411400"},{"name":"信阳市","val":"411500"},{"name":"周口市","val":"411600"},{"name":"驻马店市","val":"411700"},{"name":"省直辖县级行政区划","val":"419000"}]},{"name":"湖北省","value":"420000","data":[{"name":"武汉市","val":"420100"},{"name":"黄石市","val":"420200"},{"name":"十堰市","val":"420300"},{"name":"宜昌市","val":"420500"},{"name":"襄阳市","val":"420600"},{"name":"鄂州市","val":"420700"},{"name":"荆门市","val":"420800"},{"name":"孝感市","val":"420900"},{"name":"荆州市","val":"421000"},{"name":"黄冈市","val":"421100"},{"name":"咸宁市","val":"421200"},{"name":"随州市","val":"421300"},{"name":"恩施州","val":"422800"},{"name":"省直辖行政单位","val":"429000"}]},{"name":"湖南省","value":"430000","data":[{"name":"长沙市","val":"430100"},{"name":"株洲市","val":"430200"},{"name":"湘潭市","val":"430300"},{"name":"衡阳市","val":"430400"},{"name":"邵阳市","val":"430500"},{"name":"岳阳市","val":"430600"},{"name":"常德市","val":"430700"},{"name":"张家界市","val":"430800"},{"name":"益阳市","val":"430900"},{"name":"郴州市","val":"431000"},{"name":"永州市","val":"431100"},{"name":"怀化市","val":"431200"},{"name":"娄底市","val":"431300"},{"name":"湘西州","val":"433100"}]},{"name":"广东省","value":"440000","data":[{"name":"广州市","val":"440100"},{"name":"韶关市","val":"440200"},{"name":"深圳市","val":"440300"},{"name":"珠海市","val":"440400"},{"name":"汕头市","val":"440500"},{"name":"佛山市","val":"440600"},{"name":"江门市","val":"440700"},{"name":"湛江市","val":"440800"},{"name":"茂名市","val":"440900"},{"name":"肇庆市","val":"441200"},{"name":"惠州市","val":"441300"},{"name":"梅州市","val":"441400"},{"name":"汕尾市","val":"441500"},{"name":"河源市","val":"441600"},{"name":"阳江市","val":"441700"},{"name":"清远市","val":"441800"},{"name":"东莞市","val":"441900"},{"name":"中山市","val":"442000"},{"name":"潮州市","val":"445100"},{"name":"揭阳市","val":"445200"},{"name":"云浮市","val":"445300"}]},{"name":"广西省","value":"450000","data":[{"name":"南宁市","val":"450100"},{"name":"柳州市","val":"450200"},{"name":"桂林市","val":"450300"},{"name":"梧州市","val":"450400"},{"name":"北海市","val":"450500"},{"name":"防城港市","val":"450600"},{"name":"钦州市","val":"450700"},{"name":"贵港市","val":"450800"},{"name":"玉林市","val":"450900"},{"name":"百色市","val":"451000"},{"name":"贺州市","val":"451100"},{"name":"河池市","val":"451200"},{"name":"来宾市","val":"451300"},{"name":"崇左市","val":"451400"}]},{"name":"海南省","value":"460000","data":[{"name":"海口市","val":"460100"},{"name":"三亚市","val":"460200"},{"name":"三沙市","val":"460300"},{"name":"儋州市","val":"460400"},{"name":"省直辖县级行政单位","val":"469000"}]},{"name":"重庆市","value":"500000","data":[]},{"name":"四川省","value":"510000","data":[{"name":"成都市","val":"510100"},{"name":"自贡市","val":"510300"},{"name":"攀枝花市","val":"510400"},{"name":"泸州市","val":"510500"},{"name":"德阳市","val":"510600"},{"name":"绵阳市","val":"510700"},{"name":"广元市","val":"510800"},{"name":"遂宁市","val":"510900"},{"name":"内江市","val":"511000"},{"name":"乐山市","val":"511100"},{"name":"南充市","val":"511300"},{"name":"眉山市","val":"511400"},{"name":"宜宾市","val":"511500"},{"name":"广安市","val":"511600"},{"name":"达州市","val":"511700"},{"name":"雅安市","val":"511800"},{"name":"巴中市","val":"511900"},{"name":"资阳市","val":"512000"},{"name":"阿坝藏族州","val":"513200"},{"name":"甘孜藏族州","val":"513300"},{"name":"凉山彝族自治州","val":"513400"}]},{"name":"贵州省","value":"520000","data":[{"name":"贵阳市","val":"520100"},{"name":"六盘水市","val":"520200"},{"name":"遵义市","val":"520300"},{"name":"安顺市","val":"520400"},{"name":"毕节市","val":"520500"},{"name":"铜仁市","val":"520600"},{"name":"黔西南州","val":"522300"},{"name":"黔东南州","val":"522600"},{"name":"黔南州","val":"522700"}]},{"name":"云南省","value":"530000","data":[{"name":"昆明市","val":"530100"},{"name":"曲靖市","val":"530300"},{"name":"玉溪市","val":"530400"},{"name":"保山市","val":"530500"},{"name":"昭通市","val":"530600"},{"name":"丽江市","val":"530700"},{"name":"普洱市","val":"530800"},{"name":"临沧市","val":"530900"},{"name":"楚雄彝族州","val":"532300"},{"name":"红河州","val":"532500"},{"name":"文山壮族州","val":"532600"},{"name":"西双版纳州","val":"532800"},{"name":"大理白族州","val":"532900"},{"name":"德宏傣族州","val":"533100"},{"name":"怒江傈僳族州","val":"533300"},{"name":"迪庆藏族州","val":"533400"}]},{"name":"西藏自治区","value":"540000","data":[{"name":"拉萨市","val":"540100"},{"name":"日喀则市","val":"540200"},{"name":"昌都地区","val":"542100"},{"name":"山南地区","val":"542200"},{"name":"那曲地区","val":"542400"},{"name":"阿里地区","val":"542500"},{"name":"林芝地区","val":"542600"}]},{"name":"陕西省","value":"610000","data":[{"name":"西安市","val":"610100"},{"name":"铜川市","val":"610200"},{"name":"宝鸡市","val":"610300"},{"name":"咸阳市","val":"610400"},{"name":"渭南市","val":"610500"},{"name":"延安市","val":"610600"},{"name":"汉中市","val":"610700"},{"name":"榆林市","val":"610800"},{"name":"安康市","val":"610900"},{"name":"商洛市","val":"611000"}]},{"name":"甘肃省","value":"620000","data":[{"name":"兰州市","val":"620100"},{"name":"嘉峪关市","val":"620200"},{"name":"金昌市","val":"620300"},{"name":"白银市","val":"620400"},{"name":"天水市","val":"620500"},{"name":"武威市","val":"620600"},{"name":"张掖市","val":"620700"},{"name":"平凉市","val":"620800"},{"name":"酒泉市","val":"620900"},{"name":"庆阳市","val":"621000"},{"name":"定西市","val":"621100"},{"name":"陇南市","val":"621200"},{"name":"临夏回族自治州","val":"622900"},{"name":"甘南藏族州","val":"623000"}]},{"name":"青海省","value":"630000","data":[{"name":"西宁市","val":"630100"},{"name":"海东市","val":"630200"},{"name":"海北藏族州","val":"632200"},{"name":"黄南藏族自治州","val":"632300"},{"name":"海南藏族自治州","val":"632500"},{"name":"果洛藏族州","val":"632600"},{"name":"玉树藏族州","val":"632700"},{"name":"海西","val":"632800"}]},{"name":"宁夏省","value":"640000","data":[{"name":"银川市","val":"640100"},{"name":"石嘴山市","val":"640200"},{"name":"吴忠市","val":"640300"},{"name":"固原市","val":"640400"},{"name":"中卫市","val":"640500"}]},{"name":"新疆","value":"650000","data":[{"name":"乌鲁木齐市","val":"650100"},{"name":"克拉玛依市","val":"650200"},{"name":"吐鲁番地区","val":"652100"},{"name":"哈密地区","val":"652200"},{"name":"昌吉回族州","val":"652300"},{"name":"博尔塔拉","val":"652700"},{"name":"巴音郭楞","val":"652800"},{"name":"阿克苏地区","val":"652900"},{"name":"克孜勒苏州","val":"653000"},{"name":"喀什地区","val":"653100"},{"name":"和田地区","val":"653200"},{"name":"伊犁哈萨克州","val":"654000"},{"name":"塔城地区","val":"654200"},{"name":"阿勒泰地区","val":"654300"},{"name":"省直辖行政单位","val":"659000"}]}];
    })(this);

    /**
     * 初始化一级数据
     */
    (function(a){
        var innerHtml = '<select id="'+a.selectorPIdStr+'"><option  value="">--请选择--</option>';
        $.each(a.cityMap,function(index,obj){
            var selectedFlag = '';
            if(a.levelOneDefault == obj.value){
                selectedFlag = 'selected';
                a.levelOneDefault = false;
            }

            innerHtml += '<option value="'+obj.value+'"' + selectedFlag+'>'+obj.name+'</option>';

        })
        innerHtml += '</select><select id="'+a.selectorCIdstr+'" style="visibility: hidden;"></select>';
        a.host.after(innerHtml);
    })(this);

    this.addListener();
    $('#'+this.selectorPIdStr).change();
}

/**
 * 添加监听
 */
workCity.prototype.addListener = function(){
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
            var innerHtml = '';
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

$(function(){
    new workCity({
        selectorStr:'#work_addr1',
        callback:function(val,name){
            $('#work_addr1').val(val);
        }
    });

});


