<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/10/18
 * Time: 14:27
 */

namespace Home\Controller;

use Home\Common\CardList\WhereConditions;
use Think\Controller;


class DriverSearchController extends SearchController
{
    public function driver_job_search()
    {
        $whereCond = $this->createNewWhereConditions();

        $data = $this->getOrderWithoutExist($whereCond,0,"找车");

        $this->assign("li_array", $data['li_array']);
        $this->assign("where_cond_json", $data['where_cond_json']);
        $this->assign("stage", $data['stage']);
        if(I('post.isAjax', '', 'trim,strip_tags')){
            echo json_encode($data);
            return;
        }
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
        $data = $this->getOrderWithoutExist($whereCond, $post['stage'],"找车");
        $data['page'] = $post['page']; // 把page送回去，作为校验
        echo json_encode($data);
        return;
    }


    /**
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
            $input['areaStartName'] = $post['area_start_name'];
            $input['areaEndName'] = $post['area_end_name'];
            //对输入的名字进行处理
            $input['areaStartName'] = preg_replace("/市|区|县|镇/", "", $input['areaStartName']);
            $input['areaEndName'] = preg_replace("/市|区|县|镇/", "",  $input['areaEndName']);
            //TODO 用short_name 取代正则替换
        }
//        elseif (cookie('search_tag')) {
//            $input['areaStart'] = cookie('area_start_id');
//            $input['areaEnd'] = cookie('area_end_id');
//            $input['searchInput'] = cookie('search_input');
//            $input['searchTag'] = cookie('search_tag');
//        } else {
//            $input['areaStart'] = "610800";
//            $input['areaEnd'] = "410100";
//            $input['searchInput'] = "";
//            $input['searchTag'] = "all";
//        }
        else{
            $input['areaStart'] = '';
            $input['areaEnd'] = '';
            $input['searchInput'] = '';
            $input['searchTag'] = '';
            $input['areaStartName'] = '';
            $input['areaEndName'] = '';
        }
        $whereCond = new WhereConditions();
        $whereCond->pushCond("vip","not in",array('5','6','7','8'));
        if ($input['searchTag'] == "all") {
            $whereCond->pushSearchCond("content_all", $input['searchInput']);
            return $whereCond;
        } else {
            return $this->combineAreaInput($input, $whereCond);
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

    /**
     * @param $input
     * @param $whereCond
     * @return mixed
     */
    private function combineAreaInput($input,WhereConditions $whereCond)
    {
        $whereCond->pushSearchCond("content_all", $input['searchInput']);
        //添加白名单
        if(($input['areaStartName'] == '神木') || ($input['areaEndName'] == '神木')){
            $whereCond->pushSearchCond("content_all",'神木 包府路 店塔 孙家岔 大柳塔 尔林兔 锦界','OR');
        }else if(($input['areaStartName'] == '府谷') || ($input['areaEndName'] == '府谷')){
            $whereCond->pushSearchCond("content_all",'府谷 府谷镇 庙沟门 新民 大昌汗 三道沟 老高川 田家寨','OR');
        }else if(($input['areaStartName'] == '榆阳') || ($input['areaEndName'] == '榆阳')){
            $whereCond->pushSearchCond("content_all",'榆阳 麻黄梁 金鸡滩 小壕兔 小纪汗','OR');
        }else if(($input['areaStartName'] == '鄂尔多斯东胜') || ($input['areaEndName'] == '鄂尔多斯东胜')){
            $whereCond->pushSearchCond("content_all",'鄂尔多斯东胜 包府路','OR');
        }else{}

        if ($input['areaStart'] && !$input['areaEnd']) {
            $whereCond->pushCond("area_start", "like", substr($input['areaStart'], 0, 2) . "%");
            $whereCond->pushCond("area_start", "eq", $input['areaStart']);
        }
        if ($input['areaEnd'] && !$input['areaStart']) {
            $whereCond->pushCond("area_end", "like", substr($input['areaEnd'], 0, 2) . "%");
            $whereCond->pushCond("area_end", "eq", $input['areaEnd']);
        }
        if ($input['areaEnd'] && $input['areaStart']) {
            $whereCond->pushCond("area_start", "like", substr($input['areaStart'], 0, 2) . "%");
            $whereCond->pushCond("area_end", "like", substr($input['areaEnd'], 0, 2) . "%");
            $whereCond->pushCond("area_start", "eq", $input['areaStart']);
            $whereCond->pushCond("area_end", "eq", $input['areaEnd']);
        }
        return $whereCond;
    }

}