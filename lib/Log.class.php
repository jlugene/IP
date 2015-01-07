<?php


class Log
{
	public $m_log_file;

	function  __construct ($log_file_name) 
	{
		$this->m_log_file = $log_file_name;
	}


	function Write($str)
	{
		file_put_contents($this->m_log_file, $str."\r\n",  FILE_APPEND  |  LOCK_EX );
	}
}

?>