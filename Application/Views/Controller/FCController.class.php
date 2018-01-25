<?php
/**
 * Created by PhpStorm.
 * User: LX
 * Date: 2018/1/9
 * Time: 15:57
 */

namespace Views\Controller;

use Think\Controller;

class FCController extends Controller
{
    public function showExcel($start_time = null, $end_time = null)
    {
        vendor('PHPExcel.PHPExcel');
        if (!$start_time || !$end_time) {
            echo 'add parameter : ?start_time=&end_time=';
            exit;
        }

        // Create new PHPExcel object
        $objPHPExcel = new \PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("LX")
            ->setLastModifiedBy("LX")
            ->setTitle("FC Document")
            ->setSubject("FC Document")
            ->setDescription("FreightCaculation for Excel, generated using PHP classes.")
            ->setKeywords("office PHPExcel php FC FreightCaculation")
            ->setCategory("result file");
        // Add Data
        $objPHPExcel->getActiveSheet()
            ->setCellValue('A1', 'id')
            ->setCellValue('B1', '起始地')
            ->setCellValue('C1', '目的地')
            ->setCellValue('D1', '距离')
            ->setCellValue('E1', '运费');

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth('15');
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth('21');
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth('12');

        $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFont()->setSize(14);
        $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFont()->setBold(true);

//        $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFill()->setFillType('solid');
//        $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getFill()->getStartColor()->setARGB('FF7CCD7C');
        $objPHPExcel->getActiveSheet()->getStyle('A1:E1')->getAlignment()->setHorizontal('center')->setVertical('center');

        if ($area_name == '鄂尔多斯') {
            $cm = D('Views/CoalPriceMessage')->query("SELECT * from `ck_coal_price_message` WHERE `area_name` = '鄂尔多斯' AND `invalid_id` = 0 AND `update_time` > DATE_SUB(CURDATE(),INTERVAL 7 DAY) ORDER BY CONVERT(`refinery_name` USING gbk)");
        } elseif ($area_name == '山西') {
            $cm = D('Views/CoalPriceMessage')->query("SELECT * from `ck_coal_price_message` WHERE `area_name` = '山西' AND `invalid_id` = 0 AND `update_time` > DATE_SUB(CURDATE(),INTERVAL 7 DAY) ORDER BY CONVERT(`refinery_name` USING gbk)");
        } elseif ($area_name == '神木') {
            $cm = D('Views/CoalPriceMessage')->query("SELECT * from `ck_coal_price_message` WHERE `area_name` = '神木' AND `invalid_id` = 0 AND `update_time` > DATE_SUB(CURDATE(),INTERVAL 7 DAY) ORDER BY CONVERT(`refinery_name` USING gbk)");
        } elseif ($area_name == '榆阳') {
            $cm = D('Views/CoalPriceMessage')->query("SELECT * from `ck_coal_price_message` WHERE `area_name` = '榆阳' AND `invalid_id` = 0 AND `update_time` > DATE_SUB(CURDATE(),INTERVAL 7 DAY) ORDER BY CONVERT(`refinery_name` USING gbk)");
        } elseif ($area_name == '府谷') {
            $cm = D('Views/CoalPriceMessage')->query("SELECT * from `ck_coal_price_message` WHERE `area_name` = '府谷' AND `invalid_id` = 0 AND `update_time` > DATE_SUB(CURDATE(),INTERVAL 7 DAY) ORDER BY CONVERT(`refinery_name` USING gbk)");
        } elseif ($area_name == '横山') {
            $cm = D('Views/CoalPriceMessage')->query("SELECT * from `ck_coal_price_message` WHERE `area_name` = '横山' AND `invalid_id` = 0 AND `update_time` > DATE_SUB(CURDATE(),INTERVAL 7 DAY) ORDER BY CONVERT(`refinery_name` USING gbk)");
        } else {
            $cm = D('Views/CoalPriceMessage')->query("SELECT * from `ck_coal_price_message` WHERE `area_name` NOT IN ('神木','横山','榆阳','鄂尔多斯','府谷','山西') AND `invalid_id` = 0 AND `update_time` > DATE_SUB(CURDATE(),INTERVAL 7 DAY) ORDER BY CONVERT(`refinery_name` USING gbk)");
        }

        $row = 3;
        $col = 3;
        $objPHPExcel->getActiveSheet()->mergeCells('A2:G2');
        $objPHPExcel->getActiveSheet()->setCellValue('A2', $area_name);
        $objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(18);
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getFill()->setFillType('solid');
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getFill()->getStartColor()->setARGB('FFFFFF00');
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal('center')->setVertical('center');
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setSize(14);
        $objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
        foreach ($cm as $cmKey => $cmItem) {
            $cc = D('Views/CoalPriceContent')->where(array('message_id' => $cmItem['message_id']))->select();
            //High efficient but not reset index
            $ccValid = array_filter($cc, function ($ccItem) {
                return $ccItem['kind_name'] ? true : false;
            });
            $ccValid = array_values($ccValid);
            $colNumber = count($ccValid);
            $objPHPExcel->getActiveSheet()->mergeCells('A' . $col . ':A' . ($col + $colNumber - 1));
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $col, $cmItem['refinery_name']);
            foreach ($ccValid as $ccValidKey => $ccValidItem) {
                $objPHPExcel->getActiveSheet()->setCellValue('B' . ($col + $ccValidKey), $ccValidItem['kind_name']);
                $objPHPExcel->getActiveSheet()->getStyle('B' . ($col + $ccValidKey))->getAlignment()->setHorizontal('center');
                $objPHPExcel->getActiveSheet()->setCellValue('F' . ($col + $ccValidKey), ($ccValidItem['price'] && $ccValidItem['price'] > 0) ? $ccValidItem['price'] : '-');
                $objPHPExcel->getActiveSheet()->getStyle('F' . ($col + $ccValidKey))->getAlignment()->setHorizontal('center');
                $objPHPExcel->getActiveSheet()->setCellValue('G' . ($col + $ccValidKey), ($ccValidItem['tax'] && $ccValidItem['tax'] > 0) ? $ccValidItem['tax'] : '-');
                $objPHPExcel->getActiveSheet()->getStyle('G' . ($col + $ccValidKey))->getAlignment()->setHorizontal('center');
                $cdi = D('Views/CoalPriceDetailedIndex')->where(array('content_id' => $ccValidItem['content_id']))->select();
                foreach ($cdi as $cdiItem) {
                    if ($cdiItem['index_name'] == '热量') {
                        $objPHPExcel->getActiveSheet()->setCellValue('C' . ($col + $ccValidKey), $cdiItem['index_value'] ? $cdiItem['index_value'] : '-');
                        $objPHPExcel->getActiveSheet()->getStyle('C' . ($col + $ccValidKey))->getAlignment()->setHorizontal('center');
                    } elseif ($cdiItem['index_name'] == '硫') {
                        $objPHPExcel->getActiveSheet()->setCellValue('D' . ($col + $ccValidKey), $cdiItem['index_value'] ? $cdiItem['index_value'] : '-');
                        $objPHPExcel->getActiveSheet()->getStyle('D' . ($col + $ccValidKey))->getAlignment()->setHorizontal('center');
                    } elseif ($cdiItem['index_name'] == '灰分') {
                        $objPHPExcel->getActiveSheet()->setCellValue('E' . ($col + $ccValidKey), $cdiItem['index_value'] ? $cdiItem['index_value'] : '-');
                        $objPHPExcel->getActiveSheet()->getStyle('E' . ($col + $ccValidKey))->getAlignment()->setHorizontal('center');
                    }
                }
            }
            $objPHPExcel->getActiveSheet()->getStyle('A' . $col)->getAlignment()->setVertical('center')->setHorizontal('center');
            $col = $col + $colNumber;
        }

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Simple');

