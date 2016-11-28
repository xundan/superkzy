<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/11/18
 * Time: 16:51
 */

namespace Home\Controller;

use Think\Controller;

header("Content-type: text/html; charset=utf-8");

class WxAccessController extends Controller
{

    public function index()
    {
        $options = array(
            'token' => C('WX_TOKEN'),
            'encodingaeskey' => C('WX_ENCODINGAESKEY'),
            'appid' => C('WX_APPID'),
            'appsecret' => C('WX_APPSECRET')
        );
        $weObj = new \Org\Util\Wechat($options);
        $uri = $weObj->getOauthRedirect(C('ROOT_URL') . "WxAccess/oauth", "", "snsapi_base");
//        echo $uri;
        $this->success("正在跳转", $uri);
        // 已经把uri打印出来直接作为入口，不用注册两次weObj了
    }

    // scope为snsapi_base时 回调的方法
    public function base()
    {
        $options = array(
            'token' => C('WX_TOKEN'),
            'encodingaeskey' => C('WX_ENCODINGAESKEY'),
            'appid' => C('WX_APPID'),
            'appsecret' => C('WX_APPSECRET')
        );
        $weObj = new \Org\Util\Wechat($options);
        // 获取用户授权后的信息
        $resultArr = $weObj->getOauthAccessToken();

        $userModel = M("User");
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
        $options = array(
            'token' => C('WX_TOKEN'),
            'encodingaeskey' => C('WX_ENCODINGAESKEY'),
            'appid' => C('WX_APPID'),
            'appsecret' => C('WX_APPSECRET')
        );
        $weObj = new \Org\Util\Wechat($options);
        // 获取用户授权后的信息
        $resultArr = $weObj->getOauthAccessToken();

        $resultArr["access_token"];
        $userInfo = $weObj->getOauthUserinfo($resultArr["access_token"], $resultArr["openid"]);

        if ($userInfo) {
            $this->save_user_info($userInfo);
            $this->goto_home();
        } else {
            // TODO 写日志，微信平台调用失败
        }
    }

    public function goto_home()
    {
        // 跳转地址设置为默认主页，如果cookie里有上次浏览地址，就跳到上次浏览的地址
        $target_url = "PersonalCenter/personal_center";
        // 等所有视图文件夹里的js/css文件删除后再打开下面的注释
//        if(!empty($_COOKIE['last_url'])){
//            $target_url =  $_COOKIE['last_url'];
//        }
        $this->redirect($target_url, '页面跳转中...');
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
        $data['province'] = $userInfo["province"];
        $data['city'] = $userInfo["city"];
        $data['country'] = $userInfo["country"];
        $data['sex'] = $userInfo["sex"];
        $data['heading_url'] = $userInfo["headimgurl"];
        $data['group_id'] = 2;
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
}