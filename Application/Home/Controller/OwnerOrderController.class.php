<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/11/1
 * Time: 9:13
 */

namespace Home\Controller;

use Think\Controller;

header("Content-type: text/html; charset=utf-8");

class OwnerOrderController extends ComController
{
    public function owner_order()
    {
        $this->display();
    }

    public function owner_order_transport_detail($id = null)
    {
        $this->assign_data($id);
        $this->display();
    }

    public function owner_order_trade_detail($id = null)
    {
        $this->assign_data($id);
        $this->display();
    }

    /**
     * 缓存数据
     * @param $id
     */
    private function assign_data($id)
    {
        vendor("jssdk.signPackage");
        $this->assign("signPackage", getSignPackage());
        if ($id) {
            // 如果路径里有uid
            $temp['id'] = $id;
            $message = M('Messages')->where($temp)->find();

            if ($message) {
                $this->assign('message', $message);
                $this->assign_user($message);
                $this->assign_area($message);
                $this->assign_product($message);
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
    }

    /**
     * 判断并缓存用户信息
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
     * 判断并缓存地址信息
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

    /**
     * 判断并缓存产品信息
     * @param $message
     */
    private function assign_product($message)
    {
        if ($message['product_id']) {
            $product = M('Product')->where(array('id' => $message['product_id']))->find();
            if ($product) {
                if ($product['kind_id']) {
                    $coal_kind = M('coal_kind')->where(array('id' => $product['kind_id']))->find();
                    $this->assign('coal_kind_title', $coal_kind['title']);
                }
                if ($product['place_origin_id']) {
                    $place_origin = M('Districts')->where(array("id" => $product['place_origin_id']))->find();
                    $this->assign('place_origin_name', $place_origin['name']);
                }
                $this->assign('product', $product);
            }
        }
    }

}