<?php

/**
 * the web application 
 * @author ardar
 * @since 2.0
 */
class Application implements IApplication
{
	protected $_version = "2.3";
	
	/**
	 * @var AppSetting
	 */
	protected $_settings = null;
	
	/**
	 * The current runtime instance
	 * @var Runtime
	 */
	protected $_runtime = null;
	
	/**
	 * configured module object list
	 * @var array
	 */
	protected $_modules = array();
	
	/**
	 * the current moduleId
	 * @var string
	 */
	protected $_moduleId = null;
	
	/**
	 * The IUser instance
	 * @var IUser
	 */
	protected $_user = null;
	
	/**
	 * The IRequest instance
	 * @var IRequest
	 */
	protected $_request = null;
	
	/**
	 * The IResponse instance
	 * @var IRequest
	 */
	protected $_response = null;
	/**
	 * the IView instance, view or template engine.
	 * @var IView
	 */
	protected $_view = null;
	
	/**
	 * the cache management object instance.
	 * @var ICache
	 */
	protected $_cache = null;
	
	/**
	 * the logger
	 * @var ILogger
	 */
	protected $_logger = null;
	
	/**
	 * the database instance.
	 * @var IDatabase
	 */
	protected $_db = null;
	
	/**
	 * the controller instance.
	 * @var FwController
	 */
	protected $_controller = null;
	
	/**
	 * The class auto loader instance.
	 * @var FwLoader
	 */
	protected $_loader = null;
	
	/**
	 * constructor
	 */
	public function __construct()
    {
		$this->initLoader();

		Fw::setApp($this);
    }
    
    public function __destruct()
    {
    	
    }
    
    public function initLoader()
    {
    	$this->_loader = new FwLoader();
    	
    	$this->_loader->init(FW_DIR);
    	
		spl_autoload_register(array($this->_loader, 'autoLoader'));
		
    	//$this->_classfiles = require(FW_DIR."Core/ClassMaps.php");

		// Imports all the framework files
    	$this->import(FW_DIR."core/");
    	$this->import(FW_DIR."exceptions/");
    	$this->import(FW_DIR."exts/");
    	$this->import(FW_DIR."helpers/");
    	$this->import(FW_DIR."html/");
    	$this->import(FW_DIR."models/");
    	$this->import(FW_DIR."widgets/");
    	
    }
    
    /**
     * (non-PHPdoc)
     * @see IApplication::import()
     */
    public function import($path)
    {
    	$this->_loader->import($path);
    }
    
    
    /**
     * (non-PHPdoc)
     * @see IApplication::end()
     */
    public function end()
    {
    	$this->clean();
    	exit;
    }
    
