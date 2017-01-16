<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/11/24
 * Time: 10:27
 */

namespace App\Service\admin;


use App\Model\Admin\EmailActionModel;

class EmailActionService extends Service
{
    public function get_email_action_list(){
        $emailActionModel = new EmailActionModel();
        $info = $emailActionModel->get_email_action_list();
        return $info;
    }

    public function insert_email_action($data){
        $emailActionModel = new EmailActionModel();
        $info = $emailActionModel->insert_email_action($data);
        return $info;
    }

    public function delete_email_action($id){
        $emailActionModel = new EmailActionModel();
        $info = $emailActionModel->delete_email_action($id);
        return $info;
    }
}