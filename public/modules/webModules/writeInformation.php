<?php
header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json');
error_reporting(0);
$gps_function = $_POST['gps'];
$ip_function = $_POST['ip'];
$camera_function = $_POST['camera'];
$browser_function = $_POST['browser'];
$url = $_POST['url'];
$key = $_POST['key'];
if($gps_function != clean($gps_function) || $ip_function != clean($ip_function) || $camera_function != clean($camera_function) || $browser_function != clean($browser_function) || $key != clean($key)){
    $json = array(
        "code" => 404, 
        "msg" => '非法输入'
    );
    echo json_encode($json, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}
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
include_once(dirname(__FILE__,2)."/mySQL/dbconfig.php");  // $db_host/port/name/user/password
// 创建连接
$conn = new mysqli($db_host.":".$db_port, $db_user, $db_password, $db_name);
// Check connection
if ($conn->connect_error) {
    $json = array(
        "code" => 201, 
        "msg" => '链接到MySQL服务器失败,'.$conn->connect_error
    );
    echo json_encode($json, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}
//判断是否已经存在key
$sql = "SELECT * FROM tanzhen_web WHERE keyvalue='$key'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $json = array(
        "code" => 203,
        "msg" => '已经存在key.'
    );
}else{
    $sql = "INSERT INTO `tanzhen_web` (`id`, `keyvalue`, `gps_function`, `ip_function`, `camera_function`, `browser_function`, `url`) VALUES (NULL, '$key', '$gps_function', '$ip_function', '$camera_function', '$browser_function', '$url');";
    if ($conn->query($sql) === TRUE) {
        $json = array(
            "code" => 200,
            "msg" => '插入成功'
        );
    } else {
        $json = array(
            "code" => 202,
            "msg" => '插入失败,'.$conn->error
        );
    }
}
$conn->close();
echo json_encode($json, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>