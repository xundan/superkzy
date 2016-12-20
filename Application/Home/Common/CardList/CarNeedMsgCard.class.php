<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/12/20
 * Time: 14:49
 */

namespace Home\Common\CardList;


class CarNeedMsgCard extends MsgCard
{

    function toLi()
    {
        $message = $this->_message;
        $personal_page = $this->getPersonalUrl();
        if ($message['is_formatted']) { // 如果用户按照标准格式填写
            $li_str = "<li class=\"weui_panel weui_panel_access\" style=\"border-radius: 5px\">
<div class=\"weui_media_box weui_media_appmsg\" style=\"margin: 0;padding-left: 0;padding-right: 0\">
    <a href=\"{$personal_page}\">
        <div class=\"weui_media_hd\">
            <img src=\"{$message['heading_url']}\" class=\"weui_media_appmsg_thumb\">
        </div>
    </a>
    <div class=\"weui_media_bd\">
        <a href=\"{$personal_page}\">
            <h4 class=\"weui_media_title\">{$message['user_name']}<img src=\"__PUBLIC__/home/images/medal.png\" style=\"width: 15px;height: 20px\"></h4>
        </a>
        <p class=\"weui_media_desc\">
            <img src=\"__PUBLIC__/home/images/area_start.png\" style=\"width: 15px;height: 20px\">
            {$message['area_start']}→{$message['area_end']}
            <img src=\"__PUBLIC__/home/images/area_end.png\" style=\"width: 15px;height: 20px\">
        </p>
    </div>
    <div class=\"weui_media_bd\" style=\"margin-right: -20%\">
        <a href=\"tel:{$message['phone_number']}\" class=\"\">
            <h4><img src=\"__PUBLIC__/home/images/phone.png\" class=\"\" style=\"width: 20px\">
            <span style=\"\">拨打电话</span></h4>
        </a>
        <div>
            <button class=\"btn btn-xs btn-default\" style=\"padding-left: 20%;padding-right: 20%;border-color: #04bfc6;color: #04bfc6\">{$message['in_collection']}</button>
        </div>
    </div>
</div>
<table class=\"table table-condensed\" style=\"margin: 0\">
    <tbody>
        <tr><td>发煤量:{$message['quantity']}</td><td>粒度:{$message['coal_granularity']}</td></tr>
        <tr><td>装车时间:{$message['loading_time']}</td><td>发布时间:{$message['publish_time']}</td></tr>
    </tbody>
</table>
</li>";
        } else {
            $li_str = "<li class=\"weui_panel weui_panel_access\" style=\"border-radius: 5px\">
<div class=\"weui_media_box weui_media_appmsg\" style=\"margin: 0;padding-left: 0;padding-right: 0\">
    <a href=\"{$personal_page}\">
        <div class=\"weui_media_hd\">
            <img src=\"{$message['heading_url']}\" class=\"weui_media_appmsg_thumb\">
        </div>
    </a>
    <div class=\"weui_media_bd\">
        <a href=\"{$personal_page}\">
            <h4 class=\"weui_media_title\">{$message['user_name']}<img src=\"__PUBLIC__/home/images/medal.png\" style=\"width: 15px;height: 20px\"></h4>
        </a>
        <p class=\"weui_media_desc\">
            <img src=\"__PUBLIC__/home/images/area_start.png\" style=\"width: 15px;height: 20px\">
            {$message['area_start']}→{$message['area_end']}
            <img src=\"__PUBLIC__/home/images/area_end.png\" style=\"width: 15px;height: 20px\">
        </p>
    </div>
    <div class=\"weui_media_bd\" style=\"margin-right: -20%\">
        <a href=\"tel:{$message['phone_number']}\" class=\"\">
            <h4><img src=\"__PUBLIC__/home/images/phone.png\" class=\"\" style=\"width: 20px\">
            <span style=\"\">拨打电话</span></h4>
        </a>
        <div>
            <button class=\"btn btn-xs btn-default\" style=\"padding-left: 20%;padding-right: 20%;border-color: #04bfc6;color: #04bfc6\">{$message['in_collection']}</button>
        </div>
    </div>
</div>
<div>{$message['content']}</div><div class='pull-right'>发布时间:{$message['publish_time']}</div>
</li>";
        }
        return $this->replacePublicString($li_str);
    }

    protected function getOrderDetailUrl()
    {
        return U('OwnerOrder/owner_order_transport_detail',array("id"=>$this->_message['id']));
    }

}