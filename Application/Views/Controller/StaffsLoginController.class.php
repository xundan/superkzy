<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/8/17
 * Time: 17:40
 */

namespace Views\Controller;

use Think\Controller;

class StaffsLoginController extends Controller
{
    const SUCCESS = 1;
    const FAIL = -1;

    public function Login()
    {
        $this->display();
    }

    public function test()
    {

    }

    public function doRegister()
    {
        if (IS_POST) {
            $data['user_name'] = I('post.user_name');
            $data['password'] = I('post.password', '', 'strip_tags');
            $data['invalid_id'] = 0;
            $result = M('ck_staffs')->where("user_name='%s'", $data['user_name'])->find();
            if ($result) {
                $this->error('用户名已被使用,返回注册', 'Register.html', 3);
            } else {
                M('ck_staffs')->add($data);
                //注册成功将信息存入session方便直接跳转
                $resultNew = M('ck_staffs')->where("user_name='%s'", $data['user_name'])->find();
                $_SESSION['cur_user'] = $resultNew;
                //设置成功后跳转页面的地址，默认的返回页面是$_SERVER['HTTP_REFERER']
                $this->success('注册成功，正在前往用户配置界面', 'after_success', 3);
            }
        }
    }

    public function doLogin()
    {
        //是否post方式传
        $name = $_POST["user_name"];
        $staff = M("ck_staffs")->where("user_name='$name'")->find();
        //数据库中没有记录
        if (!$staff) {
            $this->error("用户名错误！返回登录", U('StaffsLogin/Login'));
        } else if ($staff['password'] != $_POST['password'] && $staff['password'] != "") {   //有记录再检查密码
            $this->error("密码错误！返回登录");
            $this->redirect('StaffsLogin/Login');
        } else {      //登录记录存到session里，并跳转至审核页面
            $_SESSION['cur_user'] = $staff;
            header("Content-Type:text/html; charset=utf-8");    //中文非乱码
            $this->redirect('StaffsLogin/UserProfile', '', 2, "您好 " . $staff['user_name'] . "！ 请稍等，正在跳转。。。");
        }
    }

    public function UserProfile()
    {
        $staff = $_SESSION['cur_user'];
        $this->assign('cur_user', $staff);
        $this->display();
    }

    public function modify()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $where['user_name'] = $_SESSION['cur_user']['user_name'];
        $result = M('ck_staffs')->where($where)->save($subInfo);
        if ($result) {
            $new = M('ck_staffs')->where("user_name='%s'", $subInfo['user_name'])->find();
            $_SESSION['cur_user'] = $new;
        }
        echo 'success';
    }

    public function summary()
    {
        $staff = $_SESSION['cur_user'];
        $this->assign('cur_user', $staff);
        $this->display();
    }

    public function summary_submit()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $where = [];
        $where['user_name'] = $subInfo['name'];
        $result_name = M('ck_staffs')->where($where)->find();
        if ($result_name) {
            $data['uid'] = $result_name['id'];
            $data['name'] = $subInfo['name'];
            $data['process'] = $subInfo['process'];
            $data['project'] = $subInfo['project'];
            $data['problem'] = $subInfo['problem'];
            $data['propose'] = $subInfo['propose'];
            if (!$subInfo['record_time']) {
            } else {
                $data['record_time'] = $subInfo['record_time'];
            }
            $result = M('ck_summary')->add($data);
            if ($result) {
                echo 'success';
            } else {
                echo 'failure1';
            }
        } else {
            echo 'failure2';
        }
    }

    //登出
    public function logout()
    {
        unset($_SESSION['cur_user']);
        $this->redirect('StaffsLogin/Login');
    }

    public function after_success()
    {
        header("Location: UserProfile.html"); /* 跳转 */
        exit;/* 确保其他php代码不会执行. */
    }

    //查询信息
    public
    function summaryInp()
    {
        $subInfo = I('post.', '', 'trim');
        $where = [];
        if ($subInfo['date']!=NULL) {
            if ($subInfo['name'] != NULL && $subInfo['name'] != '全部') {
                $where['name'] = $subInfo['name'];
                $where['record_time'] = array('between', array($subInfo['time_start'],$subInfo['time_end']));
            } else {
                $where['record_time'] = array('between', array($subInfo['time_start'],$subInfo['time_end']));
            }
        }else{
            if ($subInfo['name'] != NULL && $subInfo['name'] != '全部'){
                $where['name'] = $subInfo['name'];
                $where['record_time'] = array('between', array($subInfo['time_start'],$subInfo['time_end']));
            }else{
                $where['record_time'] = array('between', array($subInfo['time_start'],$subInfo['time_end']));
            }
        }
        $where['invalid_id']=0;
        $result = M('ck_summary')->where($where)->select();
        $returnArr = array();
        if ($result) {
            $returnArr['msg'] = 'yes';
            $returnArr['data'] = $result;
        } else {
            $returnArr['msg'] = 'no';
        }
        echo json_encode($returnArr);
    }

    //信息展示
    public
    function summaryShow()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $result = M('ck_summary')->select();
        $returnArr = array();
        if ($result) {
            $returnArr['msg'] = 'yes';
            $returnArr['data'] = $result;
        } else {
            $returnArr['msg'] = 'no';
        }
        echo json_encode($returnArr);

    }

    //信息删除
    public function summaryDel(){
        $subInfo = I('post.', '', 'trim,strip_tags');
        $where['id']=$subInfo['id'];
        $data['invalid_id']=2;
        $result=M('ck_summary')->where($where)->save($data);
        if($result=== false){
            echo self::FAIL;
        }else{
            echo self::SUCCESS;
        }
    }
}