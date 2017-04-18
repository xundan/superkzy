<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2017/4/11
 * Time: 10:10
 */

namespace Home\Controller;


class LogController
{
    public function fetch(){
        $subInfo = I('post.','','strip_tags,trim');
        $data['uid'] = $_SESSION['user_info']['uid'];
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $data['page'] = $subInfo['page']?$subInfo['page']:$_SERVER['REQUEST_URI'];
        $data['param'] = $subInfo['param']?$subInfo['param']:null;
        $data['title'] = $subInfo['title']?$subInfo['title']:null;
        $data['oper'] = $subInfo['oper']?$subInfo['oper']:"browse";
        $data['result'] = $subInfo['result']?$subInfo['result']:"OK";
        $res = M('log')->data($data)->add();
        if($res){
            $returnArr['status'] = 1;
            $returnArr['msg'] = "提交成功";
            echo json_encode($returnArr);
        }else{
            $returnArr['status'] = 0;
            $returnArr['msg'] = "提交失败";
            M("product")->delete($res);
            echo json_encode($returnArr);
            exit;
        }
    }
}