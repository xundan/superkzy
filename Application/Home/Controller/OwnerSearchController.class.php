<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/10/18
 * Time: 23:41
 */

namespace Home\Controller;

use Home\Common\CardList\WhereConditions;
use Think\Controller;

class OwnerSearchController extends SearchController
{
    public function owner_car_search(){
        $whereCond = $this->createNewWhereConditions();

        $data = $this->getOrderWithoutExist($whereCond,0,"车源");
        $data = $this->search_highlight($data);

        $this->assign("li_array", $data['li_array']);
        $this->assign("where_cond_json", $data['where_cond_json']);
        $this->assign("stage", $data['stage']);
        $this->display();
    }


    /**
     * 上拉加载
     */
    public function owner_car_search_more()
    {
        $post = I('post.', '', 'trim,strip_tags');
        $whereCondJson = $post['where_cond_json'];
        $whereCond = WhereConditions::parseJson($whereCondJson);
        $data = $this->getOrderWithoutExist($whereCond, $post['stage'],"车源");
        $data['page'] = $post['page']; // 把page送回去，作为校验
        $data = $this->search_highlight($data);
        echo json_encode($data);
        return;
    }

    /**
     * 根据输入条件组装WhereCondition
     * @return WhereConditions
     */
    function createNewWhereConditions()
    {

        $post = I('post.', '', 'trim,strip_tags');
        if ($post) {
            $input['areaStart'] = $post['area_start'];
            $input['areaEnd'] = $post['area_end'];
            $input['searchInput'] = $post['search_input'];
            $input['searchTag'] = $post['select_category'];
        } elseif (cookie('search_tag')) {
            $input['areaStart'] = cookie('area_start_id');
            $input['areaEnd'] = cookie('area_end_id');
            $input['searchInput'] = cookie('search_input');
            $input['searchTag'] = cookie('search_tag');
        } else {
            $input['areaStart'] = "610800";
            $input['areaEnd'] = "410100";
            $input['searchInput'] = "";
            $input['searchTag'] = "all";
        }

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
     * 替换搜索内容以高亮显示
     * @param $data
     * @return mixed
     */
    private function search_highlight($data){
        if(cookie('search_input_for_highlight')){
            $search = urldecode(cookie('search_input_for_highlight'));
            $search_replace = "<span style='color:red'>".$search."</span>";
            $data['li_array'] = str_replace($search,$search_replace,$data['li_array']);
        }else{}

        return $data;

    }
}