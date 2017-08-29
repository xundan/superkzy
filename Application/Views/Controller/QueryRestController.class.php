<?php
/**
 * Created by PhpStorm.
 * User: CLEVO
 * Date: 2017/6/13
 * Time: 19:36
 */

namespace Views\Controller;


use Views\Model\QueryModel;
use Views\Model\MessageModel;
use Think\Controller\RestController;

class QueryRestController extends RestController
{
    protected $allowMethod = array('get', 'post'); // REST允许的请求类型列表
    protected $allowType = array('json'); // REST允许请求的资源类型列表

//    public function _initialize(){
//        S(array('type'=>'file','expire'=>60));
//    }

    //记录一条历史消息
    Public function q_text()
    {
        $data = $this->defaultResponse();
        switch ($this->_method) {
            case 'get': // get请求处理代码

//                echo "Cache is ".S("a")."! <br>";
//                S("a",time()%10);

                $this->defaultGetAction();
                break;
            case 'post': // post请求处理代码
                $object2 = $this->decodeJSONFromBody();
//                print_r($object2);
                if ($object2) {
                    //测试样本“查询”： {"kw":"\u67e5\u8be2","user":"guest01","self":"isme"}
                    //测试样本“求购”： {"kw":"\u6c42\u8d2d","user":"guest01","self":"isme"}
                    $kw = $object2['kw'];
                    $user = $object2['user'];
                    $self = $object2['self'];

                    // 融合过程
                    $cc = $this->checkCompleteness($kw);

                    $Query = new QueryModel();
                    if ($cc < 0) { // 有类型才会融合，无类型直接查

                        if ($cc == -2) { // 类型直接融合不要查
                            $Query->saveIntroRecord($kw, $user, $self);
                        } else {

                            $fused_kw = $this->fuseHistory($kw, $user, $self);
                            if ($fused_kw == $kw) { // 说明融合失败
                                // 把这条存为待融合数据
                                $Query->saveIntroRecord($kw, $user, $self);
                            } else {
                                $kw = $fused_kw;
//                                此处要在验证一次，如果完备存，不完备更新
                            }
                        }

                    } else {
                        $Query->saveRecord($kw, $user, $self);
                    }

                    $kw .= $cc;

                    $result = $this->q_text_do($kw);
                    if ($result) {
                        $data['result_code'] = "201";
                        $data['reason'] = "获取成功";
                        $data['error_code'] = "0";
                        $data['message_id'] = null;
                        $data['result'] = $result;
                    } else {
                        $data = $this->dbErrorResponse($data);
                    }
                } else {
                    $data = $this->internalErrorResponse($data);
                }

                $this->response($data, 'json');
                break;
        }
    }

    /**
     * 融合上一次查询
     * @param $kw
     * @return int -2类型，-1指标不全待融合（纯数字），1完整可直接查
     */
    private function checkCompleteness($kw)
    {

        // 只允许30长度以下的字符串查询
        if (strlen($kw) > 30) {
//            $kw=substr($kw,0,30);
            return 1;
        }

        $category_pattern = "/(求购|供应|求车|车源)/";

        $match = array();
        $res = preg_match_all($category_pattern, $kw, $match);
        if ($res) {
            return -2; // 必须和其他融合
        }

        // (\d+),(热量|水分|挥发)
        return -1; // 待融合
    }


    /**
     * 融合数据库的额历史信息
     * @param $kw
     * @param $user
     * @param $self
     * @return string
     */
    private function fuseHistory($kw, $user, $self)
    {

        $Query = new QueryModel();
        $new_kw = $kw;

        $last_query = $Query->findLast($user, $self);

        if ($last_query['remark'] == 'intro') {
            return $last_query['last_record']." ".$new_kw;
        } else
            return $new_kw;

    }


    /**
     * 以文本形式返回查询结果
     * @param null $kw
     * @return bool|string
     */
    public function q_text_do($kw = null)
    {
        if ($kw === null) {

        } else {
            $data = $this->prepareList($kw, 1);

            $res = "$kw:\n\n";
            foreach ($data as $row) {
                $content = $this->formatRow($row);
                $res .= $content . "\n\n";
            }
//            echo $res;
            return $res;
        }
        return false;
    }

    /**
     * 规范化化每行数据成文本
     * @param $row
     * @return mixed
     */
    private function formatRow($row)
    {
        $content = "web" . $row['id'];
        if ($row['type'] == 'web') {
            // TODO 规范平台网站数据
        } else {
            $content = $row['content'];
        }
        return $content;
    }

    /**
     * 查询输入框输入内容整理
     * @param $str string       输入字符串
     * @return string           替换分隔符后返回的字符串
     */
    private function arrangeInput($str)
    {
        $tempStr = trim($str);
        $tempStr = preg_replace("/[\\s,，]{1,}/", " ", $tempStr);

        return $tempStr;
    }

