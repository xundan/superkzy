<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/11/3
 * Time: 10:57
 */

namespace Home\Controller;
use Think\Controller;
use Home\Common\CardList\CardList;
use Home\Common\CardList\WhereConditions;
header("Content-type: text/html; charset=utf-8");

class OwnerPublishHistoryController extends ComController
{
    public function owner_publish_history(){
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