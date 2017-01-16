<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/4/11
 * Time: 15:56
 */

namespace App\Service\base;


use App\Model\Admin\ArticleModel;
use App\Service\mobile\Service;

class ArticleService extends Service
{
    public function get_article_list($type=1, $subclass=1){
        try {
            $articleModel = new ArticleModel();
            $info = $articleModel->get_type_subclass($type, $subclass);
            return array('status'=>true,'message'=>array('data'=>'获取问题成功','article'=>$info));
        }catch(\Exception $e){
            return array('status'=>false,'message'=>array('data'=>'获取问题失败','article'=>''));
        }
    }
}