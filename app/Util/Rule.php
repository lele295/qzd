<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2016/1/18
 * Time: 11:32
 */

namespace App\Util;


use App\Log\Facades\Logger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class Rule {
    static public function strFilter($info){
        $array = array();
        foreach($info as $key=>$str){
            $str = str_replace('`','',$str);
            $str = str_replace(' ','',$str);
            $str = str_replace('!','',$str);
            $str = str_replace('！','',$str);
            $str = str_replace('{', '', $str);
            $str = str_replace('}', '', $str);
            $str = str_replace(';', '', $str);
            $str = str_replace('；', '', $str);
            $str = str_replace(',','',$str);
            $str = str_replace('，','',$str);
            $str = str_replace('#','',$str);
            $str = str_replace('~','',$str);
            $str = ltrim($str);
            $str = chop($str);
            $array = array_add($array,$key,$str);
        }

        return $array;
    }

    //实名认证
    static public function auth_filter($info){
        $rule = array(
            'real_name' => 'required',
            'id_card' => 'required',
        );
        $result = self::validator($rule,$info);
        return $result;
    }

    static public function loan_shisuan_filter($info){
        $rule = array(
            'amount'=>'required',
            'period'=>'required',
            'remark_descript'=>'required',
            'remark'=>'required',
        );
        $result = self::validator($rule,$info);
        return $result;
    }

    //试算过虑
    static public function loan_filter($info){
        if(DxOperator::$flag == true){
            return array('status'=>true,'data'=>array('message'=>'电销录单，不需过滤'));
        }
        $rule = array(
            'Sex' => 'required',
            'CertType' => 'required',
            'NativePlace' => 'required',
            'Villagetown' => 'required',
            'Street' => 'required',
            'Community' => 'required',
            'CellNo' => 'required',
            'FamilyAdd' => 'required',
            'EmployRecord' => 'required',
            'HeadShip' => 'required',
            'UnitKind' => 'required',
            'CellProperty' => 'required',
            'Flag3' => 'required',
            'House' => 'required',
            'HouseRent' => 'required',
            'Flag10' => 'required',
            'KinshipAdd' => 'required',
            'EduExperience' => 'required',
            'FamilyMonthIncome' => 'required',
            'JobTime' => 'required',
            'JobTotal' => 'required',
            'Falg4' => 'required',
            'Contactrelation' => 'required'
            //'Issueinstitution' => 'required'
        );
        $validator = Validator::make($info, $rule);
        if(!$validator->passes()){
            $message = $validator->messages();
            return array('status'=>false,'data'=>array('message'=>$message->first()));
        }else{
            return array('status'=>true,'data'=>array('message'=>'信息完整，我们符合贷款'));
        }
    }

    static public function car_loan_filter($array){
        $rule = array(
            'CertType' => 'required',
        );
        $result = self::validator($rule,$array);
        return $result;
    }

    static public function person_info_filter($info){
    //    $filter_array = self::strFilter($info);
        $rule = array(
            'Marriage' => 'required',
            'Childrentotal' =>'required|integer|max:10',
            'SpouseName'=> 'required_if:Marriage,2|as_text',
            'SpouseTel'=> 'required_if:Marriage,2',

            'MaturityDate' => sprintf('required|after:%s',date('Y-m-d')),
            'Flag2' => 'required',
            'FamilyAdd' => 'required_if:Flag2,2|as_text',
            'Countryside' => 'required_if:Flag2,2|as_text|max:15',
            'Villagecenter' => 'required_if:Flag2,2|as_text|max:15',
            'Plot' => 'required_if:Flag2,2|as_text|max:100',
            'Room' => 'required_if:Flag2,2|as_text',

            'ReplaceAccount' => 'required|bank_account',
            'KinshipName'=>'required|as_text',
            'KinshipTel'=>'required',
            'RelativeType'=>'required',
            'OpenBranch'=>'required',
            'OpenBank'=>'required',
        );
        $result = self::validator($rule,$info);
        return $result;
    }

    static public function firm_info_filter($info){
        $rule = array(
            'WorkAdd'=>'required',
            'WorkCorp'=>'required|max:40|as_text',
            'UnitCountryside'=>'required|as_text|max:15',
            'UnitStreet' => 'required|as_text|max:15',
            'UnitRoom' => 'required|as_text|max:100',
            'UnitNo' => 'required|as_text|max:100',

            'SelfMonthIncome' => 'required|numeric',

            'Flag8' => 'required',
            'area_code' => 'required',
            'WorkTel'=>'required|max:20',
            'OtherContact'=>'required|as_text',
            'ContactTel'=>'required'
        );
        $result = self::validator($rule,$info);
        return $result;
    }

    static public function validator($rule,$array,$filter=true){
        if($filter){
            $array = self::strFilter($array);
        }
        $validator = Validator::make($array,$rule);
        if(!$validator->passes()){
            $message = $validator->messages();
            return array('status'=>false,'data'=>array('message'=>$message->first()));
        }else{
            return array('status'=>true,'data'=>array('message'=>$array));
        }
    }

    static public function file_picture_filter($info){
        $rule =  array(
            'cert_face_pic' => 'required|check_image',
            'cert_opposite_pic' => 'required|check_image',
            'custom_pic' => 'required|check_image'
        );
        $result = self::validator($rule,$info);
        return $result;
    }


    static public function work_message_filter($info){
        $rule = array(
            'WorkCorp'=>'required|min:5|max:40|as_text',
            'EmployRecord'=>'required',
            'HeadShip'=>'required',
            'UnitKind'=>'required',
            'CellProperty'=>'required',
            'Flag3'=>'required|numeric',
            'WorkAdd'=>'required',
            'UnitCountryside'=>'required|as_text|max:15',
            'UnitStreet'=>'required|as_text|max:15',
            'UnitRoom'=>'required',
            'UnitNo'=>'required',
        );
        $result = self::validator($rule,$info);
        return $result;
    }

    static public function comm_add_message_filter($info,$flag = true){
        if($flag){
            $rule = array(
                'Flag8'=>'required',
//            'CommAdd'=>'required',
//            'EmailCountryside'=>'required',
//            'EmailStreet'=>'required',
//            'EmailPlot'=>'required',
//            'EmailRoom'=>'required',
                //'MobileTelephone'=>'required',
                'WorkTel'=>'required|numeric',
                'area_code'=>'required'
            );
        }else{
            $rule = array(
                'Flag8'=>'required',
                'MobileTelephone'=>'required',
                'WorkTel'=>'required|numeric',
            );
        }

        $result = self::validator($rule,$info);
        return $result;
    }

    static public function custom_base_message_filter($info){
        $rule = array(
            'MaturityDate'=>'required|maturity_date',
            'NativePlace'=>'required',
            'Villagetown'=>'required',
            'Street'=>'required',
            'Community'=>'required',
            'CellNo'=>'required',
            'Flag2'=>'required',
            'FamilyAdd'=>'required_if:Flag2,2',
            'Countryside'=>'required_if:Flag2,2',
            'Villagecenter'=>'required_if:Flag2,2',
            'Plot'=>'required_if:Flag2,2',
            'Room'=>'required_if:Flag2,2',
        );
        $result = self::validator($rule,$info);
        return $result;
    }

    static public function family_message_filter($info){
        $rule = array(
            'Marriage'=>'required',
            'Childrentotal'=>'required',
            'SpouseName'=> 'required_if:Marriage,2|as_text',
            'SpouseTel'=> 'required_if:Marriage,2|mobile',
            'House'=>'required',
            'Houserent'=>'required',
            'KinshipName'=>'required',
            'KinshipTel'=>'required',
            'Flag10'=>'required',
            'KinshipAdd'=>'required',
            'RelativeType'=>'required',
        );
        $result = self::validator($rule,$info);
        return $result;
    }

    static public function income_message_filter($info){
        $rule = array(
            'EduExperience'=>'required',
            'FamilyMonthIncome'=>'required|numeric',
            'JobTime'=>'required',
            'JobTotal'=>'required',
            'SelfMonthIncome'=>'required|numeric|between:1000,99999',
            'Falg4'=>'required',
            'OtherContact'=>'required',
            'Contactrelation'=>'required',
            'ContactTel'=>'required|mobile',
        );
        $result = self::validator($rule,$info);
        return $result;
    }

    static public function repayment_message_filter($info){
        $rule = array(
            'ReplaceAccount'=>'required|bank_account',
            'OpenBranch'=>'required',
        );
        $result = self::validator($rule,$info);
        return $result;
    }

    //PC注册验证
    static public function pc_register_filter($info){
        $rule = array(
            'mobile' => 'required|min:10',
            'mobile_code' => 'required|min:5|numeric',
            'password' => 'required|min:5|confirmed'
        );
        $result = self::validator($rule,$info);
        return $result;
    }

    static public function pc_set_forget_password($input){
        $rule = array(
            'oldPassword'=>'required',
            'password'=>'required|min:5',
        );
        $result = self::validator($rule,$input);
        return $result;
    }
}