    /**
     * 按照正则取出符合正则的关键字数组，并返回，并且把源字符串作删除关键字处理
     * @param $kwString string 输入字符串，注意这里传的是指针，会把改动保存下来
     * @param $pattern string 关键字
     * @return array 截取关键字的数组
     */
    public function extractParam(&$kwString, $pattern)
    {
        $kwString = $this->arrangeInput($kwString);
        $category_pattern = $pattern;
        $category = array();
        $match = array();
        $res = preg_match_all($category_pattern, $kwString, $match);
        if ($res) {
            $category = $match[0];
        }
        $kwString = trim(preg_replace($category_pattern, "", $kwString));
        return $category;
    }

    /**
     * 检查完备性
     * @param $kw
     * @param $page
     * @return mixed array:回查数组列表；-1,无任何指标；-2无类型指标
     */
    private function prepareList($kw, $page)
    {
        $str = $kw;

        // 剥离关键字
        $categoryArr = $this->extractParam($str, "/(求购|供应|求车|车源)/");
        $granularityArr = $this->extractParam($str, "/(块煤|原煤|籽煤|沫煤|面煤)/");
        // 所有关键字
        $kindArr = $this->extractParam($str, "/(动力煤|喷吹煤|炼焦煤|焦炭|气化煤|煤泥|气煤|电煤)/");
        $digitsArr = $this->extractParam($str, "/(\\d+)/");
        $residue = $str;

        $mergedArr = array_merge($granularityArr, $kindArr, $digitsArr);


        $Msg = new MessageModel();

        // 如果审核细化，可以打开并修改此方法：
//            $data = $Msg->selectQuery($categoryArr,$granularityArr,$kindArr,$digitsArr);
        $data = $Msg->selectSearch($categoryArr, $mergedArr, $page);

        return $data;
    }


    /**
     * @param null $input
     * @return array 查询结果
     */
    public function filtered_result($input = null)
    {
        $subInfo = I('post.', '', 'trim');
//        dump($subInfo);
        if(!$input){
            $input = $subInfo;
        }
        //类别筛选
        $resultGranularity = array();
        $whereGranularity = array();
        $whereGranularity['kind_filter'] = array('in', $input['filter_granularity']);
        $resultGranularity = M('coal_price_content')->field('content_id,message_id,kind_name,kind_filter')->where($whereGranularity)->select();
//        dump(M()->getLastSql());
//        dump($resultGranularity);
        $resultGranularityContent = array();
        foreach ($resultGranularity as $v) {
            array_push($resultGranularityContent, $v['content_id']);
        }
//        dump($resultGranularityContent);

        //热值筛选
        $resultHeatValue = array();
        $whereHeatValue = array();
        if ($input['filter_heat_min'] || $input['filter_heat_max']) {
            $whereHeatValue['index_name'] = '热量';
            $whereHeatValue['index_value'] = array();
            if ($input['filter_heat_min']) {
                array_push($whereHeatValue['index_value'], array('egt', $input['filter_heat_min']));
            }
            if ($input['filter_heat_max']) {
                array_push($whereHeatValue['index_value'], array('elt', $input['filter_heat_max']));
            }
            array_push($whereHeatValue['index_value'], array('gt', 0));
            array_push($whereHeatValue['index_value'], 'and');
        }
        $resultHeatValue = M('coal_price_detailed_index')->where($whereHeatValue)->group('content_id')->select();
//        dump(M()->getLastSql());
//        dump($resultHeatValue);
        $resultHeatValueContent = array();
        foreach ($resultHeatValue as $v) {
            array_push($resultHeatValueContent, $v['content_id']);
        }
//        dump($resultHeatValueContent);

        //硫筛选
        $resultSulfur = array();
        $whereSulfur = array();
        if ($input['filter_sulfur']) {
            $whereSulfur['index_name'] = '硫';
            $whereSulfur['index_value'] = array();
            foreach ($input['filter_sulfur'] as $v) {
                if ($v == 1) {
                    array_push($whereSulfur['index_value'], array('between', array(0, 0.5)));
                } else if ($v == 2) {
                    array_push($whereSulfur['index_value'], array('between', array(0.5, 1.5)));
                } else if ($v == 3) {
                    array_push($whereSulfur['index_value'], array('between', array(1.5, 2.5)));
                } else if ($v == 4) {
                    array_push($whereSulfur['index_value'], array('gt', 2.5));
                }
            }
            array_push($whereSulfur['index_value'], 'or');
            $whereSulfur['index_value'] = array(array('neq', 0), $whereSulfur['index_value']);
        };
        $resultSulfur = M('coal_price_detailed_index')->field('content_id')->where($whereSulfur)->group('content_id')->select();
//        dump(M()->getLastSql());
//        dump($resultSulfur);
        $resultSulfurContent = array();
        foreach ($resultSulfur as $v) {
            array_push($resultSulfurContent, $v['content_id']);
        }
//        dump($resultSulfurContent);

        //挥发筛选
        $resultVolatile = array();
        $whereVolatile = array();
        if ($input['filter_volatile']) {
            $whereVolatile['index_name'] = '挥发';
            $whereVolatile['index_value'] = array();
            foreach ($input['filter_volatile'] as $v) {
                if ($v == 1) {
                    array_push($whereVolatile['index_value'], array('between', array(0, 10)));
                } else if ($v == 2) {
                    array_push($whereVolatile['index_value'], array('between', array(10, 20)));
                } else if ($v == 3) {
                    array_push($whereVolatile['index_value'], array('between', array(20, 30)));
                } else if ($v == 4) {
                    array_push($whereVolatile['index_value'], array('gt', 30));
                }
            }
            array_push($whereVolatile['index_value'], 'or');
            $whereVolatile['index_value'] = array(array('neq', 0), $whereVolatile['index_value']);
        };
        $resultVolatile = M('coal_price_detailed_index')->field('content_id')->where($whereVolatile)->group('content_id')->select();
//        dump(M()->getLastSql());
//        dump($resultVolatile);
        $resultVolatileContent = array();
        foreach ($resultVolatile as $v) {
            array_push($resultVolatileContent, $v['content_id']);
        }
//        dump($resultVolatileContent);

        //取所有筛选条件的交集，找出类别content_id
        $resultContent = array_intersect($resultGranularityContent, $resultHeatValueContent, $resultSulfurContent, $resultVolatileContent);
//        dump($resultContent);

        //判断交集是否有值
        if ($resultContent) {
            //从类别表中选取所筛选出来的类别
            $whereContent['content_id'] = array('in', $resultContent);
            $result = M('coal_price_content')->where($whereContent)->select();

            //返回查询到的值
            $returnArray = array();
            //将该类别的详细指标插入，并统一以message_id作为键名分组
            foreach ($result as $k => $v) {
                $cdi = M('coal_price_detailed_index')->where(array('content_id' => $v['content_id']))->select();
                $v['detailed_index'] = $cdi;
                $returnArray[$v["message_id"]]['content'][] = $v;
            }
//        dump($returnArray);
            //将该键名为message_id的矿信息插入结果数组
            foreach ($returnArray as $k => $v) {
                $whereMsg['message_id'] = $k;
                $whereMsg['invalid_id'] = 0;
                $resultMsg = M('coal_price_message')->where($whereMsg)->find();
                $returnArray[$k]['msg'] = $resultMsg;
            }
//            dump($returnArray);
            return $returnArray;
        } else {
            return -1;
        }

    }


