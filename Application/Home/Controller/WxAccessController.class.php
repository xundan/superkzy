<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/11/18
 * Time: 16:51
 */

namespace Home\Controller;


class WxAccessController
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
//        $weObj->valid();
        echo "ChatRecord api works";
        $uri=$weObj->getOauthRedirect("http://www.kuaimei56.com/index.php/Views/WeChatDemo/demo");
        $this->success("hue",$uri);
    }

    public function demo(){
//        $this->success("huehue","http://");
        $options = array(
            'token' => C('WX_TOKEN'),
            'encodingaeskey' => C('WX_ENCODINGAESKEY'),
            'appid' => C('WX_APPID'),
            'appsecret' => C('WX_APPSECRET')
        );
        $weObj = new \Org\Util\Wechat($options);
//        $weObj->valid();
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
        $this->save($userInfo);
    }



    private function save($userInfo){
        $userModel=M("Users");
        $r=$userModel->where(array("open_id"=>$userInfo["openid"]))->find();
        if(!empty($r)){//验证用户是否注册
            $returnArr['status']=0;
            $returnArr['msg']="该手机号已经注册过。";
            echo json_encode($returnArr);exit;
        }
        $data['open_id'] = $userInfo["openid"];
        $data['user_name'] = $userInfo["nickname"];
        $data['province'] = $userInfo["province"];
        $data['city'] = $userInfo["city"];
        $data['country'] = $userInfo["country"];
        $data['sex'] = $userInfo["sex"];
        $data['headimg_url'] = $userInfo["headimgurl"];
        $res = $userModel->add($data);
        if($res){
            $returnArr['status']=1;
            $returnArr['msg']="注册成功。";
//            $tesp['phone_number'] = $phone_numbers;
//            $res = $userModel->where($tesp)->find();
//            $_SESSION['user_info'] = $res;
//            $_SESSION['role_id'] = $res['role_id'];
            echo jsonEcho($returnArr);exit;
        }
        else{
            $returnArr['status']=0;
            $returnArr['msg']="注册失败。";
            echo jsonEcho($returnArr);exit;
        }
    }
}