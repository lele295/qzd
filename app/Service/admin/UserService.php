<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/10/9
 * Time: 17:04
 */

namespace App\Service\admin;


use App\Log\Facades\Logger;
use App\Model\Admin\UserAdminModel;
use App\Model\Base\AsUserAuthModel;
use App\Model\Base\AuthModel;
use App\Model\Base\UserModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService extends Service
{
    private $userAdminMdoel;

    public function __construct(){
        $this->userAdminMdoel = new UserAdminModel();
    }

    public function get_user_list($condition)
    {
        $array = $condition;
        array_pull($condition,'page');
        if(empty($condition)){
            $info = $this->userAdminMdoel->get_user_all_list();
        }else{
            $searchService = new SearchService();
            $info = $searchService->get_user_index_search($array);
        }
        return $info;
    }

    /*
     * 获取所有的预约客户
     */
    public function get_all_cusomer($input_data)
    {
        $asuserauth = new AsUserAuthModel();
        $res = $asuserauth->get_serach_customer($input_data);

        return $res;
    }

    /*
     * 导出用户信息信息
     */
    public function con_out_user($input_data)
    {
        if(empty($input_data)){
            $userAdminModel = new UserAdminModel();
            $res = $userAdminModel->get_user_all_list('down');
        }else{
            $searchService = new SearchService();
            $res = $searchService->get_user_index_search($input_data,'down');
        }

        if(!$res){
            return false;
        }
        $csvDocumentService = new CsvDocumentService();
        $header = array('ID' ,'用户名', '手机号', '身份证号', '最高贷款额', '客户类型', '安硕编号', '所属城市', '注册日期', '最近登录日期', '进度查询','来源');
        $filename = "用户列表".date('Y-m-d',time());
        $fp = $csvDocumentService->down($header,$filename);


        $res->chunk(10000, function($user)use($csvDocumentService, $fp)
        {
            foreach($user as $info)
            {
                $row = array();
                if(!isset($info->real_name)){
                    $info->real_name = $info->realname;
                }
                if(!isset($info->id_card)){
                    $info->id_card = "";
                }
                if(!isset($info->CreditLimit)){
                    $info->CreditLimit = "";
                }
                if(!isset($info->CustomerID)){
                    $info->CustomerID = "";
                }
                if(!isset($info->WorkAdd)){
                    $info->WorkAdd = "";
                }

                if(isset($info->step_status))
                {
                    $info->step_status = $this->parse_step_status($info->step_status);
                }else{
                    $info->step_status = "";
                }
                array_push($row,$csvDocumentService->text_format($info->user_table_id));
                array_push($row,$csvDocumentService->text_format($info->real_name));
                array_push($row,$csvDocumentService->text_format($info->mobile));
                array_push($row,$csvDocumentService->text_format($info->id_card, true));
                array_push($row,$csvDocumentService->text_format($info->CreditLimit));
                array_push($row,$csvDocumentService->text_format($info->real_name?'交叉现金贷':"未认证"));
                array_push($row,$csvDocumentService->text_format($info->CustomerID));
                array_push($row,$csvDocumentService->text_format($info->WorkAdd));
                array_push($row,$csvDocumentService->text_format(str_replace("-", "/", $info->user_created_at)));
                array_push($row,$csvDocumentService->text_format(str_replace("-", "/", $info->user_updated_at)));
                array_push($row,$csvDocumentService->text_format($info->step_status));
                array_push($row,$csvDocumentService->text_format($info->source==1?'借钱么微信':"分期购"));
                fputcsv($fp,$row);
            }
        });
        fclose($fp);
        exit;
    }



    public function con_out_cousmer($input_data)
    {die(var_dump("待开发"));
        $csvDocumentService = new CsvDocumentService();
        $header = array('客户编号','身份号', '姓名', '城市', '现住址', '工作地址', '最高贷款额', '最高还款额', '电话', '活动客户所属阶段', '产品类型', '产品特征', '活动名称', '活动期限');
        $filename = "预约名单列表".date('Y-m-d',time());
        $fp = $csvDocumentService->down($header,$filename);


        $f= AsUserAuthModel::with('auth');

        $f->chunk(1000, function($users)
        {
            $i = 0;

            Log::info($i);

        });
        foreach (AsUserAuthModel::with('auth')->get() as $res)
        {
            die(var_dump(count($res)));
        }


        $asuserauth = new AsUserAuthModel();
        $res = $asuserauth->get_serach_customer($input_data, "all");

        //计数器
        $cnt = 0;
        // 每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
        $limit = 1000;
        foreach($res as $info){
            $cnt++;
            if($limit == $cnt){ //刷新一下输出buffer，防止由于数据过多造成问题
                ob_flush();
                flush();
                $cnt = 0;
            }
            $row = array();
            array_push($row,$csvDocumentService->text_format($info->CUSTOMERID));
            array_push($row,$csvDocumentService->text_format($info->CERTID, true));
            array_push($row,$csvDocumentService->text_format($info->CUSTOMERNAME));
            array_push($row,$csvDocumentService->text_format($info->CITY));
            array_push($row,$csvDocumentService->text_format($info->FAMILYADD));
            array_push($row,$csvDocumentService->text_format($info->CITY));
            array_push($row,$csvDocumentService->text_format($info->CREDITLIMIT));
            array_push($row,$csvDocumentService->text_format($info->TOPMONTHPAYMENT));
            array_push($row,$csvDocumentService->text_format($info->MOBILETELEPHONE));
            array_push($row,$csvDocumentService->text_format($info->CUSTOMERPHASE));
            array_push($row,$csvDocumentService->text_format("交叉现金贷"));
            array_push($row,$csvDocumentService->text_format($info->PRODUCTFEATURES));
            array_push($row,$csvDocumentService->text_format($info->EVENTNAME));
            array_push($row,$csvDocumentService->text_format($info->EVENTDATE));
            fputcsv($fp,$row);
            unset($row);

        }
        ob_flush();
        flush();
        fclose($fp);
        exit;
    }

    public function get_user_message($mobile,$id_card,$real_name){
        $userModel = new UserModel();
        $info = $userModel->get_user_message_by_mobile($mobile);
        if($info){
            $userModel->update_user_info_by_mobile($mobile,array('updated_at'=>date('Y-m-d H:i:s',time())));
            return array('status'=>true,'data'=>array('entry'=>$info));
        }else{
            $user = UserModel::firstOrCreate(array('mobile'=>$mobile));
            $user->password = Hash::make(substr($id_card,-6,6));
            $user->realname = '';
            $user->source = 3;
            $user->group = 1;
            $user->mark = $real_name.':'.$id_card.',';
            $user->save();
            return array('status'=>true,'data'=>array('entry'=>$user));
        }
    }

    public function update_user_message_by_user_id($user_id,$update){
        $userModel = new UserModel();
        $userModel->update_user_info_by_id($user_id,$update);
        return array('status'=>true,'data'=>array('message'=>'更新成功'));
    }
}
