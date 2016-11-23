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
//        dump($weObj->getOauthAccessToken());
        /*array(5) {
  ["access_token"] => string(107) "1QxYPUczGQ3ImL0OjYDpFxFySJnVZk_zRqb7m-wDsxwg6TcMWjaYsXtcwA-CH4E0sGQ5rIV4BUO3g0hyKOVs6LnmV1F98psRW59mYN6qsA8"
  ["expires_in"] => int(7200)
  ["refresh_token"] => string(107) "DiPlJSHk_2n-PkdL1gTIfTlC0rpsVzS5OAwFBZ4sQ7vaiYFBX1qXcxwJCqlDoRpCrxZa75FbXE8Dff4BJOgypDqZt50klQIqLoZhN7aFr-w"
  ["openid"] => string(28) "o5DarwPJb6a2i9Iex1I2skl3ewtM"
  ["scope"] => string(15) "snsapi_userinfo"
}*/
        $resultArr["access_token"];
        $userInfo=$weObj->getOauthUserinfo($resultArr["access_token"],$resultArr["openid"]);
//        dump($userInfo);
        /*array(9) {
  ["openid"] => string(28) "o5DarwPJb6a2i9Iex1I2skl3ewtM"
  ["nickname"] => string(9) "荀辰龙"
  ["sex"] => int(1)
  ["language"] => string(5) "zh_CN"
  ["city"] => string(7) "Haidian"
  ["province"] => string(7) "Beijing"
  ["country"] => string(2) "CN"
  ["headimgurl"] => string(131) "http://wx.qlogo.cn/mmopen/0pygn8iaZdEcicRnOAlTtXy2ia1iaMTPIZTrOnSBJNwY6phBhPYk9MjLuicibnbpx45MOGvcoAN2rexMAL1hf4t0icFcUqAbLysPnBx/0"
  ["privilege"] => array(0) {}
        }*/
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