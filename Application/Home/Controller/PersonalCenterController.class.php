<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/11/15
 * Time: 15:58
 */

namespace Home\Controller;

use Think\Controller;

header("Content-type: text/html; charset=utf-8");

class PersonalCenterController extends ComController
{
    public function index()
    {
        $this->redirect('PersonalCenter/personal_center');
    }

    /**
     * 个人资料主页
     */
    public function personal_center()
    {
        // 一级页面，保存cookie
        cookie("last_url", U('PersonalCenter/personal_center'));

        $user_info = $_SESSION['user_info'];
        $this->assign('user_info', $user_info);
        $user_role = session('user_info')['role_id'] ? session('user_info')['role_id'] : "0";
        $this->assign('user_role', $user_role);

        $this->display();
    }

    /**
     * 货主用户界面
     * 如果uid为当前用户uid, 则界面是编辑界面，后退会跳回个人中心
     * 如果uid为其他用户uid, 则界面是展示界面，记得在进入本页面前记录cookie，以便能回退至原界面
     * @param null $uid 用户id
     */
    public function owner_data($uid = null)
    {
        if ($uid) {
            // 如果路径里有uid
            $temp['uid'] = $uid;

            $user_owner = M('User')->where($temp)->find();
            if ($user_owner) {
                $this->assign('user_owner', $user_owner);
                // 如果有公司信息，载入之
                if ($user_owner['company_id']) {
                    $user_company = M('Company')->where(array("id" => $user_owner['company_id']))->find();
                    $this->assign('user_company', $user_company);
                }
                // 如果有地理信息，载入之
                if ($user_owner['district_id']) {
                    $user_district = M('Districts')->where(array("id" => $user_owner['district_id']))->find();
                    $this->assign('user_district', $user_district['name']);
                }
                // 如果id是本人id，进入设置页面，否则进入展示页面
                if ($uid == session('user_info')['uid']) {
                    $this->display('owner_data_self');
                } else {
                    // 确保来源地，能够退回
                    $this->assign('last_url', cookie('last_url'));
                    $this->display();
                }
            } elseif ($user_owner === false) {
                // TODO false说明查询出错，记录日志
                $this->display("Common:500");

            } else {
                // TODO 查询为空，用户查到了不该到的地方，记日志
                $this->display("Common:404");
            }
        } else {
            // uid没有就跳转到自己的页面，为了可分享，路径必须含有uid
            $user_info = session('user_info');
            $this->redirect('PersonalCenter/owner_data', array('uid' => $user_info['uid']), 0, "");
        }
    }

    /**
     * 处理货主基本信息表单提交
     */
    public function owner_data_do()
    {
        //读取post数据
        $subInfo = I('post.', '', 'strip_tags,trim');
        $user['uid'] = session('user_info')['uid'];
        $user['user_name'] = $subInfo['nickname'];
        $user['phone_number'] = $subInfo['phone_number'];
        // TODO 改变电话需要验证
        $user['sex'] = $subInfo['sex'];
        $user['birthday'] = $subInfo['birthday'];
        $company['name'] = $subInfo['supply_company'];
        $user['company_id'] = $this->save_company($company);
        $user['district_id'] = $this->get_area_id($subInfo['reside_area']);
        $user['area_detail'] = $subInfo['area_detail'];
        $user['invite_id'] = $subInfo['invite_id'];
        //插入
        $res = M('User')->save($user);
        if ($res || $res === 0) {
            $this->success("修改成功", "personal_center", 3);
        } else {
            //todo log here
            $this->display("Common:500");
        }
    }

    /**
     * 处理货主从业经验表单提交
     */
    public function owner_work_exp_do()
    {
        //读取post数据
        $subInfo = I('post.', '', 'strip_tags,trim');
        //煤炭产品属性
        $user['uid'] = session('user_info')['uid'];
        $company['name'] = $subInfo['company_name'];
        $company['type'] = $subInfo['owner_work_type'];
        $user['work_time'] = $subInfo['work_time'];
        $user['owner_department'] = $subInfo['owner_department'];
        $user['owner_position'] = $subInfo['owner_position'];
        $user['work_description'] = $subInfo['work_description'];
        $user['company_id'] = $this->save_company($company);
        //插入
        $res = M('User')->save($user);
        if ($res || $res === 0) {
            $this->success("修改成功", "personal_center", 3);
        } else {
            //todo log here
            $this->display("Common:500");
        }
    }

