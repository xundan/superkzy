<?php

/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/12/29
 * Time: 14:49
 */
class DriverOrderControllerTest extends PHPUnit_Framework_TestCase
{

    use \Think\PhpUnit; // 只有控制器测试类才需要它

    static public function setupBeforeClass()
    {
        // 下面四行代码模拟出一个应用实例, 每一行都很关键, 需正确设置参数
        self::$app = new \Think\PhpunitHelper();
        self::$app->setMVC('kuaimei56.com','Home','Index');
        self::$app->setTestConfig(['DB_NAME'=>'test_db', 'DB_HOST'=>'127.0.0.1',]); // 一定要设置一个测试用的数据库,避免测试过程破坏生产数据
//        self::$app->start();
    }

    /**
     * 其实测得是WhereConditions的toJson和parseJson方法。
     */
    public function testIndex()
    {
        // build up
        $Collection = new \Home\Model\CollectionModel();
        $Message = new \Home\Model\MessagesModel();
        $whereConditions = new \Home\Common\CardList\WhereConditions();

        $Collection->add_c(1,1);
        $Collection->add_c(1,2);
        $Collection->add_c(1,3);
        $Collection->add_c(1,4);
        $Collection->add_c(1,5);
        $Collection->add_c(1,6);
        $Collection->add_c(1,7);
        $Collection->add_c(1,8);
        $Collection->add_c(1,9);
        $Collection->add_c(1,10);
        $Collection->add_c(1,11);
        $Collection->add_c(1,12);
        $Collection->add_c(1,13);
        $Collection->add_c(1,14);

        $whereConditions->pushCond("id", "in", $Collection->getCollectionById(1));
        $messages = $Message->findWhere($whereConditions);
        $this->assertEquals(C('DEFAULT_ROW'), count($messages));

        $json = $whereConditions->toJson();
        $newWhereCond = \Home\Common\CardList\WhereConditions::parseJson($json);

        $this->assertEquals(true, ($newWhereCond instanceof \Home\Common\CardList\WhereConditions));

        if ($newWhereCond instanceof \Home\Common\CardList\WhereConditions){
            $newWhereCond->setPage(2);
            $messages = $Message->findWhere($newWhereCond);
        }
        $this->assertEquals(4, count($messages));

//
//        $output = $this->execAction('driver_order_more',array(2,));
//        $this->assertEquals('123',$output);

        // tear down
        $Collection->del_c(1,1);
        $Collection->del_c(1,2);
        $Collection->del_c(1,3);
        $Collection->del_c(1,4);
        $Collection->del_c(1,5);
        $Collection->del_c(1,6);
        $Collection->del_c(1,7);
        $Collection->del_c(1,8);
        $Collection->del_c(1,9);
        $Collection->del_c(1,10);
        $Collection->del_c(1,11);
        $Collection->del_c(1,12);
        $Collection->del_c(1,13);
        $Collection->del_c(1,14);
    }
}
