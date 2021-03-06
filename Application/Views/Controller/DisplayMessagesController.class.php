<?php
/**
 * 小消息的审核与更新
 * User: LX
 * Date: 2016/8/12
 * Time: 10:29
 */

namespace Views\Controller;

use Think\Controller;
use Think\Exception;
use Views\Model\MessageModel;

class DisplayMessagesController extends Controller
{

    public function showDemo($id, $r = null, $group = null)
    {

//        dump($_SESSION['cur_user']);
        if (true) {
//        if ($_SESSION['cur_user']) {
            // 有where包装的时候不要直接find($id)，否则where会失效
            if ($group == 'off') {
                $data = D('Message')->where("invalid_id=0 AND type in ('plain','wx_mp') AND status=0 AND id=%d", $id)->find();
                $this->assign("data", $data);
                $this->assign("id", $id);
                $this->assign("group", 'off');
                $cur_user = $_SESSION['cur_user'];
                $username = $cur_user['name'];
                $this->assign('username', $username);
                $id_minus = $this->find_prev($id, $group);
                $id_plus = $this->find_next($id, $group);
                //审核进度
                $uncheck_count = $this->uncheckCount($id);
                $this->assign('uncheck_count', $uncheck_count);

                if (!$data) {
                    echo "<h4>没有更多数据了。</h4>";
                }

                if ($id_minus == -1) {
                    echo "<h4>已经是第一条了。</h4>";
                    $id_minus = $id;
                }
                if ($id_plus == -1) {
                    echo "<h4>已经是最后一条了。</h4>";
                    $id_plus = $id;
                }
                $url_prev = U('Views/DisplayMessages/showDemo') . "?id=$id_minus&r=$r&group=off";
                $url_next = U('Views/DisplayMessages/showDemo') . "?id=$id_plus&r=$r&group=off";
//        $url_delete = U('Views/DisplayMessages/delete')."?id=$id";
                $this->assign("prev", $url_prev);
                $this->assign("next", $url_next);
                $this->display();
            } else {
                $data = D('Message')->where("invalid_id=0 AND type in ('plain','group','wx_mp') AND status=0 AND id=%d", $id)->find();
                $this->assign("data", $data);
                $this->assign("id", $id);
                $this->assign("group", 'on');
                $this->assign("r", $r);
//                $cur_user = $_SESSION['cur_user'];
//                $username = $cur_user['name'];
//                $this->assign('username', $username);
                $id_minus = $this->find_prev($id);
                $id_plus = $this->find_next($id);
                //审核进度
                $uncheck_count = $this->uncheckCount($id);
                $this->assign('uncheck_count', $uncheck_count);

                if (!$data) {
                    echo "<h4>没有更多数据了。</h4>";
                }

                if ($id_minus == -1) {
                    echo "<h4>已经是第一条了。</h4>";
                    $id_minus = $id;
                }
                if ($id_plus == -1) {
                    echo "<h4>已经是最后一条了。</h4>";
                    $id_plus = $id;
                }
                $url_prev = U('Views/DisplayMessages/showDemo') . "?id=$id_minus&r=$r";
                $url_next = U('Views/DisplayMessages/showDemo') . "?id=$id_plus&r=$r";
//        $url_delete = U('Views/DisplayMessages/delete')."?id=$id";
                $this->assign("prev", $url_prev);
                $this->assign("next", $url_next);
                $this->display();
            }
        } else {
            header("Content-Type:text/html; charset=utf-8");//解决乱码
            $this->redirect('StaffsLogin/Login', '', 3, "您并没有登录，正在返回登录");
        }
    }

    public function check($id,$r,$group)
    {
        $tags = I('post.tag');
        $content = I('post.content');
//        $content = $_REQUEST["content"];

        // 检测tags里有没有包含必选项
        $main_tag = $this->is_check_valid($tags);

        // 删掉内容里的标签
        try {
            $content = preg_replace("/<span.*?span>|<b.*?>/", " ", $content);
            $content = preg_replace("/&lt;span.*?span&gt;|&lt;b.*?&gt;/", " ", $content);
        } catch (Exception $e) {
            // todo sth here
        }

        if ($main_tag) {
            /** deprecated: 因为不拉取了，所以不再更新relation相关的表 20180125*/
            /*
            // 更新与标签关系表
            $wx_arr = $this->update_relation_label($id, $tags);
            // 更新与微信关系表
            $this->add_relation_wx($id, $wx_arr);
            */
            // 更新消息表状态
            $subInfo = I('post.', '', 'trim');
            $this->update_message($id, $main_tag, $content,$subInfo);

//            $this->success('提交成功', 'showDemo?id=' . $this->find_next($id));
            $this->redirect('DisplayMessages/showDemo', array('id' => $this->find_next($id,$group),'r'=>$r,'group'=>$group), 0, "");
//            $this->redirect('DisplayMessages/showDemo', 'id=$this->find_next($id,$group)&r=$r&group=$group', 0, "");
//            redirect(U('DisplayMessages/showDemo','id='.$this->find_next($id,$group).'&group='.$group), 0, "");
        } else {
            $this->error('五个主要类型（求购，供应，找车，车源，其他）至少选一个。', 'showDemo?id=' . $id);
        }
    }

