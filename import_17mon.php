<?php
require_once "lib/IPv4.class.php";
require_once "lib/IP_cz88.class.php";
require_once "lib/IP_taobao.class.php";
require_once "lib/IP.class.php";
require_once "lib/Log.class.php";



if($argc != 3)
{
	$log->Write("Wrong argments. There must be 3 argments.\r\n");
	$log->Write("Usage: php import_17mon.php IP_import_from IP_import_to \r\n");
	$log->Write("Example: php import_17mon.php  255.0.0.0  255.255.255.255 \r\n ");
	$log->Write("Example: php import_17mon.php  255.0.0.0  255.255.255.255 \r\n ");
	return;
}


$from  = $argv[1];
$to    = $argv[2];


$mysqli = new mysqli("localhost", "root", "123456", "ip");
if ($mysqli->connect_errno) {
	return;
}


$num = 0;
$ip_from = new IP($from);
$ip_to   = new IP($to);

$log = new Log($from. "_". $to. ".log");

try
{
	$mysqli->begin_transaction(); 
	
	for ($ip = $ip_from; $ip->less_than($ip_to); $ip = $ip->plus(1))
	{
		$ip_str   = $ip->get_string();
		$log->Write("\r\n". $ip_str. ":");
		$location = IP_cz88::find($ip_str);

		if (empty($location))
		{
			continue;
		} 
		if ($location == 'N/A')
		{ 
			$log->Write("N/A");
			continue;
		}
		if(!is_array($location))
		{
			$log->Write("not an array");
			continue;
		}

		//17mon的格式
		$location_str = json_encode($location, JSON_UNESCAPED_UNICODE);
		$log->Write(iconv("UTF-8", "GBK", $location_str));
			
		if($location[0] == "保留地址" || $location[0] == "局域网")
		{
			continue;
		}
		else
		{
			//$ip->insert_or_update($mysqli, $location_str);
		}

		$num = (++$num) % 100;
		if($num == 0)
		{
			$mysqli->commit();
			$mysqli->begin_transaction();
		}
	}
	$mysqli->commit();
}
catch(Exception $e) 
{
	$mysqli->rollback();
}

$mysqli->close();


	function insert_or_update($mysqli, $ipv4, $location_str)
	{
		$query = $mysqli->query("select * from ip where id = ". $ipv4->get_id(). ";");
		if($query->num_rows > 0)
		{
			$stmt = $mysqli->prepare("update ip set location_17mon = ?, uptime_17mon = ? where id = ? ");
			$stmt->bind_param('ssd', $location_str, date("Y-m-d H:i:s") , $ipv4->get_id());
			$stmt->execute();
		}
		else
		{
			$stmt = $mysqli->prepare("insert into ip(id, name, location_17mon, uptime_17mon) VALUES (?, ?, ?, ?)");
			$stmt->bind_param('dsss', $ipv4->get_id(), $ipv4->get_string(), $location_str, date("Y-m-d H:i:s"));
			$stmt->execute();	   
		}

	}
?>