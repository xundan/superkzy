<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/9/27
 * Time: 22:32
 */

namespace Home\Model;


use Think\Model;

class CollectionModel extends Model
{
    protected $tableName = "collection";// 不用写表前缀

    protected $fields = array( //辅助模型识别字段，不会影响查询，会影响增改
        "id",
        "user_id",
        "msg_id",
        "record_time",
        "invalid_id",
        '_pk' => "id",
    );


    /**
     * 添加收藏，如果有记录，直接更新invalid_id字段
     * @param $user_id
     * @param $msg_id
     * @return int
     */
    public function add_c($user_id, $msg_id)
    {
        $exist = $this->where(array("user_id" => $user_id, "msg_id" => $msg_id))->find();
        if ($exist) {
            $exist["invalid_id"] = 0;
            $temp = $this->save($exist);
            if ($temp===false) return 0;
        } else {
            $temp = $this->add(array("user_id" => $user_id, "msg_id" => $msg_id, "invalid_id" => 0));
            if ($temp===false) return 0;
        }
        return 1;
    }

    /**
     * 取消关注
     * @param $user_id
     * @param $msg_id
     * @return int
     */
    public function del_c($user_id, $msg_id)
    {
        $exist = $this->where(array("user_id" => $user_id, "msg_id" => $msg_id))->find();
        if ($exist) {
            $exist["invalid_id"] = 1;
            $temp = $this->save($exist);
            if ($temp===false) return 0;
        }
        return 1;
    }

    public function getCollectionById($user_id)
    {
        $id_result = $this->field("msg_id")->where(array("user_id" => $user_id, "invalid_id" => 0))->select();
        $ids = array();
        foreach ($id_result as $id_row) {
            array_push($ids, $id_row["msg_id"]);
        }
        return $ids;
    }
}