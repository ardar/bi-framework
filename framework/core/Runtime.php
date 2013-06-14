<?php
class Runtime 
{
	public $timestamp;
	public $clientIp;
	public $referer;
	public $queryString;
	public $beginExecuteTime;
	
	public function init()
	{
		$this->referer = $_SERVER['HTTP_REFERER'];
		$this->queryString = $_SERVER["QUERY_STRING"];
		$this->timestamp = time();
		$this->clientIp = OpenDev::getRemoteIP();

		$mtime = explode(' ', microtime());
		$this->beginExecuteTime = $mtime[1] + $mtime[0];
	}
	
}