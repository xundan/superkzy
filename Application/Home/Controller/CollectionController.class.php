<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2017/1/6
 * Time: 9:22
 */

namespace Home\Controller;

use Home\Model\CollectionModel;
use Think\Controller;

class CollectionController extends ComController
{
    
    public function add(){
        $Collection = new CollectionModel();
        $user = session('user_info');
        $post = I('post.', '', 'trim,strip_tags');
        $uid = $user['uid'];
        $msg_id = $post['id'];
        $data=$Collection->add_c($uid,$msg_id);
        echo json_encode($data);
        return;
    }

    public function del(){
        $Collection = new CollectionModel();
        $user = session('user_info');
        $post = I('post.', '', 'trim,strip_tags');
        $uid = $user['uid'];
        $msg_id = $post['id'];
        $data=$Collection->del_c($uid,$msg_id);
        echo json_encode($data);
        return;
    }
}