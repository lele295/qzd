<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/11/12
 * Time: 16:19
 */

namespace App\Service\admin;


use App\Log\Facades\Logger;
use App\Model\Admin\EmailActionModel;
use App\Model\Admin\EmailModel;
use App\Model\Admin\EmailRoleModel;
use App\Model\Admin\EmailRoleRelateModel;
use App\Model\Admin\LoanAdminModel;
use App\Model\Admin\SyncRecordModel;
use App\Model\Base\AsUserAuthModel;
use App\Model\Base\SyncBankputInfoModel;
use App\Model\Base\SyncBusinessCashloaneventModel;
use App\Model\Base\SyncBusinessCashloanRelativeModel;
use App\Model\Base\SyncBusinessTypeModel;
use App\Model\Base\SyncCodeLibraryModel;
use App\Model\Base\SyncEcrmImageTypeModel;
use App\Model\Base\SyncProductBusinesstypeModel;
use App\Model\Base\SyncProductTypesModel;
use App\Model\Base\SyncRetailInfoModel;
use App\Model\Base\SyncStoreInfoModel;
use App\Model\Base\SyncStorerelativesalesmanModel;
use App\Model\Base\SyncUserInfoModel;
use App\Util\Email;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class EmailService extends Service
{
    public function send($exception,$descript){
        $email = new Email();
        $email->setData(array('exception'=>$exception));
        $email->setDescription($descript);
        $email->setEmailName('email_exception');
        $email->setView('email.admin.exceptemail');
        $this->sendEmail($email);
    }

    public function send_every_day_sync_data_to_admin(){
        $email = new Email();
        $array = $this->get_sync_data();
        $email->setData(array('array'=>$array));
        $email->setView('email.admin.sync_email');
        $email->setEmailName('email_sync_data');
        $email->setDescription('数据同步结果报告');
        $string = '';
        foreach ($array as $val) {
            $string = $string.$val['name'].':'.$val['old_data'].','.$val['new_data'].';';
        }
        $email->setPhoneMessage(date('Y-m-d H:i:s',time()).$string);
        $this->sendEmail($email);
    }

    public function get_sync_data(){
        $date = date('Y-m-d',strtotime("-1 days"));
        $record = $this->get_history_record($date);
        $syncRecordModel = new SyncRecordModel();
        $array = array();
        $asUserAuthModel = new AsUserAuthModel();
        $asUserAuthCount = $asUserAuthModel->get_user_auth_count();
        $data = array('table'=>'sync_cashloan_customer','old_data'=>$record['sync_cashloan_customer'],'new_data'=>$asUserAuthCount,'name'=>'预约名单');
        $syncRecordModel->insert_new_record(array('table_name'=>'sync_cashloan_customer','count'=>$asUserAuthCount,'date'=>date('Y-m-d',time())));
        $array = array_add($array,'sync_cashloan_customer',$data);
        $syncBankputInfoModel = new SyncBankputInfoModel();
        $syncBankputInfoCount = $syncBankputInfoModel->get_sync_bankput_info_count();
        $data = array('table'=>'sync_bankput_info','old_data'=>$record['sync_bankput_info'],'new_data'=>$syncBankputInfoCount,'name'=>'支行码表');
        $syncRecordModel->insert_new_record(array('table_name'=>'sync_bankput_info','count'=>$syncBankputInfoCount,'date'=>date('Y-m-d',time())));
        $array = array_add($array,'sync_bankput_info',$data);
        $syncBusinessTypeModel = new SyncBusinessTypeModel();
        $syncBusinessTypeCount = $syncBusinessTypeModel->get_sync_business_type_count();
        $data = array('table'=>'sync_business_type','old_data'=>$record['sync_business_type'],'new_data'=>$syncBusinessTypeCount,'name'=>'产品表');
        $syncRecordModel->insert_new_record(array('table_name'=>'sync_business_type','count'=>$syncBusinessTypeCount,'date'=>date('Y-m-d',time())));
        $array = array_add($array,'sync_business_type',$data);
        $syncCodeLibraryModel = new SyncCodeLibraryModel();
        $syncCodeLibraryCount = $syncCodeLibraryModel->get_sync_code_library_count();
        $data = array('table'=>'sync_code_library','old_data'=>$record['sync_code_library'],'new_data'=>$syncCodeLibraryCount,'name'=>'数据字典');
        $syncRecordModel->insert_new_record(array('table_name'=>'sync_code_library','count'=>$syncCodeLibraryCount,'date'=>date('Y-m-d',time())));
        $array = array_add($array,'sync_code_library',$data);
        $syncEcrmImageTypeModel = new SyncEcrmImageTypeModel();
        $syncEcrmImageTypeCount = $syncEcrmImageTypeModel->get_sync_ecm_image_type_count();
        $data = array('table'=>'sync_ecrm_image_type','old_data'=>$record['sync_ecrm_image_type'],'new_data'=>$syncEcrmImageTypeCount,'name'=>'图片类型');
        $syncRecordModel->insert_new_record(array('table_name'=>'sync_ecrm_image_type','count'=>$syncEcrmImageTypeCount,'date'=>date('Y-m-d',time())));
        $array = array_add($array,'sync_ecrm_image_type',$data);
        $syncRetailInfoModel = new SyncRetailInfoModel();
        $syncRetailInfoCount = $syncRetailInfoModel->get_sync_retail_info_count();
        $data = array('table'=>'sync_retail_info','old_data'=>$record['sync_retail_info'],'new_data'=>$syncRetailInfoCount,'name'=>'商户信息');
        $syncRecordModel->insert_new_record(array('table_name'=>'sync_retail_info','count'=>$syncRetailInfoCount,'date'=>date('Y-m-d',time())));
        $array = array_add($array,'sync_retail_info',$data);
        $syncStoreInfoModel = new SyncStoreInfoModel();
        $syncStoreInfoCount = $syncStoreInfoModel->get_sync_store_info_count();
        $data = array('table'=>'sync_store_info','old_data'=>$record['sync_store_info'],'new_data'=>$syncStoreInfoCount,'name'=>'门店信息');
        $syncRecordModel->insert_new_record(array('table_name'=>'sync_store_info','count'=>$syncStoreInfoCount,'date'=>date('Y-m-d',time())));
        $array = array_add($array,'sync_store_info',$data);
        return $array;
    }

    public function send_status_null_email(){
        $loanAdminService = new LoanService();
        $string = $loanAdminService->get_loan_by_status(array(''));
        if($string){
            $email = new Email();
            $email->setData(array('exception'=>$string));
            $email->setDescription('合同状态为空');
            $email->setEmailName('email_exception');
            $email->setView('email.admin.exceptemail');
            $this->sendEmail($email);
        }else{
            return '';
        }
    }


    public function get_history_record($date){
        $syncRecord = new SyncRecordModel();
        $info = $syncRecord->get_record_by_date($date);
        $array = array();
        if($info){
            foreach($info as $val){
                $array = array_add($array,$val->table_name,$val->count);
            }
        }else{
            $array['sync_cashloan_customer']=0;
            $array['sync_bankput_info']=0;
            $array['sync_business_type']=0;
            $array['sync_code_library']=0;
            $array['sync_ecrm_image_type']=0;
            $array['sync_retail_info']=0;
            $array['sync_store_info']=0;
        }
        return $array;
    }

    /**
     * @param Email $email
     */
    public function sendEmail(Email $email){
        $emailActoinModel = new EmailActionModel();
        $emailRoleRelateModel = new EmailRoleRelateModel();
        $emailModel = new EmailModel();
        $sendService = new SendService();
        $info = $emailActoinModel->get_email_action_by_email_name($email->getEmailName());
        if($info){
            $data = $emailRoleRelateModel->get_email_id_by_role_id(unserialize($info->email_role));
            $array = array();
            foreach($data as $val){
                array_push($array,$val->email_id);
            }
            $email_list = $emailModel->get_email_list_by_id($array);
            foreach($email_list as $val) {
                try {
                    Mail::send($email->getView(), $email->getData(), function ($message) use ($email, $val) {
                        $message->to($val->email, $email->getDescription())->subject($email->getDescription());
                    });
                }catch(\Exception $e){
                    Logger::info('邮件发送出现异常');
                }
                    if ($email->getPhoneMessage()) {
                        $sendService->send_msn_to_admin($email->getPhoneMessage(), $val->mobile);
                    }
            }
        }else{
            return false;
        }

    }


}