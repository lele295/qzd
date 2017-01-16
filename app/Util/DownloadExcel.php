<?php
namespace App\Util;

/**
 * 下载excel
 * Class DownloadExcel
 */
class DownloadExcel
{

    /**
     * 公用的下载excel的方法
     * 
     * @param Array $data            
     */
    static public function publicDownloadExcel(Array $data)
    {
        /*
         * $data['name']：文件名，字符串
         * $data['title']：表格头，数组
         * $data['data']：表数据，数组
         * $data['length']：表格长度，数组，可为空。例：['A'=>20,'B'=>10,'C'=>30]
         */
        ob_clean();
        // 创建对象
        $objPHPExcel = new \PHPExcel();
        // 设置属性
        $objPHPExcel->getProperties()
            ->setCreator("baiqian_stsr")
            ->setTitle("baiqian_stsr");
        // 创建当前活动工作表对象
        $objActSheet = $objPHPExcel->getActiveSheet();
        
        if (! empty($data['length'])) {
            // 设置表格长度
            foreach ($data['length'] as $k => $v) {
                $objActSheet->getColumnDimension($k)->setWidth($v);
            }
        }
        // 设置标题
        $i = 1;
        foreach ($data['title'] as $key => $val) {
            $index = $key + 1;
            $objActSheet->setCellValue(self::getColumnByNum($index) . $i, $val);
        }
        
        // 设置内容
        foreach ($data['data'] as $item) {
            $i ++;
            foreach ($item as $key => $val) {
                $index = $key + 1;
                if (isset($data['format_text_array']) && in_array($index, $data['format_text_array'])) {
                    $objActSheet->setCellValueExplicit(self::getColumnByNum($index) . $i, $val, \PHPExcel_Cell_DataType::TYPE_STRING);
                } else {
                    $objActSheet->setCellValue(self::getColumnByNum($index) . $i, $val . ' ');
                }
            }
        }
        // 保存文件
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        // $objWriter->save(str_replace('.php', '.xlsx', "sybjs.xlsx"));
        $saveName = $data['name'] . '.xlsx';
        header('Pragma:public');
        header('Content-Type:application/x-msexecl;name="' . $saveName . '"');
        header("Content-Disposition:inline;filename=\"$saveName\"");
        $objWriter->save('php://output');
    }

    /**
     * 下载excel文件
     * 
     * @param array $data            
     */
    static public function downLoadExcel(Array $data)
    {
        
        // 创建对象
        $objPHPExcel = new \PHPExcel();
        // 设置属性
        $objPHPExcel->getProperties()->setCreator("qzd");
        $objPHPExcel->getProperties()->setTitle("qzd");
        // 创建当前活动工作表对象
        $objActSheet = $objPHPExcel->getActiveSheet();
        // 设置列的宽度
        // $objActSheet->getColumnDimension('B')->setWidth(50);
        
        $i = 1;
        foreach ($data['title'] as $key => $val) {
            $index = $key + 1;
            $objActSheet->setCellValue(self::getColumnByNum($index) . $i, $val);
        }
        
        // 设置内容
        foreach ($data['data'] as $item) {
            $i ++;
            foreach ($item as $key => $val) {
                $index = $key + 1;
                $objActSheet->setCellValue(self::getColumnByNum($index) . $i, $val);
            }
        }
        
        // 保存文件
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $saveName = $data['name'] . '.xlsx';
        
        // 生成xlsx文件并存入当前文件目录
        function saveExcelToLocalFile($objWriter, $saveName)
        {
            $dirPath = storage_path() . '/../public/excel/' . date('Y-m-d', time());
            if (! file_exists($dirPath)) {
                mkdir($dirPath);
            }
            $filePath = $dirPath . '/' . $saveName;
            $objWriter->save($filePath);
            
            return 'excel/' . date('Y-m-d', time()) . '/' . $saveName;
        }
        
        // 返回已经存好的文件目录地址提供下载
        $response = array(
            'success' => true,
            'url' => saveExcelToLocalFile($objWriter, $saveName)
        );
        return json_encode($response);
        
        // header('Pragma:public');
        // header('Content-Type:application/x-msexecl;name="'.$saveName.'"');
        // header("Content-Disposition:inline;filename=\"$saveName\"");
        
        // header('Content-Type: application/vnd.ms-excel');
        // header('Content-Disposition: attachment;filename="'.$saveName.'.xls"');
        // header('Cache-Control: max-age=0');
        // $objWriter->save('php://output');
        /*
         * //保存文件
         * $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
         * $saveName = $data['name'].'.xlsx';
         * header('Pragma:public');
         * header('Content-Type:application/x-msexecl;name="'.$saveName.'"');
         * header("Content-Disposition:inline;filename=\"$saveName\"");
         * //$objWriter->save('php://output');
         */
    }
    
