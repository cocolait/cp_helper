<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/11
 * Time: 16:14
 */
// 应用公共文件
if (!function_exists('p')) {
    /**
     * 格式化打印数据
     * @param $data 需要打印的数据
     */
    function p($data){
        header("Content-type:text/html;charset=utf-8");
        // 定义样式
        $str='<pre style="display: block;padding: 9.5px;margin: 44px 0 0 0;font-size: 13px;line-height: 1.42857;color: #333;word-break: break-all;word-wrap: break-word;background-color: #F5F5F5;border: 1px solid #CCC;border-radius: 4px;">';
        // 如果是boolean或者null直接显示文字；否则print
        if (is_bool($data)) {
            $show_data=$data ? 'true' : 'false';
        }elseif (is_null($data)) {
            $show_data='null';
        }else{
            $show_data=print_r($data,true);
        }
        $str.=$show_data;
        $str.='</pre>';
        echo $str;
    }
}

if (!function_exists('display_p')) {
    /**
     * 格式化打印数据
     * @param $data 需要打印的数据
     */
    function display_p($data){
        header("Content-type:text/html;charset=utf-8");
        echo "<pre>";
        var_export($data);
        echo "</pre>";
    }
}

if (!function_exists('object2array')) {
    /**
     * 对象转换为数组
     * @param $object
     * @return mixed
     */
    function object2array($object) {
        if (is_object($object)) {
            foreach ($object as $key => $value) {
                $array[$key] = $value;
            }
        }
        else {
            $array = $object;
        }
        return $array;
    }
}

if (!function_exists('think_encrypt')) {
    /**
     * 系统加密方法
     * @param string $data 要加密的字符串
     * @param string $key  加密密钥
     * @param int $expire  过期时间 单位 秒
     * @return string
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    function think_encrypt($data, $key = '', $expire = 0) {
        $md5_key  = 'http://www.cocolait.cn';
        $key  = md5(empty($key) ? $md5_key : $key);
        $data = base64_encode($data);
        $x    = 0;
        $len  = strlen($data);
        $l    = strlen($key);
        $char = '';

        for ($i = 0; $i < $len; $i++) {
            if ($x == $l) $x = 0;
            $char .= substr($key, $x, 1);
            $x++;
        }

        $str = sprintf('%010d', $expire ? $expire + time():0);

        for ($i = 0; $i < $len; $i++) {
            $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1)))%256);
        }
        return str_replace(array('+','/','='),array('-','_',''),base64_encode($str));
    }
}

if (!function_exists('think_decrypt')) {
    /**
     * 系统解密方法
     * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
     * @param  string $key  加密密钥
     * @return string
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    function think_decrypt($data, $key = ''){
        $md5_key  = 'http://www.cocolait.cn';
        $key    = md5(empty($key) ? $md5_key : $key);
        $data   = str_replace(array('-','_'),array('+','/'),$data);
        $mod4   = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        $data   = base64_decode($data);
        $expire = substr($data,0,10);
        $data   = substr($data,10);

        if($expire > 0 && $expire < time()) {
            return '';
        }
        $x      = 0;
        $len    = strlen($data);
        $l      = strlen($key);
        $char   = $str = '';

        for ($i = 0; $i < $len; $i++) {
            if ($x == $l) $x = 0;
            $char .= substr($key, $x, 1);
            $x++;
        }

        for ($i = 0; $i < $len; $i++) {
            if (ord(substr($data, $i, 1))<ord(substr($char, $i, 1))) {
                $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
            }else{
                $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
            }
        }
        return base64_decode($str);
    }
}

if (!function_exists('encrypt_password')) {
    /**
     * 密码加密方法
     * @param string $pw 要加密的字符串
     * @return string
     */
    function encrypt_password($pw,$authcode='http://www.cocolait.cn'){
        return md5(md5(md5($authcode . $pw)));
    }
}

if (!function_exists('compare_password')) {
    /**
     * 密码比较方法
     * @param string $password 要比较的密码
     * @param string $password_in_db 数据库保存的已经加密过的密码
     * @return boolean 密码相同，返回true
     */
    function compare_password($password,$password_in_db){
        if (encrypt_password($password) == $password_in_db) {
            return true;
        } else {
            return false;
        }
    }
}


if (!function_exists('keyWrods_replace')) {
    /**
     * 替换关键字并且写入样式
     * @param $keywords 查询的关键字
     * @param $content  查询的内容
     * @return mixed
     */
    function keyWrods_replace($keywords,$content){
        $str = "<span style='color: #D2322D;font-weight: 700;'>{$keywords}</span>";
        return str_replace($keywords,$str,$content);
    }
}

if (!function_exists('time_format')) {
    /**
     * 格式化时间
     * @param $time
     * @return bool|string
     */
    function time_format($time){
        //获取当前时间
        $now = time();
        //今天零时零分零秒
        $today = strtotime(date('y-m-d',$now));
        //当前时间与传递时间相差的秒数
        $diff = $now - $time;
        switch ($time) {
            case $diff < 60 :
                $str = $diff . ' 秒前';
                break;
            case $diff < 3600 :
                $str = floor($diff / 60) . ' 分钟前';
                break;
            case $diff < (3600 * 8) :
                $str = floor($diff / 3600) . ' 小时前';
                break;
            case $time > $today :
                $str = '今天&nbsp;&nbsp;' . date('H:i',$time);
                break;
            default:
                $str = date('Y-m-d H:i',$time);
                break;
        }
        return $str;

    }
}

