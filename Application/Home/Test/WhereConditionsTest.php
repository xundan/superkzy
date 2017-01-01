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


    public function testPush()
    {
            $whereConditions = new \Home\Common\CardList\WhereConditions();

        $this->assertEquals(false, $whereConditions->pushCond("a", "b", "c", "d"));
        $this->assertEquals(0, count($whereConditions->getWhereConditions()));

        $whereConditions->pushCond("id", "eq", "1");
        $this->assertEquals(1, count($whereConditions->getWhereConditions()));
        $this->assertEquals(3, count($whereConditions->getWhereConditions(), 1));
        $this->assertEquals('{"conditions":"{\"id\":[\"eq\",\"1\"]}","page":1,"asc":"record_time desc","exists":[],"last_count":-1}', $whereConditions->toJson());

        $whereConditions->pushCond("id", "eq", "2", "OR");
        $this->assertEquals(1, count($whereConditions->getWhereConditions()));
        $this->assertEquals(8, count($whereConditions->getWhereConditions(), 1));
        $this->assertEquals('{"conditions":"{\"id\":[[\"eq\",\"1\"],[\"eq\",\"2\"],\"OR\"]}","page":1,"asc":"record_time desc","exists":[],"last_count":-1}', $whereConditions->toJson());

        $whereConditions->pushCond("name", "like", "'age'");
        $this->assertEquals(2, count($whereConditions->getWhereConditions()));
        $this->assertEquals(11, count($whereConditions->getWhereConditions(), 1));
        $this->assertEquals('{"conditions":"{\"id\":[[\"eq\",\"1\"],[\"eq\",\"2\"],\"OR\"],\"name\":[\"like\",\"\'age\'\"]}","page":1,"asc":"record_time desc","exists":[],"last_count":-1}', $whereConditions->toJson());

        $whereConditions->pushCond("id", "like", "'%0'");
        $this->assertEquals(2, count($whereConditions->getWhereConditions()));
        $this->assertEquals(16, count($whereConditions->getWhereConditions(), 1));
        $this->assertEquals('{"conditions":"{\"id\":[[[\"eq\",\"1\"],[\"eq\",\"2\"],\"OR\"],[\"like\",\"\'%0\'\"],\"AND\"],\"name\":[\"like\",\"\'age\'\"]}","page":1,"asc":"record_time desc","exists":[],"last_count":-1}', $whereConditions->toJson());

        $whereConditions->popCond('test');
        $this->assertEquals('{"conditions":"{\"id\":[[[\"eq\",\"1\"],[\"eq\",\"2\"],\"OR\"],[\"like\",\"\'%0\'\"],\"AND\"],\"name\":[\"like\",\"\'age\'\"]}","page":1,"asc":"record_time desc","exists":[],"last_count":-1}',$whereConditions->toJson());

        $whereConditions2 = \Home\Common\CardList\WhereConditions::parseJson($whereConditions->toJson());

        $whereConditions->popCond('name');
        $this->assertEquals('{"conditions":"{\"id\":[[[\"eq\",\"1\"],[\"eq\",\"2\"],\"OR\"],[\"like\",\"\'%0\'\"],\"AND\"]}","page":1,"asc":"record_time desc","exists":[],"last_count":-1}',$whereConditions->toJson());


        $whereConditions->popCond('id');
        $this->assertEquals('{"conditions":"{\"id\":[[\"eq\",\"1\"],[\"eq\",\"2\"],\"OR\"]}","page":1,"asc":"record_time desc","exists":[],"last_count":-1}',$whereConditions->toJson());

        $whereConditions->popCond('id');
        $this->assertEquals('{"conditions":"[]","page":1,"asc":"record_time desc","exists":[],"last_count":-1}',$whereConditions->toJson());

        $this->assertEquals('{"conditions":"{\"id\":[[[\"eq\",\"1\"],[\"eq\",\"2\"],\"OR\"],[\"like\",\"\'%0\'\"],\"AND\"],\"name\":[\"like\",\"\'age\'\"]}","page":1,"asc":"record_time desc","exists":[],"last_count":-1}',$whereConditions2->toJson());

        $whereConditions2->popCond();
        $this->assertEquals('{"conditions":"{\"id\":[[[\"eq\",\"1\"],[\"eq\",\"2\"],\"OR\"],[\"like\",\"\'%0\'\"],\"AND\"]}","page":1,"asc":"record_time desc","exists":[],"last_count":-1}',$whereConditions2->toJson());

        $whereConditions2->popCond();
        $this->assertEquals('{"conditions":"[]","page":1,"asc":"record_time desc","exists":[],"last_count":-1}',$whereConditions2->toJson());


    }

    public function testToSql()
    {

    }

    public function testPage(){

    }
}
