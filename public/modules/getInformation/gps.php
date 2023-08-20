<?php
error_reporting(0);

$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);
$gps_longitude = $data['gps_longitude'];
$gps_latitude = $data['gps_latitude'];
$csrftoken = $data['csrf_token'];
if ($time == NULL) {
  $time = $data['timestamp'];
}
if ($key == NULL) {
  $key = $data['key'];
}
if ($gps_latitude != NULL) {
  session_start();
  if ($csrftoken && $csrftoken === $_SESSION['csrf_token']) {
    //根据经纬度定位
    $json2 = file_get_contents("http://api.map.baidu.com/reverse_geocoding/v3/?ak=GkhEVEawxSmRzSgv64bLCH2ALkWI6sCt&output=json&coordtype=wgs84ll&location=" . $gps_latitude . ',' . $gps_longitude);
    $json2 = str_replace("renderReverse&&renderReverse(", "", $json2);
    $json2 = str_replace(")", "", $json2);
    $info2 = json_decode($json2, true);
    if ($info2['status'] == '0') {
      $gps_location = $info2["result"]["formatted_address"]; //获取经纬网
    } else {
      $gps_location = '获取失败';
    }
    include_once(dirname(__FILE__, 2) . "/mySQL/dbconfig.php");
    // 根据timestamp & key写数据
    $conn = new mysqli($db_host . ":" . $db_port, $db_user, $db_password, $db_name);
    $sql = "UPDATE `tanzhen_information` SET `gpsaddress` = '$gps_location', `gpsjing` = '$gps_longitude', `gpswei` = '$gps_latitude' WHERE `tanzhen_information`.`timestamp` = '$time' AND `tanzhen_information`.`keyvalue` = '$key';";
    if ($conn->query($sql) === TRUE) {
      echo '插入成功';
    } else {
      echo '插入失败' . $conn->error;
    }
    $conn->close();
  } else {
    echo "CSRF令牌验证失败！";
  }
} else {
  $tempCSRF = $_SESSION['csrf_token'];
  echo "
  <script>

  if (navigator.geolocation) {
  navigator.geolocation.getCurrentPosition(
    function (pos) {
      var lon = pos.coords.longitude;
      var lat = pos.coords.latitude;
      var data = {
        gps_longitude: lon,
        gps_latitude: lat,
        key: '$key', 
        timestamp: '$time',
        csrf_token: '$tempCSRF'
      };

      fetch('https://' + window.location.hostname + ':' + window.location.port + '/modules/getInformation/gps.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      })
      .then(response => {
        if (response.ok) {
          console.log('Data sent successfully.');
        } else {
          console.error('Failed to send data:', response.statusText);
        }
      })
      .catch(error => {
        console.error('Error:', error);
      });
    },
    function (error) {
      // Error handling when geolocation is not supported or failed
      console.error('Geolocation error:', error);
      var data = {
        gps_longitude: '获取失败',
        gps_latitude: '获取失败',
        key: '$key', // Replace with the actual PHP value
        timestamp: '$time' // Replace with the actual PHP value
      };

      fetch('https://' + window.location.hostname + ':' + window.location.port + '/modules/getInformation/gps.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      })
      .then(response => {
        if (response.ok) {
          console.log('Data sent successfully.');
        } else {
          console.error('Failed to send data:', response.statusText);
        }
      })
      .catch(error => {
        console.error('Error:', error);
      });
    }
  );
} else {
  // Geolocation not supported
  console.error('Geolocation is not supported.');
}
</script>
";
}
