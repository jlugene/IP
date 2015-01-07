<?php

//判断当前操作系统是否是Windows
function is_windows()
{
	return strtoupper(substr(PHP_OS,0,3)) === 'WIN' ? true : false;
}

//echo的包装函数
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
?>