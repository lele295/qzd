<?php
namespace App\Providers;

use App\Util\WechatCallback;
use Illuminate\Support\ServiceProvider;

/**
 * Class WechatServiceProvider
 * @package App\Providers
 */
class WechatServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('WechatCallback',function(){
            return new WechatCallback([
                'appid'=>config('wx.appid'),
                'appsercret'=>config('wx.secret'),
                'mechid'=>env('WECHAT_MECHID'),
                'key'=>env('WECHAT_KEY'),
                'token'=>env('WECHAT_TOKEN'),
                'token_path'=>storage_path() . '/token/access_token_s.json',
                'ticket_path'=>storage_path() . '/token/jsapi_ticket_s.json',
                'menu_path'=>storage_path() . '/wechat/menu.json' //自定义菜单存储路径
            ],new \App\Util\MyWechatResponse());
        });
    }

    public function boot()
    {
    }
}