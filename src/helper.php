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

if (!function_exists('cp_del_dir')) {
    /**
     * 删除目录以及目录下的所有文件
     * @param $dir 目录路径
     * @return bool
     */
    function del_dir($dir) {
        if (!is_dir($dir)) {
            return false;
        }
        $handle = opendir($dir);
        while (($file = readdir($handle)) !== false) {
            if ($file != "." && $file != "..") {
                is_dir("$dir/$file") ? del_dir("$dir/$file") : @unlink("$dir/$file");
            }
        }
        if (readdir($handle) == false) {
            closedir($handle);
            @rmdir($dir);
        }
    }
}


if (!function_exists('cp_addFileToZip')) {
    /**
     * TODO 将文件夹打包成zip文件
     * PHP ZipArchive是PHP自带的扩展类
     * 可以轻松实现ZIP文件的压缩和解压，使用前首先要确保PHP ZIP扩展已经开启
     * 将文件夹打包成zip文件
     * 添加文件到zip包中
     * @param $path 目录路径
     * @param $zip  PHP ZipArchive是PHP自带的扩展类 对象
     * 使用案例：
     *  $zip=new \ZipArchive();
        if (!file_exists('./Data/img/15773677249.zip')) {
            // 经过多次测试 在处理压缩ZIP文件包时 必须先创建zip包文件不然ZIP OPEN会打不开
            // 创建压缩包
            $fp = fopen("./Data/img/15773677249.zip", "w");
            fclose($fp);
        }
        if($zip->open('./Data/img/15773677249.zip', \ZipArchive::OVERWRITE) === TRUE){
            addFileToZip('./Data/img/15773677249', $zip); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
            $zip->close(); //关闭处理的zip文件
            // 压缩处理完毕 删除目录以及目录所有文件
            del_dir('./Data/img/15773677249');
        }
     */
    function addFileToZip($path,$zip){
        $handler=opendir($path); //打开当前文件夹由$path指定。
        while(($filename=readdir($handler))!==false){
            if($filename != "." && $filename != ".."){//文件夹文件名字为'.'和‘..’，不要对他们进行操作
                if(is_dir($path."/".$filename)){// 如果读取的某个对象是文件夹，则递归
                    addFileToZip($path."/".$filename, $zip);
                }else{ //将文件加入zip对象
                    $zip->addFile($path."/".$filename,$filename);//向压缩包中添加文件 第二个参数可以定义别名
                }
            }
        }
        @closedir($path);
    }
}

if (!function_exists('cp_rand_award')) {
    /**
     * TODO 抽奖概率算法
     * 不同概率的抽奖原理就是把0到*（比重总数）的区间分块
     * 分块的依据是物品占整个的比重，再根据随机数种子来产生0-* 中的某个数
     * 判断这个数是落在哪个区间上，区间对应的就是抽到的那个物品。
     * 随机数理论上是概率均等的，那么相应的区间所含数的多少就体现了抽奖物品概率的不同。
     * 案例：
     * $arr = [
        ['id'=>1,'name'=>'特等奖','v'=>1],
        ['id'=>2,'name'=>'一等奖','v'=>5],
        ['id'=>3,'name'=>'二等奖','v'=>10],
        ['id'=>4,'name'=>'三等奖','v'=>120],
        ['id'=>5,'name'=>'四等奖','v'=>22],
        ['id'=>6,'name'=>'没中奖','v'=>50]
     ];
     * 测试1W次的结果 TODO 权重值越大 中奖概率越大
     * Array
        (
            [6] => 2449
            [4] => 5751
            [3] => 489
            [5] => 1056
            [2] => 220
            [1] => 35
        )
     * @param $proArr 被抽奖的数组
     * @return array
     */
    function cp_rand_award($proArr) {
        $result = array();
        foreach ($proArr as $key => $val) {
            $arr[$key] = $val['v'];
        }
        // 概率数组的总概率
        $proSum = array_sum($arr);
        asort($arr);
        // 概率数组循环
        foreach ($arr as $k => $v) {
            $randNum = mt_rand(1, $proSum);
            if ($randNum <= $v) {
                $result = $proArr[$k];
                break;
            } else {
                $proSum -= $v;
            }
        }
        return $result;
    }
}

