<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/11/24
 * Time: 10:40
 */

namespace Home\Controller;
use Think\Controller;
header("Content-type: text/html; charset=utf-8");
class DriverPublishHistoryController extends ComController
{
    public function driver_publish_history(){
        $this->display();
    }
}