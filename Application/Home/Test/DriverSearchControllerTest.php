<?php

/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2017/1/4
 * Time: 16:21
 */
class DriverSearchControllerTest extends PHPUnit_Framework_TestCase
{

    use \Think\PhpUnit; // 只有控制器测试类才需要它

    static public function setupBeforeClass()
    {
        // 下面四行代码模拟出一个应用实例, 每一行都很关键, 需正确设置参数
        self::$app = new \Think\PhpunitHelper();
        self::$app->setMVC('xuncl.com','Home','DriverSearch');
        self::$app->setTestConfig(['DB_NAME'=>'test_db', 'DB_HOST'=>'127.0.0.1',]); // 一定要设置一个测试用的数据库,避免测试过程破坏生产数据
//        self::$app->start();
    }

    public function testIndex()
    {
        $Model = new \Home\Model\MessagesModel();
//        $Controller = new \Home\Controller\DriverSearchController();
        $wc = \Home\Common\CardList\WhereConditions::parseJson('{"conditions":"[]","page":1,"asc":"record_time desc","exists":["42","35","34","31","30","29","28","27"],"last_count":-1}');
        $this->assertEquals("SELECT * FROM `ck_messages` WHERE ( `invalid_id`=0 AND category='找车' ) AND `id` NOT IN ('42','35','34','31','30','29','28','27') ORDER BY record_time desc LIMIT 0,8  ", $Model->findWhereWithoutExistToSql($wc, 2, "找车"));

        $this->assertEquals(false,$wc->preSQL());

        $sql = $Model->findWhereWithoutExistToSql($wc, 0,"找车");
        $this->assertEquals("SELECT * FROM `ck_messages` WHERE ( `invalid_id`=0 AND category='找车' ) AND `id` NOT IN ('42','35','34','31','30','29','28','27') ORDER BY record_time desc LIMIT 0,10  ",$sql);


//        $data = $Controller->getOrderWithoutExist($wc,0);
//        $this->assertEquals('{"conditions":"[]","page":1,"asc":"record_time desc","exists":["42","35","34","31","30","29","28","27"],"last_count":0}',$data["where_cond_json"]);
//        $this->assertEquals(3,$data["stage"]);
//        $this->assertEquals("0a123d",$data["msg"]);
//        $wc = \Home\Common\CardList\WhereConditions::parseJson($data["where_cond_json"]);

    }

}
