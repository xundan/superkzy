<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2016/12/17
 * Time: 11:52
 */

namespace Home\Model;

use Home\Common\CardList\WhereConditions;
use Think\Controller;
use Think\Model;

class MessagesModel extends Model
{
    protected $tablePrefix = 'ck_';
    protected $tableName = "messages";

    private $_message = null;

    //自动验证
    protected $_validate = array(
        array('phone_number', 'require', '手机号不能为空', 0),
        array('phone_number', '/^\\d{11}$/', '请填写正确的手机号', 0, 'regex'),
    );

    //自动完成
    protected $_auto = array(
        array('deadline', 'set_deadline', '1', 'function'),    //插入时设置截止日期
//        array('content_all', 'join_content', '3', 'callback'),    //新增和编辑的时候拼接表单数据
        array('publish_time', 'time', '1', 'function'),
//        array('area_start', 'get_area_id', '1', 'function'),
//        array('area_end', 'get_area_id', '1', 'function'),
    );

    public function __construct()
    {
        $tableSuffix = date('Ym', time());
        $tableTo = $this->tablePrefix . $this->tableName . '_' . $tableSuffix;
        $day = (int)substr(date('Ymd',time()),6,2);
        if($day == 1){
            $result = M()->query('show tables like "' . $tableTo . '"');
            if ($result) {
                $this->tableName = $this->tableName . '_' . $tableSuffix;
            } else {
                $tableFromSuffix = date('Ym', strtotime('-1 month'));
                $tableFrom = $this->tablePrefix . $this->tableName . '_' . $tableFromSuffix;
                $whereUpdateTime = date('Y-m-d', strtotime('-1 week'));
                M()->execute('create table ' . $tableTo . ' like ' . $tableFrom);
                M()->execute('insert into ' . $tableTo . ' select * from ' . $tableFrom . ' where update_time > "' . $whereUpdateTime . '"');
                $this->tableName = $this->tableName . '_' . $tableSuffix;
            }
        }else{
            $this->tableName = $this->tableName . '_' . $tableSuffix;
        }
        parent::__construct();
    }

    public function join_content()
    {
        $subInfo = I('post.', '', 'trim');
        //把id释放掉防止污染content_all
        unset($subInfo['id']);
        $data['content_all'] = json_encode($subInfo, JSON_UNESCAPED_UNICODE);
        return $data['content_all'];
    }

    public function addInto()
    {
        $subInfo = I('post.', '', 'trim');
        $data = $subInfo;
        //清除热值默认值
        if(!$subInfo['heat_value_max']){
            unset($data['heat_value_max']);
        }
        if(!$subInfo['heat_value_min']){
            unset($data['heat_value_min']);
        }
        // 判断重复
        $data['content_all'] = $this->join_content();
        $data['content_all_md5'] = md5($data['content_all']);
        $origin = $this->where("content_all_md5='%s' and invalid_id=0", $data['content_all_md5'])->find();
        if ($origin) {
            //重复的消息直接更新截止日期
            $this->updateMessageState($origin['id'], 3, true);
            $returnArr['status'] = 3;
            $returnArr['msg'] = '发布内容已更新';
            echo json_encode($returnArr);
        } else {
            // 拼装数据并插入
            if (!$subInfo['area_start'] && !$subInfo['area_end'] && !$subInfo['kind'] && !$subInfo['trait'] && !$subInfo['granularity']) {
                $data['formatted'] = 0;
            } else {
                $data['formatted'] = 1;
            }
            $data['trait'] = implode(',', $subInfo['trait']);
            $data['publisher_rid'] = $_SESSION['user_info']['uid'];
            $data['sender'] = $_SESSION['user_info']['user_name'];
            $data['type'] = "web";
            $data['vip'] = "9";
            $data['valid_time'] = 7; // C("EXPIRE_DAYS");
            $data['times_number'] += 1;
            if ($this->create($data)) {
                $result = $this->add();
                if ($result) {
                    // 如果主键是自动增长型 成功后返回值就是最新插入的值
                    $returnArr['status'] = 1;
                    $returnArr['msg'] = "发布成功";
                    echo json_encode($returnArr);
                } else {
                    //todo 数据库错误
//                $this->display('Common:403');
                }
            } else {
                //验证没通过
//            exit($this->getError());
                $returnArr['status'] = 2;
                $returnArr['msg'] = $this->getError();
                echo json_encode($returnArr);
//            throw new Exception;
//            $this->display('Common:403');
            }
        }
    }

