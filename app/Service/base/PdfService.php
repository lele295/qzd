<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/9/30
 * Time: 9:56
 */

namespace App\Service\base;


use App\Service\mobile\Service;

class PdfService extends Service
{
    public function put_content_to_pdf($html,$title,$type = 'I'){
        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        //设置文档信息
        $pdf->SetTitle($title);
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, PHP');
        // 设置页眉和页脚字体
        $pdf->setHeaderFont(Array('stsongstdlight', '', '0'));
        $pdf->setFooterFont(Array('helvetica', '', '0'));
        // 设置页眉和页脚字体
        $pdf->setHeaderFont(Array('stsongstdlight', '', '10'));
        $pdf->setFooterFont(Array('helvetica', '', '8'));

        // 设置默认等宽字体
        $pdf->SetDefaultMonospacedFont('courier');

        // 设置间距
        $pdf->SetMargins(15, 27, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);
        //设置字体
        $pdf->SetFont('stsongstdlight', '', 10);
        $pdf->AddPage();

        $pdf->writeHTML($html, true, false, true, false, '');

        $pdf->lastPage();

        //PDF输出的方式。I，默认值，在浏览器中打开；D，点击下载按钮， PDF文件会被下载下来；F，文件会被保存在服务器中；S，PDF会以字符串形式输出；E：PDF以邮件的附件输出
        $pdf->Output('loandeal.pdf', $type);
    }
}