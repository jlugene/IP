<?php
/*
	一些类库
	Code by 金小虎 278643263@qq.com
*/

//判断当前操作系统是否是Windows
function is_windows()
{
	return strtoupper(substr(PHP_OS,0,3)) === 'WIN' ? true : false;
}

//echo的包装函数，为了CLI命令行模式下在终端用合适的编码显示字符
function echo_adv($str)
{
	if(is_windows())
	{
		echo iconv("UTF-8", "GBK", $str)."\r\n";
	}
	else
	{
		echo iconv("GBK", "UTF-8", $str)."\n";
	}
}

//用递归的方法将一个对象内所有的元素全部做iconv转码
function iconv_obj($in_charset, $out_charset, $obj)
{
	if(is_string($obj))
	{
		return iconv($in_charset, $out_charset, $obj);
	}
	if(is_array($obj))
	{
		$arr = array();
		foreach ($obj as $key => $value) {
			$newkey = iconv_obj($in_charset, $out_charset, $key);
			$newvalue = iconv_obj($in_charset, $out_charset, $value);
			$arr[$newkey] = $newvalue;
		}
		return $arr;
	}
	if(is_object($obj))
	{
		$className = get_class($obj);
		$newobj = call_user_func ($className);
		$obj_vars = get_object_vars($obj) ;
		foreach ($obj_vars as $key => $value) {
			$newvalue = iconv_obj($in_charset, $out_charset, $value);
			$newobj->key = $newvalue;
		}
	}
	return $obj;
}

//monip，PHP扩展的方式查询IP
class IP_17mon_ext
{
	public static function find($ip)
	{
		if(!function_exists("monip_init"))
		{
			echo "出错。请检查是否安装了ipip.net的PHP扩展！";
			return false;
		}
		monip_init(__DIR__ . "/../iplib/17monipdb.dat");
		return monip_find($ip);
	}
}

//PHP扩展geoip查询IP
class IP_geoip
{
	public static function find($ip)
	{
		if(!function_exists("geoip_record_by_name"))
		{
			echo "出错。请检查是否安装了PHP扩展geoip！";
			return false;
		}
		return geoip_record_by_name($ip);
	}
}

//查询新浪JSON格式的IP
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

function handle_result($api, $result)
{
	
	switch ($api) {
		case 'geoip':
			return var_export($result, true);
		case '17mon':
		case '17mon_ext':
			return var_export($result, true);
		case 'cz88':
			$arr = iconv_obj("GBK", "UTF-8", $result);
			return var_export($arr, true);
		case 'sina':
			//返回的json数据中，汉字是用"\u5409"这种格式编码的，把它解码后再用汉字编码
			$obj = json_decode($result);
			return json_encode($obj, JSON_UNESCAPED_UNICODE);
			break;
		default:
			break;
	}

}
?>