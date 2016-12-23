<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/11/24
 * Time: 10:17
 */

namespace Home\Controller;

use Home\Common\CardList\CardList;
use Home\Common\CardList\WhereConditions;
use Think\Controller;

header("Content-type: text/html; charset=utf-8");

class DriverOrderController extends ComController
{
    public function driver_order()
    {
        $uid = session('uid');
        $whereCond = new WhereConditions();
        $whereCond->pushCond("publisher_rid", "eq", "2");
        $messages = D('messages')->findWhere($whereCond);
//        dump($messages);
        $cards = new CardList($messages);
        $this->assign("li_array",$cards->toLiArray());
//        dump($cards->toLiArray());
        $this->display();
    }

    /**
     * 缓存数据
     * @param null $id
     */
    public function driver_order_detail($id = null)
    {
        vendor("jssdk.signPackage");
        $this->assign("signPackage", getSignPackage());

        if ($id) {
            // 如果路径里有uid
            $temp['id'] = $id;

            $message = M('Messages')->where($temp)->find();

            if ($message) {
                $this->assign('message', $message);
                // 判断用户
                $this->assign_user($message);
                // 判断地址
                $this->assign_area($message);
            } elseif ($message === false) {
                // TODO false说明查询出错，记录日志
                $this->display("Common:500");
            } else {
                // TODO 查询为空，用户查到了不该到的地方，记日志
                $this->display("Common:404");
            }
        } else {
            // TODO 查询为空，用户查到了不该到的地方，记日志
            $this->display("Common:404");
        }
        $this->display();

    }

    /**
     * 判断并缓存用户数据
     * @param $message
     */
    private function assign_user($message)
    {
        if ($message['publisher_rid']) {
            $user = M('User')->where(array("uid" => $message['publisher_rid']))->find();
            $this->assign('user', $user);
        } elseif ($message['sender']) {
            // 用户来自微信，展示一个静态页面+广告，显示手机号码
            $this->display("user_from_wx");
        }
    }

    /**
     * 判断并缓存地址数据
     * @param $message
     */
    private function assign_area($message)
    {
        if ($message['area_start']) {
            $area_start = M('Districts')->where(array("id" => $message['area_start']))->find();
            $this->assign('area_start', $area_start);
        }
        if ($message['area_end']) {
            $area_end = M('Districts')->where(array("id" => $message['area_end']))->find();
            $this->assign('area_end', $area_end);
        }
    }

}