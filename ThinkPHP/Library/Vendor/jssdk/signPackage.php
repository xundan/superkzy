<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/11/23
 * Time: 13:48
 */
function getSignPackage()
{
    vendor("jssdk.jssdk");
    $jssdk = new JSSDK(C('WX_APPID'), C('WX_APPSECRET'));
    return $jssdk->GetSignPackage();
}