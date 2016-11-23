<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/10/18
 * Time: 11:54
 */

namespace Home\Controller;
use Think\Controller;
class ComController extends Controller
{
    public function _initialize(){
        $user = $_SESSION['user_info'];

        //把当前路径放入cookie中
        $module_name = CONTROLLER_NAME.'/'.ACTION_NAME;
        cookie("last_url",$module_name);
        if(!isset($user['uid'])){
            //判断是否有uid，如果没有分两种情况
            // 如果是本机测试，从数据库取uid=1的用户登录
            // 否则请求微信授权登录
            if(C('IS_LOCALHOST')==1){
                $temp['uid'] = 1;
                $user_r = M('User')->where($temp)->find();
                session('user_info',$user_r);
                session('role_id',$user_r['role_id']);
            }else{
                $this->redirect("WxAccess/index",'');
            }
        }
        $Auth = new \Think\Auth();

//        if(!$Auth->check($module_name,$user['uid'])){
//            $this->error('没有权限访问本页面',$url);
//        }
    }

    /**
     * 查询输入框输入内容整理
     * @param $str              输入字符串
     * @return mixed|string     拆分数组
     */
    public function arrange_input($str){
        $tempStr = trim($str);
        $tempStr = preg_replace("/\\s{1,}/"," ",$tempStr);
        return $tempStr;
    }

    /**
     * 设置有效期限
     * @return int
     */
    public function set_deadline(){
        $userRoleId = $_SESSION['user_info']['role_id'];
        $level = $_SESSION['user_info']['level'];
        if($userRoleId==null){
            return strtotime('+3 day');
        }else if($userRoleId==0){
            return strtotime('+3 day');
        }else if($userRoleId==1){
            return strtotime('+7 day');
        }
    }

    /**
     * 获取煤炭属性名
     * @param $id       煤炭id
     * @return $mix     煤炭属性名
     */
    public function get_coal_kind_title($id){
        $res = M('coal_kind')->find($id);
        return $res->title;
    }

    public function get_coal_trait_title($id){
        $res = M('coal_trait')->find($id);
        return $res->title;
    }

    public function get_coal_granularity_title($id){
        $res = M('coal_granularity')->find($id);
        return $res->title;
    }




}