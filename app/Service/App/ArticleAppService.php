<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/4/11
 * Time: 15:41
 */

namespace App\Service\App;


use App\Facades\AppRequest;
use App\Service\base\ArticleService;
use App\Service\mobile\Service;

class ArticleAppService extends Service
{
    /**
     * @param int $type
     * @param int $subclass
     * @return array
     * status = 100成功    status = 200 失败
     */
    public function get_article_list($type=1, $subclass=1){
        $articleService = new ArticleService();
        $info = $articleService->get_article_list($type,$subclass);
        if($info['status']){
            return array('status'=>100,'data'=>array('message'=>'获取文章成功','entry'=>$info['data']['article']));
        }else{
            return array('status'=>200,'data'=>array('message'=>'获取文章失败','entry'=>''));
        }
    }
}