<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2017/6/29
 * Time: 16:44
 */

namespace Views\Model;


use Think\Model;

class QueryModel extends Model
{
    protected $trueTableName = "ck_query_record";


    protected $fields = array( //辅助模型识别字段，不会影响查询，会影响增改
        "id",
        "last_record",
        "last_query_code",
        "user_id",
        "self",
        "amount",
        "status",
        "send_time",
        "invalid_id",
        "remark",
        '_pk' => "id",
    );


    public function saveRecord($kw,$user,$self){
//        $map["self"]=$self;
//        $map["user_id"]=$user;
        $data["last_record"]=$kw;
        $data["user_id"]=$user;
        $data["self"]=$self;
        $data["status"]=0;
        $data["invalid_id"]=0;
        $data["remark"]="query_plain";
//        $res = $this->where($map)->find();
//        if ($res){
//            $data["id"]=$res["id"];
//            return $this->save($data);
//        }else{
//            return $this->add($data);
//        }
        return $this->add($data);
    }

    public function saveIntroRecord($kw,$user,$self){
        $data["last_record"]=$kw;
        $data["user_id"]=$user;
        $data["self"]=$self;
        $data["status"]=0;
        $data["invalid_id"]=0;
        $data["remark"]="intro";
        return $this->add($data);
    }

    public function findLast($user, $self){
        $map["self"]=$self;
        $map["user_id"]=$user;
        $res = $this->where($map)->order('send_time desc')->find();
        return $res;
    }
}