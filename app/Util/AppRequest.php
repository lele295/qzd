<?php
/**
 * Author: yan
 * Date: 2016/4/1 - 15:25
 */
namespace App\Util;
use App\Crypt3Des\Facades\Crypt3Des;
use App\Log\Facades\Logger;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Request;

/**
 * 移动app接口参数请求转换类
 * Class AppRequest
 * @package App\Util
 */
class AppRequest{

    private $_arr = [];

    public function __construct(){

        $originData = Request::all();
        Logger::info($originData,'app');
        if(isset($originData['data'])){
            $originData = $originData['data'];
        }else{
            $this->_arr = [];
            return;
        }
        $originData = Crypt3Des::decrypt($originData);
        Logger::info($originData,'app');
        if($arr = json_decode($originData)){
            $this->_arr = (array)$arr;
        }

    }

    /**
     * @param $name 请求参数名
     * @param string $defaultValue
     * @return mixed
     */
    public function get($name,$defaultValue = ''){
        if(isset($this->_arr[$name])){
            return $this->_arr[$name];
        }else{
            return $defaultValue;
        }
    }

    /**
     * @return array|mixed
     */
    public function all(){
        return $this->_arr;
    }
}