    // phpexcel操作设置列宽
    static public function getColumnByNum($index)
    {
        switch ($index) {
            case 1:
                return 'A';
            case 2:
                return 'B';
            case 3:
                return 'C';
            case 4:
                return 'D';
            case 5:
                return 'E';
            case 6:
                return 'F';
            case 7:
                return 'G';
            case 8:
                return 'H';
            case 9:
                return 'I';
            case 10:
                return 'J';
            case 11:
                return 'K';
            case 12:
                return 'L';
            case 13:
                return 'M';
            case 14:
                return 'N';
            case 15:
                return 'O';
            case 16:
                return 'P';
            case 17:
                return 'Q';
            case 18:
                return 'R';
            case 19:
                return 'S';
            case 20:
                return 'T';
            case 21:
                return 'U';
            case 22:
                return 'V';
            case 23:
                return 'W';
            case 24:
                return 'X';
            case 25:
                return 'Y';
            case 26:
                return 'Z';
            case 27:
                return 'AA';
            case 28:
                return 'AB';
            case 29:
                return 'AC';
            case 30:
                return 'AD';
            case 31:
                return 'AE';
            case 32:
                return 'AF';
            case 33:
                return 'AG';
            case 34:
                return 'AH';
            case 35:
                return 'AI';
            case 36:
                return 'AJ';
            case 37:
                return 'AK';
            case 38:
                return 'AL';
            case 39:
                return 'AM';
        }
    }

    /**
     * 下载excel文件
     * @param array $data
     */
    static public function pushExcel(Array $data){

        //创建对象
        $objPHPExcel = new \PHPExcel();
        //设置属性
        $objPHPExcel->getProperties()->setCreator("qzd");
        $objPHPExcel->getProperties()->setTitle("qzd");
        //创建当前活动工作表对象
        $objActSheet = $objPHPExcel->getActiveSheet();
        //设置列的宽度
        //$objActSheet->getColumnDimension('B')->setWidth(50);

        $i = 1;
        foreach($data['title'] as $key=>$val){
            $index = $key+1;
            $objActSheet->setCellValue(self::getColumnByNum($index) . $i,$val);
        }

        //设置内容
        foreach($data['data'] as $item){
            $i++;
            foreach($item as $key=>$val){
                $index = $key + 1;
                $objActSheet->setCellValue(self::getColumnByNum($index) . $i,$val);
            }
        }

        //保存文件
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $saveName = $data['name'].'.xlsx';



//        header("Content-Type: application/force-download");
//        header("Content-Type: application/octet-stream");
//        header("Content-Type: application/download");
//        header('Content-Disposition:inline;filename="'.$saveName.'"');
//        header("Content-Transfer-Encoding: binary");
//        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
//        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
//        header("Pragma: no-cache");
//        $objWriter->save('php://output');

        //生成xlsx文件并存入当前文件目录
        function saveExcelToLocalFile($objWriter,$saveName){
            $path = '/export/excel/'.date('Y-m-d',time());
            $dirPath = storage_path().$path;
            if(!file_exists($dirPath)){
                mkdir($dirPath, 0777, true);
            }
            $filePath = $dirPath.'/'.$saveName;
            $objWriter->save($filePath);

            return $path.'/'.$saveName;
        }

        //返回已经存好的文件目录地址提供下载
        $response = array(
            'success' => true,
            'url' => saveExcelToLocalFile($objWriter,$saveName)
        );
        return json_encode($response);

    }
}