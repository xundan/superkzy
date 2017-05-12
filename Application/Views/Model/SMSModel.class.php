<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2017/5/11
 * Time: 9:09
 */

namespace Views\Model;


use Think\Model;

class SMSModel extends Model
{
    protected $tableName = "ck_sms";

    protected $fields = array( //辅助模型识别字段，不会影响查询，会影响增改
        "id",
        "phone",
        "msg_id",
        "user_id",
        "amount",
        "status",
        "send_time",
        "invalid_id",
        "remark",
        '_pk' => "id",
    );

}