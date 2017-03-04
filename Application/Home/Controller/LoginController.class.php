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
use Think\Log;

header("Content-type:text/html; charset=utf-8");

class LoginController extends Controller
{


    public function register()
    {
//        $user_info = $_SESSION['user_info']['role_id'];
        $user_info = session('user_info');
        if (!isset($user_info)){
            cookie("current_url",__SELF__);
            header('Location: '.C('REDIRECT_URL_BASE'));
        }
        $this->assign('user_info', $user_info);
        $this->display();
    }

    /*
     * 注册-发送验证码
     */
    public function randomCodeReg()
    {
        $phone = I("post.phone_number");
        $randStr = str_shuffle('1234567890');
        $str = substr($randStr, 0, 4);
        session(md5($phone), $str);
        vendor("test");
        sendCode($phone, "'" . $str . "'");
    }

    //清空session
    public function clearSession()
    {
        session('randomCode', null);
        session('phone', null);
    }

    public function t5_upgrade_success()
    {
        if (cookie('last_url_for_auth')) {
            // 如果设置了跳回地址
//            $temp = cookie('last_url');
//            $b = U("Homepage/homepage");
//            $this->success('设置成功'.$temp.'###'.$b, U($temp),30);
            $this->success('设置成功', cookie('last_url_for_auth'));
        }else {
            // 否则跳回默认地址
            $this->success('设置成功', U("Homepage/homepage"));
        }
    }


    //注册表单提交处理页面
    public function register_do()
    {
        $phone_numbers = I('post.phone_number', '', 'strip_tags');
        $role_id = I('post.role_id', '', 'strip_tags');
        $clients_id = I('post.invite_code', '', 'strip_tags') ? I('post.invite_code', '', 'strip_tags') : "";//邀请码
        $code = session(md5($phone_numbers));
        $codes = I('post.code', '', 'strip_tags');
        $returnArr = array();
        if ($codes == $code) {
            $userModel = M("User");
            $r = $userModel->where(array("phone_number" => $phone_numbers))->find();
            if (!empty($r)) {//验证用户是否注册
                $returnArr['status'] = 0;
                $returnArr['msg'] = "该手机号已经注册过。";
                echo json_encode($returnArr);
                exit;
            }
            $data['phone_number'] = $phone_numbers;
            $data['role_id'] = $role_id;
            $data['invite_id'] = $clients_id;
            $data['group_id']=C('AUTH_USER');
            $now_user = session('user_info');
            if ($now_user) {
                $open_id = $now_user['open_id'];
                $res = $userModel->where(array("open_id" => $open_id))->save($data);
                if ($res || ($res === 0)) {
                    $returnArr['status'] = 1;
                    $returnArr['msg'] = "注册成功。";
                    $temp['phone_number'] = $phone_numbers;
                    $res = $userModel->where($temp)->find();
                    $_SESSION['user_info'] = $res;
                    $_SESSION['role_id'] = $res['role_id'];
                    echo jsonEcho($returnArr);
                    exit;
                } else {
                    Log::record("user saved error: ".$now_user["uid"], Log::ERR);
                    // TODO 日志
                    $returnArr['status'] = 500;
                    $returnArr['msg'] = "注册失败。";
                    echo jsonEcho($returnArr);
                    exit;
                }
            }else{
                Log::record("no user_info in session: ".$now_user["uid"], Log::ERR);
            }
        } else {
            //验证码填写错误
            $returnArr['status'] = 0;
            $returnArr['msg'] = "验证码错误。";
            echo jsonEcho($returnArr);
            exit;
        }
    }


}