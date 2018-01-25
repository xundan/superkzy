<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/12/21
 * Time: 9:58
 */

namespace Home\Controller;
use Think\Controller;

class FeedbackController extends Controller
{
    public function feedback(){
        $this->display();
    }

    public function feedback_action(){
        $subInfo = I('post.','','strip_tags,trim');
        $data['uid'] = $_SESSION['user_info']['uid'];
        $data['content'] = $subInfo['feedback'];
        $data['contact'] = $subInfo['contact'];
        if(empty($subInfo))
            return;
        $fres = M('feedback')->data($data)->add();
        if($fres){
            $returnArr['status'] = 1;
            $returnArr['msg'] = "提交成功";
            echo json_encode($returnArr);
        }else{
            $returnArr['status'] = 0;
            $returnArr['msg'] = "提交失败";
            M("product")->delete($fres);
            echo json_encode($returnArr);
            return;
        }
    }



}