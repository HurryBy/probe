<?php
// 方式一 速度较快
// 1.获取IP
if($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"])
{
    $ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
}
elseif ($HTTP_SERVER_VARS["HTTP_CLIENT_IP"])
{
    $ip = $HTTP_SERVER_VARS["HTTP_CLIENT_IP"];
}
elseif ($HTTP_SERVER_VARS["REMOTE_ADDR"])
{
    $ip = $HTTP_SERVER_VARS["REMOTE_ADDR"];
}
elseif (getenv("HTTP_X_FORWARDED_FOR"))
{
    $ip = getenv("HTTP_X_FORWARDED_FOR");
}
elseif (getenv("HTTP_CLIENT_IP"))
{
    $ip = getenv("HTTP_CLIENT_IP");
}
elseif (getenv("REMOTE_ADDR"))
{
    $ip = getenv("REMOTE_ADDR");
}
else
{
    $data = file_get_contents("https://ip.seeip.org/geoip");
    $dataa = json_decode($data,true);
    if($dataa['ip']){
        $ip = $dataa['ip'];
    }else{
        $data = file_get_contents("https://ip.nf/me.json");
        $dataa = json_decode($data,true);
        if($dataa['ip']['ip']){
            $ip = $dataa['ip']['ip'];
        }else{
            $ip = "获取失败";
        }
    }
}
// 2.定位IP经纬度
$json1 = file_get_contents("http://api.map.baidu.com/location/ip?ip=$ip&ak=GkhEVEawxSmRzSgv64bLCH2ALkWI6sCt&coor=bd09ll");
$info1 = json_decode($json1,true);
if($info1['status'] == '0')
{
    $latitude = $info1['content']['point']['y'];
    $longitude = $info1['content']['point']['x'];
}
else
{
    // (1) 速度较快，较不精准
    $data = file_get_contents("https://ip.seeip.org/geoip");
    $dataa = json_decode($data,true);
    if($dataa['latitude']){
        $latitude = $dataa['latitude'];
        $longitude = $dataa['longitude'];
    }else{
        // (2) 速度较慢，较精准
        $data = file_get_contents("https://ip.nf/me.json");
        $dataa = json_decode($data,true);
        if($dataa['ip']['latitude']){
            $latitude = $dataa['ip']['latitude'];
            $longitude = $dataa['ip']['longitude'];
        }else{
            $latitude = "获取失败";
            $longitude = "获取失败";
        }
    }
}
// 3.获取IP位置
$json2 = file_get_contents("http://api.map.baidu.com/reverse_geocoding/v3/?ak=GkhEVEawxSmRzSgv64bLCH2ALkWI6sCt&output=json&coordtype=wgs84ll&location=".$info1['content']['point']['y'].",".$info1['content']['point']['x']);
$json2=str_replace("renderReverse&&renderReverse(","",$json2);
$json2=str_replace(")","",$json2);
$info2 = json_decode($json2,true);
if($info2['status'] == '0')
{
    $ip_location = $info2["result"]["formatted_address"];//获取经纬网
}
else
{
    $data = file_get_contents("https://whois.pconline.com.cn/ipJson.jsp");
    $data = str_replace("if(window.IPCallBack) {IPCallBack(","",$data);
    $data = str_replace(");}","",$data);
    $dataa = json_decode($data,true);
    if($dataa['addr']){
        $ip_location = $dataa;
    }else{
        $ip_location = "获取失败";
    }
}
?>
