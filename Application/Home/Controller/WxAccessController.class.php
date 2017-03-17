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
        $this->getWeObj()->valid();
        $type = $this->getWeObj()->getRev()->getRevType();
        switch($type) {
            case \Org\Util\Wechat::MSGTYPE_TEXT:
                $content = $this->getWeObj()->getRevContent();
                if ($content=="你好"){
                    $this->getWeObj()->text("你好，这里是超级矿资源，感谢您的支持！")->reply();
                    exit;
                }
                if ($content=="查看结构"){
                    $str = json_encode($this->getWeObj()->getRevData());
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
                        $this->getWeObj()->text("您的消息已经收到，我们将及时为您转发。")->reply();
                    } else {
                        Log::record("WxAccess: insert_raw() failed. ", Log::ERR);
                    }

                    exit;
                }


                break;
            case \Org\Util\Wechat::MSGTYPE_EVENT:
                $event = $this->getWeObj()->getRevEvent();

                if ($event['event']=='subscribe'){
                    $welcome_str = "感谢关注【超级矿资源】微信公众平台！
您可以点击下方的进入 <a href='http://www.kuaimei56.com/index.php/Home/Homepage/homepage'>首页</a>开始 <a href='http://www.kuaimei56.com/index.php/Home/OwnerPublish/owner_publish'>发布</a>或 <a href='http://www.kuaimei56.com/index.php/Home/Homepage/homepage'>查询</a> 运单、订单信息。也可以在这里回复直接提出您的问题。

在本页面直接回复您要转发的消息，我们会为您转发到我们的<a href='http://www.kuaimei56.com/index.php/Home/Homepage/homepage'>平台网站</a>和所有超矿微信的朋友圈。";
                    $this->getWeObj()->text($welcome_str)->reply();
                    exit;
                }
                break;
            case \Org\Util\Wechat::MSGTYPE_IMAGE:
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
                        'name' => '会员中心',
                        'sub_button' => array(
                            array('type' => 'view', 'name' => '会员注册', 'url' => 'http://www.kuaimei56.com/index.php/Home/Login/register'),
//                            array('type' => 'view', 'name' => '会员活动', 'url' => 'http://www.kuaimei56.com/index.php/Home/Login/register'),
                        ),
                    ),
                    array('type' => 'view', 'name' => '平台网站', 'url' => 'http://www.kuaimei56.com/index.php/Home/Homepage/homepage'),
                    array(
                        'name' => '戳我福利',
                        'sub_button' => array(
                            array('type' => 'view', 'name' => '优惠加油', 'url' => 'https://u.wcar.net.cn/1dN'),
                            array('type' => 'view', 'name' => '超矿金融', 'url' => 'http://www.kuaimei56.com/index.php/Views/FinancialClient/show'),
//                            array('type' => 'view', 'name' => '推荐好友', 'url' => 'http://www.kuaimei56.com/index.php/Home/Homepage/homepage'),
                        ),
                    ),
                )
        );
        $result = $weObj->createMenu($newmenu);
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