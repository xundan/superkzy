<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/10/18
 * Time: 14:27
 */

namespace Home\Controller;
use Think\Controller;
header("Content-type: text/html; charset=utf-8");
class DriverSearchController extends ComController
{
    public function driver_job_search(){
        $this->display();
    }

}