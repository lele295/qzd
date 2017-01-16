<?php
namespace App\Extensions;
use App\Model\Base\SyncModel;
use App\Util\FileReader;

class CustomValidator extends \Illuminate\Validation\Validator {

    /**
     * @desc 字段值仅允许汉字
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateHanzi($attribute, $value, $parameters){
        if(!preg_match("/^[\x{4e00}-\x{9fa5}]+$/u",$value)){
            return false;
        } else{
            return true;
        }
    }

    /**
     * @desc 字段值仅允许汉字,字母，数字
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateHanziAlphaNum($attribute, $value, $parameters){
        if(!preg_match("/^[\x{4e00}-\x{9fa5}A-Za-z0-9]+$/u",$value)){
            return false;
        } else{
            return true;
        }
    }

    /**
     * @desc 安硕文本，不能有引号，这个我也不知道他们为什么要这样
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateAsText($attribute, $value, $parameters){
        if(preg_match('/^.*[\"\']+.*/',$value)){
            return false;
        }else{
            return true;
        }
    }



    /**
     * @desc 字段值仅允许安硕的yes,no码值
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateAsAccepted($attribute, $value, $parameters){
        $itemanme = SyncModel::yesNoName($value);
        if($itemanme === ''){
            return false;
        }else{
            return true;
        }
    }

    /**
     * @desc 验证手机号
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateMobile($attribute, $value, $parameters){
        if(!preg_match("/^1[34578][0-9]{9}$/",$value)){
            return false;
        } else{
            return true;
        }
    }

    /**
     * @desc 安硕其它联系人关系验证
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateAsRelationshipOther($attribute, $value, $parameters){
        $itemanme = SyncModel::RelationshipOther($value);
        if($itemanme === ''){
            return false;
        }else{
            return true;
        }
    }

    /**
     * @desc 工作年份
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateAsYears($attribute, $value, $parameters){
        $itemanme = SyncModel::yearsName($value);
        if($itemanme === ''){
            return false;
        }else{
            return true;
        }
    }

    /**
     * @desc 安硕在职时间
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateAsJobTime($attribute, $value, $parameters){
        $itemanme = SyncModel::jobTime($value);
        if($itemanme === ''){
            return false;
        }else{
            return true;
        }
    }


    /**
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateAsEducationExperience($attribute, $value, $parameters){
        $itemanme = SyncModel::educationExperienceName($value);
        if($itemanme === ''){
            return false;
        }else{
            return true;
        }
    }


    /**
     * @desc 家庭成员关系
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateAsFamilyRelative($attribute, $value, $parameters){
        $itemanme = SyncModel::familyRelativeName($value);
        if($itemanme === ''){
            return false;
        }else{
            return true;
        }
    }


    /**
     * @desc 检查图片文件是否存在
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateCheckImage($attribute, $value, $parameters){
        if(FileReader::get_file_exists($value))
        {
            return true;
        }else{
            return false;
        }
    }

    /**
     * @desc 检测安硕的地址
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateAsAddress($attribute, $value, $parameters){
        $itemanme = SyncModel::cityName($value);
        if($itemanme === ''){
            return false;
        }else{
            return true;
        }
    }

    /**
     * @desc 职务
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateAsDuties($attribute, $value, $parameters){
        $itemanme = SyncModel::dutiesName($value);
        if($itemanme === ''){
            return false;
        }else{
            return true;
        }
    }


    /**
     * @desc 单位性质
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateAsOrgAttribute($attribute, $value, $parameters){
        $itemanme = SyncModel::orgAttributeName($value);
        if($itemanme === ''){
            return false;
        }else{
            return true;
        }
    }

    /**
     * @desc 单位所属行业
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateAsUnitKind($attribute, $value, $parameters){
        $itemanme = SyncModel::unitKindName($value);
        if($itemanme === ''){
            return false;
        }else{
            return true;
        }
    }

    /**
     * @desc 座机验证
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateTel($attribute, $value, $parameters){
        if(!preg_match("/^(0[0-9]{2,3}\-)?([2-9][0-9]{6,7})+(\-[0-9]{1,4})?$/u",$value)){
            return false;
        } else{
            return true;
        }
    }

    /**
     * @desc 银行卡号验证
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateBankAccount($attribute, $value, $parameters){
        if(!preg_match("/^[1-46-9]\d{15,18}$/u",$value)){
            return false;
        } else{
            return true;
        }
    }

    /**
     * @desc 验证身份证到期日,必须大于今天
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public function validateMaturityDate($attribute, $value, $parameters){
        $date = strtotime($value);
        if($date > time()){
            return true;
        }else{
            return false;
        }
    }
}