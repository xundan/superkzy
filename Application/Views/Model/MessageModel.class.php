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
        $exist = $this->where("content_all_md5='%s' and invalid_id=0",$md5)->order("publish_time desc")->find();
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

    // 信息总量统计
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

    public function group_statistics_by_day(){
        return $this->field("substr(`record_time`,1,10) as a ,COUNT(*) as s")->where("type='group'")->group('a')->order('a')->select();
    }

    // 信息过期提醒SMS发送
    public function get_expiring_msg($date){
        return $this->field("id,publisher_rid,phone_number,category")->where("deadline like '$date%' and invalid_id=99 and type='web'")->select();
    }

    public function del_all_group_msg(){
        $where_cond = array(
            "type" => "group",
            "status" => 0,
            "invalid_id" => 0,
        );
        $update_delete = array(
            "status" => -1,
            "invalid_id" => 2,
        );
        return $this->where($where_cond)->save($update_delete);
    }

    public function get_all_history($kw, $category,$s_date, $e_date){
        return $this->field("phone_number,content,type,sender,update_time")->where("content like '%$kw%' and content not like '%$kw"
            ."元%' and content not like '%$kw"."吨%' and category='$category' and update_time>'$s_date' and update_time<'$e_date'")->order("id desc")->select();
    }

    /**
     * @param $categoryArr
     * @param $searchArr
     * @return array
     */
    public function selectSearch($categoryArr, $searchArr, $page)
    {
        $res = array(); // 默认返回值
        $map = array(); // 如果没有map就不执行sql
        if ($categoryArr) $map['category'] = array("IN",implode(",",$categoryArr));
        if ($searchArr){
            $query = array();
            array_push($query,'like');
            // 为关键字数组$tempStr
            foreach ($searchArr as &$item) {
//            $query[] = array('like', '%' . $item . '%');
                $item = "%".$item."%";
            }
            array_push($query,$searchArr);
            array_push($query,"AND");
            $map['content_all']=$query;
        }
        if ($map){
            $map['invalid_id']=array("EQ",0);
            $res=$this->where($map)->order('update_time desc')->page($page,5)->select();
        }
//        return $this->getLastSql();
        return $res;
    }

    /**
     * @param $categoryArr
     * @param $granularityArr
     * @param $kindArr
     * @param $digitsArr
     * @return array
     */
    public function selectQuery($categoryArr,$granularityArr,$kindArr,$digitsArr)
    {
        $res = array(); // 默认返回值
        $map = array(); // 如果没有map就不执行sql
        if ($categoryArr) $map['category'] = array("IN",implode(",",$categoryArr));
        if ($granularityArr) $map['granularity'] = array("IN",implode(",",$granularityArr));
        if ($kindArr) $map['kind'] = array("IN",implode(",",$kindArr));
        if ($digitsArr){
            // todo 这里的逻辑需要再考虑
            $map['heat_value_max'] = array("IN",implode(",",$digitsArr));
            $map['heat_value_min'] = array("IN",implode(",",$digitsArr));
            $map['price_min'] = array("IN",implode(",",$digitsArr));
            $map['price_max'] = array("IN",implode(",",$digitsArr));
        }
        if ($map){
            $map['invalid_id']=array("EQ",0);
            $res=$this->where($map)->select();
        }
        return $res;
    }

    public function getById($id)
    {
        $res=$this->where("id=".$id)->find();
        return $res;
    }


}