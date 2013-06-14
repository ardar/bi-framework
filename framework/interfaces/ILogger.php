<?php
interface ILogger 
{
	public function __construct($config);
	
	public function log($msg, $level, $params);
	
	public function flush();
}