<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2017/2/28
 * Time: 16:47
 */

namespace Views\Model;

use Think\Model;

class FinancialClientModel extends Model
{

    protected $tableName = "ck_financial_client";

    protected $fields=array( //辅助模型识别字段，不会影响查询，会影响增改
        "id",
        "phone_number",
        "type",
        "record_time",
        "status",
        "invalid_id",
        '_pk'=>"id",
    );
}