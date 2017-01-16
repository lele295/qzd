<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/11/23
 * Time: 10:03
 */

namespace App\Service\admin;


use App\Model\Admin\EmailLevelModel;

class EmailLevelService extends Service{
    private $emailLevelModel;

    public function __construct(){
        $this->emailLevelModel = new EmailLevelModel();
    }
    public function add_email_level($data){
        $info = $this->emailLevelModel->add_email_level($data);
        return $info;
    }
}