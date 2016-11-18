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
        $this->display();
    }

}