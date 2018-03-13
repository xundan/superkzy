<?php

/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2017/12/4
 * Time: 10:59
 */

namespace Home\Controller;

use Think\Controller;
use Think\Image;

//header('Content-type:text/html;charset=utf-8');

class VoteController extends Controller
{
    /**
     * 被骗经历展示并投票页面
     */
    public function cheatedExp()
    {
        $data = json_decode($this->get_php_file("vote.php"), true);
        if ($data) {
            $this->assign('voteData', $data);
        }
        vendor("jssdk.signPackage");
        $this->assign("signPackage", getSignPackage());
        $this->display();
    }

    /**
     * 投票回调处理
     */
    public function voteSub()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        if ($subInfo) {
            $data = json_decode($this->get_php_file("vote.php"), true);
            if ($data && $data[$subInfo['id'] - 1]) {
                $data[$subInfo['id'] - 1]['num']++;
                $this->set_php_file('vote.php', json_encode($data));
            } else {
                $data[$subInfo['id'] - 1] = array('num' => 1, 'comment' => '');
                $this->set_php_file('vote.php', json_encode($data));
            }
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * 评论展示
     */
    public function commentShow()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        if ($subInfo['id']) {
            $data = json_decode($this->get_php_file("vote.php"), true);
            if ($data && $data[$subInfo['id'] - 1]) {
                echo $data[$subInfo['id'] - 1]['comment'];
            } else {
                echo 0;
            }
        }
        return;
    }

