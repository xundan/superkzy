<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/11/17
 * Time: 9:45
 */

namespace Home\Controller;
use Think\Controller;
header("Content-type: text/html; charset=utf-8");

class HomepageController extends ComController
{
    public function homepage(){
        $this->display();
    }

    public function homepage_visitor(){
        $this->display();
    }

    public function homepage_client(){
        $this->display();
    }
}