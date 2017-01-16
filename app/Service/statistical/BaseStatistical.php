<?php
namespace App\Service\statistical;
/**
 * 统计基类
 * Class BaseStatistical
 * @package App\Service\statistical
 */
abstract class BaseStatistical{
    protected $_query;
    public $_per_page_count = 15;

    public function __construct(Array $arr = []){
        $this->commQuery($arr);
    }

    abstract public function commQuery();

    /**
     * 下载excel
     */
    public function downloadExcel(){
        $data = $this->downloadData();
        DownloadExcel::publicDownloadExcel($data);
        exit;
    }

    public function downloadData(){
	    $data = $this->getDownloadData();
	    DownloadExcel::publicDownloadExcel($data);
	    exit;
    }

	abstract function getDownloadData();
}