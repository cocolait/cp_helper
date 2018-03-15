<?php
namespace cocolait\helper;
/**
 * 获取资源类
 * Created by PhpStorm.
 * User: Cocolait
 * Date: 2016/12/16
 * Time: 11:24
 * 博  客：http://www.mgchen.com
 */
final class CpGet{
    /**
     * 远程路径保存文件到指定目录
     * @param $url 远程路径
     * @param string $save_dir  保存的目录
     * @param int $type         保存的方式 0 服务缓存区保存方式 1：curl获取保存 默认curl方式保存
     * @return array ['file_name' => 文件名称,'save_path' => 保存的全路径 ,'error' => 错误码 ,'time' => '花费的时间']
     */
    public static function getImage($url,$type=1,$save_dir=''){
        $start_time = time();
        if(trim($url)==''){
            return ['file_name'=>'','save_path'=>'','error'=>1];
        }

        if(trim($save_dir)==''){
            //创建文件保存目录 默认目录可根据具体业务扩展,确认save_path是你想要的路径
            $save_dir = './uploads/user_face/' . date('Y-m-d');
            if (!file_exists($save_dir)) {
                mkdir($save_dir,0777,true);
            }
        } else {
            if (!file_exists($save_dir)) {
                mkdir($save_dir,0777,true);
            }
        }

        // 判断文件的后缀
        $ext=strrchr($url,'.');

        if ($ext != ".gif" && $ext != ".jpg" && $ext != ".png" && $ext != ".jpeg") {
            $filename = "/" . CpMsubstr::uuid() . ".png";
        } else {
            $filename = "/" . CpMsubstr::uuid() . $ext;
        }

        //获取远程文件所采用的方法
        if($type){
            $ch=curl_init();
            $timeout=5;
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $img=curl_exec($ch);
            curl_close($ch);
        }else{
            ob_start();
            readfile($url);
            $img=ob_get_contents();
            ob_end_clean();
        }
        //$size=strlen($img);
        //文件大小
        $fp2=@fopen($save_dir . $filename,'a');
        fwrite($fp2,$img);
        fclose($fp2);
        unset($img,$url);
        $time = time() - $start_time . "s";
        return ['file_name'=>$filename,'save_path'=>substr($save_dir . $filename,1),'error'=>0,'time'=>$time];
    }

    /**
     * 获取客户端IP地址
     * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
     * @return mixed
     */
    public static function get_client_ip($type = 0,$adv=false) {
        $type       =  $type ? 1 : 0;
        static $ip  =   NULL;
        if ($ip !== NULL) return $ip[$type];
        if($adv){
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos    =   array_search('unknown',$arr);
                if(false !== $pos) unset($arr[$pos]);
                $ip     =   trim($arr[0]);
            }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip     =   $_SERVER['HTTP_CLIENT_IP'];
            }elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip     =   $_SERVER['REMOTE_ADDR'];
            }
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u",ip2long($ip));
        $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }
}