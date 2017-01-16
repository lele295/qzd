<?php

namespace App\Model\Backend;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Privilege extends Model
{
    //查询权限
    public function getPrivilege()
    {
        $data = DB::table('backend_permissions')->get();
        $data = $this->getTree($data);
        return $data;
    }

    //数据处理，无限极分类
    public function getTree($cate,$pid=0,$level=0)
    {
        static $tree = array();
        foreach($cate as $v){
            $v = get_object_vars ($v);//将对象转换成关联数组并返回
            if($v['pid'] == $pid){
                $v['level'] = $level;
                $tree[] = $v;
                $this->getTree($cate,$v['id'],$level+1);
            }
        }
        return $tree;
    }
}