    public function add_relation_wx($id, $wx_arr)
    {
        // 更新前把旧数据都删掉
        $delete_data['invalid_id'] = 2;
        D('RelationM2W')->where("msg_id='$id'")->save($delete_data);

        // 添加新的关系数据
        foreach ($wx_arr as $wx) {
            $data['msg_id'] = $id;
            $data['wx'] = $wx;
            $data['invalid_id'] = 0;
            D("RelationM2W")->add($data);
        }

    }

    private function exist_invalid_relation($rid, $label_name)
    {
        // 寻找失效的关系
        if (D('RelationLabel')->
        where("object_rid='%s' and label_name='%s' and invalid_id>0", array($rid, $label_name))->find()
        ) {
            return true;
        }
        return false;
    }

    public function delete($id)
    {
        $update_delete = array(
            "id" => $id,
            "status" => -1,
            "invalid_id" => 2,
        );

        if (D('Message')->save($update_delete)) echo 'deleted';
    }

    /**
     * 删除所有未审核的群消息
     */
    public function delete_all()
    {
        $Msg = new MessageModel();
        if ($Msg->del_all_group_msg()) echo 'deleted';
    }

    public function find_prev($id, $group = null)
    {
        if ($group == 'off') {
            if ($id < 1) return -1;
            $prev = D('Message')->where("invalid_id=0 AND type in ('plain','wx_mp') AND status=0 AND id<" . $id)
                ->order('id desc')->find();
            if ($prev) {
                return $prev['id'];
            } else {
                return -1;
            }
        } else {
            if ($id < 1) return -1;
            $prev = D('Message')->where("invalid_id=0 AND type in ('plain','group','wx_mp') AND status=0 AND id<" . $id)
                ->order('id desc')->find();
            if ($prev) {
                return $prev['id'];
            } else {
                return -1;
            }
        }
    }

    public function find_next($id, $group = null)
    {
        if ($group == 'off') {
            $next = D('Message')->where("invalid_id=0 AND type in ('plain','wx_mp') AND status=0 AND id>" . $id)
                ->order('id asc')->find();
            if ($next) {
                return $next['id'];
            } else {
                return -1;
            }
        } else {
            $next = D('Message')->where("invalid_id=0 AND type in ('plain','group','wx_mp') AND status=0 AND id>" . $id)
                ->order('id asc')->find();
            if ($next) {
                return $next['id'];
            } else {
                return -1;
            }
        }
    }

    /**
     *获取未审核信息条数
     */
    public function uncheckCount($id)
    {
        $result = D('Message')->where("invalid_id=0 AND type in ('plain','group','wx_mp') AND status=0 AND id>" . $id)
            ->select();
        if ($result) {
            return count($result);
        } else {
            return 0;
        }
    }

    /**
     * 获取未审核群信息条数和今日已审核信息条数
     */
    public function uncheckGroupCount()
    {
        $resultGroupAll = D('Message')->where("invalid_id=0 AND type ='group' AND status=0")
            ->select();
        $resultGroupChecked = D('Message')->where("invalid_id=0 AND type ='group' AND status=102 AND update_time>substr(CURRENT_DATE ,1,10)")
            ->select();
        $result['group_all'] = count($resultGroupAll);
        $result['group_checked'] = count($resultGroupChecked);
//        $result['group_checked'] = M()->getLastSql();
        return $result;
    }

    /**
     * 将tag下的微信号去重后放入wx_arr数组中并返回
     * @param $tag
     * @param $arr
     * @return array
     */
    public function assemble($tag, $arr)
    {
        $wx_arr = $arr;
// 查询该label对应的微信号，放入数组
        $labels = D('Label')->where("invalid_id=%d and label_name='%s'", array(0, $tag))->select();
        if ($labels) {
            // 遍历所有$tag作为标签名对应的微信号
            foreach ($labels as $label_info) {
                $exist_flag = false;
                // 检查$wx_arr的重复性
                foreach ($wx_arr as $wx) {
                    if ($wx == $label_info['remark']) {
                        $exist_flag = true;
                        break;
                    }
                }
                if (!$exist_flag) {
                    array_push($wx_arr, $label_info['remark']);
                }
            }
        }
        return $wx_arr;
    }

