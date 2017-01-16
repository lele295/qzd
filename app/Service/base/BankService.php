<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/9/25
 * Time: 16:34
 */

namespace App\Service\base;


use App\Api\api\BankNoApi;
use App\Log\Facades\Logger;
use App\Model\Base\AuthModel;
use App\Model\Base\LoanModel;
use App\Model\Base\SyncModel;
use App\Model\Base\UniqueCodeModel;
use App\Model\Base\UserBankCardModel;
use App\Model\Base\UserBankNoModel;
use App\Model\Base\UserModel;
use App\Model\mobile\BankCodeLikeModel;
use App\Service\admin\CsvDocumentService;
use App\Service\mobile\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Mockery\CountValidator\Exception;

class BankService extends Service
{
    private $userBankNoModel;
    public function __construct(){
        $this->userBankNoModel = new UserBankNoModel();
    }

    public function get_bank_list_by_status($input_data){
        $input_data = array_filter($input_data);
        if($input_data){
            $info = $this->userBankNoModel->get_search_bank_query($input_data);
        }else{
            $info = $this->userBankNoModel->get_user_bank_no_list();
        }
        return $info;
    }

    public function export_bank_list_by_condition($input_data){
        $input_data = array_filter($input_data);
        if($input_data){
            $info = $this->userBankNoModel->get_search_bank_query($input_data,'down');
        }else{
            $info = $this->userBankNoModel->get_user_bank_no_list('15','down');
        }
        $csvDocumentService = new CsvDocumentService();
        $header = array('姓名' ,'身份证号', '手机号', '银行卡账号', '银行名称', '申请时间', "更新时间",'认证id', '状态','认证码值','描述','备注','是否收费');
        $filename = "银行卡账户认证-".date('Y-m-d',time());
        $fp = $csvDocumentService->down($header,$filename);
        if(!$info){
            return false;
        }
        //计数器
        $cnt = 0;
        // 每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
        $limit = 1000;
        foreach ($info as $bank) {
            $cnt++;
            if ($limit == $cnt) { //刷新一下输出buffer，防止由于数据过多造成问题
                ob_flush();
                flush();
                $cnt = 0;
            }
            $row = array();
            array_push($row, $csvDocumentService->text_format($bank->real_name));
            array_push($row, $csvDocumentService->text_format($bank->id_card, true));
            array_push($row, $csvDocumentService->text_format($bank->mobile));
            array_push($row, $csvDocumentService->text_format($bank->bank_card_no,true));
            array_push($row, $csvDocumentService->text_format($bank->open_bank_name));
            array_push($row, $csvDocumentService->text_format($bank->created_at));
            array_push($row, $csvDocumentService->text_format($bank->updated_at));
            array_push($row, $csvDocumentService->text_format($bank->out_id));
            if($bank->status == '1'){
                $status = '认证通过';
            }else if($bank->status == '2'){
                $status = '认证不通过';
            }else if($bank->status == '3'){
                $status = '认证中';
            }else if($bank->status == '4'){
                $status = '易极付出错';
            }else{
                $status = '未认证';
            }
            array_push($row, $csvDocumentService->text_format($status));
            array_push($row, $csvDocumentService->text_format($bank->resultcode));
            array_push($row, $csvDocumentService->text_format($bank->descript));
            array_push($row, $csvDocumentService->text_format($bank->remark));
            if($bank->pay == '1'){
                $pay = '收费';
            }else{
                $pay = '不收费';
            }
            array_push($row, $csvDocumentService->text_format($pay));

            fputcsv($fp, $row);
            unset($row);
        }
        ob_flush();
        flush();
        fclose($fp);
        exit;
    }

