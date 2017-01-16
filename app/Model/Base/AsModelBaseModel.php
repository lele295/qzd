<?php
namespace App\Model\Base;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class AsModelBaseModel extends Model{
    public $timestamps = false; //关闭时间戳更新
    protected $primaryKey = 'Id';
    protected $guarded = [];
    protected $_wx_fill_data = array('ReplaceAccount','OpenBranch');


    protected function validateRule(){
        return array();
    }

    protected function dealWithData($data){
        return $data;
    }


    public function wxUpdate(array $attributes = Array()){
        $attributes = array_only($attributes,$this->_wx_fill_data);
        $formData = array_merge(array('OperationTime'=>date('Y-m-d H:i:s')),$attributes);
        $this->update($this->dealWithData($formData));
    }


    public function customUpdate(array $attributes = Array()){



        //验证
        $validateRes = $this->validate($attributes,$this->validateRule());
        if($validateRes instanceof ResourceErrorModel){
            return $validateRes;
        }

        $formData = array_merge(array('OperationTime'=>date('Y-m-d H:i:s')),$attributes);
        $this->update($formData);
        /**
         * 然后这个去check是否填写完成,获得当前的OrderId
         */
//        (new Order($this->OrderId))->judgeResourceDone();
    }

    public function toArray(){
        $arr = parent::toArray();
        $exceptArray = array('Id','OrderId','OperationTime');
        $filterArray = $this->toArrayFilter();
        foreach($filterArray as $key=>$val){
            array_push($exceptArray,$val);
        }
        return array_except($arr,$exceptArray);
    }

    /**
     * 转换成数组的时候的过滤函数
     */
    public function toArrayFilter(){
        return array();
    }


    /**
     *
     */
    public function check(){
        $formData = $this->toArray();
        $validateRes = $this->validate($formData,$this->validateRule());
        if($validateRes instanceof ResourceErrorModel){
            return $validateRes;
        }
        return $formData;
    }

    /**
     * @desc model验证，如果如果验证未通过返回一个ResourceError对象
     * @param $attributes
     * @param $rules
     * @return mixed
     */
    public function validate($attributes,$rules){
        $validator = Validator::make($attributes,$rules);
        if($validator->passes()){
            return true;
        }else{
            /**
             * 获取第一条错误信息
             */
            $message = $validator->messages();
            return new ResourceErrorModel($message->first());
        }
    }


    /**
     * @desc 属性表单过滤
     * @param $attributes
     * @return bool
     */
    public function filterUpdateData($attributes){
        return array_except($attributes,array('Id','OrderId','OperationTime'));
    }
}