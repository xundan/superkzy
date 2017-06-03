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

    protected $fields = array( //辅助模型识别字段，不会影响查询，会影响增改
        "id",
        "uid",
        "ip",
        "now",
        "page",
        "param",
        "title",
        "oper",
        "result",
        "duration",
        '_pk' => "id",
    );

    // 统计的方法

    public function all_by_date($s_date,$e_date){
        $res = $this->join('ck_user on ck_user.uid = ck_log.uid')->field('ck_log.uid,user_name,phone_number,heading_url,role_id,max(now) as a,count(*) as b')->where("now>'$s_date' and now<'$e_date'")->group('uid')->order("a")->select();
        return $res;
    }

    public function detail_by_uid($uid, $s_date,$e_date){
        if (!$uid) return false;
        $res = $this->field("uid,ip,title,page,param,oper,result,now,duration")->where("uid = $uid and now>'$s_date' and now<'$e_date'")->order("now")->select();
        $user = M('ck_user')->where(array('uid'=>$uid))->find();
        $res['user'] = $user;
        return $res;
    }

    /**
     * 按日期对页面时长和点击量进行统计
     * @param $s_date
     * @param $e_date
     * @return mixed
     */
    public function all_by_title($s_date, $e_date){
        //SELECT sum(duration) as d,count(*) as c,title from ck_log where oper='browse'and now like "2017-05-01%" group by title ORDER BY c DESC
        return $this->field("sum(duration) as d,count(*) as c,avg(duration) as a,title")->where("oper='browse'and now>'$s_date' and now<'$e_date'")->group('title')->order('c desc')->select();
    }

    public function detail_by_title($title, $s_date,$e_date){
        if (!$title) return false;
        $res = $this->field("uid,ip,title,page,param,oper,result,now,duration")->where("title = '$title' and now>'$s_date' and now<'$e_date'")->order("now")->select();
        return $res;
    }

    public function all_by_oper($s_date,$e_date){
        return $this->field("count(*) as c, param, oper, title")->where("oper!='browse'and now>'$s_date' and now<'$e_date'")->group('param, title')->order('c desc')->select();
    }

    public function detail_by_oper($title,$oper, $s_date,$e_date){
        if (!$title) return false;
        $res = $this->field("uid,ip,title,page,param,oper,result,now,duration")->where("title = '$title' and oper='$oper' and now>'$s_date' and now<'$e_date'")->order("now")->select();
        return $res;
    }

    public function all_by_dial($s_date,$e_date){
        return $this->field("count(*) as c, param, oper")->where("oper='dial'and now>'$s_date' and now<'$e_date'")->group('param')->order('c desc')->select();
    }

    public function detail_by_dial($phone, $s_date,$e_date){
        if (!$phone) return false;
        $res = $this->field("uid,ip,title,page,param,oper,result,now,duration")->where("oper='dial' and param='$phone' and now>'$s_date' and now<'$e_date'")->order("now")->select();
        return $res;
    }

    public function all_user_to_dial($s_date,$e_date){
        return $this->join('ck_user on ck_user.uid = ck_log.uid')->field("user_name, ck_log.uid as u,count(param) as c")->where("oper='dial'and now>'$s_date' and now<'$e_date'")->group('u')->order('c desc')->select();
    }

    public function detail_user_to_dial($uid, $s_date,$e_date){
        if (!$uid) return false;
        $res = $this->field("uid,ip,title,page,param,oper,result,now,duration")->where("uid = '$uid' and oper='dial' and now>'$s_date' and now<'$e_date'")->order("now")->select();
        return $res;
    }

    public function dial_web($s_date,$e_date){
        $res = $this->join('ck_user on ck_user.uid = ck_log.uid')->join('ck_messages on ck_messages.owner = ck_log.param')->field('user_name, ck_log.uid as u,param, ck_messages.sender as s,count(param) as c')->where("oper='dial'and now>'$s_date' and now<'$e_date'")->group('param,ck_log.uid')->order("c desc")->select();
        return $res;
    }

    //更新时长的方法

    /**
     * 按日期更新log表的duration字段
     * @param $day string 日期
     * @return bool|int true:更新成功，false:更新出错，0：没有要更新的数据
     */
    public function update_duration($day){
        $users = $this->fetch_unprepared_user($day);
        if ($users){
            $total_res = true;
            foreach($users as $user){
                $data = $this->fetch_unprepared_log($day,$user['uid']);
                $res = $this->update_single_user_duration($data);
                $total_res = $total_res && $res;
            }
            return $total_res;
        }else{
            return 0;
        }
    }

    /**
     * 返回当天操作的用户列表
     * @param $day string 日期
     * @return mixed 用户列表
     */
    public function fetch_unprepared_user($day){
        if (!$day) return false;
        $res = $this->distinct(true)->field("uid")->where("now like '$day%'")->order("now")->select();
        return $res;
    }

    /**
     * 返回指定用户、日期的log，包含三个字段，用于更新
     * @param $day string 日期
     * @param $uid int 用户id
     * @return mixed $data
     */
    public function fetch_unprepared_log($day, $uid){
        if (!$uid) return false;
        $res = $this->field("id,now,duration")->where("uid = $uid and duration = -2 and oper='browse' and now like '$day%'")->order("now")->select();
        return $res;
    }

    /**
     * @param $data mixed id,now,duration 三个字段
     * @return mixed 更新结果
     */
    public function update_single_user_duration($data){
        $post_value = false;
        for($i =count($data)-1; $i>=0; $i--){ // 从后向前算，最后的页面没有时长
            if (!$post_value){// 第一个数据或上个数据损坏
                if (!isset($data[$i]['duration'])) continue;
                $data[$i]['duration'] = -1; // -1:未知
            }else{
                // 上个数据$post_value存在
                $duration = strtotime($post_value['now'])-strtotime($data[$i]['now']);
                if ($duration>100) $duration = 0;
                $data[$i]['duration'] = $duration;
            }
            $post_value = $data[$i];
        }
        return $this->save_all($data);
    }

    private function save_all($data){
        $result = true;
        foreach ($data as $row){
            $res = $this->save($row);
            $result = $result && $res;
        }
        return $result;
    }

}