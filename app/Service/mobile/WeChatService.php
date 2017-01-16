<?php
namespace App\Service\mobile;

use App\Log\Facades\Logger;
use App\Model\Base\LoanModel;
use App\Model\mobile\WechatModel;
use App\Service\base\LoanBeforeService;
use App\Service\mobile\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class WeChatService extends Service {

    //微信验证
    public function valid() {
        if (!env("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = env("TOKEN");
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
  //      Logger::info($tmpStr);Logger::info($signature);
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    //接收到微信消息
    public function receiveMsg($postObject) {
        $MsgType = $postObject->MsgType;
        $result = "";
        switch ($MsgType) {
            case "event":   //事件消息
                $result = $this->receiveEvent($postObject);
                break;
            case "text":   //文本消息
                $result = $this->receiveText($postObject);
                break;
            case "image":  //图片消息
                $result = $this->receiveImage($postObject);
                break;
            case "voice":  //音频消息
                break;
            case "video":  //视频消息
                break;
            case "location" :   //地理位置消息
                $result = $this->reciveLocation($postObject);
                break;
            case "link" :   //链接消息
                break;
            default :
                break;
        }
        return $result;
    }

    //对事件消息进行分类
    public function receiveEvent($object) {
        $content = "";
        $result = "";

        switch ($object->Event) {
            case "subscribe":
                $url = urlencode("http://".env("JS_URL")."/users/register1");
                $help_url = "http://".env("JS_URL")."/more/help";
                $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".env("APPID")."&redirect_uri=".$url."&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect";
                $content = "亲，您终于来啦~里面请~\n我们为你准备了许多“礼物”，现在需要你一一去拆哦~\n点击<a href='".$url."'>【优惠通道】</a>，尽享VIP福利；\n 点击<a href='".$help_url."'>【热门问题】</a>看看大家都在关注什么；\n你也可以直接告诉我们你的问题，钱么么会第一时间解答哒~\n客服热线400-998-7850，客服妹纸会尽力为您答疑解惑~";
                $result = $this->TextXml($object, $content);
                break;
            case "unsubscribe":
                //$content = "取消关注本公众号";
                //$result = $this->TextXml($object, $content);
                break;
            case "LOCATION":
                break;
            case "SCAN":
                $content = "扫描场景";
                break;
            case "CLICK":
                $content = $this->receiveClick($object);
                $result = $this->TextXml($object, $content);
                break;
            case "VIEW":
                $content = "跳转链接";
                break;
            default :
                $content = "开发中";
                break;
        }
        return $result;
    }

    //对CLICK事件进行分类回复
    private function receiveClick($object) {
        $key = $object->EventKey;
        $content = "";

        $fromUsername = $object->FromUserName;
        switch ($key) {
            case "wx_get_loan":
                $cache_name = $fromUsername."loan";
                if(Cache::has($cache_name)){
                    $content = Cache::get($cache_name);
                }else{
                    $content = $this->wx_get_loan($fromUsername);
                    $expiresAt = Carbon::now()->addMinutes(30);
                    Cache::put($cache_name, $content, $expiresAt);
                }
                break;
            case "repay_detail":
                $content = "暂无还款信息";
                break;
            default:
                Logger::info("没有定义该事件");
                break;
        }
        return $content;
    }

    //回复文本消息
    private function receiveText($object) {
        $FromUserName = $object->FromUserName;
        $Content = $object->Content;
        if(Cache::has($FromUserName)){
            $item = Cache::get($FromUserName)."-".$Content;
        }else{
            $item = $Content."";
        }
        $res_data = $this->wx_user_reply($item);
        if($res_data['number'] != "4") {
            $expiresAt = Carbon::now()->addMinutes(1);
            Cache::put($FromUserName, $res_data['number'], $expiresAt);
        }else{
            Cache::forget($FromUserName);
        }

        $result = $this->TextXml($object, $res_data['contentStr']);
        return $result;
    }

    //用户发送文本内容
    public function wx_user_reply($item){
        $replay_data = $this->wx_replay_data();
        if(isset($replay_data[$item]))
        {
            $txt = $replay_data[$item];
            $contentStr['contentStr'] = $txt;
            $contentStr['number'] = $item;
        }else{
            $contentStr = $this->defualt_replay($item);
        }
        return $contentStr;
    }
    //回复数据包
    public function wx_replay_data()
    {
        $url = urlencode("http://".env("JS_URL")."/users/register");
        $url3 = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".env("APPID")."&redirect_uri=".$url."&response_type=code&scope=snsapi_userinfo&state=schedules#wechat_redirect";
        $url2 = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".env("APPID")."&redirect_uri=".$url."&response_type=code&scope=snsapi_userinfo&state=refund#wechat_redirect";
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".env('APPID')."&redirect_uri=".$url."&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect";
        $data["1"] = "您可能需要以下服务：\n1-还款方式\n2-提前还清\n3-还款查询\n4-逾期还款\n回复序号，尊享贴心服务";
        $data["1-1"] = "尊敬的客户，您可能需要以下服务：\n4-手机银行转账\n5-网上银行转账\n6-银行柜台转账\n7-银行卡代扣还款\n回复序号，尊享贴心服务";
        $data["1-1-4"] = "尊敬的客户，请您按以下顺序操作还款：\n1.登陆银行卡官方手机APP\n2.选择对公转账\n3.填写完整、正确的还款账号、户名及开户行
4.确认转账\n,点击【<a href='".$url2."'>还款贴士</a>】查询还款账户";
        $data["1-1-5"] = "尊敬的客户，请您按以下顺序操作还款：\n1.登陆银行卡官方网站\n2.选择对公转账\n3.填写完整、正确的还款账号、户名及开户行
4.确认转账\n点击【<a href='".$url2."'>还款贴士</a>】查询还款账户";
        $data["1-1-6"] = "尊敬的客户，请您按以下顺序操作还款：\n1.将款项存入银行卡\n2.在银行人工柜台办理对公转账\n3.填写完整、正确的还款账号、户名及开户行
点击【<a href='".$url2."'>还款贴士</a>】查询还款账户";
        $data["1-1-7"] = "尊敬的客户，请您在每月还款日前存入足够金额，以便银行代扣还款，若到期未划扣，请联系客服400-998-7850
点击【<a href='".$url2."'>还款贴士</a>】查询还款账户";
        $data["1-2"] = "尊敬的客户，您可能需要以下服务：\n5-提前还清条件\n6-提交还清申请\n7-提前还款查询\n8-提前还款费用\n9-取消提前还款\n回复序号，尊享贴心服务";
        $data["1-2-5"] = "尊敬的客户，申请提前还款，需满足：\n1.正常分期状态\n2.当前无逾期、无欠款\n3.距离上一个还款日至少15天
4.拨打400-998-7850人工申请\n点击【<a href='".$url2."'>还款贴士</a>】查询还款";
        $data["1-2-6"] = "请拨打400-998-7850客服热线转人工申请";
        $data["1-2-7"] = "请点击左下方切换至菜单，点击【<a href='".$url2."'>还款贴士</a>】查询还款";
        $data["1-2-8"] = "尊敬的客户，申请提前还款，费用包含：\n1.当月期款\n2.剩余未还本金\n3.提前还款手续费\n如有疑问请咨询客服热线400-998-7850";
        $data["1-2-9"] = "请拨打400-998-7850客服热线转人工申请";
        $data["1-3"] = "点击【<a href='".$url3."'>还款详情</a>】查询";
        $data["1-4"] = "尊敬的客户，请保持良好还款记录：\n1.逾期还款会收取滞纳金\n2.影响下次借款通过率\n3.不良记录登记至个人征信\n请点击左下方切换至菜单，点击【<a href='".$url2."'>还款贴士</a>】查询还款方式";
        $data["2"] = "您可能需要以下服务：\n1-借钱资格\n2-申请流程\n3-放款时效\n4-新用户贷款\n5-贷款额度\n回复序号，尊享贴心服务";
        $data["2-1"] = "尊敬的客户，申请佰仟现金贷款，需要：\n1.办理过佰仟分期业务\n2.保持良好还款记录\n3.等待工作人员电话/短信邀约
详细请咨询客服热线400-998-7850";
        $data["2-2"] = "尊敬的客户，微信申请只需三步：\n1.验证手机号；\n2.填写资料及银行卡；\n3.审核/放款；\n<a href='".$url."'>点击此处</a> 立即申请";
        $data["2-3"] = "尊敬的客户，收到审批通过通知：\n1.第2个工作日内放款\n2.逢节假日、周末顺延\n3.<a href='".$url."'>点击此处</a> 立即申请\n详细请咨询客服热线400-998-7850";
        $data["2-4"] = "尊敬的客户，您好，我司暂不开通新用户贷款，如您资金用途为消费，请咨询我司消费分期热线：400-998-7101";
        $data["2-5"] = "尊敬的客户您好，贷款额度根据您在佰仟金融的还款信用度得出；点击 <a href='".$url."'>身份认证</a>  获知额度";
        $data["3"] = "您可能需要以下服务：\n1-还款账户变更\n2-还款到账查询\n3-申请划扣还款\n4-还款账户须知\n回复序号，尊享贴心服务";
        $data["3-1"] = "尊敬的客户，申请银行代扣/代扣变更：\n1.变更前后账户名不变\n2.拨打客服热线办理\n3.办理流程录音\n详细请咨询客服热线400-998-7850";
        $data["3-2"] = "尊敬的客户，扣款成功后，需2-3个工作日方可查询到账，届时您可以\n1.点击【<a href='".$url2."'>还款详情</a>】查询
2.拨打客服热线400-998-7850转人工查询详情";
        $data["3-3"] = "尊敬的客户，申请再次发起银行代扣需满足：\n1.绑定银行卡信息正确\n2.银行卡状态正常\n3.卡内余额充足
4.还款日后第5天\n5.拨打客服热线400-998-7850转人工服务";
        $data["3-4"] = "尊敬的客户，若您当前存在2笔或2笔以上业务合同，则还款/代扣账户以最新一笔为准，例如：
第一笔合同使用A银行卡，每月还款200元；\n第二笔合同使用B银行卡，每月还款400元；\n则B银行卡每月划扣还款金额600元，A银行卡不再划扣；\n详情请咨询客服热线：400-998-7850";
        $data["4"] = "ha,让我猜猜，您是不是：\n1-还钱啦？\n2-缺钱啦？\n3-办业务？\n回复序号，让我帮您解决";
        return $data;
    }
    public function defualt_replay($keyword)
    {
        $reg_res = preg_match("/还款|结|清|查|询|逾期/", $keyword);
        $reg_res1 = preg_match("/怎么|如何|借|贷|款|钱|放|打/", $keyword);
        $reg_res2 = preg_match("/帐户|账户|银行卡|认证/", $keyword);
        $reg_res3 = preg_match("/客服|电话/", $keyword);
        $reg_res4 = preg_match("/审核|时长|间|多久/", $keyword);
        if($reg_res)
        {
            $res['contentStr'] = "您可能需要以下服务：\n1-还款方式\n2-提前还清\n3-还款查询\n4-逾期还款\n回复序号，尊享贴心服务";
            $res['number'] = "1";
        }
        elseif($reg_res1)
        {
            $res['contentStr'] = "您可能需要以下服务：\n1-借钱资格\n2-申请流程\n3-放款时效\n4-新用户贷款\n回复序号，尊享贴心服务";
            $res['number'] = "2";
        }
        elseif($reg_res2)
        {
            $res['contentStr'] = "您可能需要以下服务：\n1-还款账户变更\n2-还款到账查询\n3-申请划扣还款\n4-银行卡认证\n回复序号，尊享贴心服务";
            $res['number'] = "3";
        }elseif($reg_res3){
            $res['contentStr'] = "借钱么服务热线：400-998-7850";
            $res['number'] = "0";
        }elseif($reg_res4){
            $url = urlencode("http://".env("JS_URL")."/users/register");
            $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".env('APPID')."&redirect_uri=".$url."&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect";
            $res['contentStr'] = "尊敬的客户你好，客户提交申请后，最快1小时内审批完毕，具体通过时间请打开<a href='".$url."'>个人中心</a>查询";
            $res['number'] = "0";
        }else{
            $res['contentStr'] = "ha,让我猜猜，您是不是：\n1-还钱啦？\n2-缺钱啦？\n3-办业务？\n回复序号，让我帮您解决";
            $res['number'] = "4";
        }
        return $res;
    }

    //回复图片消息
    private function receiveImage($object) {
        $content = array("MediaId" => $object->MediaId);
        $result = $this->ImageXml($object, $content);
        return $result;
    }

    //接收地理位置消息
    private function reciveLocation($object) {
        $loanBeforeService = new LoanBeforeService();
        $loanBeforeService->get_location(Auth::id(),$object->Location_X,$object->Location_Y);
   //     $content = "地理位置维度：" . $object->Location_X . "地理位置经度：" . $object->Location_Y . "地理位置信息：" . $object->Label;
    //    $result = $this->TextXml($object, $content);
    //    return $result;
    }

    //回复图文XML组装
    private function ImageTextXml($object, $content) {
        if (!is_array($content)) {
            return;
        }
        $itemxml = "<item>
                    <Title><![CDATA[%s]]></Title>
                    <Description><![CDATA[%s]]></Description>
                    <PicUrl><![CDATA[%s]]></PicUrl>
                    <Url><![CDATA[%s]]></Url>
                    </item>";
        $itemStr = "";
        foreach ($content as $item) {
            $itemStr .= sprintf($itemxml, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $xml = "
                <xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>
                    <ArticleCount>%s</ArticleCount>
                    <Articles>
                        " . $itemStr . "
                    </Articles>
                    </xml> 
                    ";
        $result = sprintf($xml, $object->FromUserName, $object->ToUserName, time(), count($content));
        return $result;
    }

    //回复文本消息XML组装
    public function TextXml($object, $content) {
        $xml = "
                <xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                </xml>
            ";
        $result = sprintf($xml, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }

    //回复图片消息XML组装
    public function ImageXml($object, $content) {
        if (!is_array($content)) {
            return;
        }
        $xml = "
                <xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[image]]></MsgType>
                    <Image>
                        <MediaId><![CDATA[%s]]></MediaId>
                    </Image>
                </xml>
            ";
        $result = sprintf($xml, $object->FromUserName, $object->ToUserName, time(), $content['MediaId']);
        return $result;
    }

    //回复音乐消息XML组装
    private function MusicXml($object, $content) {
        if (!is_array($content)) {
            return;
        }
        $xml = "
            <xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[music]]></MsgType>
                <Music>
                    <Title><![CDATA[%s]]></Title>
                    <Description><![CDATA[%s]]></Description>
                    <MusicUrl><![CDATA[%s]]></MusicUrl>
                    <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
                </Music>
            </xml>
            ";
        $result = sprintf($xml, $object->FromUserName, $object->ToUserName, time(), $content['Title'], $content['Description'], $content['MusicUrl'], $content['HQMusicUrl']);
        return $result;
    }

    //创建自定义菜单
    public function CreateMenu() {
        $wechat = new WechatModel();

        $access_token = $wechat->get_access_token();

        $url = urlencode("http://".env("JS_URL")."/users/register");
        $homeUrl = "http://".env("JS_URL")."/m/index/home";
        $url1 = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".env("APPID")."&redirect_uri=".$url."&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect";
        $url2 = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".env("APPID")."&redirect_uri=".$url."&response_type=code&scope=snsapi_userinfo&state=refund#wechat_redirect";
        $url3 = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".env("APPID")."&redirect_uri=".$url."&response_type=code&scope=snsapi_userinfo&state=schedules#wechat_redirect";
        /*$data = '{
            "button": [
                    {
                    "name": "首页",
                    "type": "view",
                    "url": "'.$homeUrl.'"
                    }
            ]
        }';*/
        $data = '{
            "button": [
            {
            "name": "我要借钱",
            "sub_button":[
                {
                    "type": "view",
                    "name": "极速借款",
                    "url": "http://'.env("JS_URL").'/users/register1?source=jqm"
                },
                {
                    "type": "view",
                    "name": "我能借多少",
                    "url": "http://'.env("JS_URL").'/users/register-loan?source=jqm"
                }
                ]
            },
            {
            "name": "个人中心",
            "sub_button":[
                {
                    "type": "view",
                    "name": "首页",
                    "url": "http://'.env("JS_URL").'/m/index/home?source=jqm"
                },
                {
                    "type": "view",
                    "name": "我的借款",
                    "url": "http://'.env("JS_URL").'/account/myloans?source=jqm"
                },
                {
                    "type": "view",
                    "name": "还款查询",
                    "url": "http://'.env("JS_URL").'/account/myrequitals?source=jqm"
                },
                {
                    "type": "view",
                    "name": "还款贴士",
                    "url": "http://'.env("JS_URL").'/loan/refund-info?source=jqm"
                }
                ]
            },
            {
            "name": "更多",
            "sub_button": [
                {
                    "type": "view",
                    "name": "帮助中心",
                    "url": "http://'.env("JS_URL").'/more/help?source=jqm"
                },
                {
                    "type": "view",
                    "name": "意见反馈",
                    "url": "http://'.env("JS_URL").'/more/response?source=jqm"
                },
                {
                    "type": "view",
                    "name": "关于借钱么",
                    "url": "http://'.env("JS_URL").'/more/about?source=jqm"
                }
                ]
            }
            ]
        }';
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$access_token;
        $return = $wechat->curl_post($url, $data);
        die(print_r($return));
    }

    /*
     *获取微信授权相关信息
     */
    public function get_wechat_userinfo($code)
    {
        $wechatm = new WechatModel();
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".env("APPID")."&secret=".env("SECRET")."&code=".$code."&grant_type=authorization_code";
        $wxres = $wechatm->curl_get($url);
        //$page_access = $wxres->access_token;
        if(!isset($wxres->openid)){

            Session::flash('msg_503', '哎呀！好挤啊！过几分钟再来。');
            abort(503);
            die("获取信息失败，请重新访问");
        }
        return $wxres;
    }


    /*
     * 根据授权获取的access_token 和 openid获取用户信息
     * $access_token, $openid
     */
    public function get_wx_userinfo($page_access, $openid){
        $wechatm = new WechatModel();
        //$url1 = "https://api.weixin.qq.com/sns/userinfo?access_token=".$page_access."&openid=".$openid."&lang=zh_CN";
        $url1 = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$wechatm->get_access_token()."&openid=".$openid."&lang=zh_CN";
        $wxuserinfo = $wechatm->curl_get($url1);
        if(isset($wxuserinfo->nickname)){
            return $wxuserinfo;
        }else{
            $error_txt = "access_token=".$wechatm->get_access_token()."&openid=".$openid."获取个人信息失败";
            Logger::error($error_txt);
            return false;
        }
    }

    //微信获取项目信息
    public function wx_get_loan($fromUsername)
    {
        $loanmodel = new LoanModel();
        $openid = $fromUsername;
        $res = $loanmodel->getloanuseropenid($openid);
        if($res)
        {
            $contentStr = "金额：".$res->loan_amount."  |  期数：".$res->loan_period."  |  申请时间：".$res->created_at."  |  状态：".$res->status;
        }else{
            $contentStr = "未查询到您的订单请绑定账户";
        }
        return $contentStr;
    }

    /*
     * 获取所有的微信分组信息
     */
    static public function get_all_group(){
        $wechat_m = new WechatModel();
        $res_arr = $wechat_m->get_all_group();
        foreach($res_arr as $val){
            $group_list[$val->name] = $val;
        }
        if($group_list) {
            Cache::forever("group_list", $group_list);
        }else{die("获取微信分组失败");}
    }

    static public function get_user_information($openid){
        $wechatm = new WechatModel();
        $access_token = $wechatm->get_access_token();
        //$url1 = "https://api.weixin.qq.com/sns/userinfo?access_token=".$page_access."&openid=".$openid."&lang=zh_CN";
        $url1 = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
        $wxuserinfo = $wechatm->curl_get($url1);
        if($wxuserinfo){
            Logger::info(json_encode($wxuserinfo));
            return $wxuserinfo;
        }else{
            $error_txt = "access_token=".$wechatm->get_access_token()."&openid=".$openid."获取个人信息失败";
            Logger::error($error_txt);
            return false;
        }
    }
}

