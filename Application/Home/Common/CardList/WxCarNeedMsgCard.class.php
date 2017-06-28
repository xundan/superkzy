<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2017/1/10
 * Time: 16:19
 */

namespace Home\Common\CardList;


class WxCarNeedMsgCard extends MsgCard
{

    function toLi()
    {
        $img_name = $this->getImgName();
        $img = $img_name['img'];
        $name = $img_name['name'];
        $message = $this->buildCollection();
        $dial_param = ''.$message['phone_number'].','.$message['id'];
        $publish_date = substr($message['update_time'],0,10);
        if($message['invalid_id'] == 99){
            $imageString = "<img src='__PUBLIC__/home/images/need_overdue.png' width='50px'>";
        }else{
            $imageString = "<img src='__PUBLIC__/home/images/need.png' width='50px'>";
        }
        $li_str = "<li class=\"weui_panel weui_panel_access\" style=\"border-radius: 5px\">
<div style=\"position: absolute;right: 0px;\">".$imageString."</div>
<div class=\"weui_media_box weui_media_appmsg\" style=\"margin: 0;padding-left: 0;padding-right: 0\">
    <div class=\"weui_media_hd\">
        <img src=\"__PUBLIC__/home/images/$img\" class=\"weui_media_appmsg_thumb\">
    </div>
    <div class=\"weui_media_bd\">
        <div>
            <h4 class=\"weui_media_title\">$name</h4>
        </div>
    </div>
    <div class=\"weui_media_bd\" style=\"\">
        <a href=\"tel:{$message['phone_number']}\" onclick=\"ck_log('dial', '{$dial_param}')\" class=\"\" style='text-decoration: none;color: black'>
            <h4><img src=\"__PUBLIC__/home/images/phone.png\" class=\"\" style=\"width: 20px\">
            <span style=\"\">拨打电话</span></h4>
        </a>
        <div>
            <button class=\"btn btn-xs btn-default\" style=\"width: 70%;border-color: #04bfc6;color: #04bfc6\" onclick='collection_switch(this,{$message['id']})'>{$message['in_collection']}</button>
        </div>
    </div>
</div>
<div class=\"highlight\">{$message['content']}</div><div class='pull-right'>发布时间:{$publish_date}</div>
</li>";
        return $this->replacePublicString($li_str);
    }

}