    public function updateMessageState($id, $state, $refill = false)
    {
        if ($refill) {
            $msg['invalid_id'] = 0;
            $msg['deadline'] = set_deadline();
            $temp = D('messages')->where(array("id" => $id))->save($msg);
            if ($temp === false) return 0;
            return 1;
        } else {
            $msg['invalid_id'] = $state;
            $temp = D('messages')->where(array("id" => $id))->save($msg);
            if ($temp === false) return 0;
            return 1;
        }
    }

    public function updateMessage()
    {
        $subInfo = I('post.', '', 'trim');
        $data = $subInfo;
        if (!$subInfo['area_start'] && !$subInfo['area_end'] && !$subInfo['kind'] && !$subInfo['trait'] && !$subInfo['granularity']) {
            $data['formatted'] = 0;
        } else {
            $data['formatted'] = 1;
        }
        if ($this->create($data, 2)) {
            $result = $this->save();
            if ($result) {
                // 成功后返回值是影响行数
                $returnArr['status'] = 1;
                $returnArr['msg'] = "修改成功";
                echo json_encode($returnArr);
            } elseif ($result == 0) {
                // 成功后返回值是影响行数
                $returnArr['status'] = 2;
                $returnArr['msg'] = "未进行修改";
                echo json_encode($returnArr);
            } else {
                //todo 数据库错误
//                $this->display('Common:403');
            }
        } else {
            //验证没通过
//            exit($Msg->getError());
            $returnArr['status'] = 2;
            $returnArr['msg'] = $this->getError();
            echo json_encode($returnArr);
//            throw new Exception;
//            $this->display('Common:403');
        }
    }

    public function getMessageAttr($id = 1, $attr = "content")
    {
        $msg = $this->find($id);
        return $msg[$attr];
    }

    public function findWhere(WhereConditions $cond)
    {
        $countRow = C("DEFAULT_ROW");//常量
        $page = $cond->getPage();
        $asc = $cond->getAsc();
        $beginStr = ($page - 1) * $countRow;
        $this->_message = $this->where($cond->getWhereConditions())->where('invalid_id=0')->limit($beginStr, $countRow)->order($asc)->select();
//        $this->_message = $this->where($cond->getWhereConditions())->where('invalid_id=0')->limit($beginStr, $countRow)->order($asc)->cache(true,60)->select();
        return $this->_message;
    }

    /**
     * 用来区分发布历史页面,历史界面专用方法
     * @param WhereConditions $cond
     * @return string
     */
    public function findWhereForPublishHistory(WhereConditions $cond)
    {
        $countRow = C("DEFAULT_ROW");//常量
        $page = $cond->getPage();
        $asc = $cond->getAsc();
        $beginStr = ($page - 1) * $countRow;
        $this->_message = $this->where($cond->getWhereConditions())->where('invalid_id=0 or invalid_id=99')->limit($beginStr, $countRow)->order($asc)->select();
        return $this->_message;
    }

    /**
     * @param WhereConditions $cond
     * @return int 用户已发布且未删除的信息数
     */
    public function publishCount(WhereConditions $cond)
    {
        $this->_message = $this->where($cond->getWhereConditions())->where('invalid_id=0 or invalid_id=99')->select();
        return count($this->_message);
    }

    /**
     * 用来方便测试
     * @param WhereConditions $cond
     * @return string
     */
    public function findWhereToSql(WhereConditions $cond)
    {
        $this->findWhere($cond);
        return $this->getLastSql();
    }