    private function save_company($company)
    {
        $Comp = M('company');
        $res = $this->fetch_company($company);
        if ($res) {
            // 目前$company只有name字段，如果有新字段增加，则放开下面注释
            // 同时fetch_company方法也要改
            $Comp->where('id=' . $res['id'])->save($company);
            return $res['id'];
        } else {
            // added是新插入字段的id
            $added = $Comp->add($company);
            return $added;
        }
    }

    private function fetch_company($company)
    {
        $data['name'] = $company['name'];
        $res = M('Company')->where($data)->find();
        if ($res) {
            return $res;
        } else {
            // todo LOG HERE.
            return false;
        }
    }

    /**
     * 司机用户界面
     * 如果uid为当前用户uid, 则界面是编辑界面，后退会跳回个人中心
     * 如果uid为其他用户uid, 则界面是展示界面，记得在进入本页面前记录cookie，以便能回退至原界面
     * @param null $uid 用户id
     */
    public function driver_data($uid = null)
    {
        if ($uid) {
            // 如果路径里有uid
            $temp['uid'] = $uid;

            $user_driver = M('User')->where($temp)->find();
            if ($user_driver) {
                $this->assign('user_driver', $user_driver);
                // 如果有车辆信息，载入之
                if ($user_driver['car_id']) {
                    $user_car = M('car_info')->where(array("id" => $user_driver['car_id']))->find();
                    $this->assign('user_car', $user_car);
                }
                // 如果id是本人id，进入设置页面，否则进入展示页面
                if ($uid == session('user_info')['uid']) {
                    $this->display('driver_data_self');
                } else {
                    // 确保来源地，能够退回
                    $this->assign('last_url', cookie('last_url'));
                    $this->display();
                }
            } elseif ($user_driver === false) {
                // TODO false说明查询出错，记录日志
                $this->display("Common:500");

            } else {
                // TODO 查询为空，用户查到了不该到的地方，记日志
                $this->display("Common:404");
            }
        } else {
            // uid没有就跳转到自己的页面，为了可分享，路径必须含有uid
            $user_info = session('user_info');
            $this->redirect('PersonalCenter/driver_data', array('uid' => $user_info['uid']), 0, "");
        }
    }

    public function driver_data_do()
    {
        //读取post数据
        $subInfo = I('post.', '', 'strip_tags,trim');
        $user['uid'] = session('user_info')['uid'];
        $user['user_name'] = $subInfo['nickname'];
        $user['phone_number'] = $subInfo['phone_number'];
        // TODO 改变电话需要验证
        $user['sex'] = $subInfo['sex'];
        $user['id_card'] = $subInfo['id_card'];
        $user['drive_card'] = $subInfo['drive_card'];
        $car['plate_number'] = $subInfo['plate_number'];
        $car['type_id'] = $subInfo['car_type'];
        $user['car_type_id'] = $subInfo['car_type'];
        $car['carrying_capacity'] = $subInfo['carrying_capacity'];
        $user['car_id'] = $this->save_car($car);
        $user['invite_id'] = $subInfo['invite_id'];
        //插入
        $res = M('User')->save($user);
        if ($res || $res === 0) {
            $this->success("修改成功", "personal_center", 3);
        } else {
            //todo log here
            $this->display("Common:500");
        }
    }

    public function driver_work_exp_do()
    {
        //读取post数据
        $subInfo = I('post.', '', 'strip_tags,trim');
        $user['uid'] = session('user_info')['uid'];
        $user['work_time_start'] = $subInfo['work_time_start'];
        $user['work_time_end'] = $subInfo['work_time_end'];
        $user['buy_car_time'] = $subInfo['buy_car_time'];
        $user['work_description'] = $subInfo['work_description'];
        //插入
        $res = M('User')->save($user);
        if ($res || $res === 0) {
            $this->success("修改成功", "personal_center", 3);
        } else {
            //todo log here
            $this->display("Common:500");
        }
    }


    private function save_car($car)
    {
        $Car_info = M('car_info');
        $res = $this->fetch_car($car);
        if ($res) {
            $Car_info->where('id=' . $res['id'])->save($car);
            return $res['id'];
        } else {
            // added是新插入字段的id
            $added = $Car_info->add($car);
            return $added;
        }
    }

    private function fetch_car($car)
    {
        $data['plate_number'] = $car['plate_number'];
        $res = M('car_info')->where($data)->find();
        if ($res) {
            return $res;
        } else {
            // todo LOG HERE.
            return false;
        }
    }
}