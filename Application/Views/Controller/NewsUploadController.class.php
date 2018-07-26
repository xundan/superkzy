<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/8
 * Time: 11:14
 */
namespace Views\Controller;


use Think\Controller;
use Think\Log;

class NewsUploadController extends Controller
{
    const SUCCESS = 1;
    const FAIL = -1;

    public function test()
    {
        $display_user['invalid_id'] = array('neq', 1);
        $result = M('ck_news_type')->where($display_user)->order('id desc')->select();
        dump($result);
    }

    //新闻录入
    public function news_submit()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $data['title'] = $subInfo['title'];
        $data['name'] = $subInfo['name'];
        $data['content_1'] = $subInfo['content_1'];
        $data['content_2'] = $subInfo['content_2'];
        $data['content_3'] = $subInfo['content_3'];
        $data['content_4'] = $subInfo['content_4'];
        $data['content_5'] = $subInfo['content_5'];
        $data['img_1'] = $subInfo['img_1'];
        $data['img_2'] = $subInfo['img_2'];
        $data['img_3'] = $subInfo['img_3'];
        $data['img_4'] = $subInfo['img_4'];
        $data['img_5'] = $subInfo['img_5'];
        $data['img_6'] = $subInfo['img_6'];
        if ($subInfo['keywords'] == "content_1") {
            $data['invalid_id'] = 2;
        } else if ($subInfo['keywords'] == "content_2") {
            $data['invalid_id'] = 3;
        } else if ($subInfo['keywords'] == "content_3") {
            $data['invalid_id'] = 4;
        } else if ($subInfo['keywords'] == "content_4") {
            $data['invalid_id'] = 5;
        } else if ($subInfo['keywords'] == "content_5") {
            $data['invalid_id'] = 6;
        }
        $result = M('ck_news_type')->add($data);
        if ($result) {
            echo 'success';
        } else {
            echo 'failure';
        }
    }

    //新闻记录查询
    public function news_action()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $where = [];
        $returnUser = [];
        $returnArr = array();
        $returnArr['data'] = array();
        $result = M('ck_news_type')->where($where)->order('id desc')->select();
        if ($result) {
            foreach ($result as $item) {
                if ($item['invalid_id'] != 1) {
                    $returnUser['id'] = $item['id'];
                    $returnUser['title'] = $item['title'];
                    $returnUser['name'] = $item['name'];
                    $returnUser['record_time'] = $item['record_time'];
                    $returnArr['msg'] = 'yes';
                    array_push($returnArr['data'], $returnUser);
                }
            }
        } else {
            $returnArr['msg'] = 'no';
        }
        echo json_encode($returnArr);
        return;
    }

    //新闻记录文字板块修改
    public function revise_action()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $where = [];
        $where['id'] = $subInfo['id'];
        $result = M('ck_news_type')->where($where)->find();
        if ($result) {
            $temp = array();
            $temp['id'] = $subInfo['id'];
            $temp[$subInfo['sel']] = $subInfo['content'];
            $result_revise = M('ck_news_type')->save($temp);
            if ($result_revise) {
                echo 'success';
            } else {
                echo 'failure';
            }
        } else {
            echo 'failure';
        }
    }

    //新闻记录关键板块修改
    public function key_action()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $where = [];
        $where['id'] = $subInfo['id'];
        $result = M('ck_news_type')->where($where)->find();
        if ($result) {
            $temp = array();
            $temp['id'] = $subInfo['id'];
            if ($subInfo['sel_key'] == null) {
                $temp['invalid_id'] = 0;
            } else if ($subInfo['sel_key'] == "content_1") {
                $temp['invalid_id'] = 2;
            } else if ($subInfo['sel_key'] == "content_2") {
                $temp['invalid_id'] = 3;
            } else if ($subInfo['sel_key'] == "content_3") {
                $temp['invalid_id'] = 4;
            } else if ($subInfo['sel_key'] == "content_4") {
                $temp['invalid_id'] = 5;
            } else if ($subInfo['sel_key'] == "content_5") {
                $temp['invalid_id'] = 6;
            }
            $result_revise = M('ck_news_type')->save($temp);
            if ($result_revise) {
                echo 'success';
            } else {
                echo 'failure';
            }
        } else {
            echo 'failure';
        }
    }

    //新闻记录图片板块修改
    public function reviseImg_action()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $where = [];
        $where['id'] = $subInfo['id'];
        $result = M('ck_news_type')->where($where)->find();
        if ($result) {
            $temp = array();
            $temp['id'] = $subInfo['id'];
            $temp[$subInfo['sel_img']] = $subInfo['img'];
            $result_revise = M('ck_news_type')->save($temp);
            if ($result_revise) {
                echo 'success';
            } else {
                echo 'failure';
            }
        } else {
            echo 'failure';
        }
    }

    //新闻记录删除
    public function del_action()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $where = [];
        $where['id'] = $subInfo['id'];
        $result = M('ck_news_type')->where($where)->find();
        if ($result) {
            $del = array();
            $del['id'] = $subInfo['id'];
            $del['invalid_id'] = 1;
            $result_revise = M('ck_news_type')->save($del);
            if ($result_revise) {
                echo 'success';
            } else {
                echo 'failure';
            }
        }
    }

