<?php
namespace cocolait\helper;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
/**
 * 邮箱管理类 无需继承任何类
 * Created by PhpStorm.
 * User: Cocolait
 * Date: 2016/12/12
 * Time: 15:30
 * 博  客：http://www.mgchen.com
 */
final class CpEmail{
    /**
     * 发送邮件
     * 简单示咧：\cocolait\helper\CpEmail::send('enkipen@qq.com','优美的文章',"<p>这是一个很美好的开始,确实很精彩</p>",$mail_option);
     * @param $address 收件人 单发 'admin@qq.com' 群发 ['admin@qq.com','xx@qq.com']
     * @param $subject 发送邮件的标题
     * @param $content 邮件内容
     * @param array $mail_option 配置参数 [
        'send_name' => '',                  //必填项 发送人的名称
        'host' => '',                       //必填项 设置SMTP服务器
        'username' => '',                   //必填项 邮箱登录用户名
        'password' => '',                   //必填项 密码
        'smtp_secure' => 'tls',             //可选填 加密方式 'tls' 或者 'ssl' 默认是 tls
        'port' => 25                        //可选填 TCP端口 默认是25
    ];
     * @param array $cc 抄送|密送 ['acc' => '抄送人的邮箱','bcc' => '密送人的邮箱']
     * @param array $attachment 添加压缩包或者文件或者图片 ['文件的名称' => '文件的绝对路径']
     * @return array
     */
    public static function send($address,$subject,$content,$mail_option = [], $cc = [],$attachment = [])
    {
        if (empty($mail_option['send_name']) || empty($mail_option['host']) || empty($mail_option['username']) || empty($mail_option['password'])) {
            return ['error' => 1, 'msg' => '配置参数填写不完整'];
        }
        if (!$address) return ['error' => 1, 'msg' => '收件人不能为空'];
        if (!$subject) return ['error' => 1, 'msg' => '发送的标题不能为空'];
        if (!$content) return ['error' => 1, 'msg' => '发送的内容不能为空'];
        $mail = new PHPMailer(true);                              // 是否显示异常信息
        try {
            // 设置配置参数
            $mail->SMTPDebug = 0;                                 // 启用详细的调试输出
            $mail->isSMTP();                                      // 设置SMTP服务
            // 设置SMTP服务器类型 如smtp.163.com,smtp.aliyun.com 可设置主从服务 'smtp1.example.com;smtp2.example.com'
            $mail->Host = $mail_option['host'];
            $mail->SMTPAuth = true;                               // SMTP服务器是否需要身份验证
            $mail->Username = $mail_option['username'];           // SMTP 用户名
            // SMTP 密码 企业邮箱就是邮箱登录密码  个人邮箱开通SMTP服务那么就是 SMTP授权码
            $mail->Password = $mail_option['password'];
            if (empty($mail_option['smtp_secure'])) {
                // 加密方式 'tls' 或者 'ssl' 一般使用默认设置即可
                $mail->SMTPSecure = 'tls';
            } else {
                $mail->SMTPSecure = $mail_option['smtp_secure'];
            }
            if (empty($mail_option['port'])) {
                // TCP端口 默认是25  一般使用默认即可
                $mail->Port = 25;
            } else {
                $mail->Port = $mail_option['port'];
            }

            // 设置基本信息
            $mail->setFrom($mail_option['username'], $mail_option['send_name']); // 设置 发件人

            // 收件人
            if(is_array($address)){
                /*$mail->addAddress('收件人的邮箱地址',$name = '收件人的昵称');*/
                // 群发
                foreach($address as $addressv){
                    $mail->addAddress($addressv);
                }
            }else{
                //单发 指定人发送
                $mail->addAddress($address);
            }
            // 收件人回复邮箱设置 一般指定发件人邮箱看具体需求 第二个参数'收件人昵称'也是可选的
            $mail->addReplyTo($mail_option['username'], $mail_option['send_name']);
            if (isset($cc['acc']) && !empty($cc['acc'])) {
                $mail->addCC($cc['acc']);//抄送
            }
            if (isset($cc['bcc']) && !empty($cc['bcc'])) {
                $mail->addBCC($cc['bcc']);//密送
            }

            // 压缩包|文件
            if ($attachment) {
                $count = count($attachment);
                if ($count > 1) {
                    // 多个文件的情况
                    foreach ($attachment as $k => $v) {
                        /*$mail->addAttachment('/var/tmp/file.tar.gz');         //添加附加 压缩包
                        $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //添加图片文件或者普通文件*/
                        if (is_numeric($k)) {
                            $name = '';
                        } else {
                            $name = $k;
                        }
                        $mail->addAttachment($v,$name); //添加文件
                    }
                } else {
                    // 单个文件
                    if (is_numeric($attachment[0])) {
                        $name = '';
                    } else {
                        $name = $attachment[0];
                    }
                    $mail->addAttachment($attachment[1],$name); //添加文件
                }
            }

            // 内容
            $mail->isHTML(true);        //设置电子邮件为html格式方式发送
            $mail->Subject = $subject;  //邮件标题
            $mail->msgHTML($content);   //邮件正文
            $mail->send();
            return ['error' => 0, 'msg' => '发送成功'];
        } catch (Exception $e) {
            return ['error' => 1, 'msg' => 'Mailer Error: ' . $mail->ErrorInfo];
        }
    }
}