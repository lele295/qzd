;(function(){
    var win = window;
    var BAIRONG = win["BAIRONG"] = win["BAIRONG"] || {};
    BAIRONG.removeEvent = function(a, b, c) {
	    if (a.removeEventListener) {
	        a.removeEventListener(b, c, false)
	    } else {
	        if (a.detachEvent) {
	            a.detachEvent("on" + b, function() {
	                c.call(a)
	            })
	        } else {
	            a["on" + b] = null
	        }
	    }
	};
	BAIRONG.addEvent = function(a, b, c) {
	    if (a.addEventListener) {
	        a.addEventListener(b, c, false)
	    } else {
	        if (a.attachEvent) {
	            a.attachEvent("on" + b, function() {
	                c.call(a)
	            })
	        } else {
	            a["on" + b] = c
	        }
	    }
	};

    BAIRONG.createElement = function(d, a) {
		var c = document.createElement(d);
		if (a) {
	        for (var b in a) {
	            if (a.hasOwnProperty(b)) {
	                if (b === "class" || b === "className") {
	                    c.className = a[b]
	                } else {
	                    if (b === "style") {
	                        c.style.cssText = a[b]
	                    } else {
	                        c.setAttribute(b, a[b])
	                    }
	                }
	            }
	        }
	    }
	    return c
	};
	BAIRONG.getDomain=function(){
        var _url = window.location.href;
        _url = _url.replace(/^(http|ftp|https|ssh):\/\//ig, "");
        _url = _url.split("/")[0];
        _url = _url.replace(/\:\d+$/ig, "");
        return _url
    };
    BAIRONG.loadScript = function(a, b) {
	setTimeout(function() {
    var c = BAIRONG.createElement("script", {
        src: a,
        type: "text/javascript"
    });
    if (c.readyState) {
        BAIRONG.addEvent(c, "readystatechange", function() {
            if (c.readyState === "loaded" || c.readyState === "complete") {
                if (b) {
                    b()
                }
                BAIRONG.removeEvent(c, "readystatechange", arguments.callee)
            }
        })
    } else {
        BAIRONG.addEvent(c, "load", function() {
            if (b) {
                b()
            }
            BAIRONG.removeEvent(c, "load", arguments.callee)
        })
    }
    document.getElementsByTagName("head")[0].appendChild(c)
}, 0)
};
	BAIRONG.getCookie = function(sName) {
			var aCookie = document.cookie.split("; ");
				for (var i = 0; i < aCookie.length; i++) {
					var aCrumb = aCookie[i].split("=");
					if (sName == aCrumb[0]) {
						return decodeURIComponent(aCookie[i].replace(sName + "=", ""))
					}
				}
					return ""
				},
    BAIRONG.init = function(){
    var that = this;
    	//that.loadScript(("https:" == document.location.protocol ? "https://" : "http://") + "static.100credit.com/ifae/js/brcore.min.js?v=1.0.151029",function(){
        that.loadScript("/js/wx/brcore.min.js",function(){
           new ExecPageType();
	       mapping();
        })
    }

BAIRONG.init();
    var ExecPageType = (function() {
        function ExecPageType() {
    		var _core = new BRCore(function() {});

            this.Tools = BRCore.tools.Tools;

            if(!BAIRONG['BAIRONG_INFO']){
                BAIRONG['BAIRONG_INFO'] = {user_id:'',page_type:'dft'};
            }
            if(BAIRONG["BAIRONG_INFO"] && BAIRONG["BAIRONG_INFO"].user_id){
                var user_id = BAIRONG["BAIRONG_INFO"].user_id;
            }
            var client_id = BAIRONG.client_id;
            _core.options.cid = client_id;
            _core.options.uid = user_id;
            if (typeof(_core.options.uid) == "undefined" || _core.options.uid == "" || _core.options.uid == "0" || _core.options.uid == null) {
                _core.options.uid = _core.options.sid
            } 
            this.info = BAIRONG["BAIRONG_INFO"];

            var method = this[this.info.page_type];
            if (!this.info.page_type) {
                method = this["dft"]
            }
            if (method && typeof method === "function") {
                method.call(this, _core)
            }
            if(BAIRONG.client_id && BAIRONG["BAIRONG_INFO"] && BAIRONG["BAIRONG_INFO"].app && BAIRONG["BAIRONG_INFO"].event)
            {
                this.das(_core);
            }	
    	}
     ExecPageType.prototype = {
            dft: function(_core) {
                _core.options.p_t = "20";
                var user_id = BAIRONG.BAIRONG_INFO.user_id;
                _core.options.uid = user_id;
                if (typeof(_core.options.uid) == "undefined" || _core.options.uid == "" || _core.options.uid == "0" || _core.options.uid == null) {
                    _core.options.uid = _core.options.sid
                }
                var page_view = new BRCore.inputs.PageView();
                if(BAIRONG.BAIRONG_INFO.pt){
                    page_view.pt = BAIRONG.BAIRONG_INFO.pt;
                }	
                _core.send(page_view,mapping);
                BAIRONG.Cookie = _core.options.gid
            },
            das : function(_core){
                var that = this,
                    info = that.info;
                    //url = info.app + '/' + info.event;

                var antifraud = new BRCore.inputs.Antifraud('receive');
                //���ò���
                for(var param in that.info){
                    that.info[param] && (antifraud[param] = that.info[param]);
                }
				antifraud.plat_type="web";
                _core.send(antifraud,function(json){
                    mapping && mapping();
                    if(window.GetSwiftNumber || (window.GetSwiftNumber && BAIRONG.BAIRONG_INFO.fn)){
                        window.GetSwiftNumber(json);
                    }else if(BAIRONG.BAIRONG_INFO.fn){
                        BAIRONG.BAIRONG_INFO.fn(json);
                    }
                });
            }
        };
        return ExecPageType;
      })();
	var imgArr = [];
    function mapping(data) {
	
        if(BAIRONG["isMapping"]){
            return;
        }
        if (BAIRONG.getCookie("bairong_c_gid") || BRCore.prototype.options.gid || BAIRONG.cancelMapping) {
        	BAIRONG["isMapping"] = true;
            if(BAIRONG.cancelMapping) return;
            //if (!getCookie("BR_date")) {
            var cookie = BAIRONG.getCookie("bairong_c_gid") || BRCore.prototype.options.gid,
                url = BAIRONG.getDomain(),
                rand = Math.floor(Math.random() * 10000),
                img = new Image(1, 1);
                imgArr.push(img);
                
                img.src = ("https:" == document.location.protocol ? "https://" : "http://") + "cm.api.baifendian.com/Mapping.do?bfd_nid=100credit&bfd_channel=" + BAIRONG.client_id + "&bfd_client_uid=" + cookie + "&cid=" + BAIRONG.client_id + "&ep=" + url + "&brid=" + cookie + "&random=" + rand;
        } else {
            setTimeout(mapping, 1000);
        }
    }

   

})(window);


