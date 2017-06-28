<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2017/4/13
 * Time: 18:51
 */

namespace Views\Controller;


use Think\Controller;
use Views\Model\LogModel;
use Views\Model\MessageModel;

class LogStatisticsController extends Controller
{
    public function index(){
        $s_date = date("Y-m-d",strtotime("-7 day"));
        $e_date = date("Y-m-d");
        $this->redirect('LogStatistics/show_all', array('s_date' => $s_date,'e_date' => $e_date));


    }

    public function show_all($s_date, $e_date){
        // 计算前后一天
        $this_s_time = strtotime($s_date);
        $prev_s_date = date("Y-m-d",strtotime("-7 day", $this_s_time));
        $next_s_date = date("Y-m-d",strtotime("+7 day", $this_s_time));
        $this_e_time = strtotime($e_date);
        $prev_e_date = date("Y-m-d",strtotime("-7 day", $this_e_time));
        $next_e_date = date("Y-m-d",strtotime("+7 day", $this_e_time));

        $Logs = new LogModel();
        $res = $Logs->all_by_date($s_date, $e_date);
        $this->assign("res",$res);
        $this->assign("s_date",$s_date);
        $this->assign("e_date",$e_date);
        $this->assign("prev_s_date",$prev_s_date);
        $this->assign("next_s_date",$next_s_date);
        $this->assign("prev_e_date",$prev_e_date);
        $this->assign("next_e_date",$next_e_date);
        $this->display();
    }

    public function show_ajax($s_date, $e_date){
        $Logs = new LogModel();
        $res = $Logs->all_by_date($s_date, $e_date);
        echo json_encode($res);
    }

    public function user_detail($uid,$s_date, $e_date)
    {
        $Logs =  new LogModel();
        $details = $Logs->detail_by_uid($uid,$s_date, $e_date);
        $user = $details['user'];
        unset($details['user']);
        $this->assign("details",$details);
        $this->assign("s_date",$s_date);
        $this->assign("e_date",$e_date);
        $this->assign('user_info',$user);
        $this->display();
    }

    public function user_detail_ajax($uid,$s_date, $e_date)
    {
        $Logs =  new LogModel();
        $details = $Logs->detail_by_uid($uid,$s_date, $e_date);
        unset($details['user']);
        echo json_encode($details);
    }

    public function page($s_date, $e_date){
        $this_s_time = strtotime($s_date);
        $prev_s_date = date("Y-m-d",strtotime("-7 day", $this_s_time));
        $next_s_date = date("Y-m-d",strtotime("+7 day", $this_s_time));
        $this_e_time = strtotime($e_date);
        $prev_e_date = date("Y-m-d",strtotime("-7 day", $this_e_time));
        $next_e_date = date("Y-m-d",strtotime("+7 day", $this_e_time));

        $Logs = new LogModel();
        $res = $Logs->all_by_title($s_date, $e_date);
        $this->assign("res",$res);
        $this->assign("s_date",$s_date);
        $this->assign("e_date",$e_date);
        $this->assign("prev_s_date",$prev_s_date);
        $this->assign("next_s_date",$next_s_date);
        $this->assign("prev_e_date",$prev_e_date);
        $this->assign("next_e_date",$next_e_date);
        $this->display();
    }

    public function page_ajax($s_date, $e_date){
        $Logs = new LogModel();
        $res = $Logs->all_by_title($s_date, $e_date);
        echo json_encode($res);
    }

    public function page_detail($title,$s_date, $e_date)
    {
        $Logs =  new LogModel();
        $details = $Logs->detail_by_title($title,$s_date, $e_date);
        $this->assign("details",$details);
        $this->assign("s_date",$s_date);
        $this->assign("e_date",$e_date);
        $this->display();
    }


    public function oper($s_date, $e_date){
        $this_s_time = strtotime($s_date);
        $prev_s_date = date("Y-m-d",strtotime("-7 day", $this_s_time));
        $next_s_date = date("Y-m-d",strtotime("+7 day", $this_s_time));
        $this_e_time = strtotime($e_date);
        $prev_e_date = date("Y-m-d",strtotime("-7 day", $this_e_time));
        $next_e_date = date("Y-m-d",strtotime("+7 day", $this_e_time));

        $Logs = new LogModel();
        $res = $Logs->all_by_oper($s_date, $e_date);
        $this->assign("res",$res);
        $this->assign("s_date",$s_date);
        $this->assign("e_date",$e_date);
        $this->assign("prev_s_date",$prev_s_date);
        $this->assign("next_s_date",$next_s_date);
        $this->assign("prev_e_date",$prev_e_date);
        $this->assign("next_e_date",$next_e_date);
        $this->display();
    }


    public function oper_detail($title,$oper,$s_date, $e_date)
    {
        $Logs =  new LogModel();
        $details = $Logs->detail_by_oper($title,$oper,$s_date, $e_date);
        $this->assign("details",$details);
        $this->assign("s_date",$s_date);
        $this->assign("e_date",$e_date);
        $this->display();
    }


