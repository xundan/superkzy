<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2017/3/28
 * Time: 9:51
 */

namespace Views\Controller;


use Think\Controller;

class MessageStatisticController extends Controller
{
    public function show(){
        $this->display();
    }

    public function user_add_action(){
        $post = I('post.', '', 'trim,strip_tags');
        $start_time = $post['start_time'];
        $end_time = $post['end_time'];
        $start = $this->TimestampToTime($start_time);
        $end = $this->TimestampToTime($end_time);
        $where['create_time'] = array("BETWEEN", array($start, $end));
        $result['user_count'] = count(M('ck_user')->where($where)->select());
        $where['phone_number'] = array('exp','is not null');
        $result['user_reg_count'] = count(M('ck_user')->where($where)->select());
        $result['user_unreg_count'] = $result['user_count']-$result['user_reg_count'];
        echo json_encode($result);
        return;
    }

    public function feedback_action(){
        $post = I('post.', '', 'trim,strip_tags');
        $start_time = $post['start_time'];
        $end_time = $post['end_time'];
        $start = $this->TimestampToTime($start_time);
        $end = $this->TimestampToTime($end_time);
        $where['record_time'] = array("BETWEEN", array($start, $end));
        $result = M('ck_feedback')->join('ck_user ON ck_user.uid=ck_feedback.uid')->where($where)->select();
        echo json_encode($result);
        return;
    }

    public function message_add_action(){
        $post = I('post.', '', 'trim,strip_tags');
        $start_time = $post['start_time'];
        $end_time = $post['end_time'];
        $start = $this->TimestampToTime($start_time);
        $end = $this->TimestampToTime($end_time);
        $where['record_time'] = array("BETWEEN", array($start, $end));
        $origin['wx'] = 'plain';
        $origin['wx_mp'] = 'wx_mp';
        $origin['web'] = 'web';
        $result['wx'] = count(M('ck_messages')->where($where)->where("type = '%s'",$origin['wx'])->select());
        $result['wx_mp'] = count(M('ck_messages')->where($where)->where("type = '%s'",$origin['wx_mp'])->select());
        $result['web'] = count(M('ck_messages')->where($where)->where("type = '%s'",$origin['web'])->select());
        echo json_encode($result);
        return;
    }

    public function financial_client_action(){
        $post = I('post.', '', 'trim,strip_tags');
        $start_time = $post['start_time'];
        $end_time = $post['end_time'];
        $start = $this->TimestampToTime($start_time);
        $end = $this->TimestampToTime($end_time);
        $where['record_time'] = array("BETWEEN", array($start, $end));
        $result = M('ck_financial_client')->where($where)->select();
        echo json_encode($result);
        return;
    }

    public function TimestampToTime($date){
        date_default_timezone_set("Asia/Shanghai");
        $result = date("Y-m-d H:i:s", $date);
        return $result;
    }
}