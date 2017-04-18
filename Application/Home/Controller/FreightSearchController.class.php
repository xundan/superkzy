<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2017/4/1
 * Time: 16:28
 */

namespace Home\Controller;

use Think\Controller;

class FreightSearchController extends Controller
{
    public function show(){
        $where['id'] = array('in',[1,2,3,4,5]);
        $result = M('freight')->where($where)->select();
        $this->assign('data',$result);
        $this->display();
    }

    public function VoteCount(){
        $subInfo = I('post.', '', 'strip_tags,trim');
        if($subInfo['vote'] == 'upvote'){

        }
    }


}