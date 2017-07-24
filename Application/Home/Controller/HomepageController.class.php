<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/11/17
 * Time: 9:45
 */

namespace Home\Controller;

use Home\Model\MessagesModel;
use Home\Common\CardList\WhereConditions;
use Think\Controller;

header("Content-type: text/html; charset=utf-8");

class HomepageController extends ComController
{
    public function homepage()
    {
        $Msg = new MessagesModel();
        $this->assign('todayCount', $Msg->todayCount());
        $this->assign('todayTrade', $Msg->todayTradeCount());
        $this->assign('todayTrans', $Msg->todayTransportCount());
        if (session('user_info')) {
            $where = new WhereConditions();
            $where->pushCond('publisher_rid', 'EQ', session('user_info')['uid']);
            $this->assign('publishCount', $Msg->publishCount($where));
        } else {
        }
        $this->display();
    }
//
//    public function homepage_visitor(){
//        $this->display();
//    }
//
//    public function homepage_client(){
//        $this->display();
//    }
}