    public function dial($s_date, $e_date){
        $this_s_time = strtotime($s_date);
        $prev_s_date = date("Y-m-d",strtotime("-7 day", $this_s_time));
        $next_s_date = date("Y-m-d",strtotime("+7 day", $this_s_time));
        $this_e_time = strtotime($e_date);
        $prev_e_date = date("Y-m-d",strtotime("-7 day", $this_e_time));
        $next_e_date = date("Y-m-d",strtotime("+7 day", $this_e_time));

        $Logs = new LogModel();
        $res = $Logs->all_by_dial($s_date, $e_date);
        $this->assign("res",$res);
        $this->assign("s_date",$s_date);
        $this->assign("e_date",$e_date);
        $this->assign("prev_s_date",$prev_s_date);
        $this->assign("next_s_date",$next_s_date);
        $this->assign("prev_e_date",$prev_e_date);
        $this->assign("next_e_date",$next_e_date);
        $this->display();
    }


    public function dial_detail($phone,$s_date, $e_date)
    {
        $Logs =  new LogModel();
        $details = $Logs->detail_by_dial($phone,$s_date, $e_date);
        $this->assign("details",$details);
        $this->assign("s_date",$s_date);
        $this->assign("e_date",$e_date);
        $this->display();
    }

    /**
     * 按呼出电话统计用户
     * @param $s_date
     * @param $e_date
     */
    public function user_to_dial($s_date, $e_date){
        $this_s_time = strtotime($s_date);
        $prev_s_date = date("Y-m-d",strtotime("-7 day", $this_s_time));
        $next_s_date = date("Y-m-d",strtotime("+7 day", $this_s_time));
        $this_e_time = strtotime($e_date);
        $prev_e_date = date("Y-m-d",strtotime("-7 day", $this_e_time));
        $next_e_date = date("Y-m-d",strtotime("+7 day", $this_e_time));

        $Logs = new LogModel();
        $res = $Logs->all_user_to_dial($s_date, $e_date);
        $this->assign("res",$res);
        $this->assign("s_date",$s_date);
        $this->assign("e_date",$e_date);
        $this->assign("prev_s_date",$prev_s_date);
        $this->assign("next_s_date",$next_s_date);
        $this->assign("prev_e_date",$prev_e_date);
        $this->assign("next_e_date",$next_e_date);
        $this->display();
    }

    public function user_to_dial_detail($uid,$s_date, $e_date)
    {
        $Logs =  new LogModel();
        $details = $Logs->detail_user_to_dial($uid,$s_date, $e_date);
        $this->assign("details",$details);
        $this->assign("s_date",$s_date);
        $this->assign("e_date",$e_date);
        $this->display();
    }


    /**
     * 按电话统计小消息
     * @param $s_date
     * @param $e_date
     */
    public function msg_to_dial($s_date, $e_date){
        $this_s_time = strtotime($s_date);
        $prev_s_date = date("Y-m-d",strtotime("-7 day", $this_s_time));
        $next_s_date = date("Y-m-d",strtotime("+7 day", $this_s_time));
        $this_e_time = strtotime($e_date);
        $prev_e_date = date("Y-m-d",strtotime("-7 day", $this_e_time));
        $next_e_date = date("Y-m-d",strtotime("+7 day", $this_e_time));

        $Logs = new LogModel();
        $res = $Logs->all_msg_to_dial($s_date, $e_date);
        $this->assign("res",$res);
        $this->assign("s_date",$s_date);
        $this->assign("e_date",$e_date);
        $this->assign("prev_s_date",$prev_s_date);
        $this->assign("next_s_date",$next_s_date);
        $this->assign("prev_e_date",$prev_e_date);
        $this->assign("next_e_date",$next_e_date);
        $this->display();
    }

    public function msg_to_dial_detail($id,$s_date, $e_date)
    {
        if($id==1){
            echo '旧数据，未记录消息ID';
        }else{
            $Logs =  new LogModel();
            $details = $Logs->detail_msg_to_dial($id,$s_date, $e_date);
            $Msg = new MessageModel();
            $message = $Msg->getById($id);
            $this->assign("details",$details);
            $this->assign("message",$message);
            $this->assign("s_date",$s_date);
            $this->assign("e_date",$e_date);
            $this->display();
        }
    }


    public function dial_web($s_date, $e_date){
        $this_s_time = strtotime($s_date);
        $prev_s_date = date("Y-m-d",strtotime("-7 day", $this_s_time));
        $next_s_date = date("Y-m-d",strtotime("+7 day", $this_s_time));
        $this_e_time = strtotime($e_date);
        $prev_e_date = date("Y-m-d",strtotime("-7 day", $this_e_time));
        $next_e_date = date("Y-m-d",strtotime("+7 day", $this_e_time));

        $Logs = new LogModel();
        $res = $Logs->dial_web($s_date, $e_date);
        $this->assign("res",$res);
        $this->assign("s_date",$s_date);
        $this->assign("e_date",$e_date);
        $this->assign("prev_s_date",$prev_s_date);
        $this->assign("next_s_date",$next_s_date);
        $this->assign("prev_e_date",$prev_e_date);
        $this->assign("next_e_date",$next_e_date);
        $this->display();
    }

    // todo 根据电话找到信息，然后找到所有打电话的人
}