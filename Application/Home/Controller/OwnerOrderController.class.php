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

    public function owner_order_transport_detail()
    {
        vendor("jssdk.signPackage");
        $this->assign("signPackage",getSignPackage());
        $this->display();
    }

    public function owner_order_trade_detail()
    {
        vendor("jssdk.signPackage");
        $this->assign("signPackage",getSignPackage());
        $this->display();
    }
}