<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2016/11/21
 * Time: 10:12
 */

namespace Home\Controller;
use Think\Controller;
use Home\Common\CardList\WhereConditions;
use Home\Model\MessagesModel;
header("Content-type: text/html; charset=utf-8");

class AreaSearchController extends ComController
{
    public function area_search(){
        //最近常用地区 从cookie中读取
        $last_area_temp = cookie('last_area');
        $last_area_temp_php = json_decode($last_area_temp);
        foreach($last_area_temp_php as $item){
            $item->name = urldecode($item->name);
        }
        //保证cookie长度为10
        if(count($last_area_temp_php) > 10){
            $last_area_temp_php = array_slice($last_area_temp_php,-10);
        }
//        倒序排列输出
//        krsort($lastarea_temp_php,false);  //保留key
        $last_area = array_reverse($last_area_temp_php);
        $this->assign('last_area',$last_area);

        //热点地区 is_hot字段更新


        //热点地区 is_hot字段判断
        $where_hot['is_hot'] = 1;
//        $where_hot['lever_type'] = array('gt',1);
        $where_hot['lever_type'] = array('eq',2);
        $result_hot = M('districts')->where($where_hot)->select();

        //所有地区列表 默认A开始 每个字母开头assign一下
//        $where['level_type'] = array('gt',1);   //二级地区
        $where['level_type'] = array('eq',2);   //二级地区

        $where['pinyin']  = array('like', 'A%');
        $resultA = M('districts')->where($where)->select();
        $this->assign('resultA',$resultA);

        $where['pinyin']  = array('like', 'B%');
        $resultB = M('districts')->where($where)->select();
        $this->assign('resultB',$resultB);

        $where['pinyin']  = array('like', 'C%');
        $resultC = M('districts')->where($where)->select();
        $this->assign('resultC',$resultC);

        $where['pinyin']  = array('like', 'D%');
        $resultD = M('districts')->where($where)->select();
        $this->assign('resultD',$resultD);

        $where['pinyin']  = array('like', 'E%');
        $resultE = M('districts')->where($where)->select();
        $this->assign('resultE',$resultE);

        $where['pinyin']  = array('like', 'F%');
        $resultF = M('districts')->where($where)->select();
        $this->assign('resultF',$resultF);

        $where['pinyin']  = array('like', 'G%');
        $resultG = M('districts')->where($where)->select();
        $this->assign('resultG',$resultG);

        $where['pinyin']  = array('like', 'H%');
        $resultH = M('districts')->where($where)->select();
        $this->assign('resultH',$resultH);

        $where['pinyin']  = array('like', 'I%');
        $resultI = M('districts')->where($where)->select();
        $this->assign('resultI',$resultI);

        $where['pinyin']  = array('like', 'J%');
        $resultJ = M('districts')->where($where)->select();
        $this->assign('resultJ',$resultJ);

        $where['pinyin']  = array('like', 'K%');
        $resultK= M('districts')->where($where)->select();
        $this->assign('resultK',$resultK);

        $where['pinyin']  = array('like', 'L%');
        $resultL = M('districts')->where($where)->select();
        $this->assign('resultL',$resultL);

        $where['pinyin']  = array('like', 'M%');
        $resultM = M('districts')->where($where)->select();
        $this->assign('resultM',$resultM);

        $where['pinyin']  = array('like', 'N%');
        $resultN = M('districts')->where($where)->select();
        $this->assign('resultN',$resultN);

        $where['pinyin']  = array('like', 'O%');
        $resultO= M('districts')->where($where)->select();
        $this->assign('resultO',$resultO);

        $where['pinyin']  = array('like', 'P%');
        $resultP = M('districts')->where($where)->select();
        $this->assign('resultP',$resultP);

        $where['pinyin']  = array('like', 'Q%');
        $resultQ = M('districts')->where($where)->select();
        $this->assign('resultQ',$resultQ);

        $where['pinyin']  = array('like', 'R%');
        $resultR = M('districts')->where($where)->select();
        $this->assign('resultR',$resultR);

        $where['pinyin']  = array('like', 'S%');
        $resultS = M('districts')->where($where)->select();
        $this->assign('resultS',$resultS);

        $where['pinyin']  = array('like', 'T%');
        $resultT = M('districts')->where($where)->select();
        $this->assign('resultT',$resultT);

        $where['pinyin']  = array('like', 'U%');
        $resultU = M('districts')->where($where)->select();
        $this->assign('resultU',$resultU);

        $where['pinyin']  = array('like', 'V%');
        $resultV = M('districts')->where($where)->select();
        $this->assign('resultV',$resultV);

        $where['pinyin']  = array('like', 'W%');
        $resultW = M('districts')->where($where)->select();
        $this->assign('resultW',$resultW);

        $where['pinyin']  = array('like', 'X%');
        $resultX = M('districts')->where($where)->select();
        $this->assign('resultX',$resultX);

        $where['pinyin']  = array('like', 'Y%');
        $resultY = M('districts')->where($where)->select();
        $this->assign('resultY',$resultY);

        $where['pinyin']  = array('like', 'Z%');
        $resultZ = M('districts')->where($where)->select();
        $this->assign('resultZ',$resultZ);

        $this->display();
    }

    public function ajax_area_search($querystr)
    {
        $querystring = $this->arrange_input($querystr);
        $where['level_type'] = array('gt',1);
//        $where['level_type'] = array('eq',2);
        if(preg_match('/^[a-zA-Z\s]+$/',$querystr)){
//            echo '全部为英文或者字母！';
//            $where['_string'] = '(pinyin_i like "%'.$querystring.'%") OR (pinyin like "%'.$querystring.'%")';
//            $where['pinyin_i'] = $querystr;
            $map['pinyin_i'] = $querystring;
            $map['pinyin'] = $querystring;
            $map['_logic'] = 'or';
            $where['_complex'] = $map;
            $where['name'] = array('notlike','%区');
            $result = M('districts')->where($where)->limit(5)->select();
            count($result);
            echo json_encode($result);exit;
        }else{
//            echo "有中文，或者数字，特殊符号存在！";
//            $where['merger_name'] = array('like','%'.$querystring.'%');
            $where['name'] = array(array('like','%'.$querystring.'%'),array('notlike','%区'),'and');
            $result = M('districts')->where($where)->limit(5)->select();
            echo json_encode($result);exit;
        }
    }

}