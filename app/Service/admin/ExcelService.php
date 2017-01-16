<?php

namespace App\Service\admin;
class ExcelService extends Service
{
    private $PHPExcel = null;
    public function __construct(){
        $cacheMethod  = \PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
        $cacheSettings = array();
        \PHPExcel_Settings::setCacheStorageMethod($cacheMethod,$cacheSettings);
        $this->PHPExcel = new \PHPExcel();
    }

    /**
     * 读取excel文件
     */
    public function read($file, $begin_row = 2) {
        if (!$file || !file_exists($file)) {
            return false;
        }

        //require_once public_path() . '/uploads/PHPExcel/IOFactory.php';

        $this->PHPExcel = \PHPExcel_IOFactory::load($file);

        $objWorksheet = $this->PHPExcel->getActiveSheet();
        $highestRow = $objWorksheet->getHighestRow();

        $highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn); //总列数


        $ReData = array();
        $begin_row = intval($begin_row) <= 0 ? 1 : intval($begin_row);
        for ($row = $begin_row; $row <= $highestRow; $row++) {
            $Data = array();
            //注意highestColumnIndex的列数索引从0开始
            for ($col = 0; $col < $highestColumnIndex; $col++) {
                $Data[$col] = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
            }
            $ReData[] = $Data;
        }

        return $ReData;
    }

    //生成并下载excel表格
    public function download($header, $data, $filename = '', $text_cell = array(), $format = 'Excel5') {

        $write_rs = $this->write($header, $data, $text_cell, $filename);
        if ($write_rs) {
            $this->output($filename, true, $format);
            return true;
        }
        return false;
    }
    //写入excel表格内容
    public function write($header, $data, $text_cell = array(), $filename = '') {
        if (!$data || !is_array($data)) {
            return false;
        }
        if (empty($filename)) {
            $filename = date('Y-m-d') . '.xls';
        }
        //加头部第一行
        $begin = 0;
        if ($header && is_array($header)) {
            foreach ($header as $key => $val) {
                if (is_array($val) && $val) {
                    foreach ($val as $k => $v) {
                        $cell_key = $this->num_to_ascii($k) . $begin;
                        $this->PHPExcel->setActiveSheetIndex(0)->setCellValue($cell_key, $v);
                    }
                    $begin += 1;
                } else {
                    $begin = 1;   //将表头移到最顶部
                    $cell_key = $this->num_to_ascii($key) . $begin;
                    $this->PHPExcel->setActiveSheetIndex(0)->setCellValue($cell_key, $val);
                }
            }
            $begin += $begin == 1 ? 0 : 0;
        }
        //加数据行
        if (is_array($text_cell) && $text_cell) {
            foreach ($data as $key => $val) {
                if (is_array($val) && $val) {
                    $begin += 1;
                    foreach ($val as $k => $v) {
                        $cell_key = $this->num_to_ascii($k) . $begin;
                        if (in_array($k, $text_cell) && $v) {
                            $this->PHPExcel->getActiveSheet()
                                ->getStyle($cell_key)
                                ->getNumberFormat()
                                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                            $this->PHPExcel->getActiveSheet()
                                ->setCellValueExplicit($cell_key, $v, \PHPExcel_Cell_DataType::TYPE_STRING2);
                        } else {
                            $this->PHPExcel->setActiveSheetIndex(0)->setCellValue($cell_key, $v);
                        }
                    }
                }
            }
        } else {
            foreach ($data as $key => $val) {
                if (is_array($val) && $val) {
                    $begin += 1;
                    foreach ($val as $k => $v) {
                        $cell_key = $this->num_to_ascii($k) . $begin;
                        $this->PHPExcel->setActiveSheetIndex(0)
                            ->setCellValue($cell_key, $v);
                    }
                }
            }
        }


        // Rename sheet
        $this->PHPExcel->getActiveSheet()->setTitle($filename);


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $this->PHPExcel->setActiveSheetIndex(0);

        return true;
    }

    /**
     * 输出excel文件(包括浏览器直接下载和保存文件到服务器两种情况)
     */
    public function output($filename = '', $download = true, $format = 'Excel5') {
        if (empty($filename)) {
            $filename = date('Y-m-d') . ($format == 'Excel2007' ? '.xlsx' : '.xls');
        }

        $objWriter = \PHPExcel_IOFactory::createWriter($this->PHPExcel, $format);

        // Redirect output to a client’s web browser (Excel5)
        if ($download) {
            $encoded_filename = urlencode($filename);
            $encoded_filename = str_replace("+", "%20", $encoded_filename);
            $ua = $_SERVER["HTTP_USER_AGENT"];

            if ($format == 'Excel2007') {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                if (preg_match("/MSIE/", $ua)) {
                    header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
                } else if (preg_match("/Firefox/", $ua)) {
                    header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '"');
                } else {
                    header('Content-Disposition: attachment; filename="' . $filename . '"');
                }

//				header('Content-Disposition: attachment;filename="'.$filename.'"');
                header('Cache-Control: max-age=0');
            } else {
                header('Content-Type: application/vnd.ms-excel');
                if (preg_match("/MSIE/", $ua)) {
                    header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
                } else if (preg_match("/Firefox/", $ua)) {
                    header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '"');
                } else {
                    header('Content-Disposition: attachment; filename="' . $filename . '"');
                }
//				header('Content-Disposition: attachment;filename="'.$filename.'"');
                header('Cache-Control: max-age=0');
            }

            $objWriter->save('php://output');
            exit;
        } else {
            $objWriter->save($filename);
        }
        return true;
    }

    /**
     * 数字转换成ASCII码对应的大写字母(如0转换成A, 1转换成B)
     *
     * @author someday
     * @access private
     * @param int $num		数字
     *
     * @return string		该数字对应的大写字母
     */
    private static function num_to_ascii($num) {
        if (!is_numeric($num)) {
            return false;
        }
        return chr($num + 65);
    }

}