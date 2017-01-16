<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/11/20
 * Time: 16:54
 */

namespace App\Service\admin;


use App\Model\Admin\EmailModel;

class EmailSetService extends Service
{
    private $emailModel;
    public function __construct(){
        $this->emailModel = new EmailModel();
    }

    public function get_email_list(){
        $info = $this->emailModel->get_email_list();
        return $info;
    }

    public function add_email($array){
        $info = $this->emailModel->insert_email($array);
        return $info;
    }
}