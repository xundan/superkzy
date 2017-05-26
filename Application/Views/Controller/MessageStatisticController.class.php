<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2017/3/28
 * Time: 9:51
 */

namespace Views\Controller;


use Think\Controller;
use Views\Model\MessageModel;

class MessageStatisticController extends Controller
{
    public function msg_inquiry(){
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
        $result = M('ck_feedback')->join('LEFT JOIN ck_user ON ck_user.uid=ck_feedback.uid')->where($where)->select();
        echo json_encode($result);
        return;
    }

    public function message_add_action(){
        $post = I('post.', '', 'trim,strip_tags');
        $Msg = new MessageModel();
        $start_time = $post['start_time'];
        $end_time = $post['end_time'];
        $start = $this->TimestampToTime($start_time);
        $end = $this->TimestampToTime($end_time);
        $where['record_time'] = array("BETWEEN", array($start, $end));
        $result['wx'] = count($Msg->where($where)->where("type = '%s'",'plain')->select());
        $result['group'] = count($Msg->where($where)->where("type = '%s'",'group')->select());
        $result['wx_mp'] = count($Msg->where($where)->where("type = '%s'",'wx_mp')->select());
        $result['web'] = count($Msg->where($where)->where("type = '%s'",'web')->select());
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

    public function lottery_action(){
        $result = M('ck_activity_2017_05')->join('LEFT JOIN ck_user ON ck_user.uid=ck_activity_2017_05.uid')->select();
        echo json_encode($result);
        return;
    }

    public function TimestampToTime($date){
        date_default_timezone_set("Asia/Shanghai");
        $result = date("Y-m-d H:i:s", $date);
        return $result;
    }

    public function statistic_all(){
        $Msg = new MessageModel();
        $this->assign("res",$Msg->statistics_by_day());
        $this->display();
    }

    public function all_chart(){
        $this->display();
    }
    public function statistics(){
        $Msg = new MessageModel();
        $data = array();
        array_push($data, $Msg->all_statistics_by_day());
        array_push($data, $Msg->plain_statistics_by_day());
        array_push($data, $Msg->group_statistics_by_day());
        array_push($data, $Msg->wx_mp_statistics_by_day());
        array_push($data, $Msg->web_statistics_by_day());
        echo json_encode($data);
    }

    public function history_search($kw){
        $Msg = new MessageModel();
        $res = $Msg->get_all_history($kw,"供应");
        $this->assign("res",$res);



        foreach($res as $message){
            echo "<br>";
            var_dump($message);
            echo "<br>";
        }
    }
}