    /**
     * 查询界面专用方法，历史和收藏不能使用这个方法！
     * $existWhere 会覆盖掉 $cond里的 "id in" 条件，要特别注意
     * @param WhereConditions $cond
     * @param int $count
     * @param string $category
     * @return mixed 获取的消息列表
     */
    public function findWhereWithoutExist(WhereConditions $cond, $count, $category)
    {

        $countRow = (C("DEFAULT_ROW") - $count);//常量
        if ($countRow <= 0) return false;
        $page = $cond->getPage();
        $asc = $cond->getAsc();
        $existWhere["id"] = array('not in', $cond->getExist());

        // $existWhere 会覆盖掉 $cond里的 "id in" 条件，特别注意。
        $beginStr = ($page - 1) * $countRow;
        if (count($cond->getExist()) == 0) {
            $this->_message = $this->where($cond->getWhereConditions())->where('`invalid_id`=0 AND category=\'' . $category . '\'')->limit($beginStr, $countRow)->order($asc)->select();
        } else {
            $this->_message = $this->where($cond->getWhereConditions())->where('`invalid_id`=0 AND category=\'' . $category . '\'')->where($existWhere)->limit($beginStr, $countRow)->order($asc)->select();
        }
        return $this->_message;
    }


    /**
     * 用来方便测试2
     * @param WhereConditions $cond
     * @param int $count
     * @param string $category
     * @return string
     */
    public function findWhereWithoutExistToSql(WhereConditions $cond, $count, $category)
    {
        $result = $this->findWhereWithoutExist($cond, $count, $category);
        if ($result === false) return false;
        return $this->getLastSql();
    }

    /**
     * 允许返回失效（invalid_id>0）的查询
     * @param WhereConditions $cond
     * @return null
     */
    public function findWhereExtra(WhereConditions $cond)
    {
        $countRow = C("DEFAULT_ROW");//常量
        $page = $cond->getPage();
        $asc = $cond->getAsc();
        $beginStr = ($page - 1) * $countRow;
        $this->_message = $this->where($cond->getWhereConditions())->limit($beginStr, $countRow)->order($asc)->select();
        return $this->_message;
    }

    public function toAll($message, $current_user)
    {
        $message = $this->toUser($message);
        $message = $this->toDistrictStart($message);
        $message = $this->toDistrictEnd($message);
        $message = $this->toCollection($message, $current_user);
        return $message;
    }

    public function toUser($message)
    {
        $where['uid'] = $message['publisher_rid'];
        $user = M('user')->where($where)->find();
        if ($user) {
            $message['user'] = $user;
        } elseif ($user === false) {
            //todo 数据库出错
        } else {
            //todo 数据为空
        }
        return $message;
    }


    public function toDistrictStart($message)
    {
        $where['id'] = $message['area_start'];
        $district = M('districts')->where($where)->find();
        if ($district) {
            $message['district_start'] = $district;
        } elseif ($district === false) {
            //todo 数据库出错
        } else {
            $district['name'] = '空';
            $message['district_start'] = $district;
        }
        return $message;
    }

    public function toDistrictEnd($message)
    {
        $where['id'] = $message['area_end'];
        $district = M('districts')->where($where)->find();
        if ($district) {
            $message['district_end'] = $district;
        } elseif ($district === false) {
            //todo 数据库出错
        } else {
            $district['name'] = '空';
            $message['district_end'] = $district;
        }
        return $message;
    }

    public function toCollection($message, $uid)
    {
        $where['msg_id'] = $message['id'];
        $where['user_id'] = $uid;
        $in_collection = M('collection')->where($where)->find();
        if ($in_collection) {
            if ($in_collection['invalid_id'] == 0) {
                $message['in_collection'] = "撤销收藏";
            } else {
                $message['in_collection'] = "收藏";
            }
        } elseif ($in_collection === false) {
            //todo 数据库出错
        } else {
            $message['in_collection'] = "收藏";
            //todo 数据为空
        }
        return $message;
    }

