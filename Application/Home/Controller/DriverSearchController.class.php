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


class DriverSearchController extends ComController
{
    public function driver_job_search()
    {
        $input = $this->acquireInput();

        $whereCond = $this->createNewWhereConditions($input);

        $data = $this->getOrderWithoutExist($whereCond,0);

        $this->assign("li_array", $data['li_array']);
        $this->assign("where_cond_json", $data['where_cond_json']);
        $this->assign("stage", $data['stage']);
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
    public function getOrderWithoutExist(WhereConditions $whereCond, $stage)
    {
        if ($stage < CardList::END) { // 如果是结束阶段就不执行下面代码了
            $Msg = new MessagesModel();
            $cards = new CardList(array());
            $cards->setStage($stage);
            $count = "0";
            do {
                $isPopped = $whereCond->preSQL();
                if($cards->notFull()&&$isPopped&&$cards->atAccurate()){// 如果是第一次退格约束
                    $count.="1";
                    $cards->addSimilar();
                    if ($cards->isFull()){
                        $count.="b";
                        break;
                    }
                }
                if($cards->notFull()&&$whereCond->isExhausted()&&$cards->atSimilar()){// 如果条件退完
                    $count.="2";
                    $cards->addOther();
                    if ($cards->isFull()){
                        $count.="c";
                        break;
                    }
                }
                $temp_messages = $Msg->findWhereWithoutExist($whereCond, $cards->getCount(),"找车");
                $cards->appendMessage($temp_messages);
                if ($cards->atOther()&&$cards->notFull()){ //其他查询也不能满足，说明查到底了
                    $count.="3";
                    $count.="d[".$cards->getCount()."]";
                    $cards->addEnd();
                    break;
                }
                $whereCond->postSQL($temp_messages, $cards->getCount());
                $count.="a[".$cards->getCount()."]";
            } while (!($cards->isFull() || $cards->atEnd()));
            // 把$whereCond送到前台
            $data["msg"] = "".$count;
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

    /**
     * @return mixed
     */
    private function acquireInput()
    {
        $post = I('post.', '', 'trim,strip_tags');
        if ($post) {
            $input['areaStart'] = $post['area_start'];
            $input['areaEnd'] = $post['area_end'];
            $input['searchInput'] = $post['search_input'];
            $input['searchTag'] = $post['select_category'];
            return $input;
        } elseif (cookie('search_tag')) {
            $input['areaStart'] = cookie('area_start_id');
            $input['areaEnd'] = cookie('area_end_id');
            $input['searchInput'] = cookie('search_input');
            $input['searchTag'] = cookie('search_tag');
            return $input;
        } else {
            $input['areaStart'] = "610800";
            $input['areaEnd'] = "410100";
            $input['searchInput'] = "";
            $input['searchTag'] = "all";
            return $input;
        }
    }

    /**
     * @param $input
     * @return WhereConditions
     */
    private function createNewWhereConditions($input)
    {
        $whereCond = new WhereConditions();
        if ($input['searchTag'] == "all") {
            $whereCond->pushSearchCond("content_all", $input['searchInput']);
            return $whereCond;
        } else {
            $whereCond->pushSearchCond("content_all", $input['searchInput']);
            $whereCond->pushCond("area_start", "like", substr($input['areaStart'], 0, 2) . "%");
            $whereCond->pushCond("area_end", "like", substr($input['areaEnd'], 0, 2) . "%");
            $whereCond->pushCond("area_start", "eq", $input['areaStart']);
            $whereCond->pushCond("area_end", "eq", $input['areaEnd']);
            return $whereCond;
        }
    }

}