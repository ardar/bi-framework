<?php

class AppSetting
{
	public $appId = 'biface';
	public $appName = "Management System";
	public $version = "2.3";
	public $powerBy = "Perconsoft";
	public $defaultTimezone = "Asia/Chongqing";
	
	public $cookiePrefix = "__FW2_";
	
	public $uploadDir = "uploadfiles/";
	public $thumbnailDir = "uploadfiles/thumbnails/";
	public $cacheDir = "runtime/caches/";
	public $logDir = "runtime/logs/";

	public $userComponent = "User";
	public $requestComponent = "HttpRequest";
	public $responseComponent = "HttpResponse";
	public $cacheComponent = ""; 
	
	public $viewComponent = "NativeView";
	
	public $defaultLayout = "layouts/mainframe.php";
	
	public $dbEngine = "PDODatabase";
	public $dbms = "mysql";
	public $dbHost = "127.0.0.1";
	public $dbPort = 3306;
	public $dbUser = "";
	public $dbPass = "";
	public $dbName = "";
	public $dbTablePrefix = "";
	
	
	/**
	 * 配置加载的模块
	 * @var unknown
	 */
	public $loadModules = array(
			'System',
	);
}