    function toSimple()
    {
        if ($this->_message['type'] == 'plain' || $this->_message['type'] == 'wx_mp' || $this->_message['type'] == 'group') { //微信的消息
            $new_message['title'] = $this->_message['title'];
            $new_message['phone_number'] = $this->_message['origin'];
            $new_message['content'] = $this->_message['content'];
            $this->_message = $new_message;
        } else {
            if ($this->_message['category'] === 0) { //供应
                //用户信息
                $this->toUser($this->_message);
                $new_message['user_name'] = $this->_message['user']['user_name'];
                $new_message['headimg_url'] = $this->_message['user']['headimg_url'];
                $new_message['city'] = $this->_message['user']['city'];
                //煤炭信息
                $this->toProduct($this->_message);
                $new_message['coal_kind_name'] = $this->_message['product']['kind'];
                $new_message['coal_trait_name'] = $this->_message['product']['trait'];
                $new_message['coal_granularity_name'] = $this->_message['product']['granularity'];
                //消息信息
                $this->toDistrictStart($this->_message);
                $new_message['area_start'] = $this->_message['district_start']['name'];
                $new_message['phone_number'] = $this->_message['phone_number'];
                $new_message['price'] = $this->_message['price'];
                $new_message['quantity'] = $this->_message['quantity'];
                $new_message['deadline'] = date('Y-m-d H:i:s', $this->_message['deadline']);
                $new_message['publish_time'] = date('Y-m-d H:i:s', $this->_message['publish_time']);

            } elseif ($this->_message['category'] == 1) { //司机找活
                //用户信息
                $this->toUser($this->_message);
                $new_message['user_name'] = $this->_message['user']['user_name'];
                $new_message['headimg_url'] = $this->_message['user']['headimg_url'];
                //煤炭信息
                $this->toProduct($this->_message);
                $new_message['coal_granularity_name'] = $this->_message['product']['granularity'];
                //消息信息
                $new_message['phone_number'] = $this->_message['phone_number'];
                $this->toDistrictStart($this->_message);
                $new_message['area_start'] = $this->_message['district_start']['name'];
                $this->toDistrictEnd($this->_message);
                $new_message['area_end'] = $this->_message['district_end']['name'];
                $new_message['quantity'] = $this->_message['quantity'];
                $new_message['loading_time'] = date('Y-m-d H:i:s', $this->_message['loading_time']);
                $new_message['deadline'] = date('Y-m-d H:i:s', $this->_message['deadline']);
                $new_message['publish_time'] = date('Y-m-d H:i:s', $this->_message['publish_time']);

            } elseif ($this->_message['category'] == 2) { //求购
                //用户信息
                $this->toUser($this->_message);
                $new_message['user_name'] = $this->_message['user']['user_name'];
                $new_message['headimg_url'] = $this->_message['user']['headimg_url'];
                $new_message['city'] = $this->_message['user']['city'];
                //煤炭信息
                $this->toProduct($this->_message);
                $new_message['coal_kind_name'] = $this->_message['product']['kind'];
                $new_message['coal_trait_name'] = $this->_message['product']['trait'];
                $new_message['coal_granularity_name'] = $this->_message['product']['granularity'];
                //消息信息
                $this->toDistrictStart($this->_message);
                $new_message['area_start'] = $this->_message['district_start']['name'];
                $new_message['phone_number'] = $this->_message['phone_number'];
                $new_message['price'] = $this->_message['price'];
                $new_message['quantity'] = $this->_message['quantity'];
                $new_message['deadline'] = date('Y-m-d H:i:s', $this->_message['deadline']);
                $new_message['publish_time'] = date('Y-m-d H:i:s', $this->_message['publish_time']);

            } elseif ($this->_message['category'] == 3) { //货源找车
                //用户信息
                $this->toUser($this->_message);
                $new_message['user_name'] = $this->_message['user']['user_name'];
                $new_message['headimg_url'] = $this->_message['user']['headimg_url'];
                //煤炭信息
                $this->toProduct($this->_message);
                $new_message['coal_granularity_name'] = $this->_message['product']['granularity'];
                //消息信息
                $new_message['phone_number'] = $this->_message['phone_number'];
                $this->toDistrictStart($this->_message);
                $new_message['area_start'] = $this->_message['district_start']['name'];
                $this->toDistrictEnd($this->_message);
                $new_message['area_end'] = $this->_message['district_end']['name'];
                $new_message['quantity'] = $this->_message['quantity'];
                $new_message['loading_time'] = date('Y-m-d H:i:s', $this->_message['loading_time']);
                $new_message['deadline'] = date('Y-m-d H:i:s', $this->_message['deadline']);
                $new_message['publish_time'] = date('Y-m-d H:i:s', $this->_message['publish_time']);
            }
        }
    }

