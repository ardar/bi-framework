<?php
class FwException extends Exception
{
	protected $_session = null;
	protected $_nextlocation = "";
	/**
	 * the previous exception/inner exception.
	 * @var Exception
	 */
	protected $_previousException = null;
	
	public function __construct($message, $nextlocation=LOCATION_BACK, $code=null, Exception $previous=null)
	{
		parent::__construct($message, $code===null ? $code : intval($code), $previous);
		$this->_nextlocation = $nextlocation;
		$this->_session = $_SESSION;
		$this->_previousException = $previous;
	}
	
	public function GetNextLocation()
	{
		return $this->_nextlocation;
	}
	
	public function __toString()
	{
		return parent::__toString()." session:".$this->_session;
	}
}