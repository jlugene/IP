<?php

/*
淘宝在线IP库API：
http://ip.taobao.com/service/getIpInfo.php?ip=$ip
返回的数据是JSON格式的
*/

class IP_taobao
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

		$json = file_get_contents("http://ip.taobao.com/service/getIpInfo.php?ip=$ip");
		self::$cached[$nip] =  $json;
		return self::$cached[$nip];
	}


}

?>