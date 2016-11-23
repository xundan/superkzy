<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/7/22
 * Time: 9:40
 */

function getAge($year){
    $now = date('Y');
    if ($now<$year){
        return '数据错误';
    }
    return $now-$year;
}


function sentToRemote($clients)
{
    $url = 'http://app.trade.51kuaimei.com/asyn/SetUserInfoByCreate';

    $post_data = array();
    $post_data['client'] = 'api';
    $post_data['UDID'] = '123456987654321';
    $post_data['UID'] = "00000000000000000000000000000000";
    $post_data['timestamp'] = time();
    $post_data['app'] = $clients['type']; //buyer=>是买家 seller =>是卖家 supplier =>是供应商

    $post_data['mobile'] = $clients['phone']; //手机号
    $post_data['password'] = $clients['pswd']; //密码
    $post_data['invite_code'] = '8810';


    $data = getParameter($post_data);
    $o = "";
    foreach ($data as $k => $v) {
        $o .= "$k=" . urlencode($v) . "&";
    }
    $post_data = substr($o, 0, -1); // 消掉字符串末尾的'&'
//    echo "<br>this is post_data:";
//    echo "<br>".$post_data;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    $data = curl_exec($ch);
    curl_close($ch);

    //$data = json_decode($data,true);
//    echo "<h1/>";
//    echo "THIS IS RESPONSE:<BR>";
//    print_r($data);
    return $data;
}


function getParameter($param)
{
    $token = "6f27b3e5b70d10";
    $_retval = array();
    foreach ($param as $key => $val) {
        $_retval[$key] = $val;
    }

    $tmpArr = array($token, $param['timestamp'], $param['UID'], $param['UDID'], $param['client'], $param['app']);
    // use SORT_STRING rule
    sort($tmpArr, SORT_STRING);

    $tmpStr = implode($tmpArr);
    $tmpStr = md5($tmpStr);
    $_retval['signature'] = $tmpStr;
//    echo "<br>this is tmpStr:";
//    echo "<br>".$tmpStr;
    return $_retval;
}