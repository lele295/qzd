<?php
namespace App\Util;



use Illuminate\Support\Facades\Request;

class ImageRoute{
    static public $config = [
        'certify' => '/uploads/app/certify',
        'banner' => '/uploads/banner',
        'testiamge' => '/uploads/test',
        'avatar' => '/uploads/avatar',
        'advice' => '/uploads/pc/suggest',
	    'qrcode' => '/uploads/qrcode',
        'image' => '/uploads/wechat',
        'apply' => '/uploads/pc/apply',
        'customer_pic' => '/uploads/customer_pic'
    ];

    static public function getUrl($path,$index = 0,$matchConfig = []){
        if($matchConfig == []){
            $matchConfig = self::$config;
        }


        $routes = explode('/',trim($path,'/'));
        /*如果找到最后一个index都没有匹配的那就返回*/
        if(count($routes) <= $index){
            return false;
        }

        $prefix = '/' . implode('/',array_slice($routes,0,$index + 1));
        $tmpConfig = [];
        foreach($matchConfig as $key=>$val){
            if(preg_match('~^'.$prefix.'~',$val)){
                $tmpConfig[$key] = $val;
            }
        }

        if(empty($tmpConfig)){
            return false;
        }

        /**
         *
         */
        if(count($tmpConfig) == 1){
            $pathKey = '';
            $pathRoute = '';
            foreach($tmpConfig as $key=>$val){
                $pathKey = $key;
                $pathRoute = $val;
            }

            //2016-04-28 修改
            return Curl::getHttpServer(true) . '/imagestorage/' . preg_replace_callback('~^'.$val.'~',function($matchs) use ($key){
                return $key;
            },$path);

//            return Curl::getHttpServer(true) . '/imagestorage/' . preg_replace_callback('~^'.$val.'~',function($matchs) use ($key){
//                return $key;
//            },$path);
//            return Curl::getHttpServer(true) . $matchConfig[0] . ;
        }

        return self::getUrl($path,$index + 1,$tmpConfig);
    }

    static public function imageStorageRoute(){
        $config = self::$config;


        //获取当前的url
        $url = str_replace('imagestorage/','',Request::path());
        $findFlag = false;
        $findKey = '';
        foreach($config as $key=>$val){
            if(strpos($url,$key) === 0){
                //找到了这样的前缀
                $findFlag = true;
                $findKey = $key;
            }
        }
        if(!$findFlag){
            //报404错误
            header("HTTP/1.1 404 Not Found");
            header("Status: 404 Not Found");
            exit;
        }


        $path = storage_path() . preg_replace_callback('/^'.$findKey.'/',function($matchs) use ($config){
            $key = $matchs[0];
            return $config[$key];
        },$url);



        if(!file_exists($path)){
            //报404错误
            header("HTTP/1.1 404 Not Found");
            header("Status: 404 Not Found");
            exit;
        }
        //输出图片
        header('Content-type: image/jpg');
        echo file_get_contents($path);
        exit;
    }
}