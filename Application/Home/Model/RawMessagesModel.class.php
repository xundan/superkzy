<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/7/28
 * Time: 11:02
 */

namespace Home\Model;
use Think\Model;

class RawMessagesModel extends Model
{
    protected $trueTableName = "raw_messages";

    protected $fields=array( //辅助模型识别字段，不会影响查询，会影响增改
        "id",
        "rid",
        "content",
        "sender",
        "send_time",
        "type",
        "remark",
        "owner",
        "sender_wx",
        "status",
        '_pk'=>"id",
    );
}