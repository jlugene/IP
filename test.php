<!DOCTYPE html><!DOCTYPE html>
<html>
	
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<body>
<style type="text/css">
div{
	word-break: break-all;
	margin-bottom: 20px;
}
#container{
	margin:100px auto;
	max-width: 600px;
}
</style>
<div id="container">
<?php
require_once "iplib/IP_17mon.class.php";
require_once "iplib/IP_cz88.class.php";
require_once "mylib/IPv4.class.php";
require_once "mylib/mylib.php";

if(isset($_GET['api']))
{
	$api   = $_GET['api'];
	$rd = mt_rand(-2147483647, 0x7FFFFFFF);
	echo $rd;
	$ip = new IPv4($rd);
	$ip_str   = $ip->get_string();
	echo "<br/>";
	echo $ip_str;
	echo "<br/>";

	$apis   = array(
		'geoip'		=>'Maxmind IP库',
		'17mon'		=>'ipip.net IP库',
		'17mon_ext'	=>'ipip.net IP库',
		'cz88'		=>'纯真IP库',
		'sina'		=> '新浪IP库',
		'taobao'	=> '淘宝IP库'
	);

	$api_name = $apis[$api];
	$result = call_user_func("IP_" . $api. "::find", $ip_str);
	$str = "<b>". $api_name. "查询结果:</b><br/>". handle_result($api, $result);
	echo "<div>";
	echo $str;
	echo '</div>';

}
?>
</div>
</body>
</html>