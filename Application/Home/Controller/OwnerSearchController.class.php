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
class OwnerSearchController extends ComController
{
    public function owner_car_search(){
        $this->display();
    }
}