<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/12/20
 * Time: 11:36
 */

namespace Home\Common\CardList;


class CarGiveMsgCard extends MsgCard
{

    function toLi()
    {
        $message = $this->buildUpMessage();
        $personal_page = $this->getPersonalUrl();
        $message_detail = U('OwnerOrder/owner_order_transport_detail', array('id' => $message['id']));
//        $publish_date = date("Y-m-d", $message['publish_time']);
        $publish_date = substr($message['update_time'],0,10);
        $areastring = '';
        $dial_param = ''.$message['phone_number'].','.$message['id'];
        if($message['district_start']['name']!=='空' || $message['district_end']['name']!=='空'){
            $areastring = "<p class=\"weui_media_desc\">
            <img src=\"__PUBLIC__/home/images/area_start.png\" style=\"width: 15px;height: 20px\">
            <span class=\"highlight\">{$message['district_start']['name']}→{$message['district_end']['name']}</span>
            <img src=\"__PUBLIC__/home/images/area_end.png\" style=\"width: 15px;height: 20px\">
        </p>";
        }

        if($message['invalid_id'] == 99){
            $imageString = "<img src='__PUBLIC__/home/images/give_overdue.png' width='50px'>";
            $disableString = 'disabled';
        }else{
            $imageString = "<img src='__PUBLIC__/home/images/give.png' width='50px'>";
            $disableString = '';
        }

        if ($message['formatted']) { // 如果用户按照标准格式填写
            $li_str = "<li class=\"weui_panel weui_panel_access\" style=\"border-radius: 5px\">
<div style=\"position: absolute;right: 0px;\">".$imageString."</div>
<div class=\"weui_media_box weui_media_appmsg\" style=\"margin: 0;padding-left: 0;padding-right: 0\">
    <a href=\"{$personal_page}\">
        <div class=\"weui_media_hd\">
            <img src=\"{$message['user']['heading_url']}\" class=\"weui_media_appmsg_thumb img-circle\">
        </div>
    </a>
    <div class=\"weui_media_bd\">
            <h4 class=\"weui_media_title\"><span class='highlight' onclick='window.location.href=\"{$personal_page}\"'>{$message['user']['user_name']}</span></h4>
        ".$areastring."</div>
    <div class=\"weui_media_bd\" style=\"\">
        <a href=\"tel:{$message['phone_number']}\" onclick=\"ck_log('dial','{$dial_param}')\" class=\"\" style='text-decoration: none;color: black'>
            <h4><img src=\"__PUBLIC__/home/images/phone.png\" class=\"\" style=\"width: 20px\">
                <span style=\"\">拨打电话</span></h4>
        </a>
        <div>
            <button class=\"btn btn-xs btn-default\" style=\"width: 70%;border-color: #04bfc6;color: #04bfc6\" onclick='collection_switch(this,{$message['id']})'>{$message['in_collection']}</button>
        </div>
    </div>
</div>
<table onclick='window.location.href=\"{$message_detail}\"'  class=\"table table-condensed\" style=\"margin: 0\">
    <tbody>
    <tr><td class=\"highlight\">车辆类型:{$message['car_type']}</td><td class=\"highlight\">吨数:{$message['car_capacity']}</td></tr>
    </tbody>
</table>
<div class='pull-right'>发布时间:{$publish_date}</div>
</li>"."<div class='time-limit' style='margin-top:3px;margin-right:1px;text-align:right;display:none'>
<button class='btn btn-sm btn-info' onclick='refill(this,{$message["id"]})'>续时</button>
<button style='margin-left:5px' class='btn btn-sm btn-default ".$disableString."' onclick='overdue(this,{$message["id"]})'>下架</button>
<button style='margin-left:5px' class='btn btn-sm btn-danger' onclick='delete_modal(this,{$message["id"]})'>删除</button>
</div>";
        } else {
            $li_str = "<li class=\"weui_panel weui_panel_access\" style=\"border-radius: 5px\">
<div style=\"position: absolute;right: 0px;\">".$imageString."</div>
<div class=\"weui_media_box weui_media_appmsg\" style=\"margin: 0;padding-left: 0;padding-right: 0\">
    <a href=\"{$personal_page}\">
        <div class=\"weui_media_hd\">
            <img src=\"{$message['user']['heading_url']}\" class=\"weui_media_appmsg_thumb img-circle\">
        </div>
    </a>
    <div class=\"weui_media_bd\">
            <h4 class=\"weui_media_title\"><span class='highlight' onclick='window.location.href=\"{$personal_page}\"'>{$message['user']['user_name']}</span></h4>
        ".$areastring."</div>
    <div class=\"weui_media_bd\" style=\"\">
        <a href=\"tel:{$message['phone_number']}\" onclick=\"ck_log('dial','{$dial_param}')\" class=\"\" style='text-decoration: none;color: black'>
            <h4><img src=\"__PUBLIC__/home/images/phone.png\" class=\"\" style=\"width: 20px\">
                <span style=\"\">拨打电话</span></h4>
        </a>
        <div>
            <button class=\"btn btn-xs btn-default\" style=\"width: 70%;border-color: #04bfc6;color: #04bfc6\" onclick='collection_switch(this,{$message['id']})'>{$message['in_collection']}</button>
        </div>
    </div>
</div>
<div class=\"highlight\" onclick='window.location.href=\"{$message_detail}\"' >{$message['content']}</div><div class='pull-right'>发布时间:{$publish_date}</div>
</li>"."<div class='time-limit' style='margin-top:3px;margin-right:1px;text-align:right;display:none'>
<button class='btn btn-sm btn-info' onclick='refill(this,{$message["id"]})'>续时</button>
<button style='margin-left:5px' class='btn btn-sm btn-default ".$disableString."' onclick='overdue(this,{$message["id"]})'>下架</button>
<button style='margin-left:5px' class='btn btn-sm btn-danger' onclick='delete_modal(this,{$message["id"]})'>删除</button>
</div>";
        }
        return $this->replacePublicString($li_str);
    }


    protected function getOrderDetailUrl()
    {
        return U('DriverOrder/driver_order_detail',array("id"=>$this->_message['id']));
    }
}