<?php
/**
 * 用户图片信息没有这个表,这个是模拟模型
 * Class AsCustomPic
 */
namespace App\Model\mobile;
use App\Model\Base\qzdConfigModel;
use App\Util\FileReader;
use App\Model\Base\AsModelBaseModel;

class AsCustomPicModel extends AsModelBaseModel{
    protected $table = 'orders_picture';
    protected $_wx_fill_data = ['cert_face_pic','cert_opposite_pic','cert_hand_pic'];

    static public function getConfig($name){
        return  qzdConfigModel::value($name,JieqianmeConfigModel::CONFIG_TYPE_OF_CONTRACT_PIC);
    }

    protected function validateRule()
    {
        /**
         * 验证规则
         */
        return array(
            'cert_face_pic' => 'required|check_image',
            'cert_opposite_pic' => 'required|check_image',
            'cert_hand_pic' => 'cert_hand_pic|check_image'
        );
    }

    /**
     *
     */
    static public function tryCheck($orderId){
        $obj = self::where(array('OrderId'=>$orderId))->first();
        if($obj instanceof self){
            $res = $obj->check();
            if($res instanceof ResourceErrorModel){
                return $res;
            }else{
                return array(
            //        array('typeNo'=>self::getConfig('cert_face_pic'),'imageString'=>base64_encode(file_get_contents(public_path() . $res['cert_face_pic']))),
            //        array('typeNo'=>self::getConfig('cert_opposite_pic'),'imageString'=>base64_encode(file_get_contents(public_path() . $res['cert_opposite_pic']))),
            //        array('typeNo'=>self::getConfig('custom_pic'),'imageString'=>base64_encode(file_get_contents(public_path() . $res['custom_pic'])))
                      array('typeNo'=>self::getConfig('cert_face_pic'),'imageString'=>FileReader::read_storage_image_resize_file($res['cert_face_pic'],false)),
                      array('typeNo'=>self::getConfig('cert_opposite_pic'),'imageString'=>FileReader::read_storage_image_resize_file($res['cert_opposite_pic'],false)),
                      array('typeNo'=>self::getConfig('cert_hand_pic'),'imageString'=>FileReader::read_storage_image_resize_file($res['cert_hand_pic'],false))
                );
            }
        }else{
            return new ResourceErrorModel(false,'客户图片未设置');
        }
    }


    static public function tryCheckButData($orderId){
        $obj = self::where(array('OrderId'=>$orderId))->first();
        if($obj instanceof self){
            return $obj->check();
        }else{
            return new ResourceErrorModel(false,'客户图片未设置');
        }
    }

    static public function saveVivoInfo($orderId,$array){
        $obj = self::where(array('OrderId'=>$orderId))->first();
        if($obj instanceof self){
            $obj = self::where(array('OrderId'=>$orderId))->update($array);
        }else{
            $array['OrderId'] = $orderId;
            $obj = self::where(array('OrderId'=>$orderId))->insert($array);
        }
        return true;
    }
   
}