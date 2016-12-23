<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/12/17
 * Time: 11:52
 */

namespace Home\Model;

use Home\Common\CardList\WhereConditions;
use Think\Model;

class MessagesModel extends Model
{
//    protected $tableName = "messages";// 不用写表前缀

//    protected $fields = array( //辅助模型识别字段，不会影响查询，会影响增改
//        "id",
//        "title",
//        "area_start",
//        "detail_area_start",
//        "publisher_rid",
//        "invalid_id",
//        '_pk' => "id",
//    );

    private $_message = null;

    public function getMessageAttr($id = 1, $attr = "content"){
        $msg =  $this->find($id);
        return $msg[$attr];
    }

    public function findWhere(WhereConditions $cond)
    {
        $countRow = C("DEFAULT_ROW");//常量
        $page = $cond->getPage();
        $asc = $cond->getAsc();
        $beginStr = ($page - 1) * $countRow;
        $this->_message = $this->where($cond->getWhereConditions())->limit($beginStr, $countRow)->order($asc)->select();
        return $this->_message;
    }

    public function toAll($message){
        $message = $this->toProduct($message);
        $message = $this->toUser($message);
        $message = $this->toDistrictStart($message);
        $message = $this->toDistrictEnd($message);
        return $message;
    }

    public function toUser($message){
        $where['uid'] = $message['publisher_rid'];
        $user = M('user')->where($where)->find();
        if($user){
            $message['user']=$user;
        }elseif($user === false){
            //todo 数据库出错
        }else{
            //todo 数据为空
        }
        return $message;
    }

    public function toProduct($message){
        $where['id'] = $message['product_id'];
        $product = M('product')->where($where)->find();
        if($product){
            $message['product']= $product;
        }elseif($product === false){
            //todo 数据库出错
        }else{
            //todo 数据为空
        }
        return $message;
    }

    public function toDistrictStart($message){
        $where['id'] = $message['area_start'];
        $district = M('districts')->where($where)->find();
        if($district){
            $message['district_start']= $district;
        }elseif($district === false){
            //todo 数据库出错
        }else{
            //todo 数据为空
        }
        return $message;
    }

    public function toDistrictEnd($message){
        $where['id'] = $message['area_end'];
        $district = M('districts')->where($where)->find();
        if($district){
            $message['district_end']= $district;
        }elseif($district === false){
            //todo 数据库出错
        }else{
            //todo 数据为空
        }
        return $message;
    }

    function toSimple()
    {
        if ($this->_message['type'] == 'plain') { //微信的消息
            $new_message['title'] = $this->_message['title'];
            $new_message['phone_number'] = $this->_message['origin'];
            $new_message['content'] = $this->_message['content'];
            $this->_message = $new_message;
        } else {
            if ($this->_message['category'] === 0) { //供应
                //用户信息
                $this->toUser($this->_message);
                $new_message['user_name'] = $this->_message['user']['user_name'];
                $new_message['headimg_url'] = $this->_message['user']['headimg_url'];
                $new_message['city'] = $this->_message['user']['city'];
                //煤炭信息
                $this->toProduct($this->_message);
                $new_message['coal_kind_name']= $this->_message['product']['kind'];
                $new_message['coal_trait_name']= $this->_message['product']['trait'];
                $new_message['coal_granularity_name']= $this->_message['product']['granularity'];
                //消息信息
                $this->toDistrictStart($this->_message);
                $new_message['area_start'] = $this->_message['district_start']['name'];
                $new_message['phone_number'] = $this->_message['phone_number'];
                $new_message['price'] = $this->_message['price'];
                $new_message['quantity'] = $this->_message['quantity'];
                $new_message['deadline'] = date('Y-m-d H:i:s',$this->_message['deadline']);
                $new_message['publish_time'] = date('Y-m-d H:i:s',$this->_message['publish_time']);

            } elseif ($this->_message['category'] == 1) { //司机找活
                //用户信息
                $this->toUser($this->_message);
                $new_message['user_name'] = $this->_message['user']['user_name'];
                $new_message['headimg_url'] = $this->_message['user']['headimg_url'];
                //煤炭信息
                $this->toProduct($this->_message);
                $new_message['coal_granularity_name']= $this->_message['product']['granularity'];
                //消息信息
                $new_message['phone_number'] = $this->_message['phone_number'];
                $this->toDistrictStart($this->_message);
                $new_message['area_start'] = $this->_message['district_start']['name'];
                $this->toDistrictEnd($this->_message);
                $new_message['area_end'] = $this->_message['district_end']['name'];
                $new_message['quantity'] = $this->_message['quantity'];
                $new_message['loading_time'] = date('Y-m-d H:i:s',$this->_message['loading_time']);
                $new_message['deadline'] = date('Y-m-d H:i:s',$this->_message['deadline']);
                $new_message['publish_time'] = date('Y-m-d H:i:s',$this->_message['publish_time']);

            } elseif ($this->_message['category'] == 2) { //求购
                //用户信息
                $this->toUser($this->_message);
                $new_message['user_name'] = $this->_message['user']['user_name'];
                $new_message['headimg_url'] = $this->_message['user']['headimg_url'];
                $new_message['city'] = $this->_message['user']['city'];
                //煤炭信息
                $this->toProduct($this->_message);
                $new_message['coal_kind_name']= $this->_message['product']['kind'];
                $new_message['coal_trait_name']= $this->_message['product']['trait'];
                $new_message['coal_granularity_name']= $this->_message['product']['granularity'];
                //消息信息
                $this->toDistrictStart($this->_message);
                $new_message['area_start'] = $this->_message['district_start']['name'];
                $new_message['phone_number'] = $this->_message['phone_number'];
                $new_message['price'] = $this->_message['price'];
                $new_message['quantity'] = $this->_message['quantity'];
                $new_message['deadline'] = date('Y-m-d H:i:s',$this->_message['deadline']);
                $new_message['publish_time'] = date('Y-m-d H:i:s',$this->_message['publish_time']);

            } elseif ($this->_message['category'] == 3) { //货源找车
                //用户信息
                $this->toUser($this->_message);
                $new_message['user_name'] = $this->_message['user']['user_name'];
                $new_message['headimg_url'] = $this->_message['user']['headimg_url'];
                //煤炭信息
                $this->toProduct($this->_message);
                $new_message['coal_granularity_name']= $this->_message['product']['granularity'];
                //消息信息
                $new_message['phone_number'] = $this->_message['phone_number'];
                $this->toDistrictStart($this->_message);
                $new_message['area_start'] = $this->_message['district_start']['name'];
                $this->toDistrictEnd($this->_message);
                $new_message['area_end'] = $this->_message['district_end']['name'];
                $new_message['quantity'] = $this->_message['quantity'];
                $new_message['loading_time'] = date('Y-m-d H:i:s',$this->_message['loading_time']);
                $new_message['deadline'] = date('Y-m-d H:i:s',$this->_message['deadline']);
                $new_message['publish_time'] = date('Y-m-d H:i:s',$this->_message['publish_time']);
            }
        }
    }


}