    /**
     * @return array
     */
    private function defaultResponse()
    {
        $data = array(
            "result_code" => "105",
            "reason" => "应用未审核超时，请提交认证",
            "result" => null,
            "message_id" => null,
            "error_code" => 10005,
        );
        return $data;
    }

    private function defaultGetAction()
    {

        if ($this->_type == 'html') {
            echo 'html';
        } elseif ($this->_type == 'xml') {
            echo 'xml';
        }
        echo '<br>restful url is correct.';
    }

    /**
     * @return mixed
     */
    private function decodeJSONFromBody()
    {
//        $result1 = $GLOBALS['HTTP_RAW_POST_DATA'];
        $result1 = file_get_contents("php://input");;
        $object2 = json_decode($result1, true);
        return $object2;
    }

    /**
     * @param $data
     * @return mixed
     */
    private function dbErrorResponse($data)
    {
        // TODO 数据库操作失败，通知开发人员
        $data['result_code'] = "106";
        $data['reason'] = "数据库操作错误";
        $data['error_code'] = 10006;
        $data['message_id'] = "";
        $data['result'] = "";
        return $data;
    }

    /**
     * @param $data
     * @return mixed
     */
    private function internalErrorResponse($data)
    {
        //TODO 数据为空，网络错误，客户端错误，通知开发人员
        $data['result_code'] = "500";
        $data['reason'] = "内部错误";
        $data['message_id'] = null;
        $data['error_code'] = 10500;
        $data['result'] = null;
        return $data;
    }

    /**
     * 字符不合法的反馈
     * @param $data
     * @return mixed
     */
    private function illegalKWResponse($data)
    {
        $data['result_code'] = "300";
        $data['reason'] = "无效关键字";
        $data['message_id'] = null;
        $data['error_code'] = 10300;
        $data['result'] = null;
        return $data;
    }

    /**
     * 字符串太长的反馈，暂时没用
     * @param $data
     * @return mixed
     */
    private function tooLongKWResponse($data)
    {
        $data['result_code'] = "301";
        $data['reason'] = "关键字过长";
        $data['message_id'] = null;
        $data['error_code'] = 10301;
        $data['result'] = null;
        return $data;
    }
}