<?php
class DateTimeHelper
{

	public static function FormatDateTime($timestamp)
	{
		$dateFormat = Fw::app()->getModule('System')->getSysOption('dateformat');
		$timeFormat = Fw::app()->getModule('System')->getSysOption('timeformat');
		if(!$timestamp)return '';
		return date($dateFormat.' '.$timeFormat, intval($timestamp));
	}
	
	public static function FormatDate($timestamp)
	{
		$dateFormat = Fw::app()->getModule('System')->getSysOption('dateformat');
		
		if(!$timestamp)return '';
		return date($dateFormat,intval($timestamp));
	}
	
	public static function FormatTime($timestamp)
	{
		$timeFormat = Fw::app()->getModule('System')->getSysOption('timeformat');
		
		if(!$timestamp)return '';
		return date($timeFormat, intval($timestamp));
	}
}