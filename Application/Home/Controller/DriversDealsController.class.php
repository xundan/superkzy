<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/10/18
 * Time: 23:41
 */

namespace Home\Controller;
use Think\Controller;
header("Content-type: text/html; charset=utf-8");
class DriversDealsController extends ComController
{
    public function drivrs_deals(){
        $this->display();
    }
}