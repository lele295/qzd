/**
 * Created by hp1 on 2016/4/25.
 */
window.onload = function(){
    //ios/Android返回调用方法
    var backElement = document.getElementById('back');
    if (backElement) {
        backElement.onclick = function () {
            if(browser.versions.weixin){
                window.history.go(-1);
            }else if(browser.versions.ios){
                setupWebViewJavascriptBridge(function(bridge) {
                    bridge.registerHandler('logoHandler', function(data, responseCallback) {
                        responseCallback()
                    });
                });
            }else if(browser.versions.android){
                window.Android.logo();
            }else{
                window.history.go(-1);
            }
        };
    }


    //APP中隐藏
    var hide = document.getElementById('hide');
    if(hide){
        if(browser.versions.mobile && !browser.versions.weixin){
            hide.style.display = 'none';
        }
    }

};


//鍒ゆ柇璁块棶缁堢
var browser={
    versions:function(){
        var u = navigator.userAgent, app = navigator.appVersion;
        return {
            mobile: !!u.match(/AppleWebKit.*Mobile.*/), //鏄惁涓虹Щ鍔ㄧ粓绔�
            ios: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/), //ios缁堢
            android: u.indexOf('Android') > -1 || u.indexOf('Adr') > -1, //android缁堢
            iPhone: u.indexOf('iPhone') > -1 , //鏄惁涓篿Phone鎴栬�匭QHD娴忚鍣�
            iPad: u.indexOf('iPad') > -1, //鏄惁iPad
            webApp: u.indexOf('Safari') == -1, //鏄惁web搴旇绋嬪簭锛屾病鏈夊ご閮ㄤ笌搴曢儴
            weixin: u.indexOf('MicroMessenger') > -1 //鏄惁寰俊 锛�2015-01-22鏂板锛�
        };
    }(),
    language:(navigator.browserLanguage || navigator.language).toLowerCase()
};


//js和ios的桥接
function setupWebViewJavascriptBridge(callback) {
    if (window.WebViewJavascriptBridge) { return callback(WebViewJavascriptBridge); }
    if (window.WVJBCallbacks) { return window.WVJBCallbacks.push(callback); }
    window.WVJBCallbacks = [callback];
    var WVJBIframe = document.createElement('iframe');
    WVJBIframe.style.display = 'none';
    WVJBIframe.src = 'wvjbscheme://__BRIDGE_LOADED__';
    document.documentElement.appendChild(WVJBIframe);
    setTimeout(function() { document.documentElement.removeChild(WVJBIframe) }, 0)
}

