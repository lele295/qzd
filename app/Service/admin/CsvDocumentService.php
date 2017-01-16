<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/10/22
 * Time: 15:20
 */

namespace App\Service\admin;


class CsvDocumentService extends Service
{
    const text = "\t";

    public function __construct(){

    }

    private function down_header($filename){
        if(empty($filename)){
            $filename = date('Y-m-d H:i:s',time());
        }
        header ( "Content-type:application/vnd.ms-excel" );
        header ( "Content-Disposition:filename=" . iconv ( "UTF-8", "GBK", $filename ) . ".csv" );
        $fp = fopen('php://output','a');
        return $fp;
    }

    public function down($head,$filename = ''){
        $fp = $this->down_header($filename);   //获取一个新的文件句柄
        // 输出Excel列名信息
        foreach($head as $i=>$v){
            $head[$i] = iconv('utf-8','gbk',$v);
        }
        // 将数据通过fputcsv写到文件句柄
        fputcsv($fp,$head);
        return $fp;
    }

    public function text_format($content,$text_form = false){
        try {
            if ($text_form) {
                $val = iconv('utf-8', 'GBK', self::text . $content);
            } else {
                $val = iconv('utf-8', 'GBK', $content);
            }
        }catch(\Exception $e){
            return iconv('utf-8', 'GBK', '');
        }
        return $val;
    }
}