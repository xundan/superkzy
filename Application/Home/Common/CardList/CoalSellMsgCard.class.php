<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/12/20
 * Time: 10:26
 */

namespace Home\Common\CardList;


class CoalSellMsgCard extends MsgCard
{
    function toLi()
    {
        $message = $this->buildUpMessage();

        $personal_page = $this->getPersonalUrl();
        $message_detail = U('OwnerOrder/owner_order_trade_detail', array('id' => $message['id']));
        $publish_date = date("Y-m-d", $message['publish_time']);
        $deadline_date = substr($message['deadline'],0,10);
        if ($message['formatted']) { // 如果用户按照标准格式填写
            $li_str = "<li onclick='window.location.href=\"{$message_detail}\"'  class=\"weui_panel weui_panel_access\" style=\"border-radius: 5px\">
<div style=\"position: absolute;right: 0px;\">
<img src=\"__PUBLIC__/home/images/sell.png\" width=\"50px\"></div>
<div class=\"weui_media_box weui_media_appmsg\" style=\"margin: 0;padding-left: 0;padding-right: 0\">
    <a href=\"{$personal_page}\">
        <div class=\"weui_media_hd\">
            <img src=\"{$message['user']['heading_url']}\" class=\"weui_media_appmsg_thumb\">
        </div>
    </a>
    <div class=\"weui_media_bd\">
        <a href=\"{$personal_page}\">
            <h4 class=\"weui_media_title\">{$message['user']['user_name']}<img src=\"__PUBLIC__/home/images/medal.png\" style=\"width: 15px;height: 20px\"></h4>
        </a>
        <p class=\"weui_media_desc\"><img src=\"__PUBLIC__/home/images/area_start.png\" style=\"width: 10px;height: 15px\">{$message['user']['city']}</p>
    </div>
    <div class=\"weui_media_bd text-center\" style=\"\">
        <a href=\"tel:{$message['phone_number']}\" onclick='event.stopPropagation();' class=\"\">
            <h4><img src=\"__PUBLIC__/home/images/phone.png\" class=\"\" style=\"width: 20px\">
                <span style=\"\">拨打电话</span></h4>
        </a>
        <div>
            <button class=\"btn btn-xs btn-default\" style=\"width: 70%;border-color: #04bfc6;color: #04bfc6\" onclick='collection_switch(this,{$message['id']})'>{$message['in_collection']}</button>
        </div>
    </div>
</div>
<table class=\"table table-condensed\" style=\"margin: 0\">
    <tbody>
        <tr><td>煤炭种类:{$message['kind']}</td><td>{$message['price']}元/吨</td></tr>
        <tr><td>煤炭品质:{$message['trait']}</td><td>产地:{$message['district_start']['name']}</td></tr>
        <tr><td>煤炭粒度:{$message['granularity']}</td><td>吨数:{$message['quantity']}吨</td></tr>
        <tr><td>有效期至:{$deadline_date}</td><td>发布时间:{$publish_date}</td></tr>
    </tbody>
</table>
</li>";

        } else {// 如果用户填了一大段话
            $li_str = "<li onclick='window.location.href=\"{$message_detail}\"' class=\"weui_panel weui_panel_access\" style=\"border-radius: 5px\">
<div style=\"position: absolute;right: 0px;\"><img src=\"__PUBLIC__/home/images/sell.png\" width=\"50px\"></div>
<div class=\"weui_media_box weui_media_appmsg\" style=\"margin: 0;padding-left: 0;padding-right: 0\">
    <a href=\"{$personal_page}\">
        <div class=\"weui_media_hd\">
            <img src=\"{$message['user']['heading_url']}\" class=\"weui_media_appmsg_thumb\">
        </div>
    </a>
    <div class=\"weui_media_bd\">
        <a href=\"{$personal_page}\">
            <h4 class=\"weui_media_title\">{$message['user']['user_name']}<img src=\"__PUBLIC__/home/images/medal.png\" style=\"width: 15px;height: 20px\"></h4>
        </a>
        <p class=\"weui_media_desc\"><img src=\"__PUBLIC__/home/images/area_start.png\" style=\"width: 10px;height: 15px\">{$message['user']['city']}</p>
    </div>
    <div class=\"weui_media_bd text-center\" style=\"\">
        <a href=\"tel:{$message['phone_number']}\" onclick='event.stopPropagation();' class=\"\">
            <h4><img src=\"__PUBLIC__/home/images/phone.png\" class=\"\" style=\"width: 20px\">
                <span style=\"\">拨打电话</span></h4>
        </a>
        <div>
            <button class=\"btn btn-xs btn-default\" style=\"width: 70%;border-color: #04bfc6;color: #04bfc6\" onclick='collection_switch(this,{$message['id']})'>{$message['in_collection']}</button>
        </div>
    </div>
</div>
<div>{$message['content']}</div><div class='pull-right'>发布时间:{$publish_date}</div>
</li>";
        }
        return $this->replacePublicString($li_str);
    }

    protected function getOrderDetailUrl()
    {
        return U('OwnerOrder/owner_order_trade_detail',array("id"=>$this->_message['id']));
    }

}