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
        $uri=$weObj->getOauthRedirect(C('ROOT_URL')."WxAccess/oauth");
//        echo $uri;
        $this->success("正在跳转",$uri);
        //todo 可以把uri打印出来直接作为入口，不用注册两次weObj了
    }

    public function oauth(){
        $options = array(
            'token' => C('WX_TOKEN'),
            'encodingaeskey' => C('WX_ENCODINGAESKEY'),
            'appid' => C('WX_APPID'),
            'appsecret' => C('WX_APPSECRET')
        );
        $weObj = new \Org\Util\Wechat($options);
        // 获取用户授权后的信息
        $resultArr=$weObj->getOauthAccessToken();

        $resultArr["access_token"];
        $userInfo=$weObj->getOauthUserinfo($resultArr["access_token"],$resultArr["openid"]);

        $this->check_in($userInfo);
        $this->goto_home();
    }

    private function check_in($userInfo){
        $userModel=M("User");
        $r=$userModel->where(array("open_id"=>$userInfo["openid"]))->find();
        if(empty($r)){//验证用户是否注册
            $data['open_id'] = $userInfo["openid"];
            $data['user_name'] = $userInfo["nickname"];
            $data['province'] = $userInfo["province"];
            $data['city'] = $userInfo["city"];
            $data['country'] = $userInfo["country"];
            $data['sex'] = $userInfo["sex"];
            $data['heading_url'] = $userInfo["headimgurl"];
            $res = $userModel->add($data);
            if($res){
                $temp['open_id'] = $userInfo["openid"];
                $user_r = $userModel->where($temp)->find();
                session('user_info',$user_r);
                session('role_id',$user_r['role_id']);
            }
        }else{
            session('user_info',$r);
            session('role_id',$r['role_id']);
        }
    }

    public function goto_home()
    {
        // 跳转地址设置为默认主页，如果cookie里有上次浏览地址，就跳到上次浏览的地址
        $target_url = "Homepage/homepage_client";
        if(!empty($_COOKIE['last_url'])){
            $target_url =  $_COOKIE['last_url'];
        }
        $this->redirect($target_url,'页面跳转中...');
    }
}