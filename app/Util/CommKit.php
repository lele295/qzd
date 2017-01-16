<?php
namespace App\Util;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class CommKit{
    static public function requestSegmentBaseUrl(){
        return '/' . Request::segment(1) . '/' . Request::segment(2);
    }

    static public function mergeDateRange(Array $arr){
        $tmpArray = array('start_time'=>'1970-01-01','end_time'=>date('Y-m-d'));
        return array_merge($arr,$tmpArray);
    }


    /**
     * 过滤page
     */
    static public function paginatorParam(){
        $otherGetRequest = array_except(Input::all(),['page']);
        View::share(array('otherGetRequest'=>$otherGetRequest));
    }

    /**
     * likeArray,如果值为空，那么默认为不进行条件查询
     * @param $query 查询对象
     * @param $arr
     */
    static public function likeQuery($query,$arr){
        $arr = array_filter($arr,function($val){
            return $val?true:false;
        });
        foreach($arr as $key=>$val){
            $query->where($key,'like',"%$val%");
        }
    }

    /**
     * @desc 拼接日期原生串
     * @param $query
     * @param $arr
     */
    static public function dateQuery($query,$arr){
        $arr = array_filter($arr,function($val){
            return $val?true:false;
        });
        foreach($arr as $key=>$val){
            $query->whereRaw('DATE_FORMAT(' .$key . ', "%Y-%m-%d")="' . date('Y-m-d',strtotime($val)) . '"');
        }
    }

    /**
     * @desc 相等查询
     * @param $query
     * @param $arr
     */
    static public function equalQuery($query,$arr){
        $arr = array_filter($arr,function($val){
            if($val === '0' || $val === 0){
                return true;
            }
            return $val?true:false;
        });
        if($arr){
            $query->where($arr);
        }
    }

    /**
     * 表单验证
     */
    static public function validates($attributes,$rules){
        $validator = \Illuminate\Support\Facades\Validator::make($attributes,$rules);
        if($validator->passes()){
            return true;
        }else{
            /**
             * 获取第一条错误信息
             */
            $message = $validator->messages();
            echo json_encode(array('status'=>false,'data'=>$message->first()));
            exit;
        }
    }


    static public function validatesWithReturn($attributes,$rules){
        $validator = \Illuminate\Support\Facades\Validator::make($attributes,$rules);
        if($validator->passes()){
            return true;
        }else{
            /**
             * 获取第一条错误信息
             */
            $message = $validator->messages();
            if(Request::ajax()){
                echo json_encode(array('status'=>false,'data'=>$message->first()));
                exit;
            }
            return array('status'=>false,'data'=>$message->first());
        }
    }

    /**
     * 取区间时间戳
     * @param Array $data 参数配置
     * @return array
     */
    static public function betweenDateTimestamp(Array $data = []){
        $config = array(
            'start_time_param'=>'start_time',
            'end_time_param'=>'end_time',
            'column'=>''
        );

        $config = array_merge($config,$data);

        return array((Input::get($config['start_time_param'])?(strtotime(Input::get($config['start_time_param']))):time()),(Input::get($config['end_time_param'])?(strtotime(Input::get($config['end_time_param'])) + 86399):time()));
    }

    /**
     * 取时间段的函数
     */
    static public function betweenDateStringCondition($data){
        $config = array(
            'start_time_param'=>'start_time',
            'end_time_param'=>'end_time',
            'column'=>''
        );

        $config = array_merge($config,$data);

       if(Request::get($config['start_time_param']) && Request::get($config['end_time_param'])){
           return 'UNIX_TIMESTAMP(DATE_FORMAT('.$config['column'].',"%Y-%m-%d")) between '.strtotime(Request::get($config['start_time_param'])).' and ' . strtotime(Request::get($config['end_time_param']));
       }else if(Request::get($config['start_time_param'])){
           return 'UNIX_TIMESTAMP(DATE_FORMAT('.$config['column'].',"%Y-%m-%d")) >= '.strtotime(Request::get($config['start_time_param']));
       }else if(Request::get($config['end_time_param'])){
           return 'UNIX_TIMESTAMP(DATE_FORMAT('.$config['column'].',"%Y-%m-%d")) <= '.strtotime(Request::get($config['end_time_param']));
       }else{
           return '';
       }
    }


