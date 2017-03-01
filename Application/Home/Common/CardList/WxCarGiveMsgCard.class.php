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
    <img src=\"__PUBLIC__/home/images/give.png\" width=\"50px\">
</div>
<div class=\"weui_media_box weui_media_appmsg\" style=\"margin: 0;padding-left: 0;padding-right: 0\">
    <a href=\"#\">
        <div class=\"weui_media_hd\">
            <img src=\"__PUBLIC__/home/images/from_wx.png\" class=\"weui_media_appmsg_thumb\">
        </div>
    </a>
    <div class=\"weui_media_bd\">
        <div>
            <h4 class=\"weui_media_title\">来自微信</h4>
        </div>
    </div>
    <div class=\"weui_media_bd\" style=\"\">
        <a href=\"tel:{$message['phone_number']}\" class=\"\" style='text-decoration: none;color: black'>
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
        return $this->replacePublicString($li_str);
    }

}