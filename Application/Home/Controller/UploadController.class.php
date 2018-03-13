<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2018/1/26
 * Time: 15:16
 */

namespace Home\Controller;


use Think\Controller;
use Org\Net\Http;

class UploadController extends Controller
{

    public function show(){
        vendor("jssdk.signPackage");
        $this->assign("signPackage", getSignPackage());
        $this->display();
    }

    public function downloadImage($url, $openId, $savePath = './Public/upload/heka/')
    {
        // 用open_id作文件名
        $headPath = $savePath.'hd_'.$openId.".png";
        Http::curlDownload($url, $headPath);
        return 'downloadOK';
    }

    public function imageCompose($templetPath,$punchline,$name,$openId)
    {
        $waterPath = './Public/upload/heka/hd_'.$openId.".png";
        $savePath = './Public/upload/heka/ny_'.$openId.'.jpg';
        $image = new \Think\Image();
        $image->open($templetPath);
        $tffPath = './Public/home/fonts/dn.ttf';
        //$strTo祝福抬头，$strContent祝福语
        $strTo = $name.'祝您：';
//        //$str根据字号字符串截取分成多行贴到图片上去
//        $length = strlen($punchline);
//        //18字一行
//        $rows = (int)$length/8;
        $image->water($waterPath,array(55,930))->text($strTo,$tffPath,28,'#ffd700',array(250,930))
            ->text($punchline,$tffPath,40,'#ffd700',array(280,990))
            //->text()
            ->save($savePath);
    }

    public function test()
    {
        $url = 'http://thirdwx.qlogo.cn/mmopen/VB1ubazPN1cUNOrawXFCQNDkeCgicN4eKUZg4udJYia0lCpmntCKQzOnWYXYQD24HxlMCzAxiceaIz3XcVygsrAxRpib2AIMjlLa/132';
        $a = Http::curlDownload($url, "./Public/upload/heka/headTemp.png");
        dump($a);
//        dump($a);
//        $image = new \Think\Image();
//        $image->open('./Public/home/images/heka/1.jpg');
////        $image->open('./Public/home/images/heka/headTemp.png');
//        $width = $image->width();
//        $height = $image->height();
//        dump($width);
//        dump($height);
////        header("Content-Type:text/html; charset=utf-8");
//        $headPath = './Public/home/images/heka/headTemp.png';
//        $str = '狗年大吉，狗年大吉，狗年大吉，狗年大吉，狗年大吉，狗年大吉，狗年大吉狗年大吉';
//        $str2 = '12345678901234567890123456789';
//        $tffPath = './Public/home/fonts/xgyj.ttf';
//        $savePath = './Public/home/images/heka/media1.jpg';
//        dump(strlen($str));
//        $image->water($headPath,array(55,930))->text($str,$tffPath,14,'#ffffff',array(250,900))
//            ->text($str2,$tffPath,14,'#ffffff',array(250,920))
//            ->save($savePath);
        $this->display();
    }

    public function testQr(){
        $this->display();
    }

    public function testAction()
    {
//        $a = I('post.', '', 'strip,trim');
//        dump($a);
//        dump($_FILES);
//        $b = explode('/', $_FILES['file']['type']);
//        dump($b);
//        dump($b[0]);
//        $c = explode('.', $_FILES['file']['name']);
//        dump($c);
        $file = $_FILES['file'];
        $result = $this->upload($file);
        echo $result;
    }

    public function upload($file, $path = './Public/upload/')
    {
        if ($file) {
            $uploadPath = $this->setUploadPath($path,'group_qrcode');
            $typeArr = explode('/', $file['type']);
            if ($typeArr[0] == 'image') {
                //图片文件
                $result = $this->saveImage($file, $uploadPath,'qrcode');
            }else{
                $result = $this->saveFile($file, $uploadPath);
            }
            if($result){
                return $result;
            }else{
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
        $filePath = $uploadPath.$fileName;
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
}