    /**
     * 数组对象转素组
     * @param $obj
     * @return array
     */
    static public function objarray_to_array($obj) {
        $ret = array();
        foreach ($obj as $key => $value) {
            if (gettype($value) == "array" || gettype($value) == "object"){
                $ret[$key] =  self::objarray_to_array($value);
            }else{
                $ret[$key] = $value;
            }
        }
        return $ret;
    }

    //phpexcel操作设置列宽
    static public function getColumnByNum($index){
        switch($index){
            case 1:
                return 'A';
            case 2:
                return 'B';
            case 3:
                return 'C';
            case 4:
                return 'D';
            case 5:
                return 'E';
            case 6:
                return 'F';
            case 7:
                return 'G';
            case 8:
                return 'H';
            case 9:
                return 'I';
            case 10:
                return 'J';
            case 11:
                return 'K';
            case 12:
                return 'L';
            case 13:
                return 'M';
            case 14:
                return 'N';
            case 15:
                return 'O';
            case 16:
                return 'P';
            case 17:
                return 'Q';
            case 18:
                return 'R';
            case 19:
                return 'S';
            case 20:
                return 'T';
            case 21:
                return 'U';
            case 22:
                return 'V';
            case 23:
                return 'W';
            case 24:
                return 'X';
            case 25:
                return 'Y';
            case 26:
                return 'Z';
            case 27:
                return 'AA';
            case 28:
                return 'AB';
            case 29:
                return 'AC';
            case 30:
                return 'AD';
            case 31:
                return 'AE';
            case 32:
                return 'AF';
            case 33:
                return 'AG';
            case 34:
                return 'AH';
            case 35:
                return 'AI';
            case 36:
                return 'AJ';
            case 37:
                return 'AK';
            case 38:
                return 'AL';

        }
    }

    static public function currentMonthTimeStamp(){
        return strtotime(date('Y-m'));
    }

