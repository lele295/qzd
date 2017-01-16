<?php

namespace App\Http\Controllers\Backend;

use App\Model\Backend\Privilege;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class PrivilegeController extends BaseController
{
    //添加权限--显示添加权限的视图
    public function index()
    {
        $data = (new Privilege)->getPrivilege();
        return view('backend/privilege/add',compact('data'));
    }

    //权限数据入库----store
    public function store()
    {

        $data = Input::except('_token');
        $data['created_at'] = time();
        $rule=[
            'name'=>'required',
            'base_uri'=>'required',
            //'position'=>'required',
        ];
        $msg=[
            'name.required'=>'权限名不能为空',
            'base_uri.required'=>'规则名不能为空',
            //'position.required'=>'规则名不能为空',
        ];
        //字段验证
        $validator = Validator::make($data,$rule,$msg);
        if ($validator->passes()){
            $res = DB::table('backend_permissions')->insertGetId($data);
            if ($res){
                $model = '添加权限';
                $remark = '添加了id为'.$res.'权限';
                $log = (new LogController)->insertLog($model,$remark);
                return back()->with('msg','添加成功');
            } else {
                return back()->with('msg','添加失败');
            }
        } else {
            return back()->withErrors($validator);
        }
       /* $res = DB::table('backend_permissions')->insert($data);
        if ($res){
            return back()->with('msg','添加成功');
        } else {
            return back()->with('msg','添加失败');
        }*/

    }
}
