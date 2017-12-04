<?php
namespace cocolait\helper;
use Sirius\Upload\Handler as UploadHandler;
/**
 * Excel表格处理
 * Created by PhpStorm.
 * User: Cocolait
 * Date: 2017/12/4
 * Time: 15:30
 * 博  客：http://www.mgchen.com
 */
final class CpExcel{
    /**
     * 上传依赖包下载 composer require siriusphp/upload:^2.1
     * 导入Excel文件
     * @param string $input_Name 上传文件名称 示例 <input type="file" name="excel"/>
     * @return array
     * @throws \Exception
     */
    public static function importExcel($input_Name = 'excel') {
        $dir = "./uploads/excel" . "/" . date('Ymd');
        if (!file_exists($dir)) {
            if (!mkdir($dir,0777,true)) {
                return ['error'=>'1','msg'=>"无法创建目录：" . $dir];
            };
        }
        //引入Composer第三方扩展上传类
        $uploadHandler = new UploadHandler($dir);
        $uploadHandler->addRule('extension', ['allowed' => ['xls', 'xlsx']], '只能上传后缀为(.xls, .xlsx)文件');
        $uploadHandler->addRule('size', ['max' => '5M'], '文件最大上传为5M');
        $result = $uploadHandler->process($_FILES[$input_Name]);

        if ($result->isValid()) {
            try {
                $result->confirm(); // 删除后缀.lock文件
                $ext = substr(strrchr($result->name,'.'),1);
                $filename = $dir . "/" . $result->name;
                $data = self::excelFileToArray($filename, $ext);
                if(count($data)==0 && $ext=='xlsx'){
                    return ['error' => 1,'msg'=>'内容为空或文件格式无效,建议转换为xls格式再次重试'];
                }
                //写入文件
                return ['error'=>0,'msg'=> '上传成功 ^_^','url' => $filename,'file_name' => $result->name];
            } catch (\Exception $e) {
                $result->clear();
                throw $e;
            }
        } else {
            $message = $result->getMessages();
            $error = '';
            foreach ($message as $v) {
                $error = $v->template;
            }
            return ['error' => 1,'msg'=> $error];
        }
    }

    /**
     * 导出Excel文件
     * @param string $fileName 文件名称
     * @param array $excelColumnItem 设置Excel表格第一行的显示
     * @param array $data   载入表格的所有数据
     * @return bool
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * 运用实例
     * $excelColumnItem = ['用户名','邮箱','个性签名'];
     * $fileName = '后台用户信息表';
     * $testData = Db::name('admin')->field(['username','email','signature'])->select();
     * $data = [];
       foreach ($testData as $k => $v){
         $excelData[$k]['username'] = $v['username'];
         $excelData[$k]['email'] = $v['email'];
         $excelData[$k]['signature'] = $v['signature'];
       }
       外部调用 : \Cocolait\CpExcel::exportExcel($fileName,$excelColumnItem,$data);
       生成表格
     * 处理表格中 银行卡号或者数字长度问题 可用 chunk_split()函数进行处理,其实就是分割数字字符,不懂得自行查询php;
     * chunk_split(string 要分割的字符 , int 分割位数, string 分割字符);
     * 使用案例 chunk_split('201701061051',4," ");已4位数字分割,中间已空格隔开。
     */
     public static function exportExcel($fileName = '', $excelColumnItem = [], $data = []){
         $date = date("Y_m_d", time());
         $fileName .= "_{$date}.xls";
         $objPHPExcel = new \PHPExcel();
         $objProps = $objPHPExcel->getProperties();
         // 设置表头
         $key = ord("A");
         foreach ($excelColumnItem as $v) {
             $colum = chr($key);
             $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);
             $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum . '1', $v);
             $key += 1;
         }
         $column = 2;
         $objActSheet = $objPHPExcel->getActiveSheet();
         foreach ($data as $key => $rows) {// 行写入
             $span = ord("A");
             foreach ($rows as $keyName => $value) {// 列写入
                 $j = chr($span);
                 $objActSheet->setCellValue($j . $column, $value);
                 $span ++;
             }
             $column ++;
         }
         $fileName = iconv("utf-8", "gb2312", $fileName);
         // 重命名表
         $objPHPExcel->setActiveSheetIndex(0);
         header('Content-Type: application/vnd.ms-excel');
         header("Content-Disposition: attachment;filename=\"$fileName\"");
         header('Cache-Control: max-age=0');
         $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
         $objWriter->save('php://output');//文件通过浏览器下载
         return true;
     }

    /**
     * Excel文件转换为数组数据
     * @param $filePath Excel文件全路径[文件名称]
     * @param string $exts  文件后缀 默认是 xls
     * @return array
     * @throws \PHPExcel_Exception
     */
     public static function excelFileToArray($filePath, $exts = 'xls')
     {
        if (!in_array($exts,['xls','xlsx'])) {
            return ['errorMsg' => '只支持‘xls,xlsx’类型的格式'];
        }
        // 检测后缀,实例化具体操作类
        if ($exts == 'xls') {
            $PHPReader = new \PHPExcel_Reader_Excel5();
        } else if ($exts == 'xlsx') {
            //偶尔不wps另存为的xlsx文件
            $PHPReader = new \PHPExcel_Reader_Excel2007();
        }
         // 读取Excel文件
         $PHPExcel = $PHPReader->load($filePath);
         // 获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
         $currentSheet = $PHPExcel->getSheet(0);
         // 获取总列数
         $allColumn = $currentSheet->getHighestColumn();
         // 获取总行数
         $allRow = $currentSheet->getHighestRow();
         //循环读取数据，默认编码是utf8
         $data = [];
         // 循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
         for($currentRow = 1;$currentRow<=$allRow;$currentRow++)
         {
             // 从哪列开始，A表示第一列
             for($currentColumn='A';$currentColumn<=$allColumn;$currentColumn++)
             {
                    // 数据坐标
                    $address = $currentColumn.$currentRow;
                    // 获取单元格的值
                    $v = $currentSheet->getCell($address)->getValue();
                    // 匹配http/https
                    $preg_url = "/^((http|https):\/\/)+[\w-_.]+(\/[\w-_]+)*\/?$/";
                    // 过滤时间
                    if (preg_match("/^[0-9]{5}.[0-9]{1,20}$/",$v) && strrpos($v,'.') && \PHPExcel_Shared_Date::isDateTime($currentSheet->getCell($address))) {
                        // 匹配时间并且进行格式化
                        $data[$currentRow-1][$currentColumn] = gmdate("Y/m/d H:i", \PHPExcel_Shared_Date::ExcelToPHP($v));
                    } else if (preg_match($preg_url,$v)) {
                        // 匹配url
                        $data[$currentRow-1][$currentColumn] = "<a href='$v'>$v</a>";
                    } else {
                        $data[$currentRow-1][$currentColumn] = $v;
                    }
             }
        }
        //处理空白数组
        $res = [];
        if ($data) {
            foreach($data as $k => $v) {
                $res[] = array_values($v);
            }
        }
        return $res;
     }
}