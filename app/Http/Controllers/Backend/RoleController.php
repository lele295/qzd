<?php

namespace App\Http\Controllers\Backend;

use App\Model\backend\Privilege;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class RoleController extends BaseController
{
    //添加角色
    public function index()
    {
        //显示添加权限的视图
        $data = (new Privilege)->getPrivilege();//权限分级数据
        //dd($data);
        return view('backend/role/add',compact('data'));
    }
    //角色信息入库的----store
    public function store()
    {
        $data = Input::except('_token');
        $data1 = Input::except('_token','base_uri');
        $data1['created_at'] = time();
        //字段验证
        $rule=[
            'rolename'=>'required',
            'base_uri'=>'required',
        ];
        $msg=[
            'name.required'=>'角色名不能为空',
            'base_uri.required'=>'权限必须选'
        ];
        $validator = Validator::make($data,$rule,$msg);
        if ($validator->passes()){
            //接收到数据分为两个部分，分表存储，角色存入角色表，角色id对应的权限存入角色权限中间表
            $res = DB::table('backend_roles')->insertGetId($data1);
            if ($res){
                foreach ($data['base_uri'] as $value){
                    $result = DB::table('backend_permission_role')->insertGetId(['role_id'=>$res,'permission_id'=>$value]);
                }
                $model = '添加角色';
                $remark = '添加了一个id为'.$res.'的管理员角色';
                $log = (new LogController)->insertLog($model,$remark);
                return back()->with('msg','添加成功');
            } else {
                return back()->with('msg','添加失败');
            }
        } else {
            return back()->withErrors($validator);
        }
    }

    //角色列表功能
    public function show()
    {
        //$sql = "select a.*,c.name,group_concat(c.name) AS d from roles as a LEFT JOIN permission_role as b  ON a.id=b.role_id LEFT JOIN permissions AS c ON b.permission_id=c.id GROUP by a.rolename";
        $res = DB::table('backend_roles')
            ->leftJoin('backend_permission_role', 'backend_roles.id', '=', 'backend_permission_role.role_id')
            ->leftJoin('backend_permissions', 'backend_permission_role.permission_id', '=', 'backend_permissions.id')
            ->groupBy('backend_roles.rolename')
            ->select(DB::raw("backend_roles.*,group_concat(backend_permissions.name) as c"))
            ->get();
        //因为多个安硕用户对应一个角色，造成角色列表时权限重复显示，因此需要去重后传到视图里面
        $data=[];
        foreach ($res as $key => $value){
            foreach ($value as $k => $v){
                //$v = array_unique(explode(',',$value['c']));
                $data[$key][$k] = $v;
            }
        }
        foreach ($data as $key => $value){
            $v = implode(',',array_unique(explode(',',$value['c'])));
            $data[$key]['c'] = $v;
        }
        //dd($data[2]['c']);
        return view('backend.role.list',compact('data'));
    }
}