    /**
     * 评论回调处理
     */
    public function commentSub()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        if ($subInfo['comment']) {
            $data = json_decode($this->get_php_file("vote.php"), true);
            if ($data && $data[$subInfo['id'] - 1]) {
                if ($data[$subInfo['id'] - 1]['comment']) {
                    $data[$subInfo['id'] - 1]['comment'] .= '#' . $subInfo['comment'];
                } else {
                    $data[$subInfo['id'] - 1]['comment'] .= $subInfo['comment'];
                }
                $this->set_php_file('vote.php', json_encode($data));
            } else {
                $data[$subInfo['id'] - 1] = array('num' => 0, 'comment' => $subInfo['comment']);
                $this->set_php_file('vote.php', json_encode($data));
            }
            return 1;
        } else {
            return 0;
        }
    }

    public function test()
    {
//        dump(__ROOT__);
//        $path = "./Public/upload/";
////        dump($path);
//        dump(is_dir("./Public/home/images"));
//        dump(is_dir($path."/13/"));
//        $a = mkdir($path."/13/",0777,true);
//        dump($a);
//        dump(is_dir($path."/13/"));
//        $date = '2018-1-22';
//        dump(date('Y-m-d', strtotime($date) + 24 * 3600));
        dump(date("Y-m-d", time()-24*3600*30));
//        $subInfo = I('post.', '', 'trim,strip_tags');
//        $a = '';
//        foreach($subInfo['vote'] as $item){
//            $a .= '#'.$item;
//        }
//        echo json_encode($a);
    }

    /**
     * 投票结果展示
     */
    public function voteResult()
    {
        $data = $this->get_php_file("vote.php");
        if ($data) {
            $this->assign('voteData', $data);
        }
        $this->display();
    }

    public function voteInfluential()
    {
        vendor("jssdk.signPackage");
        $this->assign("signPackage", getSignPackage());
        $uid = $_SESSION['user_info']['uid'];
//        $whereVoted['uid'] = $uid;
//        $whereVoted['record_time'] = array('gt',date("Y-m-d", time()));
//        $resultVoted = M('fengyun_record_2018_01')->where($whereVoted)->find();
//        if ($resultVoted) {
//            $this->assign('voted', json_encode($resultVoted));
            $this->assign('voted', 'end');
            $voteResult = M('fengyun_2018_01')->select();
            $this->assign('result',json_encode($voteResult));
//        }
//        $resultVoted['vote'] = 2;
//        $resultVoted = array();
//        $this->assign('voted', json_encode($resultVoted));
        $this->display();
    }

    public function voteInfluentialAction()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $voteArr = $subInfo['vote'];
        $data['vote'] = implode(',',$voteArr);
        $data['uid'] = $_SESSION['user_info']['uid'];
        //解决短时间投多票问题
        $whereVerify['uid'] = $_SESSION['user_info']['uid'];
        $whereVerify['record_time'] = array('gt',date("Y-m-d", time()));
        $temp  = M('fengyun_record_2018_01')->where($whereVerify)->find();
        if($temp){
            echo 'yes';
            exit;
        }
        $result = M('fengyun_record_2018_01')->add($data);
        foreach($voteArr as $item){
            $resultUpdate = M('fengyun_2018_01')->where('id = %d',$item)->setInc('vote_count',1);
        }
        if($result){
            echo 'yes';
            exit;
        }else{
            echo 'no';
            exit;
        }
    }

    public function signUp()
    {
//        vendor("jssdk.signPackage");
//        $this->assign("signPackage", getSignPackage());
        $this->display();
    }

    public function signUpAction()
    {
//        header("Content-Type:text/html; charset=utf-8");
        $subInfo = I('post.', '', 'trim,strip_tags');
//        dump($subInfo);
        dump($_FILES['pic']);
        $string = $_FILES['pic']['name'];
        $a = str_replace('.', '_thumb.', $string);
        dump($a);
//        $url = $this->saveFile($_FILES['file']);
//        $result['url'] = $url;
//        echo json_encode($result);
    }

    public function picUpload()
    {
//        echo $_FILES['file']['name'];
        $url = $this->saveFile($_FILES['file']);
        $result['url'] = $url;
        echo json_encode($result);
    }

    private function saveFile($file)
    {
        $_SESSION['user_id'] = 13;
        $filePath_prefix = "./Public/upload/";
        $url = '';
        $thumbUrl = '';
        if (true) {
            //文件上传限制(大小格式等)
            if ($file["error"] > 0) {
                //获取文件返回错误
                $thumbUrl = 'file error';
            } else {
                //自定义文件名称
                $fileType = $_FILES["file"]["type"];
                $fileType = explode("/", $fileType);
                //自定义文件名（测试的时候中文名会操作失败）
                if (!is_dir($filePath_prefix . $_SESSION["user_id"])) {
                    //当路径不存在时创建路径
                    mkdir($filePath_prefix . $_SESSION["user_id"]);
                }
                $filePath = $filePath_prefix . $_SESSION["user_id"] . "/";//记录路径
                if (file_exists($url . $file["name"])) {
                    //当文件存在
//                    echo $file["name"] . " already exists. ";
                } else {
                    //当文件不存在
                    $url = $filePath . iconv('utf-8', 'gb2312', $file["name"]);
                    $thumbName = str_replace('.', '_thumb.', $file['name']);
                    $thumbUrl = $filePath . iconv('utf-8', 'gb2312', $thumbName);
                    $result = move_uploaded_file($file["tmp_name"], $url);
                    if ($result) {
                        //成功存储后将图片变成thumb图片方便存储
                        $image = new Image();
                        $image->open($url);
                        $image->thumb(75, 75)->save($thumbUrl);
                        //把$thumbUrl存入数据库对应头像地址字段
                    } else {
                    }
                }
            }
        } else {
            //上传文件不符合规范
            $thumbUrl = 'file invalid';
        }
        return $thumbUrl;
    }

    /**
     * @param $filename string:文件路径
     * @return string string:json数据
     */
    private function get_php_file($filename)
    {
        return trim(substr(file_get_contents($filename), 15));
    }

    /**
     * @param $filename string:文件路径
     * @param $content string:json化数据
     */
    private function set_php_file($filename, $content)
    {
        $fp = fopen($filename, "w");
        fwrite($fp, "<?php exit();?>" . $content);
        fclose($fp);
    }


}