<?php
header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json');
error_reporting(0);
include_once(dirname(__FILE__,2)."/mySQL/dbconfig.php"); 
$key=$_POST['key'];
$checkdata = $key;
function clean($data){
    // Fix &entity＼n;
    $data=str_replace(array('&','<','>'),array('&amp;','&lt;','&gt;'),$data);
    $data=preg_replace('/(&#*＼w+)[＼x00-＼x20]+;/u','$1;',$data);
    $data=preg_replace('/(&#x*[0-9A-F]+);*/iu','$1;',$data);
    $data=html_entity_decode($data,ENT_COMPAT,'UTF-8');
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
    $json = array(
        "code" => 404, 
        "msg" => '非法输入'
    );
    echo json_encode($json, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}
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

$sql = "SELECT * FROM tanzhen_information WHERE keyvalue='$key'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    // 输出数据
    $array = array();
    while($row = $result->fetch_assoc()) {
        array_push($array,$row);
    }
    $json = array(
        "code" => 200,
        "msg" => '查询成功',
        "data" => $array
    );
} else {
    // 没有找到key
    $json = array(
        "code" => 201,
        "msg" => '没有找到对应的key/无数据'
    );
}

$conn->close();
echo json_encode($json, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

?>