    public function todayCount()
    {
        // select count(*) from `ck_messages` where to_days(`record_time`) = to_days(now());
//        $count = $this->query("select count(*) AS a from ck_messages where to_days(record_time) = to_days(now()) AND category IN ('求购', '供应', '找车', '车源') AND invalid_id = 0");
        $count = $this->query("select count(*) AS a from ck_messages where category IN ('求购', '供应', '找车', '车源') AND invalid_id = 0");
        return floor((int)$count[0]['a'] * 1.5);
    }

    public function todayTradeCount()
    {
        // select count(*) from `ck_messages` where to_days(`record_time`) = to_days(now());
        $count = $this->query("select count(*) AS a from ck_messages where to_days(record_time) = to_days(now()) AND category IN ('求购', '供应') AND invalid_id = 0");
        return $count[0]['a'];
    }

    public function todayTransportCount()
    {
        // select count(*) from `ck_messages` where to_days(`record_time`) = to_days(now());
        $count = $this->query("select count(*) AS a from ck_messages where to_days(record_time) = to_days(now()) AND category IN ('找车', '车源') AND invalid_id = 0");
        return $count[0]['a'];
    }

    /**
     * @param $categoryArr
     * @param $searchArr
     * @return array
     */
    public function selectSearch($categoryArr, $searchArr, $page)
    {
        $res = array(); // 默认返回值
        $map = array(); // 如果没有map就不执行sql
        if ($categoryArr) $map['category'] = array("IN", implode(",", $categoryArr));
        if ($searchArr) {
            $query = array();
            array_push($query, 'like');
            // 为关键字数组$tempStr
            foreach ($searchArr as &$item) {
//            $query[] = array('like', '%' . $item . '%');
                $item = "%" . $item . "%";
            }
            array_push($query, $searchArr);
            array_push($query, "AND");
            $map['content_all'] = $query;
        }
        if ($map) {
            $map['invalid_id'] = array("EQ", 0);
            $res = $this->where($map)->order('update_time desc')->page($page, 5)->select();
        }
//        return $this->getLastSql();
        return $res;
    }

    /**
     * @param $categoryArr
     * @param $granularityArr
     * @param $kindArr
     * @param $digitsArr
     * @return array
     */
    public function selectQuery($categoryArr, $granularityArr, $kindArr, $digitsArr)
    {
        $res = array(); // 默认返回值
        $map = array(); // 如果没有map就不执行sql
        if ($categoryArr) $map['category'] = array("IN", implode(",", $categoryArr));
        if ($granularityArr) $map['granularity'] = array("IN", implode(",", $granularityArr));
        if ($kindArr) $map['kind'] = array("IN", implode(",", $kindArr));
        if ($digitsArr) {
            // todo 这里的逻辑需要再考虑
            $map['heat_value_max'] = array("IN", implode(",", $digitsArr));
            $map['heat_value_min'] = array("IN", implode(",", $digitsArr));
            $map['price_min'] = array("IN", implode(",", $digitsArr));
            $map['price_max'] = array("IN", implode(",", $digitsArr));
        }
        if ($map) {
            $map['invalid_id'] = array("EQ", 0);
            $res = $this->where($map)->select();
        }
        return $res;
    }
}