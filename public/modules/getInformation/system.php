<?php
if(stristr($_SERVER['HTTP_USER_AGENT'],'Android')) {
$system = 'Android';
}else if(stristr($_SERVER['HTTP_USER_AGENT'],'iPhone')){
  $system = 'iOS';
}
else if(stristr($_SERVER['HTTP_USER_AGENT'],'Windows')){
  $system = 'Windows';
}
else if(stristr($_SERVER['HTTP_USER_AGENT'],'Macintosh')){
  $system = 'MacOS';
}
else if(stristr($_SERVER['HTTP_USER_AGENT'],'Linux')){
  $system = 'Linux';
}
else if(stristr($_SERVER['HTTP_USER_AGENT'],'Unix')){
  $system = 'Unix';
}
else{
$system = 'Unknow';
}