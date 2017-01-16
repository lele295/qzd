<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class LogController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public $modelList = [
        'apply_loan' => '添加管理员',
        'quota_calculation' => '编辑管理员',
        'activity_settings' => '删除管理员',
        'prize_settings' => '添加角色',
        'recommend_settings' => '添加权限',
    ];
    public function index(Request $request)
    {
        //日志列表
        $user_name = $request->input('user_name');
        $model = $request->input('model');

        $logList = DB::table('backend_log')->select('*');
        $searchData = "";
        if( !empty($user_name) )
        {
            $logList->where( 'user_name', 'like', "%$user_name%");
            $searchData['user_name'] = $user_name;
        }

        if( !empty($model) )
        {
            $logList->where( 'model', '=', "$model");
            $searchData['model'] = $model;
        }

        $logList = $logList->orderBy('id', 'desc')->paginate(10);

        //分页中增加搜索条件
        empty($searchData) ? '' : $logList->appends($searchData);

        return view('backend.log.log-list')->with(['logList' => $logList, 'searchData' => $searchData, 'modelList' => $this->modelList]);

    }

    /**
     * @param
     * 写入日志到数据库，有更新的所有后台操作都需要调用此方法
     */
    public function insertLog($model,$remark)
    {
        //dd(json_decode(session('back_user')));
        $data['model'] = $model;
        $data['remark'] = $remark;
        $data['user_id'] = json_decode(session('back_user'))->id;
        $data['user_name'] = json_decode(session('back_user'))->username;
        $data['add_time'] = time();
        $data['ip'] = getenv("HTTP_X_FORWARDED_FOR") ? getenv("HTTP_X_FORWARDED_FOR") : $_SERVER['REMOTE_ADDR'];
        $res = DB::table('backend_log')->insert($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