    /**
     * 根据信息标的id和标签数组更新 与标签关系表 返回id对应的wx号列表
     * @param $id
     * @param $tags
     * @return array
     */
    public function update_relation_label($id, $tags)
    {
        $wx_arr = array();
        $not_in_where = "label_name not in (";
        $rid = 'MSSG' . $id;
        $Relations = D('RelationLabel');
        foreach ($tags as $tag) {
            $data['object_rid'] = $rid;
            $data['label_name'] = $tag;
            $data['invalid_id'] = 0; // data是有效的
            // 如果关系已经存在，刷新有效性，否则就insert
            if ($this->exist_invalid_relation($data['object_rid'], $data['label_name'])) {
                // 如果数据库没有变化，下面表达式会返回0，（所以不能把它作为成功失败的判断依据）
                // save只返回变化的行数
                $Relations->
                where("object_rid='%s' and label_name='%s'", array($rid, $tag))->save($data);
            } else {
                $Relations->add($data);
            }
            $wx_arr = $this->assemble($tag, $wx_arr);
            $not_in_where .= "'" . $tag . "',"; //拼接
        }
        $not_in_where .= "'whereEnd')"; // 作为单独结束
        $bad_data['invalid_id'] = 2; // 审核不通过
        // 把同ID下取消掉的标签改为审核不通过
        $Relations->where("object_rid='$rid' and " . $not_in_where)->save($bad_data);
        return $wx_arr;
    }

    /**
     * 更新message表
     * @param $id
     * @param $main_tag
     * @param $new_content
     */
    private function update_message($id, $main_tag, $new_content,$subInfo)
    {
        // 如果没有主标签，就不处理内容了
        $update_trans = array(
            "id" => $id,
            "category" => $main_tag,
            "content" => $new_content,
            "status" => 102,
            "content_all" => $new_content,
        );
        if ($main_tag) {
            // 如果有主标签，则处理一下内容的抬头
            $content = $new_content;
            // 检查是否已经有了前缀
            $prefix = substr($content, 0, 12);
            if (($prefix != "【" . $main_tag . "】")) {
                $content = "【" . $main_tag . "】" . $content;
            }
            $update_trans = array(
                "id" => $id,
                "category" => $main_tag,
                "content" => $content,
                "status" => 102,
                "content_all" => $content,
            );
        }
        //组装额外细化的字段
        if (!$subInfo['area_start'] && !$subInfo['area_end'] && !$subInfo['kind'] && !$subInfo['trait'] && !$subInfo['granularity']) {
            $update_trans['formatted'] = 0;
        } else {
            $update_trans['formatted'] = 1;
        }
        $update_trans['kind'] = $subInfo['kind'];
        $update_trans['granularity'] = $subInfo['granularity'];
        $update_trans['heat_value_max'] = $subInfo['heat_value_max'];
        $update_trans['heat_value_min'] = $subInfo['heat_value_min'];
        $update_trans['sulfur'] = $subInfo['sulfur'];
        D('Message')->save($update_trans);
    }

    /**
     * @param $tags
     * @return bool
     */
    private function is_check_valid($tags)
    {
        $valid_check = false;
        foreach ($tags as $tag) {
            if ($tag == "求购" || $tag == "供应" || $tag == "找车" || $tag == "其他" || $tag == "车源") {
                $valid_check = $tag;
                break;
            }
        }
        return $valid_check;
    }

    public function freightSubmit()
    {
        $subInfo = I('post.', '', 'strip_tags,trim');

        $data['recorder']  = $subInfo['recorder'];
        $data['message_id'] = $subInfo['message_id'];
        $data['phone_number'] = $subInfo['phone_number'];
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
//        if ($subInfo['area_start_id']) {
//            $result = M('ck_districts')->where("id=%d", array($subInfo['area_start_id']))->find();
//            $data['area_start_name'] = $result['name'];
//        }
//        if ($subInfo['area_end_id']) {
//            $result = M('ck_districts')->where("id=%d", array($subInfo['area_end_id']))->find();
//            $data['area_end_name'] = $result['name'];
//        }
        $result = M('ck_freight')->add($data);
        if ($result) {
            $returnArr['status'] = 1;
            $returnArr['msg'] = "发布成功";
            echo json_encode($returnArr);
        } else {
        }
    }

    public function area_check(){
        $area_name = I('post.area_name','','trim');
        if($area_name == '天津'){
            $area_name = '天津市';
        }
        $where['name|short_name'] = $area_name;
        $result = M('ck_districts')->where($where)->find();
        if($result){
            echo json_encode($result);
            exit;
        }else{
            echo 0;
            exit;
        }
    }


}