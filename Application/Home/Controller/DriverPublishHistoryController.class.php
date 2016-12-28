<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/11/24
 * Time: 10:40
 */

namespace Home\Controller;
use Home\Common\CardList\CardList;
use Home\Common\CardList\WhereConditions;
use Think\Controller;
header("Content-type: text/html; charset=utf-8");
class DriverPublishHistoryController extends ComController
{
    public function driver_publish_history(){
        $uid = session('uid');
        $whereCond = new WhereConditions();
        $whereCond->pushCond("publisher_rid", "eq", $uid);
        $messages = D('messages')->findWhere($whereCond);
//        dump($messages);
        $cards = new CardList($messages);
        $this->assign("li_array",$cards->toLiArray());
//        dump($cards->toLiArray());
        $this->display();
    }
}