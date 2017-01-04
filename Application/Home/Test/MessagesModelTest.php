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
        $msg = $Model->toCollection($msg,3);
        $this->assertEquals("收藏", $msg["in_collection"]);

//        $msg = $Model->toCollection($msg,1);
//        $this->assertEquals("已收藏", $msg["in_collection"]);
    }

    public function testFindWhereToSql(){
        $Model = new \Home\Model\MessagesModel();
        $whereConditions = new \Home\Common\CardList\WhereConditions();
        $whereConditions->pushSearchCond("content","a 你好\t123 \n c");
        $this->assertEquals('SELECT * FROM `ck_messages` WHERE ( `content` LIKE \'%a%\' AND `content` LIKE \'%你好%\' AND `content` LIKE \'%123%\' AND `content` LIKE \'%c%\'  ) AND ( invalid_id=0 ) ORDER BY record_time desc LIMIT 0,10  ', $Model->findWhereToSql($whereConditions));
        $whereConditions->pushCond("id", "eq", "1");
        $whereConditions->pushCond("id", "like", "%0");
        $whereConditions->pushCond("category", "like", "age");
        $this->assertEquals('SELECT * FROM `ck_messages` WHERE ( `content` LIKE \'%a%\' AND `content` LIKE \'%你好%\' AND `content` LIKE \'%123%\' AND `content` LIKE \'%c%\'  ) AND ( `id` = \'1\' AND `id` LIKE \'%0\'  ) AND `category` LIKE \'age\' AND ( invalid_id=0 ) ORDER BY record_time desc LIMIT 0,10  ', $Model->findWhereToSql($whereConditions));
        $whereConditions->pushCond("name", "like", "age"); // 并没有name字段，所以没有变化
        $this->assertEquals('SELECT * FROM `ck_messages` WHERE ( `content` LIKE \'%a%\' AND `content` LIKE \'%你好%\' AND `content` LIKE \'%123%\' AND `content` LIKE \'%c%\'  ) AND ( `id` = \'1\' AND `id` LIKE \'%0\'  ) AND `category` LIKE \'age\' AND ( invalid_id=0 ) ORDER BY record_time desc LIMIT 0,10  ', $Model->findWhereToSql($whereConditions));
        $whereConditions->pushSearchCond("content","again");
        $this->assertEquals('SELECT * FROM `ck_messages` WHERE ( `content` LIKE \'%again%\'  ) AND ( `id` = \'1\' AND `id` LIKE \'%0\'  ) AND `category` LIKE \'age\' AND ( invalid_id=0 ) ORDER BY record_time desc LIMIT 0,10  ', $Model->findWhereToSql($whereConditions));
        $whereConditions->ascPage();
        $this->assertEquals('SELECT * FROM `ck_messages` WHERE ( `content` LIKE \'%again%\'  ) AND ( `id` = \'1\' AND `id` LIKE \'%0\'  ) AND `category` LIKE \'age\' AND ( invalid_id=0 ) ORDER BY record_time desc LIMIT 10,10  ', $Model->findWhereToSql($whereConditions));
        $whereConditions->setLastCount(5);
        $this->assertEquals('SELECT * FROM `ck_messages` WHERE ( `content` LIKE \'%again%\'  ) AND ( `id` = \'1\' AND `id` LIKE \'%0\'  ) AND `category` LIKE \'age\' AND ( invalid_id=0 ) ORDER BY record_time desc LIMIT 10,10  ', $Model->findWhereToSql($whereConditions));
    }

    public function testFindWhereWithoutExistToSql(){
        $Model = new \Home\Model\MessagesModel();
        $whereConditions = new \Home\Common\CardList\WhereConditions();
        $whereConditions->pushCond("id", "eq", "1");
        $whereConditions->updateExist($Model->findWhere($whereConditions));
        $whereConditions->popCond("id");

        $whereConditions->pushSearchCond("content","好消息 ");
        $this->assertEquals('SELECT * FROM `ck_messages` WHERE ( `content` LIKE \'%好消息%\'  ) AND ( `invalid_id`=0 AND category=\'找车\' ) AND `id` NOT IN (\'1\') ORDER BY record_time desc LIMIT 0,10  ', $Model->findWhereWithoutExistToSql($whereConditions,0,"找车"));
        $this->assertEquals('SELECT * FROM `ck_messages` WHERE ( `content` LIKE \'%好消息%\'  ) AND ( `invalid_id`=0 AND category=\'找车\' ) AND `id` NOT IN (\'1\') ORDER BY record_time desc LIMIT 0,7  ', $Model->findWhereWithoutExistToSql($whereConditions,3,"找车"));
        $this->assertEquals(false, $Model->findWhereWithoutExistToSql($whereConditions,10,"找车"));
        $this->assertEquals(false, $Model->findWhereWithoutExistToSql($whereConditions,13,"找车"));

        $whereConditions->popCond("content");
        $whereConditions->pushCond("area_start", "like", "61%");
        $whereConditions->pushCond("area_end", "like", "41%");
        $whereConditions->pushCond("area_start", "eq", "610800");
        $whereConditions->pushCond("area_end", "eq", "410100");
        $this->assertEquals('{"conditions":"{\"area_start\":[[\"like\",\"61%\"],[\"eq\",\"610800\"],\"AND\"],\"area_end\":[[\"like\",\"41%\"],[\"eq\",\"410100\"],\"AND\"]}","page":1,"asc":"record_time desc","exists":["1"],"last_count":-2}',$whereConditions->toJson());
        $this->assertEquals("SELECT * FROM `ck_messages` WHERE ( `area_start` LIKE '61%' AND `area_start` = '610800'  ) AND ( `area_end` LIKE '41%' AND `area_end` = '410100'  ) AND ( `invalid_id`=0 AND category='找车' ) AND `id` NOT IN ('1') ORDER BY record_time desc LIMIT 0,10  ", $Model->findWhereWithoutExistToSql($whereConditions,0,"找车"));

        $whereConditions->popCond();
        $this->assertEquals('{"conditions":"{\"area_start\":[[\"like\",\"61%\"],[\"eq\",\"610800\"],\"AND\"],\"area_end\":[\"like\",\"41%\"]}","page":1,"asc":"record_time desc","exists":["1"],"last_count":-2}',$whereConditions->toJson());
        $this->assertEquals("SELECT * FROM `ck_messages` WHERE ( `area_start` LIKE '61%' AND `area_start` = '610800'  ) AND `area_end` LIKE '41%' AND ( `invalid_id`=0 AND category='找车' ) AND `id` NOT IN ('1') ORDER BY record_time desc LIMIT 0,10  ", $Model->findWhereWithoutExistToSql($whereConditions,0,"找车"));

        $whereConditions->popCond();
        $this->assertEquals('{"conditions":"{\"area_start\":[\"like\",\"61%\"],\"area_end\":[\"like\",\"41%\"]}","page":1,"asc":"record_time desc","exists":["1"],"last_count":-2}',$whereConditions->toJson());

        $whereConditions->popCond();
        $this->assertEquals('{"conditions":"{\"area_start\":[\"like\",\"61%\"]}","page":1,"asc":"record_time desc","exists":["1"],"last_count":-2}',$whereConditions->toJson());

        $whereConditions->popCond();
        $this->assertEquals('{"conditions":"[]","page":1,"asc":"record_time desc","exists":["1"],"last_count":-2}',$whereConditions->toJson());
        $this->assertEquals(true, $whereConditions->isExhausted());

//        $this->assertEquals("61",substr("612345",0,2));

    }


}
