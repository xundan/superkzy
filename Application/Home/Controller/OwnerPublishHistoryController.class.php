<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/11/3
 * Time: 10:57
 */

namespace Home\Controller;
use Think\Controller;
header("Content-type: text/html; charset=utf-8");

class OwnerPublishHistoryController extends ComController
{
    public function owner_publish_history(){
        $this->display();
    }

}