    //更新银行卡状态与及相关联的认证状态
    public function update_bank_status_by_id($bank_id,$bank_status,$auth_status){
        $this->start_connect();
        $userBankCardModel = new UserBankCardModel();
        $authModel = new AuthModel();
        $bank = $userBankCardModel->get_bank_card_by_id($bank_id);
        $info = $userBankCardModel->update_bank_card_by_id(array('check_status'=>$bank_status),$bank_id);
        $update_info = $authModel->update_auth_info_by_user_id(array('step_status'=>$auth_status),$bank->user_id);
        $flag = $this->end_connect(array($info,$update_info));
        return $flag;
    }
    //银行卡认证
    public function bank_auth($array){
        $like_res = BankCodeLikeModel::like_bank_prefix($array['bankcardno'], $array['itemno']);
        if($like_res["status"] == false){
            return array('status'=>false,'message'=>array('data'=>$like_res['data']));
        }else{
            $info = $this->bank_no_auth_new($array);
            if($info['status']){
                return array('status'=>true,'message'=>array('data'=>$info['data']['message']));
            }else{
                return array('status'=>false,'message'=>array('data'=>$info['data']['message']));
            }
        }
    }

    /**
     * 对银行卡进行帐户认证
     * 2016-03-08
     * 新方法
     * @param $array
     */
    public function bank_no_auth_new($array){
        $authModel = new AuthModel();
        $auth = $authModel->get_auth_info_by_user_id($array['user_id']);
        $array = array_add($array,'real_name',$auth->real_name);
        $id_card = isset($array['id_card'])?$array['id_card']:$auth->id_card;
        $array = array_add($array,'id_card',$id_card);
        $array = array_add($array,'CustomerID',$auth->CustomerID);
        $info = $this->record_bank_no_message_new($array);
        if($info['status']){
            if($info['data']['step'] === '100'){
                return array('status'=>true,'data'=>array('message'=>$info['data']['message']));
            }else{
                return array('status'=>false,'data'=>array('message'=>$info['data']['message']));
            }
        }else{
            $result = $this->get_query_bank_no_auth_new($info,$array);
            return $result;
        }
    }


    public function get_query_bank_no_auth_new($info,$array){

        if($info['data']['step'] !== '300'){
            $result = $this->send_bank_no_auth($array);
            if(!$result['status']){
                return array('status'=>false,'data'=>array('message'=>$result['data']['message']));
            }
            $out_id = $result['data']['out_id'];
            //要保存out_id的啦
            $obj = UserBankNoModel::where(['id'=>$info['data']['id']])->first();
            $obj->out_id = $out_id;
            $obj->save();
        }else{
            $out_id = $info['data']['out_id'];
        }
        $bankNewService = new BankNewService();
        $query_result = $bankNewService->get_bank_no_auth($out_id);
        $result_info = $this->update_bank_no_message_new($query_result,$out_id);
        return $result_info;
   //     return $query_result;
    }


    /**
     * 更新银行卡状态
     * status = 1 认证成功
     * status =2 认证失败
     * status = 3 正在认证中，中间状态
     * status = 4 易极付系统异常
     * @param $info
     * @param $out_id
     * @return array
     */
    public function update_bank_no_message_new($info,$out_id){
        if($info['status'] === '100'){
            $this->userBankNoModel->update_user_bank_no_by_out_id(array('status'=>'1','updated_at'=>date('Y-m-d H:i:s',time()),'resultcode'=>$info['data']['code']),$out_id);
            return array('status'=>true,'data'=>array('message'=>'银行卡认证成功'));
        }elseif($info['status'] === '300'){
            $this->userBankNoModel->update_user_bank_no_by_out_id(array('status'=>'4','updated_at'=>date('Y-m-d H:i:s',time()),'resultcode'=>$info['data']['code']),$out_id);
            return array('status'=>true,'data'=>array('message'=>'银行卡认证成功'));
        }elseif($info['status'] === '400'){
            $this->userBankNoModel->update_user_bank_no_by_out_id(array('status'=>'2','updated_at'=>date('Y-m-d H:i:s',time()),'resultcode'=>$info['data']['code']),$out_id);
            return array('status'=>false,'data'=>array('message'=>'系统检测不到该银行卡'));
        }else{
            return array('status'=>false,'data'=>array('message'=>'系统繁忙，请重试'));
        }
    }


    public function send_bank_no_auth($array){
        $bankNewService = new BankNewService();
        $info = $bankNewService->send_bank_no_api_auth($array);
        if($info['status']){
            return array('status'=>true,'data'=>array('message'=>'发送成功','out_id'=>$info['data']['out_id'],'code'=>$info['data']['code']));
        }else{
            return array('status'=>false,'data'=>array('message'=>$info['data']['message'],'out_id'=>$info['data']['out_id'],'code'=>$info['data']['code']));
        }
    }


