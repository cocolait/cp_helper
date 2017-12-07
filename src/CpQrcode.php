<?php
namespace cocolait\helper;
use Endroid\QrCode\QrCode;
/**
 * 二维码处理类
 * Class CpQrCode
 * @package cocolait\helper
 */
final class CpQrCode {
    /**
     * 生成二维码
     * 注意：以http直接输出的方式不要直接访问需要在html页面用img标签来访问
     * 比如：<img src="/qrcode/show.html"/>
     * 保存图片的方式 会返回一个数据的url（img base64编码）
     * @param $content              二维码内容
     * @param string $filename      图片的文件名称
     * @param string $title         二维码图像标题
     * @param string $logo          二维码图像中间的logo
     * @param int $logoSize         logo显示的大小 默认 60 x 60
     * @param int $size             二维码图像生成的尺寸 默认 240 x 240
     * @param bool $is_save         渲染输出的方式 true 保存该图片 false 以http方式直接页面输出
     * @param int $titleSize        标题的字体大小 默认16
     * @param int $padding          二维码周围的padding值
     * @return array|string
     * @throws \Endroid\QrCode\Exceptions\DataDoesntExistsException
     * @throws \Endroid\QrCode\Exceptions\ImageFunctionFailedException
     * @throws \Endroid\QrCode\Exceptions\ImageFunctionUnknownException
     * @throws \Endroid\QrCode\Exceptions\ImageTypeInvalidException
     */
    public static function create($content, $size = 240, $title = '', $logo = '', $logoSize = 60, $is_save = false, $filename = '', $titleSize=16, $padding = 0)
    {
        $qrCode = new QrCode();
        if (!$content) return ['error' => 1, 'msg' => '二维码内容不能为空'];
        if (is_array($content)) return ['error' => 1, 'msg' => '二维码内容不能为数组'];
        if ($logo && $title) {
            // 既有LOGo也有标题的情况
            if (!file_exists($logo)) return ['error' => 1, 'msg' => 'logo路径文件不存在'];
            $qrCode
                ->setText($content)
                ->setSize($size)
                ->setPadding($padding)
                ->setErrorCorrection('high')
                ->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0])
                ->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0])
                ->setLabel($title)
                ->setLabelFontSize($titleSize)
                ->setLogo($logo)
                ->setLogoSize($logoSize)
                ->setImageType(QrCode::IMAGE_TYPE_PNG);
        }

        if (!$logo && $title) {
            // 无LOGo有标题的情况
            $qrCode
                ->setText($content)
                ->setSize($size)
                ->setPadding($padding)
                ->setErrorCorrection('high')
                ->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0])
                ->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0])
                ->setLabel($title)
                ->setLabelFontSize($titleSize)
                ->setImageType(QrCode::IMAGE_TYPE_PNG);
        }

        if ($logo && !$title) {
            // 只有LOGo没有标题的情况
            if (!file_exists($logo)) return ['error' => 1, 'msg' => 'logo路径文件不存在'];
            $qrCode
                ->setText($content)
                ->setSize($size)
                ->setPadding($padding)
                ->setErrorCorrection('high')
                ->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0])
                ->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0])
                ->setLogo($logo)
                ->setLogoSize($logoSize)
                ->setImageType(QrCode::IMAGE_TYPE_PNG);
        }

        if (!$logo && !$title) {
            // 无LOGo无标题的情况
            $qrCode
                ->setText($content)
                ->setSize($size)
                ->setPadding($padding)
                ->setErrorCorrection('high')
                ->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0])
                ->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0])
                ->setImageType(QrCode::IMAGE_TYPE_PNG);
        }

        if ($is_save) {
            $dir_name = dirname($filename);
            if (!file_exists($dir_name)) {
                if (!mkdir($dir_name,0777,true)) {
                    return ['error'=>1,'msg'=>"无法创建目录：" . $dir_name];
                }
            }
            // 保存图片
            $obj = $qrCode->save($filename);
            return $obj->getDataUri();
        } else {
            header('Content-Type: '.$qrCode->getContentType());
            $qrCode->render();
        }
    }
}