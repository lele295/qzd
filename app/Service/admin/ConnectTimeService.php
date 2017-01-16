<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/1/11
 * Time: 10:37
 */

namespace App\Service\admin;


use App\Model\Admin\ConnectTimeModel;

class ConnectTimeService extends Service{

    public function get_connect_time_list(){
        $connectTimeModel = new ConnectTimeModel();
        $info = $connectTimeModel->get_connect_time_list();
        return $info;
    }

    public function add_connect_time($request){
        $connectTimeModel = new ConnectTimeModel();
        $array['start_time'] = $request['start_time'];
        $array['end_time'] = $request['end_time'];
        $array['action_time'] = $request['action_time'];
   //     $array['count'] = $request['count'];
        $array['deal_day'] = $request['deal_day'];
        $info = $connectTimeModel->add_connect_time($array);
        $connectTimeService = new \App\Service\base\ConnectTimeService();
        $connectTimeService->init_connect_time(false);
        return $info;
    }

    public function delete_connect_time_by_id($id){
        $connectTimeModel = new ConnectTimeModel();
        $info = $connectTimeModel->delete_connect_time_by_id($id);
        return $info;
    }

    public function edit_connect_time_by_id($id,$request){
        $connectTimeModel = new ConnectTimeModel();
        $array['start_time'] = $request['start_time'];
        $array['end_time'] = $request['end_time'];
        $array['action_time'] = $request['action_time'];
        $array['count'] = $request['count'];
        $array['deal_day'] = $request['deal_day'];
        $info = $connectTimeModel->update_connect_time($id,$array);
        $connectTimeService = new \App\Service\base\ConnectTimeService();
        $connectTimeService->init_connect_time(false);
        return $info;
    }

    public function get_connect_time_by_id($id){
        $connectTimeModel = new ConnectTimeModel();
        $info = $connectTimeModel->get_connect_time_by_id($id);
        return $info;
    }
}