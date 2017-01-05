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

        $data = $this->getOrderWithoutExist($whereCond,0,$input['searchTag']);

        $this->assign("li_array", $data['li_array']);
        $this->assign("where_cond_json", $data['where_cond_json']);
        $this->assign("stage", $data['stage']);
//        echo $data['msg'];
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
        $data = $this->getOrderWithoutExist($whereCond, $post['stage'],$post['select_category']);
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
        $whereCond->pushSearchCond("content_all", $input['searchInput']);
        $whereCond->pushCond("kind","in",$input['filter_kind']);
        $whereCond->pushCond("granularity","in",$input['filter_granularity']);
        $whereCond->pushCond("heat_value_min","egt",$input['filter_heat_min']);
        $whereCond->pushCond("heat_value_max","elt",$input['filter_heat_max']);

        return $whereCond;
    }

    /**
     * @return mixed
     */
    private function fetchInput()
    {
        $post = I('post.', '', 'trim,strip_tags');
        if ($post) {
            $input['searchInput'] = $post['search_input'];
            $input['searchTag'] = $post['select_category'];
            $input['filter_kind'] = $post['filter_kind'];
            $input['filter_granularity'] = $post['filter_granularity'];
            $input['filter_heat_min'] = $post['filter_heat_min'];
            $input['filter_heat_max'] = $post['filter_heat_max'];
            return $input;
        } else {
            $input['searchInput'] = "";
            $input['searchTag'] = "求购";
            $input['filter_kind'] = null;
            $input['filter_granularity'] = null;
            $input['filter_heat_min'] = null;
            $input['filter_heat_max'] = null;
            return $input;
        }
    }
}