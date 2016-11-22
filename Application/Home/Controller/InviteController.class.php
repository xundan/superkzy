<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/11/16
 * Time: 13:33
 */

namespace Home\Controller;
use Think\Controller;
header("Content-type: text/html; charset=utf-8");

class InviteController extends ComController
{
    public function invite(){
        $this->display();
    }

    public function qrcode_invite(){
        $uid=$_SESSION['user_info']['uid'];
        $userModel=M("User");
        $userInfo=$userModel->find($uid);
        if(empty($userInfo)){
            $this->error("用户不存在。".$uid);
        }
        if(empty($userInfo["qrcode"])){
            //邀请码
//            $this->error("111");
            if(empty($userInfo['invitation_id'])){
                $userInfo['invitation_id']=createRandCode("User","invitation_id");
                $userModel->save(array("uid"=>$uid,'invitation_id'=>$userInfo['invitation_id']));
            }
            //二维码
            vendor("qrcode.echoCode");
            $userInfo['qrcode']=EchoCode(C("URL")."?invitation_id=".$userInfo['invitation_id']);
            $userModel->save(array("uid"=>$uid,'qr_code'=>$userInfo['qrcode']));
        }
        $this->assign("qrcode",$userInfo['qrcode']);
        $this->display();
    }


}