    /**
     * (non-PHPdoc)
     * @see IApplication::init()
     */
	public function init(AppSetting $setting)
	{
		try {
			set_error_handler(array($this, 'ExceptionErrorHandler'));
			
	    	$this->_settings = $setting;
	    	
			date_default_timezone_set($setting->defaultTimezone);

			$this->_runtime = new Runtime();			
			$this->_runtime->init();
			
			//Inits the request and response component
			$requestComponent = $this->getSetting()->requestComponent;
			$this->_request = new $requestComponent();
			
			$responseComponent = $this->getSetting()->responseComponent;
			$this->_response = new $responseComponent();

			// Load all configured modules
			foreach ($this->_settings->loadModules as $moduleId)
			{
				$moduleClassName = $moduleId."module";
				$fileName = APP_DIR."modules/$moduleId/$moduleClassName.php";
				if(file_exists($fileName)){
					require_once ($fileName);
					$this->_modules[$moduleId] = new $moduleClassName();
				}
				else {
					
				}
			}
			$this->GetModule('System')->init(null);
			
		} 
		catch (AuthException $e)
		{
			$this->OnMessage($e->GetMessage(), $e, $e->GetNextLocation());
		}
		catch (ArgumentException $e)
		{
			$this->OnMessage($e->GetMessage(), $e, $e->GetNextLocation());
		}
		catch (FwException $e) 
		{
			$this->OnMessage($e->getMessage(), $e);
		}
		catch(PDOException $e)
		{
			$this->OnError('数据库错误', $e);
		}
		catch(Exception $e)
		{
			$this->OnError(get_class($e).'错误', $e);
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IApplication::clean()
	 */
	public function clean()
	{
		if($this->_db) {
	    	$this->_db->clean();
		}
		if($this->_view) {
	    	$this->_view->clean();
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IApplication::run()
	 */
	public function run()
	{
		try 
		{
			$route = $this->getRequest()->getRoute();
			
			$moduleId = $route[FIELD_MODULE];
			
			$moduleTemplateDir = '';
						
			if($moduleId)
			{
			    $this->_moduleId = $moduleId;
				$moduleTemplateDir = APP_DIR."modules/".$moduleId."/views/";
				
				$module = $this->GetModule($moduleId);
				if($module && $moduleId!='System')
				{
					$moduleLoadOptions = array();
					$module->Init($moduleLoadOptions);
				}
			}
			
		    $ExTemplateDir = FW_DIR.'views/'.$this->_settings->ExTemplateDir; 
			$view_setting = array(
				'template_dir' => array($moduleTemplateDir, $ExTemplateDir, FW_DIR.'views/'),
				'compile_dir' => APP_DIR.'/runtime/caches/compiles/',
				'cache_dir' => APP_DIR.'/runtime/caches/',
				'debug_on' => 2,
			);
			// Init the view engine
			$viewClass = $this->GetSetting()->viewComponent;
			$this->_view = new $viewClass();
			$this->_view -> Init($view_setting);
			
			// Init the user
			if($this->_user==null)
			{
				$userClass = $this->GetSetting()->userComponent;
				$this->_user = new $userClass(1);
				$this->_user -> authByCookie();
				$this->_user -> getData();
			}
			
			$module->Dispatch($route);
		} 
		catch (AuthException $e)
		{
			$this->OnMessage($e->GetMessage(), $e, $e->GetNextLocation());
		}
		catch (FwException $e) 
		{
			$this->OnMessage($e->getMessage(), $e);
		}
		catch(PDOException $e)
		{
			$this->OnError('数据库错误', $e);
		}
		catch(Exception $e)
		{
			$this->OnError(get_class($e).'错误', $e);
		}
	}
	/**
	 * Gets the AppSetting instance.
	 * @return AppSetting
	 */
	public function getSetting()
	{
		return $this->_settings;
	}
	
	/**
	 * Gets the IRequest instance.
	 * @return IRequest
	 */
	public function getRequest()
	{
		return $this->_request;
	}

	/**
	 * Gets the IResponse instance.
	 * @return IResponse
	 */
	public function getResponse()
	{
		return $this->_response;
	}
	
	/**
	 * Gets the ICache instance.
	 * @return ICache
	 */
	public function getCache()
	{
		return $this->_cache;
	}
	
	/**
	 * Gets the IView instance
	 * @return IView
	 */
	public function getView($viewType='default')
	{
		if($viewType=='native')
		{
			return $this->_nativeview;
		}
		else
		{
			return $this->_view;
		}
	}
	
	/**
	 * Gets the ILogger instance
	 * @return ILogger
	 */
	public function getLogger()
	{
		return $this->_logger;
	}

    /**
     * (non-PHPdoc)
     * @see IApplication::getUser()
     */
    public function getUser()
    {
    	return $this->_user;
    }
    
    /**
     * (non-PHPdoc)
     * @see IApplication::setUser()
     */
    public function setUser(IUser $user)
    {
    	$this->_user = $user;
    }
	
	/**
	 * Gets the IDatabase instance for sys logging.
	 * @return IDatabase
	 */
	public function getDb()
	{
		if(!$this->_db)
		{
			// Init the database
			$this->_db = new PdoDatabase(
					$this->_settings->dbms, 
					$this->_settings->dbHost, 
					$this->_settings->dbPort, 
					$this->_settings->dbName, 
					$this->_settings->dbUser, 
					$this->_settings->dbPass);
			$this->_db -> Init(
    			    array(PDO::ATTR_PERSISTENT => true,
    			    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
    			    ));	
		}
		return $this->_db;
	}
	
	/**
	 * Gets the current IModule name
	 * @return string
	 */
	public function getModuleId()
	{
		return $this->_moduleId;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IApplication::getRuntime()
	 */
	public function getRuntime()
	{
		return $this->_runtime;
	}

	/**
	 * (non-PHPdoc)
	 * @see IApplication::getModule()
	 */
	public function getModule($moduleId='')
	{
		$moduleId = $moduleId ? $moduleId : $this->getModuleId();
		
		if (key_exists($moduleId, $this->_modules))
		{
			return $this->_modules[$moduleId];
		}
		return null;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IApplication::GetModules()
	 */
	public function getModules()
	{
		return $this->_modules;
	}
	
	/**
	 * Gets the modules' menu tree.
	 */
	public function getModuleMenus()
	{
	    $menus = array();
	    foreach ($this->_modules as $extname => $ext)
	    {
	        $extmenus = $ext->GetMenu();
	        foreach($extmenus as $menu)
	        {
	        	$menuname = $menu[1];
	        	$menus[$menuname]['menu'] = $menu;
	        	if(is_array($menu[4]))
	        	{
	        		foreach($menu[4] as $submenu)
	        		{
	        			$menus[$menuname]['sub'][] = $submenu;
	        		}
	        	}
	        }
	    }
	    return $menus;
	}
    
    public function ExceptionErrorHandler($errno, $errstr, $errfile, $errline ) 
    {
    	if (!(error_reporting() & $errno)) 
    	{
            // This error code is not included in error_reporting
            return;
    	}
    	$this->getModule()->getController()->message($errstr);
    	//TRACE( "ExceptionErrorHandler : $errno");
    	throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
	}
	
	/**
	 * handle the error message and exception.
	 * @param string $error
	 * @param Exception $exception
	 */
	public function onMessage($error, Exception $exception=null, $nextlocation=null)
	{
		if ($exception)
		{
			// TODO: Log
		}
		$module = $this->getModule($this->_moduleId);
	    if($module && $module->GetController())
	    {
	    	if(FW_DEBUG)
	        {
	        	syslog(LOG_INFO, $exception->getMessage());
	        	syslog(LOG_INFO, $exception->getTraceAsString());
	        }
    		if ($nextlocation)
    		{
    			$module->getController()->message($error, $nextlocation,true,$exception);
    		}
    		else
    		{
    			$module->getController()->message($error,LOCATION_BACK,true, $exception);
    		}
	    }
	    else 
	    {
	        echo "<div style='width:300px;height:200px;wrap;
	        margin:auto auto;border:solid 2px'>\n";
	        echo "<h3>$error</h3>\n";
	        //if(FW_DEBUG)
	        {
	        	TRACE($exception);
	        }	
	        echo "</div>\n";
	    }
		$this->end();
	}
	
	/**
	 * handle the error message and exception.
	 * @param string $error
	 * @param Exception $exception
	 */
	public function onError($error, Exception $exception=null, $nextlocation=null)
	{
        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
        <html xmlns=\"http://www.w3.org/1999/xhtml\">
        <head><meta http-equiv='Content-Type' content='text/html; charset=utf-8'></head><body>
        <div wrap style='margin:auto auto; overflow: display; padding:30px;text-align:
        center;vertical-align:middle;width:500px;height:200px; border:solid 2px;'>\n";
       	echo "<h3>$error</h3>\n";
        if(FW_DEBUG)
        {
        	//global $sql;
        	////echo $sql;
    		echo "<BR>------------------------<BR>\n<PRE>\n";
    		echo $exception->getMessage();
    		//echo "\n".iconv('gbk','utf-8',$exception->GetMessage());
    		echo "\n------------------------\n";
    		echo $exception->getTraceAsString();
    		echo "</PRE>\n<BR>------------------------<BR>\n";
        }
        else
        {
            echo "<div>".$exception->getMessage()."</div>\n";
        }
        echo "</div></body>\n";
		$this->end();
	}
	
}