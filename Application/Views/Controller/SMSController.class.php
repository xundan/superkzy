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
use Views\Model\UserModel;

class SMSController extends RestController
{
    protected $allowMethod = array('get', 'post', 'put'); // REST允许的请求类型列表
    protected $allowType = array('json'); // REST允许请求的资源类型列表

    public function index()
    {
        $a = 1;
        echo $a."SMS api works";
//        var_dump($this->send('15850732459','公众号',12,'求车'));
//        var_dump($this->send('18809185991','公众号',12,'求车'));
    }

    public function prepareSMSList(){

        $date = date("Y-m-d",strtotime("-1 day"));
        $Msg = new MessageModel();
        $SMS = new SMSModel();
        $User = new UserModel();
        $expiring_list = $Msg->get_expiring_msg($date);//id,publisher_rid,phone_number,category
        if ($expiring_list){
            $send_list = array();
            // 同一人发布的
            foreach($expiring_list as $row){
                $exist = false;
                for ($i=0;$i<count($send_list);$i++){
                    if ($send_list[$i]['user_id']==$row['publisher_rid']){
                        $send_list[$i]['amount']++;
                        // strpos 找得到返回位置（包括0），找不到返回false
                        if((strpos($send_list[$i]['remark'], $row['category']))===false){
                            $send_list[$i]['remark'].='/'.$row['category'];
                        }
                        $exist = true;
                        break;
                    }
                }
                if (!$exist){
                    $user = $User->getUser($row['publisher_rid']);
                    $send['phone']=$row['phone_number'];
                    $send['msg_id']=$row['id'];
                    $send['user_id']=$row['publisher_rid'];
                    $send['amount']=1;
                    $send['status']=1;
                    $send['remark']=$row['category'];
                    $send['title']=mb_substr($row['content'],0,120);
                    $send['open_id']=$user['open_id'];
                    $send['overdue']=$date;
                    array_push($send_list,$send);
                }
            }

            $one_msg = null;
            foreach($send_list as $s){
                $res=$this->send($s['phone'],"公众号",$s['amount'],$s['remark']);
                if(!$res){
                    $s['status']=-1;
//                    $s['remark'].='发送失败';
                }
                $result = $SMS->add($s);
                if (!$result) Log::record("SMSController record save error.",Log::ERR);
                if($s){
                    $this->send_wx($s);
                }
                $one_msg=$s;
            }
            // 测试
            $this->send('18809185991',"公众号",count($send_list),'提示');
            if($one_msg){
                $one_msg['open_id']="o5DarwPJb6a2i9Iex1I2skl3ewtM";
                $this->send_wx($one_msg);
            }
        }
    }

    public function send($phone,$date,$count,$category){

        $d['date']=$date;
        $d['count']=''.$count;
        $d['category']=$category;

        vendor("DialPhone");
        $res=setExpiringSMS($phone,$d);
        var_dump("$res");
        if (isset($res->code)){
            Log::record("SMSController send failed.",Log::ERR);
            return false;
        }
        return true;
    }


//    public function test($uid, $msg_id, $category, $content){
    public function send_wx($msg){
        $options = array(
            'token' => C('WX_TOKEN'),
            'encodingaeskey' => C('WX_ENCODINGAESKEY'),
            'appid' => C('WX_APPID'),
            'appsecret' => C('WX_APPSECRET')
        );
        $weObj = new \Org\Util\Wechat($options);
        $fisrt_data=array(
            'value'=>"平台网站信息发布过期提醒。",
            'color'=>'#173177'
        );

        $keyword1 = array(
            'value'=>$msg['remark'],
            'color'=>'#173177'
        );
        $keyword2 = array(
            'value'=>$msg['title'],
            'color'=>'#173177'
        );
        $keyword3 = array(
            'value'=>"过期自动下架（有效期7天）",
            'color'=>'#173177'
        );
        $keyword4 = array(
            'value'=>$msg['overdue'],
            'color'=>'#173177'
        );
        $remark = array(
            'value'=>"点击此消息一键重新发布",
            'color'=>'#173177'
        );
        $data = array(
            'first'=>$fisrt_data,
            'keyword1'=>$keyword1,
            'keyword2'=>$keyword2,
            'keyword3'=>$keyword3,
            'keyword4'=>$keyword4,
            'remark'=>$remark
        );
        $send=array(
//            "touser"=>"o5DarwPJb6a2i9Iex1I2skl3ewtM",
            "touser"=>$msg['open_id'],
            "template_id"=>"8Tiln-riqpC_s42AiDGbH3PMGxom2B4wKN2z_sm0ZsI",
            "url"=>"http://www.xuncl.com/index.php/Home/OwnerPublishHistory/owner_publish_history",
            "data"=>$data
        );
//        echo "123<br>";
        $sendResult = $weObj->sendTemplateMessage($send);
        if (!isset($sendResult)||!$sendResult||$sendResult['errcode']){
            Log::record("SMSController Send Result:".json_encode($sendResult),Log::ERR);
        }
//        $revResult = $weObj->getRevResult(); // 总是false，为什么
//        Log::record("SMSController Rev Result:".json_encode($revResult),Log::DEBUG);

//        为什么会发两遍？
//        echo "456<br>";
    }
}