    /**银行卡帐户认证
     * @param $array  银行卡相关信息
     * @return bool返回结果|array
     */
    public function bank_no_auth($array){
        $authModel = new AuthModel();
        $auth = $authModel->get_auth_info_by_user_id($array['user_id']);
        $array = array_add($array,'real_name',$auth->real_name);
        $new_id_card = isset($array['id_card'])?$array['id_card']:$auth->id_card;
        $array = array_add($array,'id_card',$new_id_card);
        $array = array_add($array,'CustomerID',$auth->CustomerID);
        $info = $this->record_bank_no_message($array);
        if($info){
            if($info['status'] == 'true' || $info['status'] == 'false'){
                return $info;
            }else{
                //是中间状态，进行结果查询
                $bankno = $info['data'];
                return $this->get_query_bank_no_auth($bankno->out_id);
            }
        }else{
            //是初始状态，进行银行卡认证整套流程
            return $this->get_bank_no_api_auth($array);
        }
    }

    /**
     * 更新银行卡帐号在数据库中的状态与设置out_id
     * @param $where
     * @param $array
     */
    public function update_bank_no_message($where,$array){
        $condition = array(
            'open_bank'=>$where['itemno'],
            'bank_card_no' =>$where['bankcardno'],
            'id_card' => $where['id_card'],
            'mobile'=>$where['mobileno'],
            'user_id'=>$where['user_id'],
        );
        $this->userBankNoModel->update_user_bank_no_by_where($condition,$array);
    }

    /**
     * 对银行卡信息进行查询记录，如果存在，则查询其结果，如果不存在，则新增一条记录
     * @param $array  银行卡相关的数组
     * @return array|bool返回结果，如果array是指已存在，bool为不存在，新增一条记录
     *
     */
    public function record_bank_no_message($array){
        $info = $this->userBankNoModel->get_user_bank_no($array['itemno'],$array['bankcardno'],
            $array['id_card'],$array['mobileno'],$array['user_id']);
        if($info){
            if($info->status == '1'){
                return array('status'=>'true','msg'=>'已经在本平台认证过');
            }else if($info->status == '2'){
                return array('status'=>'false','msg'=>'银行账号不正确，请更换');
            }else if($info->status == '4'){
                $this->userBankNoModel->update_user_bank_no_by_out_id(array('status'=>'10','updated_at'=>date('Y-m-d H:i:s',time()),'resultcode'=>''),$info['out_id']);
                return false;
            }else if($info->status == '10'){
                return false;
            }else{
                return array('status'=>'middle','msg'=>'还在中间状态','data'=>$info);
            }
        }else{
            $bank = array(
                'user_id'=>$array['user_id'],
                'real_name'=>$array['real_name'],
                'mobile'=>$array['mobileno'],
                'id_card'=>$array['id_card'],
                'open_bank'=>$array['itemno'],
                'open_bank_name'=>$array['bank_card_name'],
                'bank_card_no'=>$array['bankcardno'],
                'created_at'=>date('Y-m-d H:i:s',time()),
                'updated_at'=>date('Y-m-d H:i:s',time()),
                'status'=>'10', //为初始状态
            );
            $this->userBankNoModel->insert_user_bank_no($bank);
            return false;
        }
    }


