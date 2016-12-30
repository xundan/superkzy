<?php

/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/12/21
 * Time: 16:22
 */
class MessagesModelTest extends PHPUnit_Framework_TestCase
{


    static public function setupBeforeClass()
    {
        // 下面四行代码模拟出一个应用实例, 每一行都很关键, 需正确设置参数
        $app = new \Think\PhpunitHelper();
        $app->setMVC('kuaimei56.com', 'Home', 'Index');
        $app->setTestConfig(['DB_NAME' => 'test_db', 'DB_HOST' => '127.0.0.1',]); // 一定要设置一个测试用的数据库,避免测试过程破坏生产数据
//        $app->start();
    }

    /**
     * 获取单个小消息
     */
    public function testGetMessageAttr()
    {
        $Model = new \Home\Model\MessagesModel();
        $message = $Model->getMessageAttr();
        $this->assertEquals('随时有车', $message);
        $message = $Model->getMessageAttr(9);
        $this->assertEquals('给我电话', $message);
        $message = $Model->getMessageAttr(2, "area_start");
        $this->assertEquals('230100', $message);
    }

    public function testToUser()
    {
        $Model = new \Home\Model\MessagesModel();
        $msg = $Model->find(1);
        $msg = $Model->toUser($msg);
        $this->assertEquals(1, $msg["user"]["uid"]);
        $this->assertEquals("张三新", $msg["user"]["user_name"]);

//        $msg = null;
//        $msg = $Model->find(4); // 这条msg对应的用户不存在
//        $msg = $Model->toUser($msg);
//        $this->assertEquals(false, isset($msg["user"]));
    }

    public function testToDistrict(){
        $Model = new \Home\Model\MessagesModel();
        $msg = $Model->find(1);
        $msg = $Model->toDistrictStart($msg);
        $msg = $Model->toDistrictEnd($msg);
        $this->assertEquals(152900, $msg["district_start"]["id"]);
        $this->assertEquals("阿拉善盟", $msg["district_start"]["name"]);
        $this->assertEquals(370100, $msg["district_end"]["id"]);
        $this->assertEquals("济南市", $msg["district_end"]["name"]);

        $msg = $Model->find(6);
        $msg = $Model->toDistrictStart($msg);
        $msg = $Model->toDistrictEnd($msg);
        $this->assertEquals("洛阳市", $msg["district_start"]["name"]);
        $this->assertEquals("空", $msg["district_end"]["name"]);
    }

    public function testToCollection(){
        $Model = new \Home\Model\MessagesModel();
        $msg = $Model->find(1);
        $msg = $Model->toCollection($msg,2);
        $this->assertEquals("收藏", $msg["in_collection"]);

//        $msg = $Model->toCollection($msg,1);
//        $this->assertEquals("已收藏", $msg["in_collection"]);
    }

}