        $objPHPExcel->getActiveSheet()->setAutoFilter($objPHPExcel->getActiveSheet()->calculateWorksheetDimension());

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
//        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $area_name . '.xls"');
//        header('Content-Disposition: attachment;filename=test.xlsx');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
//        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
//        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
//        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
//        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
//        header('Pragma: public'); // HTTP/1.0

//        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);
//        ob_end_clean();
//        $objWriter->save('php://output');

        $objWriter = new \PHPExcel_Writer_Excel5($objPHPExcel);
        $objWriter->save('php://output');
        exit;
    }

    public function getGeocoder($address)
    {
//        $address = '中国,四川省,绵阳市,三台县';
        $resultJson = file_get_contents("http://api.map.baidu.com/geocoder/v2/?address=" . $address . "&output=json&ak=jTS6yYwHSCiR6xoB0ohTbv8EYHaA8iX2");
        $resultArea = json_decode($resultJson, JSON_UNESCAPED_UNICODE);
        if ($resultArea['status'] == 0) {
            $geocoder = $resultArea['result']['location']['lat'] . "," . $resultArea['result']['location']['lng'];
        } else {
            $geocoder = false;
        }
        return $geocoder;
//        return $resultArea;
    }

    public function getDistance($origins = null, $destinations = null, $tactics = 11)
    {
        $resultJson = file_get_contents("http://api.map.baidu.com/routematrix/v2/driving?output=json&origins=" . $origins . "&destinations=" . $destinations . "&ak=jTS6yYwHSCiR6xoB0ohTbv8EYHaA8iX2&tactics=" . $tactics);
        $resultDistance = json_decode($resultJson, JSON_UNESCAPED_UNICODE);
        if ($resultDistance['status'] == 0) {
            $distance = $resultDistance['result'][0]['distance']['value'];
        } else {
            $distance = null;
        }
        return $distance;
//        return $resultDistance['result'];
    }

    public function getDistanceByAddress($start, $end)
    {
        $origins = $this->getGeocoder($start);
        $destinations = $this->getGeocoder($end);
        $distance = $this->getDistance($origins,$destinations);
        return $distance;
    }

    public function test()
    {
//        $a = $this->getGeocoder('济南市历下区山大路231号');
//        header("Content-Type:text/html; charset=utf-8");
//        dump($a);
//        $b = $this->getGeocoder('崇仁县巴山镇');
//        dump($b);
//        $c = $this->getGeocoder('中国,四川省,绵阳市,三台县');
//        dump($c);
//        $r = $this->getDistance($a, $b);
//        dump($r);
//
//        if(0){dump(2);}

//        $t = $this->getGeocoder('中国,陕西省,榆林市,神木县,凉水井煤矿');
//        dump($t);
        $start = '中国,新疆维吾尔自治区,阿克苏地区,库车县';
        $end = '中国,青海省,海西蒙古族藏族自治州,格尔木市';
        $origins = $this->getGeocoder($start);
        dump($origins);
        $destinations = $this->getGeocoder($end);
        dump($destinations);
        $distance = $this->getDistance($origins,$destinations);
        dump($distance);
//        dump($this->getDistanceByAddress($origins,$destinations));
    }

    public function tempSetDistance(){
        $where['distance'] = 0;
        $resultFreight = M('ck_freight')->where($where)->select();
        foreach($resultFreight as $item){
            dump($item['id']);
            $data['distance'] = $this->getDistanceByAddress(trim($item['area_start_merger_name']),trim($item['area_end_merger_name']));
            $data['id'] = $item['id'];
            M('ck_freight')->save($data);
        }
    }
}