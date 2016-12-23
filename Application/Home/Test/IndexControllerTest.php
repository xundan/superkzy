<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/12/20
 * Time: 17:02
 */

class IndexControllerTest extends PHPUnit_Framework_TestCase
{
    use \Think\PhpUnit; // 只有控制器测试类才需要它

    static public function setupBeforeClass()
    {
        // 下面四行代码模拟出一个应用实例, 每一行都很关键, 需正确设置参数
        self::$app = new \Think\PhpunitHelper();
        self::$app->setMVC('kuaimei56.com','Home','Index');
        self::$app->setTestConfig(['DB_NAME'=>'test_db', 'DB_HOST'=>'127.0.0.1',]); // 一定要设置一个测试用的数据库,避免测试过程破坏生产数据
        self::$app->start();
    }

    /**
     * 控制器action输出测试示例
     */
    public function testIndex()
    {
        $output = $this->execAction('index');
        $this->assertEquals('123',$output);
    }
}
