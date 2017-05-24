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
        list($userInfo, $invited_url) = $this->get_user_invitation_id();
        $this->assign('invitation_id',$userInfo['invitation_id']);
        vendor("jssdk.signPackage");
        $this->assign("signPackage",getSignPackage());
        $this->display();
    }

    public function qrcode_invite(){
        list($userInfo, $invited_url) = $this->get_user_invitation_id();
        $this->assign("invited_url", $invited_url);
        $this->assign("qrcode",$userInfo['qr_code']);
        vendor("jssdk.signPackage");
        $this->assign("signPackage",getSignPackage());

        $this->display();
    }

    /**
     * @return array
     */
    private function get_user_invitation_id()
    {
        $uid = $_SESSION['user_info']['uid'];
        $userModel = M("User");
        $userInfo = $userModel->find($uid);
        $invited_url = C('URL');
        if (empty($userInfo)) {
            $this->error("用户不存在。" . $uid);
        }
        if (empty($userInfo["qr_code"])) {
            //邀请码
            if (empty($userInfo['invitation_id'])) {
                $userInfo['invitation_id'] = createRandCode("User", "invitation_id");
                $userModel->save(array("uid" => $uid, 'invitation_id' => $userInfo['invitation_id']));
            }
            //二维码
            $invited_url = "http://".C("URL") . "?invitation_id=" . $userInfo['invitation_id'];
            vendor("qrcode.echoCode");
            $userInfo['qr_code'] = EchoCode($invited_url);
            $userModel->save(array("uid" => $uid, 'qr_code' => $userInfo['qr_code']));
            return array($userInfo, $invited_url);
        }
        return array($userInfo, $invited_url);
    }
}