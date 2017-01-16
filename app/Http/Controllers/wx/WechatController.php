<?php
namespace App\Http\Controllers\wx;


use App\Model\mobile\ContractModel;
use Illuminate\Support\Facades\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Service\Wechat;
use App\Service\WxMsg;
use App\Log\Facades\Logger;
use Illuminate\Support\Facades\DB;



class WechatController extends Controller
{
    public function getIndex()
    {
        $this->valid();
    }


    //验证签名
    public function valid()
    {
        $echoStr = request("echostr", "");
        $signature = request("signature", "");
        $timestamp = request("timestamp", "");
        $nonce = request("nonce", "");
        $token = config('wx.token');
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = sha1(implode($tmpArr));
        if ($tmpStr == $signature) {
            echo $echoStr;
            exit;
        }
    }

    /**
     * 微信请求入口
     */
    public function postIndex()
    {
        $this->responseMsg();
    }

    //响应消息
    private function responseMsg()
    {
        $postStr = file_get_contents("php://input");
        if (!empty($postStr)) {
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);

            //Logger::info(json_encode($postObj,JSON_UNESCAPED_UNICODE),'responseMsg');
            $RX_TYPE = trim($postObj->MsgType);
            //消息类型分离
            switch ($RX_TYPE) {
                case "event":
                    $result = $this->receiveEvent($postObj);
                    break;
                case "text":
                    $result = $this->receiveText($postObj);
                    break;
                case "image":
                    $result = $this->receiveImage($postObj);
                    break;
                case "location":
                    $result = $this->receiveLocation($postObj);
                    break;
                case "voice":
                    $result = $this->receiveVoice($postObj);
                    break;
                case "video":
                    $result = $this->receiveVideo($postObj);
                    break;
                case "link":
                    $result = $this->receiveLink($postObj);
                    break;
                default:
                    $result = "unknown msg type: " . $RX_TYPE;
                    break;
            }
            echo $result;
        } else {
            echo "";
            exit;
        }
    }

    //接收事件消息
    private function receiveEvent($object)
    {
        $content = "";
        $event = strtolower($object->Event);
        switch ($event) {
            case "subscribe":
                $content = "欢迎关注。";
                if (isset($object->EventKey) && $object->EventKey != '') {
                    $content = static::senariosMessage($object->EventKey, 'subscribe');
                }
                //关注写入user表，更新初始化userinfo
                $res = DB::table("users")->where("openid", $object->FromUserName)->first();
                if (!empty($res)) {
                    DB::table("users")->where("openid", $object->FromUserName)->update(array("status" => 1, "gz_time" => time()));
                } else {
                    DB::table("users")->insertGetId(array(
                        'openid' => $object->FromUserName,
                        'gz_time' => time(),
                        'status' => 1
                    ));
                }
                break;
            case "unsubscribe":
                //取消关注，更新user状态为3
                DB::table("users")->where("openid", $object->FromUserName)->update(array("status" => 3, "cancel_time" => time()));
                break;
            case "click":
                $keyword = $object->EventKey;
                if ($keyword == "intro") {
                    //公司介绍(可能发送多条记录)
                    $content[] = [
                        "Title"=>"公司介绍",
                        "Description"=>"深圳市佰仟金融服务有限公司（以下简称佰仟金融），2013年12月注册成立，总部位于深圳。",
                        "PicUrl"=>"http://www.qianzidai.cn/img/baiqian.png",
                        "Url" =>"http://mp.weixin.qq.com/s?__biz=MzI5MDQwNTg3Mw==&mid=2247483653&idx=1&sn=d73d192b5c0aed53a82257c6a1a170df&chksm=ec212d9bdb56a48d28f2bd68981e9c19180a0a8aa78edcc0dce92cf9f4bf2800d253f02c7004#rd"
                    ];
                }elseif($keyword == "application"){
                    $content = "http://www.qianzidai.cn/img/application.jpg";
                }elseif($keyword == "custom"){
                    $content = [
                        "flag"=>"custom"
                    ];
                }
                break;
            case "view":
                $content = "跳转链接 " . $object->EventKey;
                break;
            case "scan":
                $content = static::senariosMessage($object->EventKey, 'scan');
                break;
            case "location":
                $data['latitude'] = $object->Latitude;
                $data['longitude'] = $object->Longitude;
                $data['precision'] = $object->Precision;
                break;
            case "scancode_waitmsg":
                break;
            case "scancode_push":
                $content = "扫码推事件";
                break;
            case "pic_sysphoto":
                $content = "系统拍照";
                break;
            case "pic_weixin":
                $content = "相册发图：数量 " . $object->SendPicsInfo->Count;
                break;
            case "pic_photo_or_album":
                $content = "拍照或者相册：数量 " . $object->SendPicsInfo->Count;
                break;
            case "location_select":
                $content = "发送位置：标签 " . $object->SendLocationInfo->Label;
                break;
            case "kf_create_session":
                //微信服务器在五秒内收不到响应会断掉连接，并且重新发起请求，总共重试三次。
                Logger::info('接通','custom');
                sleep(3);
                $content = "您已开启人工服务，小资很高兴为您服务！如需退出人工请输入#";
                $this->getTplInfo($object->FromUserName,$content);
                break;
            case "kf_close_session":
                Logger::info('退出','custom');
                $content = "您已退出人工服务，谢谢您的使用！";
                $this->getTplInfo($object->FromUserName,$content);
                break;
            case "kf_switch_session":
                break;
            default:
                $content = "receive a new event: " . $object->Event;
                break;
        }
        if (!$content) {
            return $content;
        }
        if (is_array($content)) {
            if (isset($content[0]['PicUrl'])) {
                $result = WxMsg::transmitNews($object, $content);
            } else if (isset($content['MusicUrl'])) {
                $result = WxMsg::transmitMusic($object, $content);
            } else if ($content['flag']=="custom") {//客服
                //创建会话，接入客服（发送用户的openid过去）
                $rs = $this->getKfInsert($object->FromUserName);
                $rs = json_decode($rs);
                $wxmodel = new Wechat();
                $userInfo = $wxmodel->get_user_info_by_openid($object->FromUserName);
                Logger::info('openid,nickname,kf状态码：'.$userInfo->openid.'--'.$userInfo->nickname.'--'.$rs->errcode,'custom');
                //Logger::info('用户信息'.json_encode($userInfo,JSON_UNESCAPED_UNICODE),'custom');
                if ($rs->errcode == 65415) {
                    $result = WxMsg::transmitText($object, '当前客服不在线，请稍后咨询！');
                } else if ($rs->errcode == 0) {
                    $result = WxMsg::transmitText($object, '您好，请问有什么可以帮您？');
                }
            }
        } else {
            $result = WxMsg::transmitText($object, $content);
        }
        return $result;
    }


    /**
     * @param $event_key : subscribe 为关注 scan 为普通场景
     * @param string $type
     */
    public static function senariosMessage($event_key, $type = 'subscribe')
    {
        $event_key = ($type == 'subscribe') ? substr($event_key, 8) : $event_key;
        $story_info = DB::table('sync_store_info')->select('SNAME','SNO')->where(['SNO'=>$event_key,'STATUS'=>'05'])->first();
        $message = [
            0 => isset($story_info->SNAME) ? $story_info->SNAME : '佰仟金融旗下医美分期付款',
            1 => '6期',
            2 => '12期',
            3 => '15期',
            4 => '18期',
            5 => '24期',
        ];
        $content = [];
        foreach ($message as $key => $item) {
            $content[] = [
                'Title' => $item,
                'Description' => $item,
                'PicUrl' => $key == 0 ? asset('img/wx/sub_m.jpg') : asset('img/wx/sub_l.jpg'),
                'Url' => isset($story_info->SNO) ? url('wx/loan/do-mcode').'?sno='.$event_key : url('wx/loan/mcode')
            ];
        }
        //Logger::info('商家二维码链接：'.$content[0]['Url'],'store-info');
        return $content;
    }

    //接收文本消息
    private function receiveText($object)
    {
        $content = trim($object->Content);
        $result = WxMsg::transmitText($object, $content);
        return $result;
    }

    //接收图片消息
    private function receiveImage($object)
    {
        $content = array("MediaId" => $object->MediaId);
        $result = WxMsg::transmitImage($object, $content);
        return $result;
    }

    //接收位置消息
    private function receiveLocation($object)
    {
        $content = "你发送的是位置，经度为：" . $object->Location_Y . "；纬度为：" . $object->Location_X . "；缩放级别为：" . $object->Scale . "；位置为：" . $object->Label;
        $result = WxMsg::transmitText($object, $content);
        return $result;
    }

    //接收语音消息
    private function receiveVoice($object)
    {
        if (isset($object->Recognition) && !empty($object->Recognition)) {
            $content = "你刚才说的是：" . $object->Recognition;
            $result = WxMsg::transmitText($object, $content);
        } else {
            $content = array("MediaId" => $object->MediaId);
            $result = WxMsg::transmitVoice($object, $content);
        }
        return $result;
    }

    //接收视频消息
    private function receiveVideo($object)
    {
        $content = array("MediaId" => $object->MediaId, "ThumbMediaId" => $object->ThumbMediaId, "Title" => "", "Description" => "");
        $result = WxMsg::transmitVideo($object, $content);
        return $result;
    }

    //接收链接消息
    private function receiveLink($object)
    {
        $content = "你发送的是链接，标题为：" . $object->Title . "；内容为：" . $object->Description . "；链接地址为：" . $object->Url;
        $result = WxMsg::transmitText($object, $content);
        return $result;
    }

    //创建自定义菜单
    public function getCreatemenu()
    {
        $wx = new Wechat();
        $access_token = $wx->get_access_token();
        $serverurl = url('/');
        $data = '
        {
          	"button":[
          	{
          	"name":"个人中心",
          	"sub_button":[
              	{
                	"type":"view",
                 	"name":"我的订单",
                 	"url":"' . $serverurl . '/wx/order/list"
              	},
              	{
                	"type":"click",
                 	"name":"在线客服",
                 	"key":"custom"
              	}
            	]
          	},
          	{
            	"name":"申请分期",
            	"type":"view",
            	"url":"' . $serverurl . '/wx/loan/mcode"
          	},
          	{
          	"name":"更多服务",
          	"sub_button":[
              	{
                	"type":"click",
                 	"name":"公司介绍",
                 	"key":"intro"
              	},
                {
                    "name":"申请流程",
                    "type":"view",
                    "url":"http://www.qianzidai.cn/img/application.jpg"
                }
            	]
          	}
          ]
    	}';
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . $access_token;
        $return = $wx->curl_post($url, $data);
    }

    public function getDeletemenu()
    {
        $wx = new Wechat();
        $access_token = $wx->get_access_token();
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=' . $access_token;
        $return = $wx->curl_get($url);
    }

    //下载微信服务器图片
    public function postDownwxpic()
    {
        $media_id = Request::input("media_id");
        $wx = new Wechat();
        $access_token = $wx->get_access_token();
        $url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=" . $access_token . "&media_id=" . $media_id;
        $json = $wx->downloadWeixinFile($url);
        //Logger::info($json,'wechat');
        die(json_encode($json));
    }


    /**
     * 接入客服
     * @param $openId
     * @return string
     */
    public function getKfInsert($openId)
    {
        $wx = new Wechat();
        $access_token = $wx->get_access_token();
        //获取在线客服
        $info = $this->getKfOnline();
        if(empty($info)){
            $json = [
                'errmsg' => 'false',
                'errcode' => 65415
            ];
        }else{
            //建立会话
            $url = "https://api.weixin.qq.com/customservice/kfsession/create?access_token=". $access_token;
            $data = '{
                    "kf_account" : "'.$info->kf_account.'",
                    "openid" : "'.$openId.'",
                    "text":"正在连接客服人员，请稍等"
                 }';

            $json = $wx->curl_post($url,$data);
        }

        return json_encode($json);
    }

    /**
     * 获取在线的客服
     * @return string
     */
    public function getKfOnline(){
        $wx = new Wechat();
        $access_token = $wx->get_access_token();
        $url = 'https://api.weixin.qq.com/cgi-bin/customservice/getonlinekflist?access_token='.$access_token;
        $return = $wx->curl_get($url);

        //返回当前在线闲置的第一个客服信息，如果都有会话，就返回第一个在线客服的信息
        foreach($return->kf_online_list as $v){
            if(!empty($v)){
                if($v->accepted_case==0){
                    return $v;
                }else{
                    return $return->kf_online_list[0];
                }
            }else{
                return '';
            }
        }
    }


    /**
     * 主动给指定的人推送一个指定的消息模板(文本信息)
     * @param $opneId
     * @param $content
     */
    public function getTplInfo($opneId,$content){
        $wx = new Wechat();
        $access_token = $wx->get_access_token();
        $data = '
            {
                "touser":"'.$opneId.'",
                "msgtype":"text",
                "text":
                {
                    "content":"'.$content.'"
                }
            }
        ';

        $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;
        $return = $wx->curl_post($url,$data);
        dd($return);
    }
}

?>