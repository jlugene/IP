<?php

/*
	纯真IP地址数据库
	官方网站：http://www.cz88.net/
	地址库格式详解：http://pcedu.pconline.com.cn/empolder/gj/java/0505/612860_all.html#content_page_2
	请注意及时上官网更新IP数据库
	Coded by 金小虎 278643263@qq.com
*/

class cls_ipAddress
{
	//文件读取指针
	private $fp;

	//第一条索引的指针
	private $m_indexBegin;

	//最后一条索引的指针
	private $m_indexEnd;

	const RECORD_LENGTH = 7;

	public function __construct($filename= "qqwry.dat")
	{
		$this->fp = 0;
		if(($this->fp = @fopen($filename, "rb")) !== false)
		{
			//文件的第一个4字节和第二个4字节，即第一条和最后一条索引的位置
			$this->m_indexBegin = $this->getlong();
			$this->m_indexEnd = $this->getlong();
			register_shutdown_function(array(&$this, "__destruct"));
		}
	}

	public function __destruct()
	{
		if($this->fp)
		{
			@fclose($this->fp);
		}
		$this->fp= 0;
	}

	//从文件流中读取4个字节，解析成一个32位无符号整数
	private function getlong()
	{
		$result = unpack("Vlong", fread($this->fp, 4));
		return $result["long"];
	}

	//从文件流中读取3个字节，解析成一个32位无符号整数
	private function getlong3()
	{
		//qqwry.dat存储数据时用的是小端法
		$result = unpack("Vlong", fread($this->fp, 3). chr(0));
		return $result["long"];
	}

	//返回IP地址的32位无符号整数形式
	private function packip($ip)
	{
		return pack("N", intval(ip2long($ip)));
	}

	private function getstring($data = "")
	{
		$char = fread($this->fp, 1);
		while (ord($char) > 0)
		{
			$data .= $char;
			$char = fread($this->fp, 1);
		}
		return $data;
	}

	private function getarea()
	{
		$byte= fread($this->fp, 1);
		switch(ord($byte))
		{
			case 0:
				$area = "";
				break;
			case 1:
			case 2:
				fseek($this->fp, $this->getlong3());
				$area= $this->getstring();
				break;
			default:
				$area= $this->getstring($byte);
				break;
		}
		return $area;
	}


	public function getlocation($ip)
	{
		if(!$this->fp)
		{
			return false;
		}
		$location["ip"] = gethostbyname($ip);
		$ip = $this->packip($location["ip"]);

		//对半查找，找到$ip所在的索引
		$from = 0;
		$to = ($this->m_indexEnd - $this->m_indexBegin) / self::RECORD_LENGTH;
		$findip = $this->m_indexEnd;
		while($from <= $to)
		{
			$i = floor(($from + $to) / 2);
			fseek($this->fp, $this->m_indexBegin + $i * self::RECORD_LENGTH);
			$startip = strrev(fread($this->fp, 4));
			if($ip < $startip)
			{
				$to = $i - 1;
			}
			else
			{
				fseek($this->fp, $this->getlong3());
				$endip = strrev(fread($this->fp, 4));
				if($ip > $endip)
				{
					$from = $i + 1;
				}
				else
				{
					$findip = $this->m_indexBegin + $i * 7;
					break;
				}
			}
		}
		fseek($this->fp, $findip);
		$location["startip"] = long2ip($this->getlong());
		$offset = $this->getlong3();
		fseek($this->fp, $offset);
		$location["endip"] = long2ip($this->getlong());
		$byte = fread($this->fp, 1);
		switch(ord($byte))
		{
			case 1:
				$countryOffset = $this->getlong3();
				fseek($this->fp, $countryOffset);
				$byte= fread($this->fp, 1);
				switch(ord($byte))
				{
					case 2:
						fseek($this->fp, $this->getlong3());
						$location["country"] = $this->getstring();
						fseek($this->fp, $countryOffset + 4);
						$location["area"] = $this->getarea();
						break;
					default:
						$location["country"] = $this->getstring($byte);
						$location["area"] = $this->getarea();
						break;
				}
				break;
			case 2:
				fseek($this->fp, $this->getlong3());
				$location["country"] = $this->getstring();
				fseek($this->fp, $offset+8);
				$location["area"] = $this->getarea();
				break;
			default:
				$location["country"] = $this->getstring($byte);
				$location["area"] = $this->getarea();
				break;
		}
		if(trim($location["country"]) == "CZ88.NET")
		{
			$location["country"] = "";
		}
		if(trim($location["area"]) == "CZ88.NET")
		{
			$location["area"] = "";
		}
		return $location;
	}
}


class IP_cz88
{
	private static $instance;  
	private static $cached =  array();

	public static function find($ip)
	{   
		if (empty($ip) ===  TRUE)
		{
			return false;
		}
		$nip   =  gethostbyname($ip);
		$ipdot =  explode('.',  $nip);

		if ($ipdot[0] < 0 || $ipdot[0] > 255 || count($ipdot) !==  4)
		{
			return false;
		}

		if (isset(self::$cached[$nip]) === TRUE)
		{
			return self::$cached[$nip];
		}

		if (self::$instance == null)
		{
			self::$instance = new cls_ipAddress(__DIR__ . '/qqwry.dat');
		}
		$location =  self::$instance->getlocation($ip);

		self::$cached[$nip] = $location;
		return self::$cached[$nip];
	}
}

?>