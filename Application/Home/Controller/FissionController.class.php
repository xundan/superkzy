<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2017/9/5
 * Time: 10:22
 */

namespace Home\Controller;
use Think\Controller;

class FissionController extends Controller
{
    public function index(){
        echo 'hello world!';
        exit;
    }

    public function fakeGroupJoin(){
        echo 'hello world!';
        exit;
        //WechatJDK
        vendor("jssdk.signPackage");
        $this->assign("signPackage", getSignPackage());
        //AreaAssign
        $ip = get_client_ip();
        $resultJson = file_get_contents("http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=".$ip);
        $resultArea = json_decode($resultJson,JSON_UNESCAPED_UNICODE);
        $this->assign('area',$resultArea);
        $this->display();
    }

    public function asdasd(){
        echo 'hello world!';
        exit;
        //WechatJDK
        vendor("jssdk.signPackage");
        $this->assign("signPackage", getSignPackage());
        //AreaAssign
        $ip = get_client_ip();
        $resultJson = file_get_contents("http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=".$ip);
        $resultArea = json_decode($resultJson,JSON_UNESCAPED_UNICODE);
        $this->assign('area',$resultArea);
        $this->display();
    }

    public function qrcodeScan(){
        echo 'hello world!';
        exit;
        $this->display();
    }


}