<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/12/17
 * Time: 11:52
 */

namespace Home\Model;

use Think\Model;

class MessageModel extends Model
{
    protected $tableName = "messages";// 不用写表前缀

    protected $fields=array( //辅助模型识别字段，不会影响查询，会影响增改
        "id",
        "title",
        "area_start",
        "detail_area_start",
        "invalid_id",
        '_pk'=>"id",
    );

    public function findWhere($where){
        return $this;
    }
}