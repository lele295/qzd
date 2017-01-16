<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/11/26
 * Time: 15:41
 */

namespace App\Util;


class Email
{
    private $view = '';
    private $data;
    private $description;
    private $emailName;
    private $phoneMessage = '';

    public function getView(){
        return $this->view;
    }

    public function getData(){
        return $this->data;
    }

    public function getPhoneMessage(){
        return $this->phoneMessage;
    }


    public function getDescription(){
        return $this->description;
    }

    public function getEmailName(){
        return $this->emailName;
    }

    public function setView($view){
        $this->view = $view;
    }

    public function setData($data){
        $this->data = $data;
    }


    public function setDescription($description){
        $this->description = $description;
    }

    public function setEmailName($emailName){
        $this->emailName = $emailName;
    }

    public function setPhoneMessage($phoneMessage){
        $this->phoneMessage = $phoneMessage;
    }

}