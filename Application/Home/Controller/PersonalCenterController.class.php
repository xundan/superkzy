<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/11/15
 * Time: 15:58
 */

namespace Home\Controller;
use Think\Controller;
header("Content-type: text/html; charset=utf-8");

class PersonalCenterController extends ComController
{
    public function personal_center(){
        $this->display();
    }

    public function owner_data(){
        $this->display();
    }

    public function driver_data(){
        $this->display();
    }

}