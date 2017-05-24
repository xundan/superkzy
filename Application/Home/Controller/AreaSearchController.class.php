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

class AreaSearchController extends Controller
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
        if(I('post.signal') == 1) {
//        $where['level_type'] = array('gt',1);   //二级地区
            $where['level_type'] = array('eq', 2);   //二级地区
            if (empty(S('resultsA'))) {
                $where['pinyin'] = array('like', 'A%');
                $resultA = M('districts')->where($where)->field('id,name')->select();
                S('resultA', $resultA);
            } else {
                $resultA = S('resultA');
            }
            if (empty(S('resultsB'))) {
                $where['pinyin'] = array('like', 'B%');
                $resultB = M('districts')->where($where)->field('id,name')->select();
                S('resultB', $resultB);
            } else {
                $resultB = S('resultB');
            }
            if (empty(S('resultsC'))) {
                $where['pinyin'] = array('like', 'C%');
                $resultC = M('districts')->where($where)->field('id,name')->select();
                S('resultC', $resultC);
            } else {
                $resultC = S('resultC');
            }
            if (empty(S('resultsD'))) {
                $where['pinyin'] = array('like', 'D%');
                $resultD = M('districts')->where($where)->field('id,name')->select();
                S('resultD', $resultD);
            } else {
                $resultD = S('resultD');
            }
            if (empty(S('resultsE'))) {
                $where['pinyin'] = array('like', 'E%');
                $resultE = M('districts')->where($where)->field('id,name')->select();
                S('resultE', $resultE);
            } else {
                $resultE = S('resultE');
            }
            if (empty(S('resultsF'))) {
                $where['pinyin'] = array('like', 'F%');
                $resultF = M('districts')->where($where)->field('id,name')->select();
                S('resultF', $resultF);
            } else {
                $resultF = S('resultF');
            }
            if (empty(S('resultsG'))) {
                $where['pinyin'] = array('like', 'G%');
                $resultG = M('districts')->where($where)->field('id,name')->select();
                S('resultG', $resultG);
            } else {
                $resultG = S('resultG');
            }
            if (empty(S('resultsH'))) {
                $where['pinyin'] = array('like', 'H%');
                $resultH = M('districts')->where($where)->field('id,name')->select();
                S('resultH', $resultH);
            } else {
                $resultH = S('resultH');
            }
            if (empty(S('resultsI'))) {
                $where['pinyin'] = array('like', 'I%');
                $resultI = M('districts')->where($where)->field('id,name')->select();
                S('resultI', $resultI);
            } else {
                $resultI = S('resultI');
            }
            if (empty(S('resultsJ'))) {
                $where['pinyin'] = array('like', 'J%');
                $resultJ = M('districts')->where($where)->field('id,name')->select();
                S('resultJ', $resultJ);
            } else {
                $resultJ = S('resultJ');
            }
            if (empty(S('resultsK'))) {
                $where['pinyin'] = array('like', 'K%');
                $resultK = M('districts')->where($where)->field('id,name')->select();
                S('resultK', $resultK);
            } else {
                $resultK = S('resultK');
            }
            if (empty(S('resultsL'))) {
                $where['pinyin'] = array('like', 'L%');
                $resultL = M('districts')->where($where)->field('id,name')->select();
                S('resultL', $resultL);
            } else {
                $resultL = S('resultL');
            }
            if (empty(S('resultsM'))) {
                $where['pinyin'] = array('like', 'M%');
                $resultM = M('districts')->where($where)->field('id,name')->select();
                S('resultM', $resultM);
            } else {
                $resultM = S('resultM');
            }
            if (empty(S('resultsN'))) {
                $where['pinyin'] = array('like', 'N%');
                $resultN = M('districts')->where($where)->field('id,name')->select();
                S('resultN', $resultN);
            } else {
                $resultN = S('resultN');
            }
            if (empty(S('resultsO'))) {
                $where['pinyin'] = array('like', 'O%');
                $resultO = M('districts')->where($where)->field('id,name')->select();
                S('resultO', $resultO);
            } else {
                $resultO = S('resultO');
            }
            if (empty(S('resultsP'))) {
                $where['pinyin'] = array('like', 'P%');
                $resultP = M('districts')->where($where)->field('id,name')->select();
                S('resultP', $resultP);
            } else {
                $resultP = S('resultP');
            }
            if (empty(S('resultsQ'))) {
                $where['pinyin'] = array('like', 'Q%');
                $resultQ = M('districts')->where($where)->field('id,name')->select();
                S('resultQ', $resultQ);
            } else {
                $resultQ = S('resultQ');
            }
            if (empty(S('resultsR'))) {
                $where['pinyin'] = array('like', 'R%');
                $resultR = M('districts')->where($where)->field('id,name')->select();
                S('resultR', $resultR);
            } else {
                $resultR = S('resultR');
            }
            if (empty(S('resultsS'))) {
                $where['pinyin'] = array('like', 'S%');
                $resultS = M('districts')->where($where)->field('id,name')->select();
                S('resultS', $resultS);
            } else {
                $resultS = S('resultS');
            }
            if (empty(S('resultsT'))) {
                $where['pinyin'] = array('like', 'T%');
                $resultT = M('districts')->where($where)->field('id,name')->select();
                S('resultT', $resultT);
            } else {
                $resultT = S('resultT');
            }
            if (empty(S('resultsU'))) {
                $where['pinyin'] = array('like', 'U%');
                $resultU = M('districts')->where($where)->field('id,name')->select();
                S('resultU', $resultU);
            } else {
                $resultU = S('resultU');
            }
            if (empty(S('resultsV'))) {
                $where['pinyin'] = array('like', 'V%');
                $resultV = M('districts')->where($where)->field('id,name')->select();
                S('resultV', $resultV);
            } else {
                $resultV = S('resultV');
            }
            if (empty(S('resultsW'))) {
                $where['pinyin'] = array('like', 'W%');
                $resultW = M('districts')->where($where)->field('id,name')->select();
                S('resultW', $resultW);
            } else {
                $resultW = S('resultW');
            }
            if (empty(S('resultsX'))) {
                $where['pinyin'] = array('like', 'X%');
                $resultX = M('districts')->where($where)->field('id,name')->select();
                S('resultX', $resultX);
            } else {
                $resultX = S('resultX');
            }
            if (empty(S('resultsY'))) {
                $where['pinyin'] = array('like', 'Y%');
                $resultY = M('districts')->where($where)->field('id,name')->select();
                S('resultY', $resultY);
            } else {
                $resultY = S('resultY');
            }
            if (empty(S('resultsZ'))) {
                $where['pinyin'] = array('like', 'Z%');
                $resultZ = M('districts')->where($where)->field('id,name')->select();
                S('resultZ', $resultZ);
            } else {
                $resultZ = S('resultZ');
            }
            echo json_encode(array('A'=>$resultA,'B'=>$resultB,'C'=>$resultC,'D'=>$resultD,'E'=>$resultE,'F'=>$resultF,'G'=>$resultG,
                'H'=>$resultH,'I'=>$resultI,'J'=>$resultJ,'K'=>$resultK,'L'=>$resultL,'M'=>$resultM,'N'=>$resultN,'O'=>$resultO,
                'P'=>$resultP,'Q'=>$resultQ,'R'=>$resultR,'S'=>$resultS,'T'=>$resultT,'U'=>$resultU,'V'=>$resultV,'W'=>$resultW,
                'X'=>$resultX,'Y'=>$resultY,'Z'=>$resultZ,));exit;
//            $this->assign('resultA', $resultA);
//            $this->assign('resultB', $resultB);
//            $this->assign('resultC', $resultC);
//            $this->assign('resultD', $resultD);
//            $this->assign('resultE', $resultE);
//            $this->assign('resultF', $resultF);
//            $this->assign('resultG', $resultG);
//            $this->assign('resultH', $resultH);
//            $this->assign('resultI', $resultI);
//            $this->assign('resultJ', $resultJ);
//            $this->assign('resultK', $resultK);
//            $this->assign('resultL', $resultL);
//            $this->assign('resultM', $resultM);
//            $this->assign('resultN', $resultN);
//            $this->assign('resultO', $resultO);
//            $this->assign('resultP', $resultP);
//            $this->assign('resultQ', $resultQ);
//            $this->assign('resultR', $resultR);
//            $this->assign('resultS', $resultS);
//            $this->assign('resultT', $resultT);
//            $this->assign('resultU', $resultU);
//            $this->assign('resultV', $resultV);
//            $this->assign('resultW', $resultW);
//            $this->assign('resultX', $resultX);
//            $this->assign('resultY', $resultY);
//            $this->assign('resultZ', $resultZ);
        }else{

        }
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
//            $where['name'] = array('notlike','%区');
            $result = M('districts')->where($where)->limit(5)->select();
            count($result);
            echo json_encode($result);exit;
        }else{
//            echo "有中文，或者数字，特殊符号存在！";
//            $where['merger_name'] = array('like','%'.$querystring.'%');
//            $where['name'] = array(array('like','%'.$querystring.'%'),array('notlike','%区'),'and');
            $where['name'] =array('like','%'.$querystring.'%');
            $result = M('districts')->where($where)->limit(5)->select();
            echo json_encode($result);exit;
        }
    }

}