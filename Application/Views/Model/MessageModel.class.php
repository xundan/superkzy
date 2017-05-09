<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/8/11
 * Time: 9:38
 */

namespace Views\Model;

use Think\Model;

class MessageModel extends Model
{
    protected $tableName = "ck_messages";

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

    public function add_by_md5($data){
        $md5 = $data['content_all_md5'];
        $exist = $this->where("content_all_md5='%s' and invalid_id=0",$md5)->find();
        if ($exist) {
            if ($exist['publish_time']+(86400*3)<time()){ // 三天前的不考虑去重（会导致MD5重复）
                return $this->add($data);
            }else{
                return 0;
            }
        }else{
            return $this->add($data);
        }
    }

    public function statistics_by_day(){
        return $this->field("substr(`record_time`,1,10) as a ,COUNT(*) as s")->group('a')->order('a desc')->select();
    }
    public function all_statistics_by_day(){
        return $this->field("substr(`record_time`,1,10) as a ,COUNT(*) as s")->group('a')->order('a')->select();
    }

    public function web_statistics_by_day(){
        return $this->field("substr(`record_time`,1,10) as a ,COUNT(*) as s")->where("type='web'")->group('a')->order('a')->select();
    }

    public function wx_mp_statistics_by_day(){
        return $this->field("substr(`record_time`,1,10) as a ,COUNT(*) as s")->where("type='wx_mp'")->group('a')->order('a')->select();
    }

    public function plain_statistics_by_day(){
        return $this->field("substr(`record_time`,1,10) as a ,COUNT(*) as s")->where("type='plain'")->group('a')->order('a')->select();
    }
}