//    public function show(){
//        vendor("jssdk.signPackage");
//        $this->assign("signPackage", getSignPackage());
//        $this->display();
//    }


    public function testAction()
    {
        $file = $_FILES['file'];
        $result = $this->upload($file);
        echo $result;
    }


    public function upload($file, $path = './Public/NewsUpload/')
    {
        if ($file) {
            $uploadPath = $this->setUploadPath($path, 'content');
            Log::record('###uploadPath###' . $uploadPath);
            $typeArr = explode('/', $file['type']);
            if ($typeArr[0] == 'image') {
                //图片文件
                $result = $this->saveImage($file, $uploadPath);
                Log::record('###ImagePath###' . $result);
            } else {
                $result = $this->saveFile($file, $uploadPath);
            }
            if ($result) {
                return $result;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function setUploadPath($path, $customDirName = '')
    {
        if ($customDirName) {
            $dirPath = $path . $customDirName . '/';
            if (!is_dir($dirPath)) {
                //当路径不存在时创建路径
                mkdir($dirPath);
            }
        } else {
            $dirPath = $path;
            if (!is_dir($dirPath)) {
                //当路径不存在时创建路径
                mkdir($dirPath);
            }
        }
        return $dirPath;
    }


    public function picUpload()
    {
        $url = $this->saveFile($_FILES['file']);
        $result['url'] = $url;
        echo json_encode($result);
    }

    private function saveImage($file, $uploadPath, $customFileName = '', $thumb = false)
    {
        if ($customFileName) {
            $fileNameArr = explode('.', $file['name']);
            $fileName = $customFileName . '.' . $fileNameArr[1];
        } else {
            $fileName = $file['name'];
        }
        $fileName = iconv('utf-8', 'gb2312', $fileName);
        $filePath = $uploadPath . $fileName;
        if ($thumb) {
//            $thumbName = str_replace('.', '_thumb.', $file['name']);
//            $thumbUrl = $filePath . iconv('utf-8', 'gb2312', $thumbName);
            //需要生成并保存缩略图
            //成功存储后将图片变成thumb图片方便存储
//            $image = new Image();
//            $image->open($url);
//            $image->thumb(75, 75)->save($thumbUrl);
            //把$thumbUrl存入数据库对应头像地址字段
            return false;
        } else {
            if (file_exists($filePath)) {
                //当文件存在
                unlink($filePath);
            }
            //当文件不存在
            $result = move_uploaded_file($file["tmp_name"], $filePath);
            if ($result) {
                return $filePath;
            } else {
                return false;
            }
        }
    }

    private function saveFile($file, $uploadPath)
    {
        $_SESSION['user_id'] = 13;
        $filePath_prefix = "./Public/NewsUpload/";
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


    //新闻微信界面首页展示

    public
    function newsShow()
    {
        $subInfo = I('get.', '', 'trim,strip_tags');
        if ($subInfo['isAjax']) {
            $display_user['invalid_id'] = array('neq', 1);
            $result = M('ck_news_type')->where($display_user)->order('id desc')->select();
            if ($result) {
                $returnArr['msg'] = 'yes';
                $returnArr['data'] = $result;
            } else {
                $returnArr['msg'] = 'no';
            }
        } else {
            $returnArr['msg'] = 'no';
        }
        echo json_encode($returnArr);
    }

    //新闻微信界面新闻内容展示
    public
    function newsContent()
    {
        $subInfo = I('post.', '', 'trim,strip_tags');
        $display_user['id'] = $subInfo ['id'];
        $result = M('ck_news_type')->where($display_user)->select();
        if ($result) {
            $returnArr['msg'] = 'yes';
            $returnArr['data'] = $result;
        } else {
            $returnArr['msg'] = 'no';
        }
        echo json_encode($returnArr);
    }
}
