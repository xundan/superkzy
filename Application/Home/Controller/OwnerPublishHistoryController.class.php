<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/11/3
 * Time: 10:57
 */

namespace Home\Controller;
use Home\Common\CardList\CardList;
use Home\Common\CardList\WhereConditions;
use Home\Model\MessagesModel;
use Think\Controller;

class OwnerPublishHistoryController extends ComController
{
    public function owner_publish_history(){

        $data = $this->getOrderByPage(1, "trade");
        $this->assign("trade_li_array", $data["li_array"]);
        $data = $this->getOrderByPage(1, "transport");
        $this->assign("transport_li_array", $data["li_array"]);
        $this->assign("EOA", $data["EOA"]);
        $this->display();
    }

    public function owner_publish_history_more(){
        $page = I('post.page','','trim,strip_tags');
        $category = I('post.select_category', '', 'trim,strip_tags');
        $data = $this->getOrderByPage($page, $category);
        echo json_encode($data);
        return;
    }


    /**
     * 根据页数去数据库取得相应的消息数组
     * @param $page int 页数
     * @param $category string 类型
     * @return mixed 信息数组
     */
    private function getMessagesByPage($page, $category)
    {
        $Msg = new MessagesModel();
        $uid = session('user_info')['uid'];
        $whereCond = new WhereConditions();
        $whereCond->setPage($page);
        $whereCond->pushCond("publisher_rid", "eq", $uid);
        if ($category == "trade") {
            $whereCond->pushCond("category", "in", array("求购", "供应"));
        } else if($category == "transport") {
            $whereCond->pushCond("category", "in", array("找车", "车源"));
        } else {}
        $messages = $Msg->findWhereForPublishHistory($whereCond);
        return $messages;
    }

    /**
     * 获取返回数据
     * @param $page int 页数
     * @param $category string 类型
     * @return mixed 返回数据
     */
    private function getOrderByPage($page, $category)
    {
        $data["msg"] = "success";
        $messages = $this->getMessagesByPage($page, $category);
        $counts = count($messages);
        $cards = new CardList($messages);
        if ($counts < C('DEFAULT_ROW')) {
            $data["EOA"] = $counts;
            $cards->addEnd();
        } else {
            $data["EOA"] = -1;
        }
        $data["li_array"] = $cards->toLiArray();
        $data["category"] = $category;
        return $data;
    }
}