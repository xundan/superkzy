<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2017/5/10
 * Time: 11:52
 */

namespace Views\Controller;


use Think\Controller\RestController;
use Think\Log;
use Views\Model\MessageModel;
use Views\Model\SMSModel;

class SMSController extends RestController
{
    protected $allowMethod = array('get', 'post', 'put'); // REST允许的请求类型列表
    protected $allowType = array('json'); // REST允许请求的资源类型列表

    public function index()
    {
        $a = 1;
        echo $a."SMS api works";
        var_dump($this->send('15850732459','公众号',12,'求车'));
//        var_dump($this->send('18809185991','公众号',12,'求车'));
    }

    public function prepareSMSList(){
        $date = date("Y-m-d",strtotime("-1 day"));
        $Msg = new MessageModel();
        $SMS = new SMSModel();
        $expiring_list = $Msg->get_expiring_msg($date);//id,publisher_rid,phone_number,category
        if ($expiring_list){
            $send_list = array();
            // 同一人发布的
            foreach($expiring_list as $row){
                $exist = false;
                for ($i=0;$i<count($send_list);$i++){
                    if ($send_list[$i]['user_id']==$row['publisher_rid']){
                        $send_list[$i]['amount']++;
                        if($send_list[$i]['remark']!=$row['category']){
                            $send_list[$i]['remark'].='/'.$row['category'];
                        }
                        $exist = true;
                        break;
                    }
                }
                if (!$exist){
                    $send['phone']=$row['phone_number'];
                    $send['msg_id']=$row['id'];
                    $send['user_id']=$row['publisher_rid'];
                    $send['amount']=1;
                    $send['status']=1;
                    $send['remark']=$row['category'];
                    array_push($send_list,$send);
                }
            }

            foreach($send_list as $s){
                $res=$this->send($s['phone'],"公众号",$s['amount'],$s['category']);
                if(!$res){
                    $s['status']=-1;
//                    $s['remark'].='发送失败';
                }
                $result = $SMS->add($s);
                if (!$result) Log::record("SMSController record save error.",Log::ERR);
            }
            $this->send('18809185991',"公众号",count($send_list),'提示');
        }
    }

    public function send($phone,$date,$count,$category){
        $d['date']=$date;
        $d['count']=''.$count;
        $d['category']=$category;
        vendor("test");
        $res=setExpiringSMS($phone,$d);
        if (isset($res->code)){
            Log::record("SMSController send failed.",Log::ERR);
            return false;
        }
        return true;
    }
}