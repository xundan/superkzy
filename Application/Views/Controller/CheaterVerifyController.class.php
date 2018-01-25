<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2017/12/9
 * Time: 9:40
 */

namespace Views\Controller;

use Think\Controller;

header('Content-type:text/html;charset=utf-8');

class CheaterVerifyController extends Controller
{
    /**
     * @param null $queryString get方法获得的查询字符串
     * @return string 返回结果字符串
     */
    public function cheaterVerify($queryString = null)
    {
        $query = trim($queryString);
        if($query){
            $where['name|phone_number|wx_id'] = array('like','%'.$query.'%');
            $result = M('ck_cheater')->where($where)->setInc('query_count',1);
            dump($result);
            dump(M()->getLastSql());
            if($result){
                //为1更新成功，数据库有记录
                return 'bingo';
            }else{
                return 'failure';
                //为0，查询失败或无记录
            }
        }else{
            return 'no_input';
        }
    }

    /**
     * 信息录入界面
     */
    public function cheaterInput(){
        $this->display();
    }

    /**
     * 信息提交界面
     */
    public function cheaterSubmit(){
        $subInfo = I('post.', '', 'trim,strip_tags');
        $result = M('ck_cheater')->add($subInfo);
        if($result){
            echo 'success';
        }else{
            echo 'failure';
        }
    }

    public function test($queryString = null)
    {
        if(1){echo 1;}
        if(0){echo 0;}
        exit;



        dump($queryString);
//        $a = mb_convert_encoding($queryString,'gbk','utf-8');
//        dump($queryString);
//        dump($a);
        $b = preg_match('/\d{11}/', $queryString, $match);
        dump($b);
        dump($match);
        if (!$match) {
            dump('match null');
        }
        dump($match[0]);
        $c = preg_split('/' . $match[0] . '/', $queryString);
        dump($c);
        if ($c) {
            dump(1);
        }
        dump($queryString);
    }


}