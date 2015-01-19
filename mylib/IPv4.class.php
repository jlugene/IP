<?php

require_once "mylib.php";

/*
	IPv4类库
	Code by 金小虎 278643263@qq.com
*/

class IPv4
{
	
	//IP地址与32位有符号整数是一一对应的
	//因此用一个32位有符号整数保存IP
	private  $id;

	function __construct ($param) 
	{
		if(is_integer($param))
		{
			$this->id = $param;
		}
		elseif(is_string($param))
		{
			$this->id = ip2long($param);
			if($this->id === false)
			{
				echo_adv("$param"."is not a correct IP address!");
			} 			
		}
		else
		{
			echo_adv("$param"."is not a correct IP address!");
		}
	}

    //IP地址的无符号整数格式
	function get_id()
	{
		return sprintf("%u\n" , $this->id);  
	}

    // IP的字符串格式
	function get_string()
	{
		//注意：在PHP中, >>是带符号右移， <<是左移
		$str0 = (($this->id  >> 24) & 0x000000FF);
		$str1 = (($this->id  >> 16) & 0x000000FF);
		$str2 = (($this->id  >> 8) & 0x000000FF);
		$str3 = ($this->id & 0x000000FF);
		return $str0. "." . $str1. ".". $str2. ".". $str3;
	}


	//是否小于另一个ip
	//小于的意思是指，IP地址转换成无符号整数后，无符号整数的大小比较
	//比如127.255.255.255是小于128.0.0.0的（虽然按有符号整数算，127.255.255.255是正数 而128.0.0.0是负数）
	function less_than($ip)
	{
		if($this->id >= 0 && $ip->id >= 0)
		{
			return $this->id < $ip->id;
		}
		if($this->id < 0 && $ip->id < 0)
		{
			return $this->id < $ip->id;
		}
		if($this->id >= 0 && $ip->id < 0)
		{   
			//此时若按整数算，$this->id是正的， $ip->id是负的
			return true;
		}	
		if($this->id < 0 && $ip->id >= 0)
		{
			//此时若按整数算，$this->id是负的， $ip->id是正的
			return false;
		}
	}


	//IP地址的加运算
	//加运算是指，将IP地址转换成无符号整数后，加上相应的数字，得到的无符号整数再转换成相应的IP
	function plus($i)
	{
		if(!is_integer($i))
		{
			echo_adv("IP must plus an interger!");
			return false;
		}
		if($i < 0)
		{
			echo_adv("IP must plus an positive interger!");
			return false;
		}

		if ($this->id >= 0 && (0x7FFFFFFF - $this->id >= $i))
		{
			return new IPv4($this->id + $i);
		}
		if ($this->id < 0)
		{
			return new IPv4($this->id + $i);
		}
		$a = $i - (0x7FFFFFFF - $this->id);
		//这里有个坑：
		//32系统中，PHP_INT_MAX是 2147483647 ，
		//字面量 -2147483648不会被认为是整数（C语言也有这种情况）
		//可以用 -2147483647 - 1
		return new IPv4(-2147483647 + $a - 2);
	}



}
?>