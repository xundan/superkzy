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
//    public function _initialize(){
//        if(!isset($_SESSION[C('USER_AUTH_KEY')])){
//            //判断是否有uid
//            $this->redirect("Login/login");
//        }
//        $Auth = new \Think\Auth();
//        $module_name = CONTROLLER_NAME.'/'.ACTION_NAME;
//        if($_SESSION['uname']==C('ADMIN_AUTH_KEY')){
//            //以用户名来判断是否是超级管理员，绕过验证，不用用户组来判断的原因是用户组有时候是中文  ，而且常删除或更改。
//            return true;
//        }
//        if(!$Auth->check($module_name,$_SESSION[C('USER_AUTH_KEY')])){
//            $this->error('没有权限');
//        }
//    }
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