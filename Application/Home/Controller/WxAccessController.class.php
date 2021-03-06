<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/11/18
 * Time: 16:51
 */

namespace Home\Controller;

use Home\Model\RawMessagesModel;
use Home\Model\UserModel;
use Think\Controller;
use Think\Log;
use Org\Net\Http;

header("Content-type: text/html; charset=utf-8");

class WxAccessController extends Controller
{

    public $weObj;

    public function _initialize(){
        $options = array(
            'token' => C('WX_TOKEN'),
            'encodingaeskey' => C('WX_ENCODINGAESKEY'),
            'appid' => C('WX_APPID'),
            'appsecret' => C('WX_APPSECRET')
        );
        $this->setWeObj(new \Org\Util\Wechat($options));
    }

    public function reply(){
        $emoji = unicode2utf8('\ue022');
        $activity_flag = false;
        $interaction_flag = false;
        $this->getWeObj()->valid();
        $type = $this->getWeObj()->getRev()->getRevType();
        switch($type) {
            case \Org\Util\Wechat::MSGTYPE_TEXT:
                $content = $this->getWeObj()->getRevContent();
                $content = trim($content);
//                if ($activity_flag&&($content=="我爱母亲")){
////                    $this->getWeObj()->text("你好，这里是超级矿资源，感谢您的支持！")->reply();
////                    $media_id = "4MvaT_gC28bI6pHCBq7T-zFr8p2pxSo0PmN8kGFKGj0";
////                    $this->getWeObj()->image($media_id)->reply();
//                    $n =array(
//                        "0"=>array(
//                            'Title'=>'母亲节超矿煤炭平台送iPhone7中国红啦！！！',
//                            'Description'=>'感恩母亲节大回馈，超级矿资源送iPhone7中国红啦！！！',
//                            'PicUrl'=>'http://www.xuncl.com/redphone.jpg',
//                            'Url'=>'http://mp.weixin.qq.com/s/02N7S_GcQ-UK-17eNSGAcA'
//                        )
//                    );
//                    $this->getWeObj()->news($n)->reply();
//                    exit;
//                }
//                if(preg_match('/^\d$/',$content) > 0){
//                    //选择1号模板
//                    //获取用户头像地址及用户微信昵称
//                    $resultOpenId = $this->getWeObj()->getRevFrom();
//                    $userInfo = $this->getWeObj()->getUserInfo($resultOpenId);
//                    $headUrl = $userInfo['headimgurl'];
//                    $nickname = $userInfo['nickname'];
//
//                    //下载用户头像至Public/upload/heka/文件夹下
//                    //所有用户每次回复头像都保存为同一名字:headimg.png
//                    //下载图片
//                    //实例化Upload控制器
//                    $up = A('Upload');
//                    //调用控制器downloadImage方法下载头像图片
//                    $up->downloadImage($headUrl, $resultOpenId);
//                    //利用背景图片合成
//                    $templetPath = './Public/upload/heka/1.jpg';
//                    $punchline = '狗年大吉！';
//                    switch ($content)
//                    {
//                        case '1':
//                            $templetPath = './Public/upload/heka/1.jpg';
//                            $punchline = '狗年大吉！';
//                            break;
//                        case '2':
//                            $templetPath = './Public/upload/heka/2.jpg';
//                            $punchline = '吉祥如意！';
//                            break;
//                        case '3':
//                            $templetPath = './Public/upload/heka/3.jpg';
//                            $punchline = '新春如意！';
//                            break;
//                        case '4':
//                            $templetPath = './Public/upload/heka/4.jpg';
//                            $punchline = '招财进宝！';
//                            break;
//                        case '5':
//                            $templetPath = './Public/upload/heka/5.jpg';
//                            $punchline = '富贵安康！';
//                            break;
//                        case '6':
//                            $templetPath = './Public/upload/heka/6.jpg';
//                            $punchline = '步步高升！';
//                            break;
//                        case '7':
//                            $templetPath = './Public/upload/heka/7.jpg';
//                            $punchline = '恭喜发财！';
//                            break;
//                        default:
//                            $templetPath = './Public/upload/heka/'.rand(1,7).'.jpg';
//                            $punchline = '新年快乐！';
//                    }
//                    $up->imageCompose($templetPath,$punchline,$nickname,$resultOpenId);
////
////                    //合成后的默认路径为Public/upload/heka/mediaTemp.jpg'
////                    //调用微信接口上传至素材库并获取素材的media_id
//                    $result = $this->getWeObj()->uploadMedia(array('media'=>'@/alidata/www/superkzy/Public/upload/heka/ny_'.$resultOpenId.'.jpg'),'image');
//                    $media_id = $result['media_id'];
////                    $this->getWeObj()->text(json_encode($result,JSON_UNESCAPED_UNICODE))->reply();
////                    //利用media_id回复给用户贺卡图
//                    $this->getWeObj()->image($media_id)->reply();
//
//
//                    //test
////                    $a = $up->downloadImage($headUrl);
////                    $url = 'http://thirdwx.qlogo.cn/mmopen/VB1ubazPN1cUNOrawXFCQNDkeCgicN4eKUZg4udJYia0lCpmntCKQzOnWYXYQD24HxlMCzAxiceaIz3XcVygsrAxRpib2AIMjlLa/132';
////                    $http = new \Org\Net\Http();
////                    $http::curlDownload($url, "./Public/upload/heka/headimg.png");
////                    $result = $this->getWeObj()->uploadImg(array('media'=>'./Public/home/images/heka/media1.jpg'));
////
////                    $image = new \Think\Image();
////                    $image->open('./Public/upload/heka/1.jpg');
////                    $tffPath = './Public/home/fonts/xgyj.ttf';
////                    $str = '狗年大吉大吉大吉';
////                    $image->text($str,$tffPath,14,'#ffffff',array(250,900))
////                        ->text($str,$tffPath,14,'#ffffff',array(250,920))
////                        ->save('./Public/upload/heka/media1.jpg');
////                    $result = $this->getWeObj()->uploadImg(array('media'=>'@www.xuncl.com/Public/upload/heka\1.jpg'));
////                    $result = $this->getWeObj()->uploadMedia(array('media'=>'@www.xuncl.com/Public/upload/heka/1.jpg'),'image');
////
////                    $this->getWeObj()->text(json_encode($result))->reply();
////                    $shortUrl = $this->getWeObj()->getShortUrl("www.xuncl.com/Public/upload/heka/mediaTemp.jpg");
//                    $this->getWeObj()->text("<a href='http://www.xuncl.com/Public/upload/heka/mediaTemp.jpg'>贺卡</a>")->reply();
//                }
                if ($content=="你好"){
                    $this->getWeObj()->text("你好，这里是超级矿资源，感谢您的支持！")->reply();
                    $this->getWeObj()->text("你好，这里是超级矿资源，感谢您的支持！")->reply();
                    exit;
                }
                if (preg_match('/谢谢/',$content) > 0){
                    $this->getWeObj()->text("不客气，我们将竭诚为您提供优质信息服务".$emoji)->reply();
                    exit;
                }
                import('Home.Common.Chinese2Pin');
                $pin = new \Chinese2Pin();
                $tempPy = $pin->Pinyin($content,'UTF8');
//                if (preg_match('/(警示|天才|近视|竟是|骗子|近似|金石|晶石|经视|今世|惊世|净视)/',$tempPy) > 0){
                if (preg_match('/(jingshi|jingsi|jinshi|jinsi)/',$tempPy) > 0){
                    $n =array(
                        "0"=>array(
                            'Title'=>'公布你身边的骗子，让他们去死！',
                            'Description'=>'不管是最深沉的心机，还是最拙劣的演技，当这些丑恶公布于众，我们相信，会让更多的人免于相似的骗局。',
                            'PicUrl'=>'http://www.xuncl.com/jingshi.jpg',
//                            'Url'=>'https://mp.weixin.qq.com/s?__biz=MzIzMjQ0NjE4MA==&tempkey=OTMxX1ZRMUtQZlQ5dzhOSzFBaHhBUWpMb1VwTmpJWFNILVM0YVg4VUJxRWxIVG95YjRfaTB1RTE0bGZPV0REVERIdEU0UnYxQ0hzRUdhOVI2VFN4YTgxcDFqdVB2T25RZjlmOWdqMXFCZjlReWV5bFNKUWdYZFFpdjBzX3BVXy14ZllzS0F4UFVBVFRNRVVlNFpBaWlUeUVDNG9jdWNXdWpoLUR2YmFaX2d%2Bfg%3D%3D&chksm=689583fe5fe20ae80dfac3c339d446430da333b5de0be1931cd538aaeadc25eaadafca198f42#rd'
                            'Url'=>'http://mp.weixin.qq.com/s/jnub6cmq78RALx5o8JDTRA'
                        )
                    );
                    $this->getWeObj()->news($n)->reply();
                    exit;
                }
                if ($content=="查看结构"){
//                    $resultArr = $this->getWeObj()->getRevData();
                    $resultOpenId = $this->getWeObj()->getRevFrom();
                    $userInfo = $this->getWeObj()->getUserInfo($resultOpenId);
                    $str = json_encode($userInfo,JSON_UNESCAPED_UNICODE);
                    $this->getWeObj()->text($str)->reply();
                    exit;
                }

                // 如果是小消息
                $mode = '/([0-9]{11})|(\+86[0-9]{11})/'; //正则，必须写在反斜杠里面
                preg_match($mode, $content, $match);

                if ($match) {
                    $sender = $this->getWeObj()->getRevFrom();
                    $sender_wx = "wx_mp";
                    $owner = $match[0];
                    date_default_timezone_set('PRC');
                    $title = date('y-m-d_H:i', time());
                    $title .= $owner;

                    $insert = $this->createMpRaw($title, $content, $owner, $sender, $sender_wx);
                    if ($insert) {
                        $tempReply = "您的消息已经收到，我们将及时为您转发。";
                        $this->getWeObj()->text($tempReply)->reply();
                    } else {
                        Log::record("WxAccess: insert_raw() failed. ", Log::ERR);
                    }

                    exit;
                }else{

                    $Query = new QueryController();
                    $response_text = $Query->q_text($content);

                    if($interaction_flag&&$response_text){
                        $this->getWeObj()->text($response_text)->reply();
                        exit;
                    }elseif ($activity_flag){
                        $this->getWeObj()->text($emoji.$emoji.$emoji."回复“我爱母亲”，即可参加母亲节赢iPhone7中国红手机活动。".$emoji.$emoji.$emoji."

 回复带有联系方式（手机号）的供应/求购/找车/车源信息，我们会为您公示转发。")->reply();
                        exit;
                    }else{
                        $tempReply = "回复带有联系方式（手机号）的供应/求购/找车/车源信息，我们会为您公示转发。";
                        $this->getWeObj()->text($tempReply)->reply();
                        exit;
                    }


                }

                break;
            case \Org\Util\Wechat::MSGTYPE_EVENT:
                $event = $this->getWeObj()->getRevEvent();
                switch ($event['event']){
                    case "subscribe":
                        $welcome_str = "【超级矿资源】—信息量最大的煤炭信息平台

【发布煤炭信息】请直接在公众号留言(煤炭信息+手机号)，我们会为您公示转发

【查询煤炭信息】点击<a href='http://www.xuncl.com/index.php/Home/Homepage/homepage'>超矿主页</a>，查询煤炭供求、找车信息(每日更新千条)

【付费推广业务】加大煤炭信息发布范围，请联系<a href='tel:17083425332'>17083425332(微信同号)</a>

【高质量煤炭群】点击<a href='http://www.xuncl.com/index.php/Home/Upload/show'>全国高级煤炭圈</a>，共享资源你我他

加全国煤炭、物流群
☟☟☟☟";
                        if($activity_flag){
                            $welcome_str = "感谢关注【超级矿资源】微信公众平台！

".$emoji.$emoji.$emoji."母亲节期间，公众号回复“我爱母亲”，即可参加母亲节赢iPhone7中国红手机活动。".$emoji.$emoji.$emoji."

您可以点击<a href='http://www.xuncl.com/index.php/Home/Homepage/homepage'>平台网站</a>开始 <a href='http://www.xuncl.com/index.php/Home/OwnerPublish/owner_publish'>发布</a>或 <a href='http://www.xuncl.com/index.php/Home/Homepage/homepage'>查询</a> 运单、订单信息。也可以在这里回复直接提出您的问题。

在公众号直接回复您要转发的消息，我们会为您转发到我们的<a href='http://www.xuncl.com/index.php/Home/Homepage/homepage'>平台网站</a>和所有超矿微信的朋友圈。";
                        }
                        $this->getWeObj()->text($welcome_str)->reply();
                        exit;
                        break;
                    case "CLICK":
                        switch($event['key']){
                            case 'publish_method':
                                $tempReplyString = "【公众号留言】煤炭信息+手机号
【自助发布】点击<a href='http://www.xuncl.com/index.php/Home/OwnerPublish/owner_publish'>发布信息</a>";
                                $this->getWeObj()->text($tempReplyString)->reply();
                                break;
                            case 'activity':
                                $n =array(
                                    "0"=>array(
                                        'Title'=>'母亲节超矿煤炭平台送iPhone7中国红啦！！！',
                                        'Description'=>'感恩母亲节大回馈，超级矿资源送iPhone7中国红啦！！！',
                                        'PicUrl'=>'http://www.xuncl.com/redphone.jpg',
                                        'Url'=>'http://mp.weixin.qq.com/s/02N7S_GcQ-UK-17eNSGAcA'
                                    )
                                );
                                $this->getWeObj()->news($n)->reply();
                                break;
                            default:
                                $this->getWeObj()->text('点击菜单'.$event->key)->reply();
                                break;
                        }
                        break;
                    case 'SCAN':
                        $a = $this->getWeObj()->getRevSceneId();
//                        $this->getWeObj()->text($a.'#'.$event['key'])->reply();
                        $this->getWeObj()->text('欢迎回来！')->reply();
                        break;
                }
                break;
            case \Org\Util\Wechat::MSGTYPE_IMAGE:
                if($activity_flag){
                    $n =array(
                        "0"=>array(
                            'Title'=>'恭喜您获得抽奖资格,点击领取中国红！',
                            'Description'=>'超矿煤炭平台母亲节感恩大回馈活动',
                            'PicUrl'=>'http://www.xuncl.com/redphone.jpg',
                            'Url'=>'http://www.xuncl.com/index.php/Home/ActivityPromotion/turn_plate_lottery'
                        )
                    );
                    $this->getWeObj()->news($n)->reply();
                    exit;
                }

                break;
            default:
                $this->getWeObj()->text("使用帮助")->reply();
        }
    }

    public function index()
    {
        $uri = $this->getWeObj()->getOauthRedirect(C('ROOT_URL') . "WxAccess/oauth", "", "snsapi_base");
//        echo $uri;
        $this->success("正在跳转", $uri);
        // 已经把uri打印出来直接作为入口，不用注册两次weObj了
    }

    // scope为snsapi_base时 回调的方法
    public function base()
    {
        // 获取用户授权后的信息
        $resultArr = $this->getWeObj()->getOauthAccessToken();
        $userModel = new UserModel();
        $r = $userModel->where(array("open_id" => $resultArr["openid"]))->find();
        if (empty($r)) {//验证用户是否注册
            // 授权登录，跳转到$this->oauth()方法
            header('Location: ' . C('REDIRECT_URL_USERINFO'));
        } else {
            session('user_info', $r);
            session('role_id', $r['role_id']);
            $this->goto_home();
        }
    }

    // scope为snsapi_userinfo时 回调的方法 第一次登录调用
    public function oauth()
    {
        // 获取用户授权后的信息
        $resultArr = $this->getWeObj()->getOauthAccessToken();

        $resultArr["access_token"];
        $userInfo = $this->getWeObj()->getOauthUserinfo($resultArr["access_token"], $resultArr["openid"]);

        if ($userInfo) {
            // 既然到oauth中来了，肯定是没有保存用户信息的
            $this->save_user_info($userInfo);
            $this->goto_home();

        } else {
            // TODO 写日志，微信平台调用失败
        }
    }

    public function goto_home()
    {
        // 跳转地址设置为默认主页，如果cookie里有上次浏览地址，就跳到上次浏览的地址
//        $target_url = "PersonalCenter/personal_center";
        $target_url = U("Homepage/homepage");
        if (!empty($_COOKIE['current_url'])) {
            $target_url = $_COOKIE['current_url'];
        }
//        $this->success('页面跳转中...',$target_url, 0);
        redirect($target_url, 0, '页面跳转中...');
    }

    /**
     * 用户数据入库
     * @param $userInfo
     */
    private function save_user_info($userInfo)
    {
        $userModel = M("User");

        $data['open_id'] = $userInfo["openid"];
        $data['user_name'] = $userInfo["nickname"];
        $data['nickname'] = $userInfo["nickname"];
        $data['province'] = $userInfo["province"];
        $data['city'] = $userInfo["city"];
        $data['country'] = $userInfo["country"];
        $data['sex'] = $userInfo["sex"];
        $data['heading_url'] = $userInfo["headimgurl"];
        $data['group_id'] = C("AUTH_VISITOR");
        $r = $userModel->where(array("open_id" => $data['open_id']))->find();
        if (empty($r)) {//验证用户是否注册
            $res = $userModel->add($data);
            if ($res) {
                $temp['open_id'] = $userInfo["openid"];
                $user_r = $userModel->where($temp)->find();
                session('user_info', $user_r);
                session('role_id', $user_r['role_id']);
            } else {
                // todo 此处记录错误日志
                session('user_info', null);
                session('role_id', null);
            }
        } else {
            session('user_info', $r);
            session('role_id', $r['role_id']);
        }
    }

    public function create_menu()
    {

        $options = array(
            'token' => C('WX_TOKEN'),
            'encodingaeskey' => C('WX_ENCODINGAESKEY'),
            'appid' => C('WX_APPID'),
            'appsecret' => C('WX_APPSECRET')
        );
        $weObj = new \Org\Util\Wechat($options);
        //获取菜单操作:
        $menu = $weObj->getMenu();
        echo json_encode($menu);
        echo "<br/>-<br/>";
        //设置菜单
        $newmenu = array(
            "button" =>
                array(
                    array(
                        'name' => '加煤炭群',
//                        'type' => 'view',
//                        'url' => 'http://mp.weixin.qq.com/s/LEZmYPX6LtNHVQUraiZpbg'
                        'sub_button' => array(
//                            array('type' => 'view', 'name' => '加煤炭群', 'url' => 'http://mp.weixin.qq.com/s/XM5OE7DgKN4mU4L0t4shnw'),
                            array('type' => 'view', 'name' => '加煤炭群', 'url' => 'http://www.xuncl.com/index.php/Home/Upload/show'),
                            array('type' => 'view', 'name' => '会员注册', 'url' => 'http://www.xuncl.com/index.php/Home/Login/register'),
                            array('type' => 'view', 'name' => '车主福利', 'url' => 'http://www.xuncl.com/index.php/Home/CooperatePage/WeChe'),
//                            array('type' => 'view', 'name' => '超矿金融', 'url' => 'http://www.xuncl.com/index.php/Views/FinancialClient/show'),
//                            array('type' => 'view', 'name' => '物流金融', 'url' => 'http://www.xuncl.com/index.php/Views/FinancialClient/show_wl'),
                            array('type' => 'view', 'name' => '煤炭金融', 'url' => 'http://www.xuncl.com/index.php/Views/FinancialClient/show_tp'),
                            array('type' => 'view', 'name' => '送出行险', 'url' => 'https://u.wcar.net.cn/1es'),
                        ),
                    ),
                    array(
                        'name' => '发布/查询',
                        'sub_button' => array(
                            array('type' => 'view', 'name' => '超矿主页', 'url' => 'http://www.xuncl.com/index.php/Home/Homepage/homepage'),
//                            array('type' => 'view', 'name' => '发布信息', 'url' => 'http://www.xuncl.com/index.php/Home/OwnerPublish/owner_publish'),
                            array('type' => 'click', 'name' => '发布信息', 'key' => 'publish_method'),
                            array('type' => 'view', 'name' => '找车信息', 'url' => 'http://www.xuncl.com/index.php/Home/DriverSearch/driver_job_search'),
                            array('type' => 'view', 'name' => '买卖查询', 'url' => 'http://www.xuncl.com/index.php/Home/TradeSearch/trade_search'),
                            array('type' => 'view', 'name' => '车辆信息查询', 'url' => 'https://www.xuncl.com/index.php/Home/CarLocation/mpShow'),
//                            array('type' => 'view', 'name' => '推荐好友', 'url' => 'http://www.xuncl.com/index.php/Home/Homepage/homepage'),
//                            array('type' => 'miniprogram', 'name' => '煤炭供求', 'url' => 'http://www.xuncl.com/index.php/Home/Homepage/homepage','appid'=>'wxe206341dcef42267','pagepath'=>'pages/index/index'),
                        ),
                    ),
                    array(
                        'name' => '查煤价/运费',
                        'sub_button' => array(
                            array('type' => 'view', 'name' => '查煤价', 'url' => 'http://www.xuncl.com/index.php/Home/CoalPriceSearch/search_method'),
                            array('type' => 'view', 'name' => '查运费', 'url' => 'http://www.xuncl.com/Home/FreightSearch/show'),
                        ),
                    )
                )
        );
        $result = $weObj->createMenu($newmenu);
//        $result = $weObj->deleteMenu();
        echo $result;
//        echo $weObj->deleteMenu();
    }


    /**
     * @return mixed
     */
    public function getWeObj()
    {
        return $this->weObj;
    }

    /**
     * @param mixed $weObj
     */
    public function setWeObj(\Org\Util\Wechat $weObj)
    {
        $this->weObj = $weObj;
//        $weObj->valid(); //被动接口处于加密模式时必须调用
    }


    /**
     * @param $title
     * @param $content
     * @param $owner
     * @param $sender
     * @param $sender_wx
     * @return mixed    成功与否
     */
    private function createMpRaw($title, $content, $owner, $sender, $sender_wx)
    {
        //解决短时间内重复调用问题
        $Raw = new RawMessagesModel();
        $duplicate_data = $Raw->where("rid = '$title'")->find();
        if ($duplicate_data) {
            return $duplicate_data;
        }

        $rawAttribute = array(
            'rid' => $title,
            'content' => $content,
            'sender' => $sender,
            'type' => 'wx_mp', // 来自公众号平台
            'remark' => '0',
            'status' => 0,
            'owner' => $owner,
            'sender_wx' => $sender_wx,
        );
        $insert = $Raw->add($rawAttribute);
        return $insert;
    }

}