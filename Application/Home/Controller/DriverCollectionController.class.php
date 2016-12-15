<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/12/15
 * Time: 16:59
 */

namespace Home\Controller;



class DriverCollectionController extends ComController
{

    public function index(){
        $Collection = new \Home\Model\CollectionModel();
//        $Collection->add_c(1,1);
        $Collection->del_c('2',3); // 这样也行
        $Collection->add_c(3,2);
//        $Collection->del_c(1,1);
    }


    public function add_collection($msg_id = null)
    {
        $user_id =session('user_info')['uid'];
        if ((!$user_id)||(!$msg_id)){
            //TODO 内部错误
            $returnArr['status'] = 403;
            $returnArr['msg'] = "参数缺失";
            echo jsonEcho($returnArr);
            return;
        }else{
            $returnArr['status'] = 200;
            $returnArr['msg'] = "操作成功";
            echo jsonEcho($returnArr);
            return;
        }
    }

    public function delete_collection($msg_id = null)
    {
        $user_id =session('user_info')['uid'];
        if ((!$user_id)||(!$msg_id)){
            //TODO 内部错误
            $returnArr['status'] = 403;
            $returnArr['msg'] = "参数缺失";
            echo jsonEcho($returnArr);
            return;
        }else{
            $returnArr['status'] = 200;
            $returnArr['msg'] = "操作成功";
            echo jsonEcho($returnArr);
            return;
        }
    }


}