<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/11/24
 * Time: 10:17
 */

namespace Home\Controller;
use Think\Controller;
header("Content-type: text/html; charset=utf-8");

class DriverOrderController extends ComController
{
    public function driver_order(){
        $this->display();
    }

    public function driver_order_detail(){
        $this->display();
    }

}