    /**
     * 对银行卡进行查询
     * @param $array
     * @return array
     * @throws \Exception
     */
    public function record_bank_no_message_new($array){
        $condition = array(
            'open_bank'=>$array['itemno'],
            'bank_card_no'=>$array['bankcardno'],
            'id_card'=>$array['id_card'],
            'mobile'=>$array['mobileno'],
            'user_id'=>$array['user_id'],
        );
        $info = $this->userBankNoModel->get_user_bank_no_is_exist($condition);
        if($info){
            if($info->status === '1'){
                return array('status'=>true,'data'=>array('message'=>'已经在本平台认证并且通过','step'=>'100'));
            }elseif($info->status === '2'){
                return array('status'=>true,'data'=>array('message'=>'银行账号不正确，请更换','step'=>'200'));
            }elseif($info->status === '4'){
                $this->userBankNoModel->update_user_bank_no_by_id(array('status'=>'10','updated_at'=>date('Y-m-d H:i:s',time()),'resultcode'=>'','out_id'=>''),$info['id']);
                return array('status'=>false,'data'=>array('message'=>'易极付出错，需要重新认证','id'=>$info['id'],'step'=>'110'));
            }elseif($info->status === '10'){
                return array('status'=>false,'data'=>array('message'=>'初始状态','id'=>$info['id'],'step'=>'110'));
            }else{
                return array('status'=>false,'data'=>array('message'=>'中间状态','id'=>$info['id'],'out_id'=>$info['out_id'],'step'=>'300'));
            }
        }else{
            $id = $this->insert_bank_no_message($array);
            return array('status'=>false,'data'=>array('message'=>'初始状态','id'=>$id,'step'=>'110'));
        }
    }

    public function insert_bank_no_message($array){
        try {
            $bank = array(
                'user_id' => $array['user_id'],
                'real_name' => $array['real_name'],
                'mobile' => $array['mobileno'],
                'id_card' => $array['id_card'],
                'open_bank' => $array['itemno'],
                'open_bank_name' => $array['bank_card_name'],
                'bank_card_no' => $array['bankcardno'],
                'created_at' => date('Y-m-d H:i:s', time()),
                'updated_at' => date('Y-m-d H:i:s', time()),
                'status' => '10', //为初始状态
            );
            $id = $this->userBankNoModel->insert_user_bank_no_get_id($bank);
            return $id;
        }catch(\Exception $e){
            throw $e;
        }
    }

    /**
     * 进行接口认证访问与数据发送
     * @param $array
     */
    public function get_bank_no_api_auth($array){
        $out_id = 'jqm' . date('YmdHis', time());
        $data['outid'] = $out_id;
        $data['realname'] = $array['real_name'];
        $data['certno'] = $array['id_card'];
        $data['bankcardtype'] = 'DEBIT_CARD';
        $data['bankcode'] = Config::get('bank.' . $array['itemno']);
        $data['servicetype'] = 'INSTALLMENT';
        $data['bankcardno'] = $array['bankcardno'];
        $data['mobileno'] = $array['mobileno'];
        $data['infotype'] = '2';
        $data['customerid'] = $array['CustomerID'];
        $bankNoApi = new BankNoApi();
        $result = $bankNoApi->send_message_to_web($data);
        Logger::info('返回结果为：','bank');
        Logger::info($result,'bank');
        $info = $this->return_bank_no_result($result);
        Logger::info($info,'bank');
        if($info['status'] == 'true'){
            Logger::info('睡觉1秒','bank');
            sleep(1);
            Logger::info('开始运行','bank');
            $this->update_bank_no_message($array,array('out_id'=>$out_id,'status'=>'3','resultcode'=>$info['resultcode']));
            $auth_reslut = $this->get_query_bank_no_auth($out_id,$array);
            return $auth_reslut;
        }else{
            Logger::error('系统繁忙','bank');
            return array('status' => 'false', 'msg' => '系统繁忙，请重试');
         }
    }

    /**
     * 根据out_id来获取相应的返回结果
     * @param $out_id 对应user_bank_no中的out_id字段
     * @return array 返回结果
     */
    public function get_query_bank_no_auth($out_id){
        for($i=0;$i<3;$i++){
            $flag = $this->query_bank_no_auth($out_id);
            if($flag){
                if($flag['status']=='true'){  //如果返回验证通过，则更新相应的帐号信息为通过
                    Logger::info_record($out_id.'银行卡认证通过','bank');
                    Logger::info($out_id.'银行卡认证通过','bank');
                    $this->userBankNoModel->update_user_bank_no_by_out_id(array('status'=>'1','updated_at'=>date('Y-m-d H:i:s',time()),'resultcode'=>$flag['resultcode']),$out_id);
                }elseif($flag['status'] == 'false'){ //如果返回验证不通过，则更新相应的帐号信息为不通过
                    Logger::info_record($out_id.'银行卡认证不通过，需要更换银行卡','bank');
                    Logger::info($out_id.'银行卡认证不通过，需要更换银行卡','bank');
                    $this->userBankNoModel->update_user_bank_no_by_out_id(array('status'=>'2','updated_at'=>date('Y-m-d H:i:s',time()),'resultcode'=>$flag['resultcode']),$out_id);
                }elseif($flag['status'] == 'error'){//该状态为容错，即易极付出现异常，返回结果，本平台进行容错操作
                    Logger::error_record($out_id.'银行卡认证不通过，易极付出错，进行容错操作','bank');
                    Logger::error($out_id.'银行卡认证不通过，易极付出错，进行容错操作','bank');
                    $this->userBankNoModel->update_user_bank_no_by_out_id(array('status'=>'4','updated_at'=>date('Y-m-d H:i:s',time()),'resultcode'=>$flag['resultcode']),$out_id);
                }
                return $flag;
            }else{
                if($i==2){
                    return array('status'=>'false','msg'=>'系统繁忙，请重试');
                }
                Logger::info('睡觉1秒','bank');
                sleep(1);
                Logger::info('醒','bank');
                continue;
            }
        }
    }

