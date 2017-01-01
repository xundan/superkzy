<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/10/18
 * Time: 14:27
 */

namespace Home\Controller;

use Home\Common\CardList\CardList;
use Home\Common\CardList\WhereConditions;
use Home\Model\MessagesModel;
use Think\Controller;

header("Content-type: text/html; charset=utf-8");

class DriverSearchController extends ComController
{
    public function driver_job_search()
    {
        $Msg = new MessagesModel();
        $post = I('post.', '', 'trim,strip_tags');
        if ($post) {
            $whereCondJson = $post['where_cond_json'];
            $whereCond = WhereConditions::parseJson($whereCondJson);
        } elseif (cookie('where_cond_json')) {
            $whereCondJson = cookie('where_cond_json');
            $whereCond = WhereConditions::parseJson($whereCondJson);
        } else {
            // 默认值
            $whereCond = new WhereConditions();
            $whereCond->pushCond("area_start", "eq", "610800");
            $whereCond->pushCond("area_end", "eq", "410100");
            $whereCond->pushCond("area_start", "like", "6108%");
            $whereCond->pushCond("area_end", "like", "4101%");
            $whereCond->pushCond("area_start", "like", "61%");
            $whereCond->pushCond("area_end", "like", "41%");
        }


        $messages = $Msg->findWhere($whereCond);

        $cards = new CardList($messages);
        $this->assign("li_array", $cards->toLiArray());
        $this->assign("where_cond_json", json_encode($whereCond));
        $this->display();
    }

    /**
     * 上拉加载
     */
    public function driver_job_search_more()
    {
        $post = I('post.', '', 'trim,strip_tags');
        $whereCondJson = $post['where_cond_json'];
        $whereCond = WhereConditions::parseJson($whereCondJson);
        $data = $this->getOrderWithoutExist($whereCond);
        $data['page'] = $post['page']; // 把page送回去，作为校验
        echo json_encode($data);
        return;
    }

    /**
     * 在后台拼装WhereConditions对象
     */
    public function setCookie()
    {
        $post = I('post.', '', 'trim,strip_tags');
        $area_start_id = $post['area_start_id'];
        $area_end_id = $post['area_end_id'];
        $whereCond = new WhereConditions();
        $whereCond->pushCond("area_start", "eq", $area_start_id);
        $whereCond->pushCond("area_end", "eq", $area_end_id);
        $whereCond->pushCond("area_start", "like", $area_end_id);
        $whereCond->pushCond("area_end", "like", "4101%");
        $whereCond->pushCond("area_start", "like", "61%");
        $whereCond->pushCond("area_end", "like", "41%");

        cookie();
    }

    /**
     * 根据页数去数据库取得相应的消息数组
     * @param $whereCond mixed 查询条件
     * @return mixed 信息数组
     */
    private function getMessagesWithoutExist(WhereConditions $whereCond)
    {
        $Msg = new MessagesModel();
        $messages = null;
        $count = 0;
        do {
            $whereCond->preSQL();
            $messages = $Msg->findWhereWithoutExist($whereCond,$count);
            $whereCond->postSQL($messages);
        } while (count($messages)>=C('DEFAULT_ROW')||$whereCond->isExhausted());


        return $messages;
    }

    /**
     * 获取返回数据
     * @param $whereCond mixed 查询条件
     * @return mixed 返回数据
     */
    private function getOrderWithoutExist(WhereConditions $whereCond)
    {
        $data["msg"] = "success";
        $messages = $this->getMessagesWithoutExist($whereCond);
        $cards = new CardList($messages);
        if (!$whereCond->isLastCountFull()) {
            $cards->addEnd();
        }
        // 把$whereCond送到前台
        $data['where_cond_json'] = $whereCond->toJson();
        $data["li_array"] = $cards->toLiArray();
        return $data;
    }

}