    static public function checkUploadImage($path){
        if(($path != '0') && ($path != '') && file_exists(public_path() . $path)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 带秒数的时间转换为日期字符串,2015-01-11 12:00:00 转化为 2015-01-11
     * @param $str
     * @return bool|string
     */
    static public function fullTimeStrtoDateStr($str){
        return date('Y-m-d',strtotime($str));
    }

    /**
     * 当前时间在时间字符串里面
     * @param $startTimeStr
     * @param $endTimeStr
     * @return bool
     */
    static public function inPeriods($startTimeStr,$endTimeStr){
        $currentTime = time();
        if(($currentTime > strtotime($startTimeStr)) && ($currentTime < strtotime($endTimeStr))){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 获得当前日期的时间戳范围数组
     */
    static public function dayTimestampArray(){
        $start = strtotime(date('Y-m-d'));
        return array($start,$start + 86400 - 1);
    }

    static public function lastestSevenDayTimestampArray(){
        $end = strtotime(date('Y-m-d')) - 1;
        return array($end - (86400*7),$end);
    }

    /**
     * 获得上个月的字符串,比如201510
     */
    static public function lastestMonthStr(){
        $end = strtotime(date('Y-m'))  - 1; //上个月最后的时间戳
        return date('Ym',$end);
    }

    /**
     * 获得上个月时间戳的范围
     */
    static public function lastestMonthTimestatmpArray(){
        $end = strtotime(date('Y-m'))  - 1; //上个月最后的时间戳
        $start = strtotime(date('Y-m',$end));
        return array($start,$end);
    }

    /**
     * @param $date 指定日期
     * @param string $sign
     * @return bool|string
     */
    static public function getMonth($date,$sign=0)
    {
        //得到系统的年月
        $tmp_date=date("Ym",strtotime($date));
        //切割出年份
        $tmp_year=substr($tmp_date,0,4);
        //切割出月份
        $tmp_mon =substr($tmp_date,4,2);
        $tmp_nextmonth=mktime(0,0,0,$tmp_mon+1,1,$tmp_year);
        $tmp_forwardmonth=mktime(0,0,0,$tmp_mon-1,1,$tmp_year);
        if($sign==0){
            //得到当前月的下一个月
            return $tmp_nextmonth;
        }else{
            //得到当前月的上一个月
            return $tmp_forwardmonth;
        }
    }

    /**
     * 把一周的日期转换成汉字
     * @param $day
     * @return string
     */
    static public function numb_to_hanzi_week($day){
        $res = '';
        switch($day){
            case 1:
                $res = '一';
                break;
            case 2:
                $res = '二';
                break;
            case 3:
                $res = '三';
                break;
            case 4:
                $res = '四';
                break;
            case 5:
                $res = '五';
                break;
            case 6:
                $res = '六';
                break;
            case 7:
                $res = '日';
                break;
            default:
                break;
        }
        return $res;
    }

    static public function numToStr($num){
        if (stripos($num,'e')===false) return $num;
        $num = trim(preg_replace('/[=\'"]/','',$num,1),'"');//出现科学计数法，还原成字符串
        $result = "";
        while ($num > 0){
            $v = $num - floor($num / 10)*10;
            $num = floor($num / 10);
            $result   =   $v . $result;
        }
        return $result;
    }

    /**
     * 银行卡号不能是科学计数法
     * @param $data
     */
    static public function checkBankNumberWillnotBeScience($data){
        if(false !== strpos($data,'+')){
            header("Content-type: text/html; charset=utf-8");
            echo '银行卡号不能用科学计数法表示！';
            exit;
        }
        return $data;
    }

    /**
     * 公用的ajax返回
     * @param $callback
     * @return mixed
     */
    static public function ajaxReturn($callback){
        if($callback()){
            return Response::json(array('status'=>true));
        }else{
            return Response::json(array('status'=>false));
        }
    }


    /**
     * 带数据回滚的公用ajax返回
     * @param $callback
     * @return mixed
     */
    static public function ajaxReturnWithRollback($callback){
        \DB::beginTransaction();
        if($callback()){
            \DB::commit();
            return Response::json(array('status'=>true));
        }else{
            \DB::rollback();
            return Response::json(array('status'=>false));
        }
    }

    /**
     * @param $increment 增量
     * @param $timestamp 当前时间戳
     * @return string
     */
    static public function addMonth($increment,$timestamp){
        $n = date('d',$timestamp);
        $tfd = strtotime('+' . $increment . ' months', strtotime(date('Y-m-01',$timestamp)));
        $days = date('t', $tfd);
        if($n > $days){
            return date('Y-m-',$tfd) . $days;
        }else{
            return date('Y-m-',$tfd) . str_pad($n,2,0,STR_PAD_LEFT);
        }
    }


    /**
     * 尝试获得数组的value值
     * @param array $arr
     * @param $key
     * @return mixed
     */
    static public function tryGetArrayValue(Array &$arr,$key){
        if(isset($arr[$key])){
            return $arr[$key];
        }else{
            return '';
        }
    }

    static public function entToken($f){
        $j = strlen($f);
        for ($i=0;$i<$j/2;$i++)
        {
            if (($i % 3) < 2)
            {
                $t = $f[$i];
                $f[$i] = $f[$j-$i-1];
                $f[$j-$i-1] = $t;
            }
        }
        return $f;
    }

    /**
     * @param $param 默认的查询参数
     * @return str
     */
    static public function dateStrDefaultToday($param){
        $str = Request::input($param,'');
        if($str == ''){
            return date('Y-m-d');
        }
        return $str;
    }

    /**
     * 小数转百分比
     * @param $fate
     * @return mixed
     */
    static public function fateToPercent($fate){
            return intval($fate*100) . '%';
    }

//    static public function DeCode($f){
//        $j = strlen($f);
//        for ($i=0;$i<$j/2;$i++)
//        {
//            if (($i % 3) < 2)
//            {
//                $t = $f[$i];
//                $f[$i] = $f[$j-$i-1];
//                $f[$j-$i-1] = $t;
//            }
//        }
//
//        return $f;
//    }





//     $e = 12745;
//     $f = base64_encode($e * 19);
//     $g = $f;
//     var_dump($f);
//     var_dump($f[2]);
//     var_dump(base64_decode($f));
//  for ($i=111111;$i<999999;)
//     {

//      var_dump(base64_encode($i));
//      $i = $i +  551737;
//     }
// ​
   //加密
    // $j = strlen($f);
    // for ($i=0;$i<$j/2;$i++)
    // {
    //    if (($i % 3) < 2)
    //    {
    //     $t = $f[$i];
    //     $f[$i] = $f[$j-$i-1];
    //     $f[$j-$i-1] = $t;
    //   }
    // }

    // echo $f;
   //解密
    // for ($i=0;$i<$j/2;$i++)
    // {
    // if (($i % 3) < 2)
    //    {
    //     $t = $f[$i];
    //     $f[$i] = $f[$j-$i-1];
    //     $f[$j-$i-1] = $t;
    //    }
    // }
//     var_dump($f);

//     var_dump($g == $f);

//     $h = (int )base64_decode($f)/19;
// var_dump($h);
//     var_dump($e == $h);

}