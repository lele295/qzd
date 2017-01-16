<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/2/17
 * Time: 15:55
 */

namespace App\Service\base;


use App\Service\mobile\Service;

class PictureService extends Service
{
    public function down_wechat_picture($url,$user_id){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOBODY, 0);    //只取body头
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $package = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($package,true);
        if(!isset($response['errcode'])){
            $mk_pic_file = $this->mk_path();
            if($mk_pic_file && strlen($package)>10000){
                $file_name = $mk_pic_file."/".time()."_".$user_id.".jpg";
                $path = $this->create_picture($file_name,$package);
                if($path){
                    return array('status'=>true,'message'=>array('data'=>$path));
                }else{
                    return array('status'=>false,'message'=>array('data'=>'上传失败'));
                }
            }else{
                return array("status"=>false, "message"=>array('data'=>"上传失败或图片太小"));
            }
        }else{
            Logger::info('图片上传imageDownLoadErr:' . $package);
            return array('status'=>false,'message'=>array('data'=>'服务端获取图片异常'));
        }

    }

    //检测图片文件夹
    protected function mk_path() {
        $dir_path = public_path().'/uploads/wechat/' . date("Y-m-d", time());
        if (!file_exists($dir_path)) {
            if (!mkdir($dir_path, 02770, true)) {
                return false;
            }
        }
        return $dir_path;
    }

    /**
     * 生成文件
     * 并返回存储文件的相对路径
     * @param $path
     * @param $data
     * @return bool|string
     */
    public function create_picture($path,$data){
        $local_file = fopen($path,'w');
        if(false !== $local_file){
            if (false !== fwrite($local_file, $data)) {
                fclose($local_file);
                $img_path = stristr($path, "/uploads");
                return $img_path;
            }
        }
        return false;
    }
}