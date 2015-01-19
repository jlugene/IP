<?php
require_once "iplib/IP_17mon.class.php";
require_once "iplib/IP_cz88.class.php";
require_once "mylib/IPv4.class.php";
require_once "mylib/mylib.php";

function show_usage()
{
	echo_adv("本脚本程序使用某种IP地址库，查询一段IP范围内所有IP的地理位置.");
	echo_adv("目前有种IP地址库：MaxMind、ipip.net IP库、纯真IP库、新浪IP库.");
	echo_adv("脚本用法: php demo_cli.php -from=IP_from -to=IP_to -api=api ");
	echo_adv("其中api是 geoip 或者 17mon 或者 17mon_ext 或者 cz88 或者 sina  ");
	echo_adv("用法举例: php demo_cli.php  -from=255.0.0.0  -to=255.255.255.255  -api=sina");
	echo_adv("用法举例: php demo_cli.php  -from=1.0.0.0  -to=3.255.255.0  -api=17mon");
}

$from = "";
$to   = "";
$api  = "";

foreach($argv as $arg) 
{
	if (strtolower(substr($arg, 0, 5)) == "-from") {
		$from  = substr($arg, 6);
	}
	if (strtolower(substr($arg, 0, 3)) == "-to") {
		$to = substr($arg, 4);
	}
	if (strtolower(substr($arg, 0, 4)) == "-api") {
		$api = strtolower(substr($arg, 5));
	}
}


if(empty($from) || empty($to) || empty($api))
{
	show_usage();
	exit(1);
}

if($api != "17mon"  && $api != "17mon_ext" && $api != "cz88" &&
	$api != "sina" && $api != "geoip")
{
	show_usage();
	exit(1);
}

$num = 0;
$ip_from = new IPv4($from);
$ip_to   = new IPv4($to);

for ($ip = $ip_from; $ip->less_than($ip_to); $ip = $ip->plus(1))
{
	$ip_str   = $ip->get_string();
	$location = "";
		
	$location = call_user_func("IP_" . $api. "::find", $ip_str);
	
	if (empty($location))
	{
		continue;
	} 

	if ($location == 'N/A')
	{ 
		echo_adv($ip_str. ":". "N/A");
		continue;
	}

	$json = handle_result($api, $location);
	echo_adv($ip_str. ":\t". $json);
}

?>