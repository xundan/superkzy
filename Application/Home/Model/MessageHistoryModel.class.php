<?php

namespace Home\Model;

use Think\Model;
use Home\Common\CardList\WhereConditions;

class MessageHistoryModel extends Model
{
    protected $tableName = "messages_history";

    private $_message = null;

    protected $fields = array( //辅助模型识别字段，不会影响查询，会影响增改
        "id",
        "title",
        'area_start',
        'detail_area_start',
        'area_end',
        'detail_area_end',
        'origin',
        'category',
        'product_id',
        'car_type_id',
        'publisher_rid',
        'publish_time',
        'deadline',
        'level',
        'valid_time',
        'via_type',
        'times_number',
        'type',
        'content',
        'status',
        'remark',
        'record_time',
        'invalid_id',
        'handler',
        'recorder',
        'owner',
        'sender_wx',
        'sender',
        'update_time',
        'updater_id',
        'phone_number',
        'short_allocate',
        'price',
        'open_price',
        'place_origin_id',
        'supply_company',
        'loading_time',
        'loading_cost',
        'unloading_cost',
        'pay_type',
        'price_min',
        'price_max',
        'quantity',
        'formatted',
        'content_all',
        'content_all_md5',
        'car_capacity',
        'car_type',
        'car_quantity',
        'heat_value_max',
        'heat_value_min',
        'water',
        'ash',
        'sulfur',
        'volatile',
        'kind',
        'trait',
        'granularity',
        'vip',
        '_pk' => "id",
    );

    public function findWhere(WhereConditions $cond)
    {
        $countRow = C("DEFAULT_ROW");//常量
        $page = $cond->getPage();
        $asc = $cond->getAsc();
        $beginStr = ($page - 1) * $countRow;
        $this->_message = $this->where($cond->getWhereConditions())->limit($beginStr, $countRow)->order($asc)->select();
        return $this->_message;
    }

}