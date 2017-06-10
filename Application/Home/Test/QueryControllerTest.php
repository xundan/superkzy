<?php

/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2017/6/6
 * Time: 16:28
 */
require_once('C:/wamp/www/superkzy/Application/Home/Controller/QueryController.class.php');

class QueryControllerTest extends PHPUnit_Framework_TestCase
{

    public function testExtractParam(){
        $QueryController = new \Home\Controller\QueryController();
        $str = "找求购，供b应 车s源";
        $this->assertEquals(array("求购"),$QueryController->extractParam($str,"/(求购|供应|求车|车源)/"));
        $this->assertEquals("找 供b应 车s源",$str); // 参数str的值应该剥去掉已搜的关键字
        $str = "找求购，供应  车源";
        $this->assertEquals(array("求购","供应","车源",),$QueryController->extractParam($str,"/(求购|供应|求车|车源)/"));
        $this->assertEquals("找",$str);
        $str = "求购面煤，硫0.4灰8，热量5700，水9。电话17736773323";
        $this->assertEquals(array("0.4","8","5700","9","17736773323"),$QueryController->extractParam($str,"/([\\d.]+)/"));
        $this->assertEquals("求购面煤 硫灰 热量 水。电话",$str);
    }
}