if (!function_exists('isMobile')) {
    /**
     * 验证手机
     * @param string $subject
     * @return boolean
     */
    function isMobile($subject = '') {
        $pattern = "/0?(13|14|15|18)[0-9]{9}/";
        if (preg_match($pattern, $subject)) {
            return true;
        }
        return false;
    }
}

if (!function_exists('isEmail')) {
    /**
     * 验证是否是邮箱
     * @param  string  $email 邮箱
     * @return boolean        是否是邮箱
     */
    function isEmail($email){
        if(filter_var($email,FILTER_VALIDATE_EMAIL)){
            return true;
        }else{
            return false;
        }
    }
}

if (!function_exists('is_url')) {
    /**
     * 验证是否是URL地址
     * @param  string  $email 邮箱
     * @return boolean  是否是邮箱
     */
    function is_url($url){
        if(filter_var($url,FILTER_VALIDATE_URL)){
            return true;
        }else{
            return false;
        }
    }
}


if (!function_exists('is_ip')) {
    /**
     * 验证是否是URL地址
     * @param  string  $email 邮箱
     * @return boolean  是否是邮箱
     */
    function is_ip($ip){
        if(filter_var($ip,FILTER_VALIDATE_IP)){
            return true;
        }else{
            return false;
        }
    }
}

if (!function_exists('replace_phone')) {
    /**
     * 替换手机号码
     * @param $str
     * @return string
     */
    function replace_phone($str){
        $start = substr($str,0,3);
        $end = substr($str,-4);
        return $start . "****" . $end;
    }
}


if (!function_exists('cutEmailUrl')) {
    /**
     * 截取邮箱@后面的内容 替换对应的登录地址
     * @param $email
     * @return bool
     */
    function cutEmailUrl($email){
        if (!is_string($email)) return false;
        $oldStr = substr($email,strrpos($email,"@"));
        $str = substr($oldStr,1);
        $temp = explode(".",$str);
        if ($temp[0] == 'qq' || $temp[0] == 'QQ') {
            $url = "https://mail.qq.com/cgi-bin/loginpage";
        } else if ($temp[0] == '163'){
            $url = "http://mail.163.com/";
        } else if ($temp[0] == '126') {
            $url = "http://mail.126.com/";
        } else if ($temp[0] == 'sina') {
            $url = "http://mail.sina.com.cn/?from=mail";
        } else {
            $url = "http://mail" . $temp[0] . "com";
        }
        return $url;
    }
}

if (!function_exists('randomFloat')) {
    /**
     * 随机生成0~0.1之间的数,并且保留指定位数
     * @param int $min 最小值
     * @param float $max 最大值
     * @param int $num  要取多少位数 默认2位
     * @param int $type 返回类型 true ：四舍五入制返回指定位数 false : 不是四舍五入
     * @return string
     */
    function randomFloat($num = 2, $type = true, $min = 0, $max = 0.1) {
        $rand = $min + mt_rand() / mt_getrandmax() * ($max - $min);
        if ($type === true) {
            // 四舍五入 保留指定位数
            return sprintf("%.{$num}f", $rand);
        } else {
            // 不是四舍五入 保留指定位数
            $new = $num + 1;
            return sprintf("%.{$num}f",substr(sprintf("%.{$new}f", $rand), 0, -$num));
        }
    }
}

if (!function_exists('mbs_strlen')) {
    /**
     * 计算中英文字符长度
     * @param $str
     * @return int
     */
    function mbs_strlen($str){
        preg_match_all("/./us", $str, $matches);
        return count(current($matches));
    }
}


if (!function_exists('checkEvenNum')) {
    /**
     * 检测数字是否为偶数
     * @param $num 数值
     * @return bool
     */
    function checkEvenNum($num)
    {
        if((abs($num)+2)%2==1){
            return false;
        }else{
            return true;
        }
    }
}

if (!function_exists('isArraySame')) {
    /**
     * 比较2个数组是否相等 二维数组
     * @param $arr1 数组1
     * @param $arr2 数组2
     * @return bool
     */
    function isArraySame ($arr1,$arr2){
        foreach ($arr1 as $key => $v) {
            if(isset($arr2[$key])){
                if($arr2[$key] !=  $arr1[$key]){
                    return false;
                }
            }else{
                return false;
            }
        }
        return true;
    }
}

if (!function_exists('array_sort')) {
    /**
     * 二维数组 指定字段排序
     * @param $array  要排序的数组
     * @param $row    排序依据列 指定的键位
     * @param $type   排序类型[asc or desc]
     * @return array  排好序的数组
     */
    function array_sort($array,$row,$type){
        $array_temp = array();
        foreach($array as $v){
            $array_temp[$v[$row]] = $v;
        }
        if($type == 'asc'){
            ksort($array_temp);
        }elseif($type='desc'){
            krsort($array_temp);
        }else{
        }
        return $array_temp;
    }
}

if (!function_exists('get_ip_info')) {
    /**
     * 获取ip的详细信息
     * 163.125.127.241
     * 返回信息 国家/地区	省份	   城市	  县	  运营商
     *          中国        广东省  深圳市  *  联通
     * @param $ip ip地址
     * @return mixed
     */
    function get_ip_info($ip)
    {
        // 淘宝开源api 淘宝IP地址库
        $taobaoUrl = 'http://ip.taobao.com/service/getIpInfo.php?ip=' . $ip;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $taobaoUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ( $ch,  CURLOPT_NOSIGNAL,true);//支持毫秒级别超时设置
        curl_setopt($ch, CURLOPT_TIMEOUT, 1200);   //1.2秒未获取到信息，视为定位失败
        $myCity = curl_exec($ch);
        curl_close($ch);

        $myCity = json_decode($myCity, true);
        return $myCity;
    }
}
