<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController
{
    //添加管理员
    public function index()
    {

        $res = DB::table('backend_roles')->where('position','=','')->select('rolename','id')->get();//角色列表
        //因为安硕用户的存在，所有将角色表当中的角色的unique键取消了，因此查询出来的角色会重复，因此需要去重，但是角色信息必定
        //要跟其id对应，采取如下方法,最终的目的就是去重并且id和角色对应
        $data = [];
        foreach ($res as $key => $value){
            $data[$value->id] = $value->rolename;
        }
        $data = array_unique($data);
        //显示添加权限的视图
        return view('backend/user/add',compact('data'));
    }

    //角色信息入库的----store
    public function store()
    {
        //接收表单数据
        $data = Input::except('_token');
        $id = Input::all()['id'];//对应角色的id值
        //dd($id);
        $data['password'] = md5($data['password']);
        //字段验证
        $rule=[
            'username'=>'required',
            'password'=>'required',
            'real_name'=>'required',
            'province'=>'required',
            'city'=>'required',
            'id'=>'required|numeric',
        ];
        $msg=[
            'username.required'=>'用户名不能为空',
            'password.required'=>'密码必须填',
            'real_name.required'=>'真实姓名不能为空',
            'province.required'=>'省份不能为空',
            'city.required'=>'城市不能为空',
            'id.required'=>'角色不能为空',
            'id.numeric'=>'角色不能为空'
        ];
        $validator = Validator::make($data,$rule,$msg);
        unset($data['id']);//删除角色id
        if ($validator->passes()){
            $user_id = DB::table('backend_user')->insertGetId($data);//用户id
            //接收到数据分为两个部分，分表存储，角色存入角色表，角色id对应的权限存入角色用户中间表
            if ($user_id){
                $result = DB::table('backend_role_user')->insertGetId(['user_id'=>$user_id,'role_id'=>$id]);
                $model = '添加管理员';
                $remark = '添加了id为'.$user_id.'管理员';
                $log = (new LogController)->insertLog($model,$remark);
                return back()->with('msg','添加成功');
            } else {
                return back()->with('msg','添加失败');
            }
        } else {
            return back()->withErrors($validator);
        }
    }

    //管理员列表功能
    public function show()
    {
        return view('backend.user.list');
    }

    //ajax请求，管理员查询功能
    public function search()
    {
        $res = Input::except('_token');
        //dd($res);
        if($res['type'] == 1){
            //查询本地的管理员
            $sql = "select a.id,a.username,a.position,c.rolename,a.city,a.phone from backend_user AS a LEFT JOIN backend_role_user as b ON a.id=b.user_id LEFT JOIN backend_roles AS c ON b.role_id=c.id WHERE ";
            if ($res['username']){$sql.=" a.username =\"{$res['username']}\" and ";}
            if ($res['city']){$sql.=" a.city =\"{$res['city']}\" and ";}
            if ($res['phone']){$sql.=" a.phone =\"{$res['phone']}\" and ";}
            if ($res['real_name']){$sql.=" a.real_name =\"{$res['real_name']}\" and ";}
            if ($res['position']){$sql.=" a.position like \"%{$res['position']}%\" and ";}
            $sql = substr($sql,0,strlen($sql)-5);//因为 拼接sql,最后sql都会有and执行会报错，所以截取
        } else {
            //查询安硕里面的管理员
            //dd('comeon');
            //$userid = 225268;
            //根据安硕用户的userid，查询出对应的角色
            $sql = "SELECT a.USERID,a.USERNAME,c.ITEMNAME TITLE,e.rolename,b.ITEMNAME,a.MOBILETEL FROM sync_user_info a LEFT JOIN sync_code_library b ON a.CITY=b.ITEMNO LEFT JOIN sync_code_library c on c.ITEMNO =a.JOB_TITLE LEFT JOIN backend_roles d ON c.ITEMNAME=d.position left JOIN backend_roles e ON e.position=c.ITEMNAME WHERE b.CODENO='AreaCode' AND ";
            if ($res['username']){$sql.=" a.USERID =\"{$res['username']}\" and ";};
            if ($res['city']){$sql.=" b.ITEMNAME like \"{$res['city']}%\" and ";};
            if ($res['phone']){$sql.=" a.MOBILETEL =\"{$res['phone']}\" and ";};
            if ($res['position']){$sql.=" c.ITEMNAME like \"{$res['position']}%\" and ";};
            $sql = substr($sql,0,strlen($sql)-5);
        }
        return $data = DB::select($sql);
       /* $data = DB::table('backend_user')
            ->leftJoin('backend_role_user', 'backend_user.id', '=', 'backend_role_user.user_id')
            ->leftJoin('backend_roles', 'backend_role_user.role_id', '=', 'backend_roles.id')
            ->select(DB::raw("'backend_user.id','backend_user.username','backend_user.position','backend_roles.rolename','backend_user.province','backend_user.city','backend_user.phone'"))
            ->paginate(4);
        return view('backend.user.list',compact('data'));*/
    }


    //统一登录平台，测试接口
    public function test()
    {
        dd(13);
        $data = array('username'=>'admin','password'=>'123','svscode'=>'QZD');
        $data = json_encode($data);
        dd($data);
        $ch  =  curl_init ();
        curl_setopt ( $ch ,  CURLOPT_URL ,  'http://10.80.12.18:6102/sso/logins');
        curl_setopt ( $ch ,  CURLOPT_POST ,  1 );
        curl_setopt ( $ch ,  CURLOPT_POSTFIELDS ,  $data );
        $s = curl_exec ( $ch );
        echo "<pre>";
        dd($s);
    }

    //编辑本地管理员的信息,安硕管理员没有必要修改
    public function edit(Request $request)
    {
        //管理员的id
        $id = $request->id;
        //查询出管理员的信息，显示在表格当中
        $data = DB::table('backend_user')->where('id',$id)->first();

        $role_id = DB::table('backend_role_user')->where('user_id',$id)->select('role_id')->first('');
        //dd($role_id);
        //所有的角色信息
        $role_info  = DB::table('backend_roles')
            ->where('backend_roles.position','=','')
            ->select('backend_roles.rolename','backend_roles.id')
            ->get();
        //dd($role_info);
        return view('backend.user.edit',compact('data','role_id','role_info','id'));
    }

    //更新管理员的信息入库
    public function update(Request $request)
    {
        //接收的密码需要进行加密
        $data = $request->except('_token','id','userid');
        $id = $request->userid;//用户id
        $role_id = $request->id;//角色id
        $res = DB::table('backend_user')->where('id',$id)->update($data);
        $result = DB::table('backend_role_user')->where('user_id',$id)->update(['role_id' => $role_id]);
        //dd($res);
        if ($res || $result){
            if ($res && $result){
                $remark = "编辑了id为".$id.'的管理员的角色信息和普通信息';
            } else {
                $remark = '编辑了id为'.$id.'管理员信息';
            }
            $model = '编辑管理员';
            $log = (new LogController)->insertLog($model,$remark);
            return redirect('user/list');
        } else {
            //更新失败
            return redirect('user/list')->with('errors','更新失败');
        }
    }
}
