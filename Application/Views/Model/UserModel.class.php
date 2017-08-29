<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2017/1/18
 * Time: 15:32
 */

namespace Views\Model;


use Think\Model;

class UserModel extends Model
{
    protected $tableName = "ck_user";// 不用写表前缀

    public function exist_openid($open_id)
    {
        return $this->where(array("open_id" => $open_id))->find();
    }

    public function is_registered($user)
    {
        if ($user['phone_number']) {
            return true;
        } else {
            return false;
        }
    }

    public function getUser($id)
    {
        return $this->where(array("uid" => $id))->find();
    }
}