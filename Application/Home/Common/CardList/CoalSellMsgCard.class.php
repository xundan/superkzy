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
//        $publish_date = date("Y-m-d", $message['publish_time']);
        $dial_param = ''.$message['phone_number'].','.$message['id'];
        $publish_date = substr($message['update_time'], 0, 10);
        $deadline_date = substr($message['deadline'], 0, 10);
        //标签图片 过期功能
        if ($message['invalid_id'] == 99) {
            $imageString = "<img src='__PUBLIC__/home/images/sell_overdue.png' width='50px'>";
            $invalidImage = "<img src='__PUBLIC__/home/images/invalid.png' width='80px' style='opacity: 0.5'>";
            $disableString = 'disabled';
        } else {
            $imageString = "<img src='__PUBLIC__/home/images/sell.png' width='50px'>";
            $invalidImage = '';
            $disableString = '';
        }
        //价格字符串
        if ($message['price']) {
            $priceString = '价格:' . $message['price'] . '元/吨';
        } else {
            $priceString = '价格:面议';
        }

        if ($message['formatted']) { // 如果用户按照标准格式填写
            $li_str = "<li class=\"weui_panel weui_panel_access\" style=\"border-radius: 5px\">
<div style=\"position: absolute;right: 0px;\">" . $imageString . "</div>
<div style='position: absolute;right: 20px;bottom:20px;z-index:2'>" . $invalidImage . "</div>
<div class=\"weui_media_box weui_media_appmsg\" style=\"margin: 0;padding-left: 0;padding-right: 0\">
    <a href=\"{$personal_page}\">
        <div class=\"weui_media_hd\">
            <img src=\"{$message['user']['heading_url']}\" class=\"weui_media_appmsg_thumb img-circle\">
        </div>
    </a>
    <div class=\"weui_media_bd\">
        <h4 class=\"weui_media_title\"><span class='highlight' onclick='window.location.href=\"{$personal_page}\"'>{$message['user']['user_name']}</span></h4>
        <p class=\"weui_media_desc\"><img src=\"__PUBLIC__/home/images/area_start.png\" style=\"width: 10px;height: 15px\">{$message['user']['city']}</p>
    </div>
    <div class=\"weui_media_bd text-center\" style=\"\">
        <a href=\"tel:{$message['phone_number']}\" onclick=\"ck_log('dial',  '{$dial_param}')\" class=\"\" style='text-decoration: none;color: black'>
            <h4><img src=\"__PUBLIC__/home/images/phone.png\" class=\"\" style=\"width: 20px\">
                <span style=\"\">拨打电话</span></h4>
        </a>
        <div>
            <button class=\"btn btn-xs btn-default\" style=\"width: 70%;border-color: #04bfc6;color: #04bfc6\" onclick='collection_switch(this,{$message['id']})'>{$message['in_collection']}</button>
        </div>
    </div>
    <div style=\"position: absolute;bottom: -10px;left: 0;color: red;z-index: 9;\">
    <span class=\"glyphicon glyphicon-arrow-down detail_click\" style='display:none'>详情</span>
    </div>
</div>
<table class=\"table table-condensed\" onclick='window.location.href=\"{$message_detail}\"'   style=\"margin: 0;\">
    <tbody>
        <tr><td class=\"highlight\">煤炭种类:{$message['kind']}</td><td class=\"highlight\">" . $priceString . "</td></tr>
        <tr><td class=\"highlight\">煤炭品质:{$message['trait']}</td><td class=\"highlight\">产地:{$message['district_start']['name']}</td></tr>
        <tr><td class=\"highlight\">煤炭粒度:{$message['granularity']}</td><td class=\"highlight\">吨数:{$message['quantity']}吨</td></tr>
        <tr><td>有效期至:{$deadline_date}</td><td>发布时间:{$publish_date}</td></tr>
    </tbody>
</table>
</li>" . "<div class='time-limit' style='margin-top:3px;margin-right:1px;text-align:right;display:none'>
<button class='btn btn-sm btn-info' onclick='refill(this,{$message["id"]})'>一键重发</button>
<button style='margin-left:5px' class='btn btn-sm btn-default " . $disableString . "' onclick='overdue(this,{$message["id"]})'>下架</button>
<button style='margin-left:5px' class='btn btn-sm btn-danger' onclick='delete_modal(this,{$message["id"]})'>删除</button>
</div>";

        } else {// 如果用户填了一大段话
            $li_str = "<li class=\"weui_panel weui_panel_access\" style=\"border-radius: 5px\">
<div style=\"position: absolute;right: 0px;\">" . $imageString . "</div>
<div style='position: absolute;right: 20px;bottom:20px;z-index:2'>" . $invalidImage . "</div>
<div class=\"weui_media_box weui_media_appmsg\" style=\"margin: 0;padding-left: 0;padding-right: 0\">
    <a href=\"{$personal_page}\">
        <div class=\"weui_media_hd\">
            <img src=\"{$message['user']['heading_url']}\" class=\"weui_media_appmsg_thumb img-circle\">
        </div>
    </a>
    <div class=\"weui_media_bd\">
        <h4 class=\"weui_media_title\"><span class='highlight' onclick='window.location.href=\"{$personal_page}\"'>{$message['user']['user_name']}</span></h4>
        <p class=\"weui_media_desc\"><img src=\"__PUBLIC__/home/images/area_start.png\" style=\"width: 10px;height: 15px\">{$message['user']['city']}</p>
    </div>
    <div class=\"weui_media_bd text-center\" style=\"\">
        <a href=\"tel:{$message['phone_number']}\" onclick=\"ck_log('dial',  '{$dial_param}')\" class=\"\" style='text-decoration: none;color: black'>
            <h4><img src=\"__PUBLIC__/home/images/phone.png\" class=\"\" style=\"width: 20px\">
                <span style=\"\">拨打电话</span></h4>
        </a>
        <div>
            <button class=\"btn btn-xs btn-default\" style=\"width: 70%;border-color: #04bfc6;color: #04bfc6\" onclick='collection_switch(this,{$message['id']})'>{$message['in_collection']}</button>
        </div>
    </div>
    <div style=\"position: absolute;bottom: -10px;left: 0;color: red;z-index: 9\">
    <span class=\"glyphicon glyphicon-arrow-down detail_click\" style='display:none'>详情</span>
    </div>
</div>
<div class=\"highlight\" onclick='window.location.href=\"{$message_detail}\"' >{$message['content']}</div><div class='pull-right'>发布时间:{$publish_date}</div>
</li>" . "<div class='time-limit' style='margin-top:3px;margin-right:1px;text-align:right;display:none'>
<button class='btn btn-sm btn-info' onclick='refill(this,{$message["id"]})'>一键重发</button>
<button style='margin-left:5px' class='btn btn-sm btn-default " . $disableString . "' onclick='overdue(this,{$message["id"]})'>下架</button>
<button style='margin-left:5px' class='btn btn-sm btn-danger' onclick='delete_modal(this,{$message["id"]})'>删除</button>
</div>";
        }
        return $this->replacePublicString($li_str);
    }

    protected function getOrderDetailUrl()
    {
        return U('OwnerOrder/owner_order_trade_detail', array("id" => $this->_message['id']));
    }

}