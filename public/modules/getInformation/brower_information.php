<?php
    $agent=$_SERVER["HTTP_USER_AGENT"];
    if(strpos($agent,'MSIE')!==false || strpos($agent,'rv:11.0'))
    $browser= "IE"; //需要注意的是，IE已经退出市场
    else if(strpos($agent,'Firefox')!==false)
    $browser= "Firefox";
    else if(strpos($agent,'Chrome')!==false)
    $browser= "Chrome";
    else if(strpos($agent,'Opera')!==false)
    $browser= 'Opera';
    else if((strpos($agent,'Chrome')==false)&&strpos($agent,'Safari')!==false)
    $browser= 'Safari';
		else if(strpos($agent,'Edge')!==false)
		$browser= 'Edge';
    else $browser = "Unknow";
    $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 4);
    if (preg_match("/zh-c/i", $lang))  
    $browser_language= "简体中文";  
    else if (preg_match("/zh/i", $lang))  
    $browser_language= "繁体中文";  
    else if (preg_match("/en/i", $lang))  
    $browser_language= "英语";  
    else if (preg_match("/fr/i", $lang))  
    $browser_language= "法语";  
    else if (preg_match("/de/i", $lang))  
    $browser_language= "德语";  
    else if (preg_match("/jp/i", $lang))  
    $browser_language= "日语";  
    else if (preg_match("/ko/i", $lang))  
    $browser_language= "韩语";  
    else if (preg_match("/es/i", $lang))  
    $browser_language= "西班牙语";  
    else if (preg_match("/sv/i", $lang))  
    $browser_language= "瑞典语";  
    else if (preg_match("/nk/i", $lang))  
    $browser_language= "荷兰语";  
    else if (preg_match("/es/i", $lang))  
    $browser_language= "西班牙语";  
    else if (preg_match("/it/i", $lang))  
    $browser_language= "意大利语";  
    else if (preg_match("/no/i", $lang))  
    $browser_language= "挪威语";  
    else if (preg_match("/hu/i", $lang))  
    $browser_language= "匈牙利语";  
    else if (preg_match("/tr/i", $lang))  
    $browser_language= "土耳其语";  
    else if (preg_match("/cs/i", $lang))  
    $browser_language= "捷克语";  
    else if (preg_match("/sl/i", $lang))  
    $browser_language= "斯洛文尼亚语";  
    else if (preg_match("/pl/i", $lang))  
    $browser_language= "波兰语";  
    else $browser_language= "Unknown";  
?>