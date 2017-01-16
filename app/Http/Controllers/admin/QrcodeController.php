<?php
namespace App\Http\Controllers\admin;

use App\Service\Wechat;
use \Illuminate\Support\Facades\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class QrcodeController extends Controller
{

    public $qrcode_create_url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=';

    public $qrcode_show_url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIndex()
    {
        return view('admin/qrcode');
    }

    public function anyCreate()
    {
        $son = Request::input('sno');
        if (empty($son)) {
            return [
                'success' => false,
                'message' => '门店代码不能为空'
            ];
        }
        if (DB::table('sync_store_info')->select('SNO')
            ->where([
            'SNO' => $son,
            'STATUS' => '05'
        ])
            ->count() > 0) {
            $wx = new Wechat();
            $access_token = $wx->get_access_token();
            
            $ticket = $this->get_ticket($access_token, $son);
            if (! empty($ticket)) {
                return [
                    'success' => true,
                    'message' => $this->qrcode_show_url . $ticket
                ];
            } else {
                return [
                    'success' => false,
                    'message' => '获取图片失败'
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => '门店代码不存在'
            ];
        }
    }

    public function postSrc()
    {
        $ron = Request::input('rno');
        if (empty($ron)) {
            return redirect('admin/qrcode')->with('result', [
                'success' => false,
                'message' => '商家代码不能为空'
            ]);
        }
        if (DB::table('sync_store_info')->select('SNO')
            ->where([
            'SNO' => $ron,
            'STATUS' => '05'
        ])
            ->count() > 0) {
            $wx = new Wechat();
            $access_token = $wx->get_access_token();
            $ticket = $this->get_ticket($access_token, $ron);
            dd($ticket);
            if (! empty($ticket)) {
                return redirect('admin/qrcode')->with('result', [
                    'success' => true,
                    'message' => $this->qrcode_show_url . $ticket
                ]);
            } else {
                return redirect('admin/qrcode')->with('result', [
                    'success' => false,
                    'message' => '获取图片失败'
                ]);
            }
        } else {
            return redirect('admin/qrcode')->with('result', [
                'success' => false,
                'message' => '商家代码不存在'
            ]);
        }
    }

    public function get_ticket($access_token, $code)
    {
        $data = '{"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str": "' . $code . '"}}}';
        $result = $this->curl_post($this->qrcode_create_url . $access_token, $data);
        return Arr::get($result, 'ticket', '');
    }

    public function curl_post($url = "", $data = "")
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($data)
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        
        $SSL = substr($url, 0, 8) == "https://" ? true : false;
        if ($SSL) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
        }
        
        // if(curl_exec($ch) === false) {
        // echo 'Curl error: ' . curl_error($ch);
        // } else {
        // echo '操作完成没有任何错误';
        // }
        return json_decode(curl_exec($ch), true);
    }
}
