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


    public function upgrade_t2_to_t3_neither($phone = 0)
    {
        $this->assign('phone', $phone);
        $this->display();
    }


    /**
     * 没有随便逛逛选项
     * @param int $phone 可以不写，如果该值为1，升到t3后会直接在升t5
     */
    public function upgrade_t2_to_t3($phone = 0)
    {
        $this->assign('phone', $phone);
        $this->display();
    }

    public function upgrade_t2_to_t3_do($phone = 0)
    {
        $where['uid'] = $_SESSION['user_info']['uid'];
        $self = M('user')->where($where)->find();
        $option_number = I('post.options', '', 'trim,strip_tags');
        $data['role_id'] = $option_number;
        if ($self['group_id'] < 3) { // 以防万一，已升级用户可能进入本页面
            $data['group_id'] = 3;
        }
        $res = M('user')->where($where)->save($data);
        if ($res | $res === 0) {
            // 如果有phone参数，直接跳转到升级t5界面
            $this->updateSessionForUser();
            if ($phone) {
                $this->redirect("Login/upgrade_t3_to_t5");
            } else {
                $this->success('设置成功', U("Homepage/homepage_client"));
            }
        } else {
            // TODO 数据库错误
            $this->error('设置失败', U("Homepage/homepage_client"), 3);
        }
    }

    /**
     * 升级前请填写好last_url
     */
    public function upgrade_t3_to_t5()
    {
        $this->display();
    }

    public function upgrade_t3_to_t5_do()
    {
        $where['uid'] = $_SESSION['user_info']['uid'];
        $phone_numbers = I('post.phone_number', '', 'strip_tags');
        $clients_id = I('post.invite_code', '', 'strip_tags') ? I('post.invite_code', '', 'strip_tags') : "";//邀请码
        $code = session(md5($phone_numbers));
        $codes = I('post.code', '', 'strip_tags');
        $returnArr = array();
        if ($codes == $code) {
            // 验证码正确
            $userModel = M("User");
            $r = $userModel->where(array("phone_number" => $phone_numbers))->find();
            if (!empty($r)) {//验证用户是否注册
                $returnArr['status'] = 0;
                $returnArr['msg'] = "该手机号已经注册过。";
                echo json_encode($returnArr);
                return;
            }
            $data['phone_number'] = $phone_numbers;
//            $data['role_id'] = I('post.type', '', 'strip_tags');
            $data['invite_id'] = $clients_id;
            $res = $userModel->where($where)->save($data);
            if ($res | $res === 0) {
                $returnArr['status'] = 1;
                $returnArr['msg'] = "注册成功。";
                echo jsonEcho($returnArr);
                return;
            } else {
                //TODO 内部错误
                $returnArr['status'] = 500;
                $returnArr['msg'] = "注册失败。";
                echo jsonEcho($returnArr);
                return;
            }
        } else {
            //验证码填写错误
            $returnArr['status'] = 0;
            $returnArr['msg'] = "验证码错误。";
            echo jsonEcho($returnArr);
            return;
        }
    }


    public function t5_upgrade_success(){
        $this->updateSessionForUser();
        if (cookie('last_url')){
            // 如果设置了跳回地址
            $this->success('设置成功', cookie('last_url'));
        }else{
            // 否则跳回默认地址
            $this->success('设置成功', U("Homepage/homepage_client"));
        }
    }

    private function updateSessionForUser()
    {
        $where['uid'] = $_SESSION['user_info']['uid'];
        $res = M("User")->where($where)->find();
        $_SESSION['user_info'] = $res;
        $_SESSION['role_id'] = $res['role_id'];
    }

    public function register()
    {
//        $auth = new Auth();
//        $res = $auth->check(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME,1);
//        $res = $auth->check('Article/order_index,Article/sign_del,Article/message_del',4,1,'','and');
//        dump($res);exit;
        $user_info = $_SESSION['user_info']['role_id'];
        $this->assign('user_info', $user_info);
        $this->display();
    }

    public function register_no_invite()
    {
        $user_info = $_SESSION['user_info']['role_id'];
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
        //echo $str." ".$phone;
    }

    public function echoStr()
    {
        vendor("callback.index");
    }

    public function getInfo()
    {
        vendor("callback.getInfo");
        $str = getInfo("kdt.items.onsale.get");
    }

    //清空session
    public function clearSession()
    {
        session('randomCode', null);
        session('phone', null);
    }

    //注册表单提交处理页面
    public function register_do()
    {
        $phone_numbers = I('post.phone_number', '', 'strip_tags');
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
//            $data['role_id'] = I('post.type', '', 'strip_tags');
            $data['invite_id'] = $clients_id;
            $res = $userModel->add($data);
            if ($res) {
                $returnArr['status'] = 1;
                $returnArr['msg'] = "注册成功。";
                $tesp['phone_number'] = $phone_numbers;
                $res = $userModel->where($tesp)->find();
                $_SESSION['user_info'] = $res;
                $_SESSION['role_id'] = $res['role_id'];
                echo jsonEcho($returnArr);
                exit;
            } else {
                // TODO 日志
                $returnArr['status'] = 500;
                $returnArr['msg'] = "注册失败。";
                echo jsonEcho($returnArr);
                exit;
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