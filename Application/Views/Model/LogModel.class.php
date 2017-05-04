<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2017/4/11
 * Time: 16:55
 */

namespace Views\Model;


use Think\Model;

class LogModel extends Model
{
    protected $tableName = "ck_log";

    public function all_by_date($date){
        $res = $this->join('ck_user on ck_user.uid = ck_log.uid')->field('ck_log.uid,user_name,role_id,max(now) as a,count(*) as b')->where("now like '$date%'")->group('uid')->order("a")->select();
        return $res;
    }

    public function detail_by_uid($uid, $date){
        if (!$uid) return false;
        $res = $this->field("uid,ip,title,page,param,oper,result,now")->where("uid = $uid and now like '$date%'")->order("now")->select();
        $user = M('ck_user')->where(array('uid'=>$uid))->find();
        $res['user'] = $user;
        return $res;
    }
}