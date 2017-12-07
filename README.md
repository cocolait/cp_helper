# cp_helper
composer PHP处理类 （数据处理、下载、ip处理、字符串处理,分页,编码处理,日期处理,excel表格,发送email,二维码处理）

## 链接
- 博客：http://www.mgchen.com
- github：https://github.com/cocolait
- gitee：http://gitee.com/cocolait

# 安装
```php
composer require cocolait/cp_helper
```

# 版本要求
> PHP >= 5.5

# 使用案例
```php
//发送邮件
$mail_option = [
      'send_name' => 'Cocolait博客',//发送人的名称
      'host' => 'smtp.mxhichina.com',//设置SMTP服务器
      'username' => 'xxxx',//邮件
      'password' => 'xxxx'
];
$content = "<p>这是一个很美好的开始,确实很精彩</p>";
$data = \cocolait\helper\CpEmail::send('enkipen@qq.com','优美的文章',$content,$mail_option);
```
```php
// excel表格处理
// 导入Excel文件
$data = \cocolait\helper\CpExcel::importExcel();
// 导出Excel文件
$data = \cocolait\helper\CpExcel::exportExcel('文件名称','设置Excel表格第一行的显示','需要导出的所有数据');
// Excel转换Array
$data = \cocolait\helper\CpExcel::excelFileToArray('Excel文件全路径','文件后缀 默认是 xls');
```
```php
// 创建普通二维码
\cocolait\helper\CpQrCode::create('http://www.mgchen.com');
// 创建带有标题且有LOGO的二维码
\cocolait\helper\CpQrCode::create('http://www.mgchen.com',$size = 240, $title = 'cocolait', $logo = './logo.png');
```