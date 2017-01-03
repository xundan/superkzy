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
            $areaStart = $post['area_start'];
            $areaEnd = $post['area_end'];
            $searchInput = $post['search_input'];
            $searchTag = $post['search_tag'];
            $whereCond = new WhereConditions();
            if ($searchTag == "all") {
                $whereCond->pushCond("category", "eq", "求车");
                $whereCond->pushCond("category", "like", "%$searchInput%");

            }
//            $whereCond = WhereConditions::parseJson($whereCondJson);
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
        $data = $this->getOrderWithoutExist($whereCond, $post['stage']);
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

        cookie("where_cond_json", $whereCond->toJson());
    }


    /**
     * 获取返回数据，保证每次都获取十个卡片，包括tips
     * @param $whereCond mixed 查询条件
     * @param $stage int 当前卡片列表的状态：精确、模糊、其他、结束
     * @return mixed 返回数据
     */
    private function getOrderWithoutExist(WhereConditions $whereCond, $stage)
    {
        if ($stage < CardList::END) { // 如果是结束阶段就不执行下面代码了
            $Msg = new MessagesModel();
            $cards = new CardList(array());
            $cards->setStage($stage);
            $count = 0; // 记录目前卡片的数量
            do {
                $isPopped = $whereCond->preSQL();
                if(($count < C('DEFAULT_ROW'))&&$isPopped&&$cards->atAccurate()){// 如果是第一次退格约束
                    $cards->addSimilar();
                    $count++;
                    if ($count>=C('DEFAULT_ROW')) break;
                }
                if(($count < C('DEFAULT_ROW'))&&$whereCond->isExhausted()&&$cards->atSimilar()){// 如果条件退完
                    $cards->addOther();
                    $count++;
                    if ($count>=C('DEFAULT_ROW')) break;
                }
                $temp_messages = $Msg->findWhereWithoutExist($whereCond, $count);
                $count += count($temp_messages);
                $cards->appendMessage($temp_messages);
                if ($cards->atOther()&&($count < C('DEFAULT_ROW'))){ //其他查询也不能满足，说明查到底了
                    $cards->addEnd();
                    $count++;
                    if ($count>=C('DEFAULT_ROW')) break;
                }
                $whereCond->postSQL($temp_messages, $count);
            } while ($count >= C('DEFAULT_ROW') || $cards->atEnd());
            // 把$whereCond送到前台
            $data["msg"] = "success";
            $data['where_cond_json'] = $whereCond->toJson();
            $data["li_array"] = $cards->toLiArray();
            $data["stage"] = $cards->getStage();
        } else {
            $data["msg"] = "success";
            $data['where_cond_json'] = $whereCond->toJson();
            $data["li_array"] = "";
            $data["stage"] = $stage;
        }

        return $data;
    }

}