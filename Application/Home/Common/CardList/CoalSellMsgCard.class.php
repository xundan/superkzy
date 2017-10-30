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
        $dial_param = '' . $message['phone_number'] . ',' . $message['id'];
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
        //热值字符串
        if ($message['heat_value_max'] == '0') {
            $heatValueMaxString = '高位热值:空';
        } else {
            $heatValueMaxString = '高位热值:' . $message['heat_value_max'];
        }
        //含税字符串
        if ($message['short_allocate'] == 1) {
            $taxString = '含税:含税';
        } else {
            $taxString = '含税:不含税';
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
            <div class='card_item highlight'>煤炭种类:{$message['kind']}</div>
            <div class='card_item highlight'>产地:{$message['district_start']['name']}</div>
            <div class='card_item highlight'>" . $heatValueMaxString . "</div>
            <div class='card_item highlight'>煤炭品质:{$message['trait']}</div>
            <div class='card_item highlight collapse'>" . $priceString . "</div>
            <div class='card_item highlight collapse'>" . $taxString . "</div>
            <div class='card_item highlight collapse'>价格类型:{$message['price_type']}</div>
            <div class='card_item highlight collapse'>付款方式:{$message['pay_type']}</div>
            <div class='card_item highlight collapse'>煤炭粒度:{$message['granularity']}</div>
            <div class='card_item highlight collapse'>吨数:{$message['quantity']}吨</div>
            <div class='card_item highlight collapse' style='width:100%'>供货公司:{$message['supply_company']}</div>
            <div class='card_item collapse' style='width:100%'>详细指标</div>
            <div class='card_item highlight collapse'>低位热值:{$message['heat_value_min']}</div>
            <div class='card_item highlight collapse'>全水份:{$message['water']}</div>
            <div class='card_item highlight collapse'>灰分:{$message['ash']}</div>
            <div class='card_item highlight collapse'>硫份:{$message['sulfur']}</div>
            <div class='card_item highlight collapse'>挥发份:{$message['volatile']}</div>
            <div class='card_item highlight collapse'>有效期:7天</div>
            <div class='card_item collapse' style='width:100%'>详细描述</div>
            <div class='card_item detail highlight collapse'>{$message['content']}</div>
        </div>
        <div class='card-foot pull-right help-block'>发布时间:" . $publish_date . "</div>
    </div>
    <div class='collapse-switch'><span class='collapse-arrow'>+</span></div>
</li>
<div class='time-limit' style='margin-top:3px;margin-right:1px;text-align:right;display:none'>
    <button class='btn btn-sm btn-info' onclick='refill(this,{$message["id"]})'>一键重发</button>
    <button style='margin-left:5px' class='btn btn-sm btn-default " . $disableString . "' onclick='overdue(this,{$message["id"]})'>下架</button>
    <button style='margin-left:5px' class='btn btn-sm btn-danger' onclick='delete_modal(this,{$message["id"]})'>删除</button>
</div>";
        } else {// 如果用户填了一大段话
            $li_str =
"<li class='weui_panel weui_panel_access' style=';border-radius: 5px'>
    <div class='pic-layer' style='position: absolute;right: 0'>" . $imageString . "</div>
    <div class='pic-layer' style='position: absolute;right: 20px;bottom:20px;z-index:2'>" . $invalidImage . "</div>
    <div class='card-container'>
        <div class='card-head'>
            <div class='name text-center'>
                <span class='highlight' onclick=\"window.location.href = '{$personal_page}'\">
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
                <button class='btn btn-xs btn-ck' style='width: 3.5em' onclick=\"window.location.href='{$message_detail}'\">分享</button>
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
        return U('OwnerOrder/owner_order_trade_detail', array("id" => $this->_message['id']));
    }

}