if (!function_exists('cp_rand_award_2')) {
    /**
     * 不推荐使用
     * TODO 抽奖概率算法 02
     * 测试1W次的结果 TODO 权重值越大 中奖概率越大
     * Array
     (
        [6] => 5076
        [2] => 475
        [5] => 2255
        [3] => 1010
        [4] => 1184
     )
     * @param $proArr
     * @return array
     */
    function cp_rand_award_2($proArr)
    {
        $result = array();
        foreach ($proArr as $key => $val) {
            $arr[$key] = $val['v'];
        }
        $proSum = array_sum($arr);      // 计算总权重
        $randNum = mt_rand(1, $proSum);
        $d1 = 0;
        $d2 = 0;
        for ($i=0; $i < count($arr); $i++)
        {
            $d2 += $arr[$i];
            if($i==0)
            {
                $d1 = 0;
            }
            else
            {
                $d1 += $arr[$i-1];
            }
            if($randNum >= $d1 && $randNum <= $d2)
            {
                $result = $proArr[$i];
            }
        }
        unset ($arr);
        return $result;
    }
}

if (!function_exists('cp_display_p')) {
    /**
     * 格式化打印数据
     * @param $data 需要打印的数据
     */
    function cp_display_p($data){
        header("Content-type:text/html;charset=utf-8");
        echo "<pre>";
        var_export($data);
        echo "</pre>";
    }
}


