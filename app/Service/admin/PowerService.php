<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/10/10
 * Time: 9:34
 */

namespace App\Service\admin;


use Illuminate\Support\Facades\Log;

class PowerService extends Service
{
    public function __construct(){

    }

    public function get_power_message(){
        $info = view('admin.power.index');
        $xml = simplexml_load_string($info);
        return $xml;
    }

    public function create_menu(){
        $info = view('admin.power.index');
        $xml = simplexml_load_string($info);
        $menu_html = '';
        foreach($xml->power->model as $model){
            $menu_html = $menu_html.$this->left_menu_base($model);
        }
        return $menu_html;
    }

    public function test($power){
        $info = view('admin.power.index');
        $xml = simplexml_load_string($info);
        $menu_html = '';
        foreach($xml->power->model as $model){
            if($this->get_array_data($power,$model->key)){
                $menu_html = $menu_html.$this->left_menu_base($model);
            }
        }
        Log::info($menu_html);
        return $menu_html;
    }

    public function get_array_data($power,$key){
        $flag = true;
        foreach($power as $val){
            $val = ((array)$val);
            Log::info($val);
        }
        return $flag;
    }

    public function left_menu_base($model){
        $str = '<li class="admin-parent">'.
                    '<a class="am-cf" data-am-collapse="{target: \'#collapse-nav-'.$model->key.'\'}"><span class="am-icon-file"></span>'.
                    $model->descript.
                    '<span class="am-icon-angle-right am-fr am-margin-right"></span></a>'.
                    '<ul class="am-list am-collapse admin-sidebar-sub am-in" id="collapse-nav-'.$model->key.'">';
        $li = '';
        foreach($model->menu as $menu){
            $li = $li.$this->left_detail_menu($menu);
        }
        $str = $str.$li.'</ul></li>';
        return $str;
    }

    public function left_detail_menu($menu){
        $str = '<li><a href="'.$menu->param->url.'" class="am-cf"><span class="am-icon-check"></span>'.$menu->param->descript.'</a></li>';
        return $str;
    }



    public function ex_user_power($power){

    }

    public function get_menu_data($data,$xml){

    }

}