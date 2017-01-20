<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/10/18
 * Time: 11:54
 */

namespace Home\Controller;
use Think\Controller;
use Think\Exception;

class ComController extends Controller
{
    public function _initialize(){
        $user = session('user_info');

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
                // 跳转到WxAccess/base 从base取出openid在数据库中找user，没有再跳转到WxAccess/oauth
                header('Location: '.C('REDIRECT_URL_BASE'));
            }
        }
        $Auth = new \Think\Auth();

        if(!$Auth->check($module_name,$user['uid'])){
            $this->error('没有权限访问本页面 '.$module_name." session->".json_encode($user),U('Login/register'));
        }
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
     * @param $id int  煤炭id
     */
    public function get_coal_kind_title($id){
        $res = M('coal_kind')->field('title')->find($id);
        return $res['title'];
    }

    public function get_coal_trait_title($id){
        $res = M('coal_trait')->field('title')->find($id);
        return $res['title'];
    }

    public function get_coal_granularity_title($id){
        $res = M('coal_granularity')->field('title')->find($id);
        return $res['title'];
    }

    /**
     * 获取地址id或地址名
     * @param $str  地址名
     * @return mixed    地址id
     */
    public function get_area_id($str){
        $where['name'] = $str;
        $id = M('districts')->field('id')->where($where)->find();
        return $id['id'];
    }

    public function get_area_name($id){
        $where['id'] = $id;
        $name = M('districts')->field('name')->where($where)->find();
        return $name['name'];
    }

    /**
     * @param $id   付款方式id
     * @return string   返回中文方式
     */
    public function get_pay_type_name($id){
        if($id == 1){
            return '预先付款';
        }elseif($id == 2){
            return '货到付款';
        }elseif($id == 3){
            return '电话沟通';
        }else{
            return 'error';
        }
    }




}