    /**
     * 进行api接口查询
     * @param $out_id
     * @return array|bool
     */
    public function query_bank_no_auth($out_id){
        $bankNoApi = new BankNoApi();
        $reslut = $bankNoApi->query_message_to_web($out_id);
        $info = $this->return_bank_no_result($reslut,false);
        if($info['status'] == 'middle'){
            return false;
        }else{
            return $info;
        }
    }


    /**
     * 对银行卡认证接口返回结果进行分类
     * @param $result
     * @param bool|true $type
     * @return array
     */
    public function return_bank_no_result($result,$type = true){
        if($type == true){
            if($result['result'] == '0000'){
                return array('status'=>'true','msg'=>'接收成功','resultcode'=>$result['resultcode']);
            }elseif($result['result'] == '1111'){
                return array('status'=>'false','msg'=>'接收失败','resultcode'=>$result['resultcode']);
            }elseif($result['result'] == '9001'){
                return array('status'=>'error','msg'=>'校验错误','resultcode'=>$result['resultcode']);
            }elseif($result['result'] == '9009'){
                return array('status'=>'error','msg'=>'密码或用户名错误','resultcode'=>$result['resultcode']);
            }elseif($result['result'] == '9999'){
                return array('status'=>'error','msg'=>'程序异常','resultcode'=>$result['resultcode']);
            }
        }else{
            if($result['result'] == '0000'){
                return array('status'=>'true','msg'=>'认证成功','resultcode'=>$result['resultcode']);
            }elseif($result['result'] == '1111'){
                return $this->return_error_status($result);
                //    return array('status'=>'false','msg'=>'银行账号不正确，请更换','resultcode'=>$result['resultcode']);
            }elseif($result['result'] == '1001'){
                return array('status'=>'middle','msg'=>'请等待','resultcode'=>$result['resultcode']);
            }elseif($result['result'] == '9001'){
                return array('status'=>'error','msg'=>'错误信息','resultcode'=>$result['resultcode']);
            }elseif($result['result'] == '9009'){
                return array('status'=>'error','msg'=>'密码或用户名错误','resultcode'=>$result['resultcode']);
            }elseif($result['result'] == '9999'){
                return array('status'=>'error','msg'=>'程序异常','resultcode'=>$result['resultcode']);
            }elseif($result['result'] == '9002'){
                return array('status'=>'error','msg'=>'数据不存在','resultcode'=>$result['resultcode']);
            }
        }

    }

    public function return_error_status($result){
        if(in_array($result['resultcode'],array('S037_02_1000','U000_07_1005','comn_00_0000'
        ,'S037_02_1001','S030_00_0013','S030_00_6666','S024_02_0301','S024_04_0104','S024_02_0301'))){
            return array('status'=>'error','msg'=>'易极付出错','resultcode'=>$result['resultcode']);
        }elseif($result['resultcode'] == 'S024_01_0004'){
            return array('status'=>'error','msg'=>'银行卡身份证错误,直接跳过','resultcode'=>$result['resultcode']);
        }else{
            return array('status'=>'false','msg'=>'银行账号不正确，请更换','resultcode'=>$result['resultcode']);
        }
    }

}
