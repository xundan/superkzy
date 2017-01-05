<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/10/21
 * Time: 17:48
 */

namespace Home\Controller;
use Home\Common\CardList\WhereConditions;
use Think\Controller;

class TradeSearchController extends SearchController
{

    public function trade_search(){
        $input = $this->fetchInput();

        $whereCond = $this->createNewWhereConditions($input);

        $data = $this->getOrderWithoutExist($whereCond,0,"其他");

        $this->assign("li_array", $data['li_array']);
        $this->assign("where_cond_json", $data['where_cond_json']);
        $this->assign("stage", $data['stage']);
        $this->display();
    }


    /**
     * 上拉加载
     */
    public function trade_search_more()
    {
        $post = I('post.', '', 'trim,strip_tags');
        $whereCondJson = $post['where_cond_json'];
        $whereCond = WhereConditions::parseJson($whereCondJson);
        $data = $this->getOrderWithoutExist($whereCond, $post['stage'],"其他");
        $data['page'] = $post['page']; // 把page送回去，作为校验
        echo json_encode($data);
        return;
    }

    /**
     * 根据输入条件组装WhereCondition
     * @param mixed $input
     * @return WhereConditions
     */
    function createNewWhereConditions($input)
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

    /**
     * @return mixed
     */
    private function fetchInput()
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
}