if (!function_exists('cp_object2array')) {
    /**
     * 对象转换为数组
     * @param $object
     * @return mixed
     */
    function cp_object2array($object) {
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

if (!function_exists('cp_encrypt')) {
    /**
     * 加密方法
     * @param string $data 要加密的字符串
     * @param string $key  加密密钥
     * @param int $expire  过期时间 单位 秒
     * @return string
     */
    function cp_encrypt($data, $key = '', $expire = 0) {
        $key  = md5(empty($key) ? '' : $key);
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

if (!function_exists('cp_decrypt')) {
    /**
     * 解密方法
     * @param  string $data 要解密的字符串 （必须是cp_encrypt方法加密的字符串）
     * @param  string $key  加密密钥
     * @return string
     */
    function cp_decrypt($data, $key = ''){
        $key    = md5(empty($key) ? '' : $key);
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

if (!function_exists('cp_encrypt_info')) {
    /**
     * 加密信息集合
     * @param $data
     * @return string
     */
    function cp_encrypt_info($data)
    {
        $temp = [];
        foreach ($data as $k => $v) {
            $temp[] = $v . '#' . $k;
        }
        return cp_encrypt(implode(',',$temp));
    }
}

if (!function_exists('cp_decrypt_info')) {
    /**
     * 解密信息集合 [必须是 cp_encrypt_info 加密]
     * @param $str
     * @return array
     */
    function cp_decrypt_info($str)
    {
        $temp = [];
        $info  = cp_decrypt($str);
        $data = explode(',',$info);
        foreach ($data as $k => $v) {
            $temp[] = explode('#',$v);
        }
        $return = [];
        foreach ($temp as $k => $v) {
            $return[$v[1]] = $v[0];
        }
        return $return;
    }
}

if (!function_exists('cp_encrypt_password')) {
    /**
     * 密码加密方法
     * @param string $pw 要加密的字符串
     * @return string
     */
    function cp_encrypt_password($pw,$authcode='http://www.cocolait.cn'){
        return md5(md5(md5($authcode . $pw)));
    }
}

if (!function_exists('cp_compare_password')) {
    /**
     * 密码比较方法
     * @param string $password 要比较的密码
     * @param string $password_in_db 数据库保存的已经加密过的密码
     * @return boolean 密码相同，返回true
     */
    function cp_compare_password($password,$password_in_db){
        if (encrypt_password($password) == $password_in_db) {
            return true;
        } else {
            return false;
        }
    }
}


if (!function_exists('cp_keyWrods_replace')) {
    /**
     * 替换关键字并且写入样式
     * @param $keywords 查询的关键字
     * @param $content  查询的内容
     * @return mixed
     */
    function cp_keyWrods_replace($keywords,$content){
        $str = "<span style='color: #D2322D;font-weight: 700;'>{$keywords}</span>";
        return str_replace($keywords,$str,$content);
    }
}

if (!function_exists('cp_time_format')) {
    /**
     * 格式化时间
     * @param $time
     * @return bool|string
     */
    function cp_time_format($time){
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

if (!function_exists('cp_isMobile')) {
    /**
     * 验证手机
     * @param string $subject
     * @return boolean
     */
    function cp_isMobile($subject = '') {
        $pattern = "/0?(13|14|15|18)[0-9]{9}/";
        if (preg_match($pattern, $subject)) {
            return true;
        }
        return false;
    }
}

if (!function_exists('cp_isEmail')) {
    /**
     * 验证是否是邮箱
     * @param  string  $email 邮箱
     * @return boolean        是否是邮箱
     */
    function cp_isEmail($email){
        if(filter_var($email,FILTER_VALIDATE_EMAIL)){
            return true;
        }else{
            return false;
        }
    }
}

if (!function_exists('cp_is_url')) {
    /**
     * 验证是否是URL地址
     * @param  string  $email 邮箱
     * @return boolean  是否是邮箱
     */
    function cp_is_url($url){
        if(filter_var($url,FILTER_VALIDATE_URL)){
            return true;
        }else{
            return false;
        }
    }
}


if (!function_exists('cp_is_ip')) {
    /**
     * 验证是否是URL地址
     * @param  string  $email 邮箱
     * @return boolean  是否是邮箱
     */
    function cp_is_ip($ip){
        if(filter_var($ip,FILTER_VALIDATE_IP)){
            return true;
        }else{
            return false;
        }
    }
}

if (!function_exists('cp_replace_phone')) {
    /**
     * 替换手机号码
     * @param $str
     * @return string
     */
    function cp_replace_phone($str){
        $start = substr($str,0,3);
        $end = substr($str,-4);
        return $start . "****" . $end;
    }
}


if (!function_exists('cp_cutEmailUrl')) {
    /**
     * 截取邮箱@后面的内容 替换对应的登录地址
     * @param $email
     * @return bool
     */
    function cp_cutEmailUrl($email){
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

if (!function_exists('cp_randomFloat')) {
    /**
     * 随机生成0~0.1之间的数,并且保留指定位数
     * @param int $min 最小值
     * @param float $max 最大值
     * @param int $num  要取多少位数 默认2位
     * @param int $type 返回类型 true ：四舍五入制返回指定位数 false : 不是四舍五入
     * @return string
     */
    function cp_randomFloat($num = 2, $type = true, $min = 0, $max = 0.1) {
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

if (!function_exists('cp_mbs_strlen')) {
    /**
     * 计算中英文字符长度
     * @param $str
     * @return int
     */
    function cp_mbs_strlen($str){
        preg_match_all("/./us", $str, $matches);
        return count(current($matches));
    }
}


if (!function_exists('cp_checkEvenNum')) {
    /**
     * 检测数字是否为偶数
     * @param $num 数值
     * @return bool
     */
    function cp_checkEvenNum($num)
    {
        if((abs($num)+2)%2==1){
            return false;
        }else{
            return true;
        }
    }
}

if (!function_exists('cp_isArraySame')) {
    /**
     * 比较2个数组是否相等 二维数组
     * @param $arr1 数组1
     * @param $arr2 数组2
     * @return bool
     */
    function cp_isArraySame ($arr1,$arr2){
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

if (!function_exists('cp_array_sort')) {
    /**
     * 二维数组 指定字段排序
     * @param $array  要排序的数组
     * @param $row    排序依据列 指定的键位
     * @param $type   排序类型[asc or desc]
     * @return array  排好序的数组
     */
    function cp_array_sort($array,$row,$type){
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

if (!function_exists('cp_get_ip_info')) {
    /**
     * 获取ip的详细信息
     * 163.125.127.241
     * 返回信息 国家/地区	省份	   城市	  县	  运营商
     *          中国        广东省  深圳市  *  联通
     * @param $ip ip地址
     * @return mixed
     */
    function cp_get_ip_info($ip)
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

if (!function_exists('cp_get_client_ip')) {
    /**
     * 获取客户端IP地址
     * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
     * @return mixed
     */
    function cp_get_client_ip($type = 0,$adv=false) {
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

