<?php
class Fw
{
	private static $_appInstance = null;
	
	/**
	 * Gets global App instance.
	 * @param int $appId
	 * @return IApplication
	 * @throws FwException
	 */
	public static function app()
	{
		if(self::$_appInstance==null)
		{
			//Fw::$_appInstance = new Application();
			throw new FwException("App Not initialized.");
		}
		return self::$_appInstance;
	}
	
	public static function setApp(IApplication $app)
	{
		self::$_appInstance = $app;
	}
	
	public static function getAppSetting()
	{
		return self::GetApp()->GetSetting();
	}

	/**
	 * the html helper instance
	 * @var FwHtml
	 */
	private static $_htmlHelper;
	
	/**
	 * 
	 * @return IHtml
	 */
	public static function Html()
	{
		if(self::$_htmlHelper==null)
		{
			self::$_htmlHelper = new FwHtml();
		}
		return self::$_htmlHelper;
	}
	
	private function __construct()
	{		
	}
}