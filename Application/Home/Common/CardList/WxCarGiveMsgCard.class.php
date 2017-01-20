<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2017/1/10
 * Time: 15:55
 */

namespace Home\Common\CardList;


class WxCarGiveMsgCard extends MsgCard
{
    function toLi()
    {
        $message = $this->buildCollection();
        $publish_date = date("Y-m-d", $message['publish_time']);
        $li_str = "<li class=\"weui_panel weui_panel_access\" style=\"border-radius: 5px\">
<div style=\"position: absolute;right: 0px;\">
    <img src=\"__PUBLIC__/home/images/other.png\" width=\"50px\">
</div>
<div class=\"weui_media_box weui_media_appmsg\" style=\"margin: 0;padding-left: 0;padding-right: 0\">
    <a href=\"#\">
        <div class=\"weui_media_hd\">
            <img src=\"__PUBLIC__/home/images/from_wx.png\" class=\"weui_media_appmsg_thumb\">
        </div>
    </a>
    <div class=\"weui_media_bd\">
        <a href=\"#\">
            <h4 class=\"weui_media_title\">（来自微信朋友圈）<img src=\"__PUBLIC__/home/images/medal.png\" style=\"width: 15px;height: 20px\"></h4>
        </a>
        <p class=\"weui_media_desc\">
            <img src=\"__PUBLIC__/home/images/area_start.png\" style=\"width: 15px;height: 20px\">
            →
            <img src=\"__PUBLIC__/home/images/area_end.png\" style=\"width: 15px;height: 20px\">
        </p>
    </div>
    <div class=\"weui_media_bd\" style=\"margin-right: -20%\">
        <a href=\"tel:{$message['phone_number']}\" class=\"\">
            <h4><img src=\"__PUBLIC__/home/images/phone.png\" class=\"\" style=\"width: 20px\">
                <span style=\"\">拨打电话</span></h4>
        </a>
        <div>
            <button class=\"btn btn-xs btn-default\" style=\"padding-left: 20%;padding-right: 20%;border-color: #04bfc6;color: #04bfc6\" onclick='collection_switch(this,{$message['id']})'>{$message['in_collection']}</button>
        </div>
    </div>
</div>
<div>{$message['content']}</div><div class='pull-right'>发布时间:{$publish_date}</div>
</li>";
        return $this->replacePublicString($li_str);
    }

}