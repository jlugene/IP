<?php

/*
新浪在线IP库API 
新浪IP库支持返回多种数据格式：
HTML格式：http://int.dpool.sina.com.cn/iplookup/iplookup.php?ip=IP地址字符串
JSON格式：http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=IP地址字符串
JSONP格式：http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=IP地址字符串
本类实现调用它JSON格式
*/

class IP_sina
{
	private static $cached = array();

	public static function find($ip)
	{
		if (empty($ip) === TRUE)
		{
			return false;
		}
		$nip   = gethostbyname($ip);
		$ipdot = explode('.', $nip);

		if ($ipdot[0] < 0 || $ipdot[0] > 255 || count($ipdot) !== 4)
		{
			return false;
		}

		if (isset(self::$cached[$nip]) === TRUE)
		{
			return self::$cached[$nip];
		}

		$json = file_get_contents("http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=$ip");
		//新浪IP库还支持返回HTML格式和JSONP格式的数据

		self::$cached[$nip] =  $json;
		return self::$cached[$nip];
	}
}

?>