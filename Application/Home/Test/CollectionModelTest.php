<?php

/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/12/23
 * Time: 9:23
 */
class CollectionModelTest extends PHPUnit_Framework_TestCase
{


    static public function setupBeforeClass()
    {
        // 下面四行代码模拟出一个应用实例, 每一行都很关键, 需正确设置参数
        $app = new \Think\PhpunitHelper();
        $app->setMVC('kuaimei56.com', 'Home', 'Index');
        $app->setTestConfig(['DB_NAME' => 'test_db', 'DB_HOST' => '127.0.0.1',]); // 一定要设置一个测试用的数据库,避免测试过程破坏生产数据
        $app->start();
    }

    /**
     * 模型类方法测试示例
     */
    public function testGetCollectionMessage()
    {
        $Model = new \Home\Model\CollectionModel();
        $Message = new \Home\Model\MessagesModel();
        $whereConditions = new \Home\Common\CardList\WhereConditions();

        $Model->del_c(1, 1);
        $Model->del_c(1, 2);
        $Model->del_c(1, 3);
        $Model->del_c(1, 4);
        $Model->del_c(1, 5);
        $Model->del_c(1, 6);
        $Model->del_c(1, 7);
        $Model->del_c(1, 8);
        $Model->del_c(1, 9);
        $Model->del_c(1, 10);
        $Model->del_c(1, 11);

        // 如果数据不干净，先把db_test.ck_collection truncate掉

        $Model->add_c(1, 1);
        $this->assertEquals([1], $Model->getCollectionById(1));
        // 查重
        $Model->add_c(1, 1);
        $this->assertEquals([1], $Model->getCollectionById(1));
        // 删除
        $Model->del_c(1, 1);
        $this->assertEquals([], $Model->getCollectionById(1));
        // 重复删除
        $Model->del_c(1, 1);
        $this->assertEquals([], $Model->getCollectionById(1));

        // 多次添加
        $Model->add_c(1, 1);
        $this->assertEquals([1], $Model->getCollectionById(1));
        $Model->add_c(1, 2);
        $this->assertEquals([1, 2], $Model->getCollectionById(1));
        $whereConditions->pushCond("id", "in", $Model->getCollectionById(1));
        $messages = $Message->findWhere($whereConditions);
        $this->assertEquals(2, count($messages));

        // 删掉之前的条件
        $whereConditions->popCond("id");

        // 多次添加
        $Model->add_c(1, 1);
        $Model->add_c(1, 2);
        $Model->add_c(1, 3);
        $Model->add_c(1, 4);
        $Model->add_c(1, 5);
        $Model->add_c(1, 6);
        $Model->add_c(1, 7);
        $Model->add_c(1, 8);
        $Model->add_c(1, 9);
        $Model->add_c(1, 10);
        $Model->add_c(1, 11);
        $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11], $Model->getCollectionById(1));
        $whereConditions->pushCond("id", "in", $Model->getCollectionById(1));
        $messages = $Message->findWhere($whereConditions);
        $this->assertEquals(C("DEFAULT_ROW"), count($messages)); // 只会取出10条
        $Model->del_c(1, 1);
        $Model->del_c(1, 2);
        $Model->del_c(1, 3);
        $Model->del_c(1, 4);
        $Model->del_c(1, 5);
        $Model->del_c(1, 6);
        $Model->del_c(1, 7);
        $Model->del_c(1, 8);
        $Model->del_c(1, 9);
        $Model->del_c(1, 10);
        $Model->del_c(1, 11);
    }
}
