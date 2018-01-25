<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2017/9/4
 * Time: 14:29
 */

namespace Views\Controller;

use Think\Controller;

class BaseDataFreightController extends Controller
{
    public function baseDataInput()
    {
        $this->display();
    }

    public function freightSubmit()
    {
        $subInfo = I('post.', '', 'strip_tags,trim');

        $data['area_start_id'] = $subInfo['area_start_id'];
        $data['area_start_name'] = $subInfo['area_start_name'];
        $data['area_start_detail'] = $subInfo['area_start_detail'];
        $data['area_start_merger_name'] = $subInfo['area_start_merger_name'];
        $data['area_end_id'] = $subInfo['area_end_id'];
        $data['area_end_name'] = $subInfo['area_end_name'];
        $data['area_end_detail'] = $subInfo['area_end_detail'];
        $data['area_end_merger_name'] = $subInfo['area_end_merger_name'];
        $data['freight_price'] = $subInfo['freight_price'];
        $data['invalid_id'] = 0;
        $result = M('ck_freight')->add($data);
        if ($result) {
            $returnArr['status'] = 1;
            $returnArr['msg'] = "发布成功";
            echo json_encode($returnArr);
        } else {
        }
    }

    public function area_check()
    {
        $area_name = I('post.area_name', '', 'trim');
        $where['name|short_name'] = $area_name;
        $result = M('ck_districts')->where($where)->find();
        if ($result) {
            echo json_encode($result);
            exit;
        } else {
            echo 0;
            exit;
        }
    }

    public function area_add()
    {
        $this->display();
    }

}