<?php
namespace App\Util;
/**
 * 空的分页类
 * Class EmptyPaginate
 * @package App\Util
 */
class EmptyPaginate implements \Iterator,\Countable{
     public function current (){
     }
     public function key () {}
     public function next ()  {}
     public function rewind ()  {}
     public function valid ()  {
         return false;
     }

    public function count(){
        return 0;
    }

    public function total(){
        return 0;
    }

    public function appends(){
        return $this;
    }

    public function render(){
        return '';
    }
}