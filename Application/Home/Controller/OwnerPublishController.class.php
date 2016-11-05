<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/11/3
 * Time: 16:13
 */

namespace Home\Controller;
use Think\Controller;
header("Content-type: text/html; charset=utf-8");

class OwnerPublishController extends ComController
{
    public function owner_publish(){
        $coalKind = M('coal_kind')->select();
        $coalTrait = M('coal_trait')->select();
        $coalGranularity = M('coal_granularity')->select();
//        dump($coalKind);
//        dump($coalTrait);
//        dump($coalGranularity);
        $this->assign('coalKind',$coalKind);
        $this->assign('coalTrait',$coalTrait);
        $this->assign('coalGranularity',$coalGranularity);
        $this->display();
    }
}