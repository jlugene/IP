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

	<form action="demo_web.php" method="get">
		输入IP<br/>
		<input type="text" name="ip"><br/><br/>
		<button type="submit">查询</button>
	</form>

<?php
require_once "iplib/IP_17mon.class.php";
require_once "iplib/IP_cz88.class.php";
require_once "mylib/mylib.php";

if(isset($_GET['ip']))
{
	$ip_str   = $_GET['ip'];
	$apis   = array(
		'geoip'		=>'Maxmind IP库',
		'17mon'		=>'ipip.net IP库',
		'cz88'		=>'纯真IP库',
		'sina'		=> '新浪IP库'
	);

	foreach ($apis as $api => $api_name) {
		$result = call_user_func("IP_" . $api. "::find", $ip_str);

		$str = "<b>". $api_name. "查询结果:</b><br/>". 
				handle_result($api, $result);
		$div_id = 'ip_'. $api;
		echo "<div id='$api'>";
		echo $str;
		echo '</div>';
	}
}


?>

</div>
</body>
</html>