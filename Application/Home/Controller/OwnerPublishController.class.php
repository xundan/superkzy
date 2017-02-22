<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/11/3
 * Time: 16:13
 */

namespace Home\Controller;
use Home\Model\MessagesModel;
use Think\Controller;
use Think\Model;

header("Content-type: text/html; charset=utf-8");

class OwnerPublishController extends ComController
{
    public function owner_publish(){
        $this->display();
    }

    public function car_give_action(){
        $a = new MessagesModel();
        $a->addInto();
    }

    public function car_need_action(){
        $a = new MessagesModel();
        $a->addInto();
    }

    public function coal_sell_action(){
        $a = new MessagesModel();
        $a->addInto();
    }

    public function coal_buy_action(){
        $a = new MessagesModel();
        $a->addInto();
    }
}