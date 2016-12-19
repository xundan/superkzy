<?php

/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/12/19
 * Time: 9:28
 */
require_once('C:/wamp/www/superkzy/Application/Home/Common/CardList/WhereConditions.class.php');

class WhereConditionsTest extends PHPUnit_Framework_TestCase
{

    public function testEmpty()
    {
        $whereConditions = new \Home\Common\CardList\TipCard();
//        $this->assertEmpty(($whereConditions->getWhereConditions());
    }

    public function testPush()
    {
        $whereConditions = new \Home\Common\CardList\WhereConditions();

        $this->assertEquals(false, $whereConditions->pushCond("a", "b", "c", "d"));
        $this->assertEquals(0, count($whereConditions->getWhereConditions()));

        $whereConditions->pushCond("id", "eq", "1");
        $this->assertEquals(1, count($whereConditions->getWhereConditions()));
        $this->assertEquals(3, count($whereConditions->getWhereConditions(), 1));
        $this->assertEquals('{"id":["eq","1"]}', $whereConditions->toJson());

        $whereConditions->pushCond("id", "eq", "2", "OR");
        $this->assertEquals(1, count($whereConditions->getWhereConditions()));
        $this->assertEquals(8, count($whereConditions->getWhereConditions(), 1));
        $this->assertEquals('{"id":[["eq","1"],["eq","2"],"OR"]}', $whereConditions->toJson());

        $whereConditions->pushCond("name", "like", "'age'");
        $this->assertEquals(2, count($whereConditions->getWhereConditions()));
        $this->assertEquals(11, count($whereConditions->getWhereConditions(), 1));
        $this->assertEquals('{"id":[["eq","1"],["eq","2"],"OR"],"name":["like","\'age\'"]}', $whereConditions->toJson());

        $whereConditions->pushCond("id", "like", "'%0'");
        $this->assertEquals(2, count($whereConditions->getWhereConditions()));
        $this->assertEquals(16, count($whereConditions->getWhereConditions(), 1));
        $this->assertEquals('{"id":[[["eq","1"],["eq","2"],"OR"],["like","\'%0\'"],"AND"],"name":["like","\'age\'"]}', $whereConditions->toJson());

        $whereConditions->popCond('test');
        $this->assertEquals('{"id":[[["eq","1"],["eq","2"],"OR"],["like","\'%0\'"],"AND"],"name":["like","\'age\'"]}',$whereConditions->toJson());

        $whereConditions2 = new \Home\Common\CardList\WhereConditions($whereConditions->getWhereConditions());

        $whereConditions->popCond('name');
        $this->assertEquals('{"id":[[["eq","1"],["eq","2"],"OR"],["like","\'%0\'"],"AND"]}',$whereConditions->toJson());


        $whereConditions->popCond('id');
        $this->assertEquals('{"id":[["eq","1"],["eq","2"],"OR"]}',$whereConditions->toJson());

        $whereConditions->popCond('id');
        $this->assertEquals('[]',$whereConditions->toJson());

        $this->assertEquals('{"id":[[["eq","1"],["eq","2"],"OR"],["like","\'%0\'"],"AND"],"name":["like","\'age\'"]}',$whereConditions2->toJson());

        $whereConditions2->popCond();
        $this->assertEquals('{"id":[[["eq","1"],["eq","2"],"OR"],["like","\'%0\'"],"AND"]}',$whereConditions2->toJson());

        $whereConditions2->popCond();
        $this->assertEquals('[]',$whereConditions2->toJson());


    }

    public function testToSql()
    {

    }
}
