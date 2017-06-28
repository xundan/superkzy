<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2017/1/10
 * Time: 16:21
 */

namespace Home\Common\CardList;


class WxCoalBuyMsgCard extends MsgCard
{

    function toLi()
    {
        $img_name = $this->getImgName();
        $img = $img_name['img'];
        $name = $img_name['name'];
        $message = $this->buildCollection();
        $dial_param = ''.$message['phone_number'].','.$message['id'];
        $publish_date = substr($message['update_time'], 0, 10);
        //标签图片 过期功能
        if ($message['invalid_id'] == 99) {
            $imageString = "<img src='__PUBLIC__/home/images/buy_overdue.png' width='50px'>";
            $invalidImage = "<img src='__PUBLIC__/home/images/invalid.png' width='80px' style='opacity: 0.5'>";
        } else {
            $imageString = "<img src='__PUBLIC__/home/images/buy.png' width='50px'>";
            $invalidImage = '';
        }
        $li_str = "<li class=\"weui_panel weui_panel_access\" style=\";border-radius: 5px\">
<div style=\"position: absolute;right: 0px;\">".$imageString."</div>
<div style='position: absolute;right: 20px;bottom:20px;z-index:2'>" . $invalidImage . "</div>
<div class=\"weui_media_box weui_media_appmsg\" style=\"margin: 0;padding-left: 0;padding-right: 0\">
    <div class=\"weui_media_hd\">
        <img src=\"__PUBLIC__/home/images/$img\" class=\"weui_media_appmsg_thumb\">
    </div>
    <div class=\"weui_media_bd\">
        <div>
            <h4 class=\"weui_media_title\">$name</h4>
        </div>
    </div>
    <div class=\"weui_media_bd text-center\" style=\"\">
        <a href=\"tel:{$message['phone_number']}\" onclick=\"ck_log('dial', '{$dial_param}')\" class=\"\" style='text-decoration: none;color: black'>
            <h4>
                <img src=\"__PUBLIC__/home/images/phone.png\" class=\"\" style=\"width: 20px\">
                <span style=\"\">拨打电话</span>
            </h4>
        </a>
        <div>
            <button class=\"btn btn-xs btn-default\" style=\"width: 70%;border-color: #04bfc6;color: #04bfc6\" onclick='collection_switch(this,{$message['id']})'>{$message['in_collection']}</button>
        </div>
    </div>
</div>
<div class=\"highlight\">{$message['content']}</div><div class='pull-right'>发布时间:{$publish_date}</div>
</li>
  ";
        return $this->replacePublicString($li_str);
    }
}