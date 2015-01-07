<?php
require_once "lib/IP_17mon.class.php";
require_once "lib/IP_cz88.class.php";
require_once "lib/IP_taobao.class.php";
require_once "lib/IP_sina.class.php";
require_once "lib/IPv4.class.php";
require_once "lib/Log.class.php";
require_once "lib/mylib.php";


function show_usage()
{
	echo_esc("本脚本程序使用某种IP地址库，查询一段IP范围内所有IP的地理位置.");
	echo_esc("目前有四种IP地址库：ipip.net IP库、纯真IP库、新浪IP库、淘宝IP库.");
	echo_esc("脚本用法: php show_ip.php IP_from IP_to api ");
	echo_esc("其中api是 17mon 或者 cz88 或者 sina 或者 taobao ");
	echo_esc("例如: php show_ip.php  255.0.0.0  255.255.255.255  sina");
	echo_esc("例如: php show_ip.php  1.0.0.0  3.255.255.0  17mon");
}

if($argc != 4)
{
	show_usage();
	exit(1);
}

$from  = $argv[1];
$to    = $argv[2];
$api = strtolower($argv[3]);

if($api != "17mon" && $api != "cz88" &&
	$api != "sina" && $api != "taobao")
{
	show_usage();
	exit(1);
}


$num = 0;
$ip_from = new IPv4($from);
$ip_to   = new IPv4($to);
$log = new Log($from. "_". $to. "_". $api. ".log");

for ($ip = $ip_from; $ip->less_than($ip_to); $ip = $ip->plus(1))
{
	$ip_str   = $ip->get_string();
	$location = call_user_func("IP_" . $api. "::find", $ip_str);
	if (empty($location))
	{
		continue;
	} 

	if ($location == 'N/A')
	{ 
		echo_esc($ip_str. ":". "N/A");
		continue;
	}

	//$json = handle_result($location, $api);

	//echo_adv($ip_str. ":\t". $json);
	echo_adv($ip_str. ":\t". var_dump($location));
}

function handle_result($location, $api)
{
	switch ($api) {
		case '17mon':
			return json_encode($location, JSON_UNESCAPED_UNICODE);
		case 'cz88':
			$arr = iconv_obj("GBK", "UTF-8", $location);
			return json_encode($arr, JSON_UNESCAPED_UNICODE);
		case 'sina':
			//返回的json数据中，汉字是用"\u5409"这种格式编码的，把它解码后再用汉字编码
			$obj = json_decode($location);
			return json_encode($obj, JSON_UNESCAPED_UNICODE);
			break;	
		case 'taobao':
			$obj = json_decode($location);
			return json_encode($obj, JSON_UNESCAPED_UNICODE);
		default:
			break;
	}

}
?>