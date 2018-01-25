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
        $message = $this->buildUpMessage();
        $personal_page = $this->getPersonalUrl();
        $message_detail = U('OwnerOrder/driver_order_detail', array('id' => $message['id']));
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
            $imageString = "<img src='__PUBLIC__/home/images/need_overdue.png' width='50px'>";
            $invalidImage = "<img src='__PUBLIC__/home/images/invalid.png' width='80px' style='opacity: 0.5'>";
            $disableString = 'disabled';
        }else{
            $imageString = "<img src='__PUBLIC__/home/images/need.png' width='50px'>";
            $invalidImage = "";
            $disableString = '';
        }

        if ($message['formatted']) { // 如果用户按照标准格式填写
            $li_str =
"<li class='weui_panel weui_panel_access' style='border-radius: 5px'>
    <div class='pic-layer' style='position: absolute;right: 0'>" . $imageString . "</div>
    <div class='pic-layer' style='position: absolute;right: 20px;bottom:20px;z-index:2'>" . $invalidImage . "</div>
    <div class='card-container'>
        <div class='card-head'>
            <div class='name text-center'>
                <span class='highlight' onclick=\"window.location.href='{$personal_page}'\">
                {$message['user']['user_name']}
                </span>
            </div>
            <div class='dial text-center'>
                <a href='tel:{$message['phone_number']}' onclick=\"ck_log('dial', '{$dial_param}')\">
                    <img src='__PUBLIC__/home/images/phone.png'>
                    <span>拨打电话</span>
                </a>
            </div>
            <div class='function_button collapse text-center'>
                <button class='btn btn-xs btn-ck' style='width: 5.5em' onclick=\"collection_switch(this,{$message['id']})\">{$message['in_collection']}</button>
                <button class='btn btn-xs btn-ck' style='width: 3.5em' onclick=\"window . location . href = '{$message_detail}'\">分享</button>
            </div>
        </div>
        <div class='card-content'>
            <div class='card_item'>
                <img src='__PUBLIC__/home/images/area_start.png' style='width: 15px;height: 20px'>
                <span class='highlight'>{$message['district_start']['name']}</span>
            </div>
            <div class='card_item'>
                <img src='__PUBLIC__/home/images/area_end.png' style='width: 15px;height: 20px'>
                <span class='highlight'>{$message['district_end']['name']}</span>
            </div>
            <div class='card_item highlight'>出发地详址:{$message['detail_area_start']}</div>
            <div class='card_item highlight'>目的地详址:{$message['detail_area_end']}</div>
            <div class='card_item highlight'>发煤量:{$message['quantity']}</div>
            <div class='card_item highlight'>运费:{$message['price']}</div>
            <div class='card_item highlight'>粒度:{$message['granularity']}</div>
            <div class='card_item highlight'>装车时间:{$message['loading_time']}</div>
            <div class='card_item highlight collapse'>需要车辆:{$message['car_quantity']}</div>
            <div class='card_item highlight collapse'>装车费用:{$message['loading_cost']}</div>
            <div class='card_item highlight collapse'>卸车费用:{$message['unloading_cost']}</div>
            <div class='card_item highlight collapse'>有效期:7天</div>
            <div class='card_item collapse' style='width:100%'>详细描述</div>
            <div class='card_item detail highlight collapse'>{$message['content']}</div>
        </div>
        <div class='card-foot pull-right help-block'>发布时间:" . $publish_date . "</div>
    </div>
    <div class='collapse-switch'><span class='collapse-arrow'>+</span></div>
</li>
<div class='time-limit'>
    <button class='btn btn-sm btn-info' onclick='refill(this,{$message["id"]})'>一键重发</button>
    <button style='margin-left:5px' class='btn btn-sm btn-default ".$disableString."' onclick='overdue(this,{$message["id"]})'>下架</button>
    <button style='margin-left:5px' class='btn btn-sm btn-danger' onclick='delete_modal(this,{$message["id"]})'>删除</button>
</div>";
        } else {
            $li_str =
"<li class='weui_panel weui_panel_access' style=';border-radius: 5px'>
    <div class='pic-layer' style='position: absolute;right: 0'>" . $imageString . "</div>
    <div class='pic-layer' style='position: absolute;right: 20px;bottom:20px;z-index:2'>" . $invalidImage . "</div>
    <div class='card-container'>
        <div class='card-head'>
            <div class='name text-center'>
                <span class='highlight' onclick=\"window.location.href='{$personal_page}'\">
                {$message['user']['user_name']}
                </span>
            </div>
            <div class='dial text-center'>
                <a href='tel:{$message['phone_number']}' onclick=\"ck_log('dial', '{$dial_param}')\">
                    <img src='__PUBLIC__/home/images/phone.png'>
                    <span>拨打电话</span>
                </a>
            </div>
            <div class='function_button collapse text-center'>
                <button class='btn btn-xs btn-ck' style='width: 5.5em' onclick=\"collection_switch(this,{$message['id']})\">{$message['in_collection']}</button>
                <button class='btn btn-xs btn-ck' style='width: 3.5em' onclick=\"window . location . href = '{$message_detail}'\">分享</button>
            </div>
        </div>
        <div class='highlight content brief_content'>{$message['content']}</div>
        <div class='card-foot pull-right help-block'>发布时间:" . $publish_date . "</div>
    </div>
    <div class='collapse-switch'><span class='collapse-arrow'>+</span></div>
</li>
<div class='time-limit'>
    <button class='btn btn-sm btn-info' onclick='refill(this,{$message["id"]})'>一键重发</button>
    <button style='margin-left:5px' class='btn btn-sm btn-default " . $disableString . "' onclick='overdue(this,{$message["id"]})'>下架</button>
    <button style='margin-left:5px' class='btn btn-sm btn-danger' onclick='delete_modal(this,{$message["id"]})'>删除</button>
</div>";
        }
        return $this->replacePublicString($li_str);
    }

    protected function getOrderDetailUrl()
    {
        return U('OwnerOrder/owner_order_transport_detail',array("id"=>$this->_message['id']));
    }

}