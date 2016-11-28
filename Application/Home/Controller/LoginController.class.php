<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/10/10
 * Time: 16:19
 */

namespace Home\Controller;
//use Think\Auth;
use Think\Controller;
header("Content-type:text/html; charset=utf-8");
class LoginController extends Controller
{
    public function login(){
        $this->display();
    }


    public function register(){
//        $auth = new Auth();
//        $res = $auth->check(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME,1);
//        $res = $auth->check('Article/order_index,Article/sign_del,Article/message_del',4,1,'','and');
//        dump($res);exit;
        $user_info = $_SESSION['user_info']['role_id'];
        $this->assign('user_info',$user_info);
        $this->display();
    }

    public function register_no_invite(){
        $user_info = $_SESSION['user_info']['role_id'];
        $this->assign('user_info',$user_info);
        $this->display();
    }

    /*
     * 注册-发送验证码
     */
    public function randomCodeReg(){
        $phone=I("post.phone_number");
        $randStr = str_shuffle('1234567890');
        $str = substr($randStr,0,6);
        session(md5($phone),$str);
        vendor("test");
        sendCode($phone,"'".$str."'");
        //echo $str." ".$phone;
    }

    public function echoStr(){
        vendor("callback.index");
    }

    public function getInfo(){
        vendor("callback.getInfo");
        $str = getInfo("kdt.items.onsale.get");
    }

    //清空session
    public function clearSession(){
        session('randomCode',null);
        session('phone',null);
    }

    //注册表单提交处理页面
    public function register_do(){
        $phone_numbers = I('post.phone_number','','strip_tags');
        $clients_id = I('post.invite_code','','strip_tags')?I('post.invite_code','','strip_tags'):"";//邀请码
        $code = session(md5($phone_numbers));
        $codes = I('post.code','','strip_tags');
        $returnArr=array();
        if($codes==$code){
            $userModel=M("User");
            $r=$userModel->where(array("phone_number"=>$phone_numbers))->find();
            if(!empty($r)){//验证用户是否注册
                $returnArr['status']=0;
                $returnArr['msg']="该手机号已经注册过。";
                echo json_encode($returnArr);exit;
            }
            $data['phone_number'] = $phone_numbers;
            $data['role_id'] = I('post.type','','strip_tags');
            $data['clients_id'] = $clients_id;
            $res = $userModel->add($data);
            if($res){
                $returnArr['status']=1;
                $returnArr['msg']="注册成功。";
                $tesp['phone_number'] = $phone_numbers;
                $res = $userModel->where($tesp)->find();
                $_SESSION['user_info'] = $res;
                $_SESSION['role_id'] = $res['role_id'];
                echo jsonEcho($returnArr);exit;
            }
            else{
                $returnArr['status']=0;
                $returnArr['msg']="注册失败。";
                echo jsonEcho($returnArr);exit;
            }
        }
        else {
            //验证码填写错误
            $returnArr['status']=0;
            $returnArr['msg']="验证码错误。";
            echo jsonEcho($returnArr);exit;
        }
    }

}