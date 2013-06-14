<?php
/**
 * The base controller.
 * all controller should inherite from this class.
 * @author ardar
 * @since 2.0
 */
abstract class FwController
{
	
	/**
	 * The current requesting module id.
	 * @var unknown
	 */
	protected $_moduleId;
	
	/**
	 * The parent module instance of the cotroller.
	 */
	protected $_module;
	
	/**
	 * The current request controller.
	 * @var string
	 */
	protected $_controllerId;
	
	/**
	 * The current request action.
	 * @var string
	 */
	protected $_actionId;
	
	
	protected $_page;
	protected $_offset = 0;
	protected $_perpage = 8;
	
	public $showTitleBar;
	public $breadcrumbs;
	public $pageTitle;
	public $pageIcon;
	public $parentTitle;
	public $subTitle;
	public $pageMenu;
	public $pageButtons;
	public $returnUrl;
	public $tabBar;
	public $navBar;
	
	public function __construct()
	{
	}
	
	/**
	 * Initialize the controller
	 * @param IModule $module
	 * @param IRequest $request
	 * @param IView $view
	 */
	public function init(IModule $module)
	{
		$this->_module = $module;
		$this->_actionId = $this->getRequest()->getInput("action",'string');
		if (!$this->_actionId)
		{
			$this->_actionId = "index";
		}
		$className = get_class($this);
		$moduleId = $module->getId();
		$this->_moduleId = $moduleId;
		$this->parentTitle = $module->getName();
		$this->getView()->Assign('controller',$this);
		
		$moduleprefix = $this->_moduleId ? $this->_moduleId.'/':'';
		$this->_controllerId = $moduleprefix . substr($className, 0, strrpos($className, "Controller"));
		
		
		// Pager
		$this->_page = $this->getRequest()->GetInput('page','int');
		$this->_page = $this->_page<=0 ? 1 : $this->_page;
		$this->_offset = $this->_perpage * ($this->_page - 1);

		$this->getView()->assign('module', $this->getModule());
		$this->getView()->assign('controller', $this);
		$this->getView()->assign('actionId', $this->_controllerId);
		$this->getView()->assign('actionId', $this->getActionId());
		$this->getView()->setLayout( Fw::app()->getSetting()->defaultLayout );
	}
	
	/**
	 * Get the IApplication instance.
	 * @return IApplication
	 */
	public final function getApp()
	{
		return Fw::app();
	}
	
	/**
	 * Gets the IRequest object;
	 * @return IRequest
	 */
	public final function getRequest()
	{
		return $this->getApp()->getRequest();
	}
	
	/**
	 * Gets the IResponse object;
	 * @return IRequest
	 */
	public final function getResponse()
	{
		return $this->getApp()->getResponse();
	}
	
	/**
	 * Gets the IView object;
	 * @return IView
	 */
	public final function getView()
	{
		return $this->getApp()->getView();
	}
	
	/**
	 * Gets the current login Account instance;
	 * @return IUser
	 */
	public final function getUser()
	{
		return $this->getApp()->getUser();
	}
	
	public final function getModuleId()
	{
		return $this->_moduleId;
	}
	
	public final function getModule()
	{
		return $this->_module;
	}
	
	public final function getControllerId()
	{
		return $this->_controllerId;
	}
	
	public final function getActionId()
	{
		return $this->_actionId;
	}
	
	/**
	 * 获取当前页码
	 * @return number
	 */
	public final function getPage()
	{
		$this->_page = $this->GetRequest()->GetInput('page','int');
	    if ($this->_page<=0) 
		{
			$this->_page = 1;
		}
		return $this->_page;
	}
	
	/**
	 * 获取根据当前页码和每页记录数计算出的当前记录偏移
	 * @return number
	 */
	public final function getOffset()
	{
	    $this->GetPage();
	    return $this->_offset = $this->_perpage * ($this->_page - 1);
	}
	
	/**
	 * 执行请求路由
	 * @throws FwException
	 */
	public function dispatch()
	{
//         VisitLogSession::LogCurrSession($this->GetAccount()->GetId(), 
//             $this->GetControllerId(), $this->GetActionId());
        
		$actionname = $this->_actionId."Action";
		if (method_exists($this, $actionname)) 
		{
            call_user_func(array($this, $actionname));
        }
        else
        {
        	throw new FwException("无效的操作： ".$this->_controllerId."::".$this->_actionId, LOCATION_BACK);
        }
        
        
	}
	
	/**
	 * 执行清理
	 */
	public function clean()
	{
		//Nothing todo.
	}
	
	/**
	 * 执行跳转并终止本页的执行
	 * @param unknown $url
	 */
	public function redirect($url)
	{
		ob_clean();
		header("Location:$url");
		$this->GetApp()->end();
	}
	
	/**
	 * 根据不同的当前用途（usage）返回不同的默认模板页
	 * @return string
	 */
	protected function getTemplate($template='defaultPage.php')
	{
		if($this->GetInput('usage')=='dialog')
		{
			$this->GetView()->SetLayout('layouts/emptyframe.php');
			
			return 'naked_'.$template;
		}
		return $template;
	}
	
	/**
	 * 显示弹出对话框
	 * @param string $message
	 * @param string $nextlocation LOCATION_BACK
	 * @param bool $isForceDown
	 */
	public function alert($message, $nextlocation, $isForceDown, $exData=null)
	{
		if(WEBSERVICE==1)
		{
			echo $message;
			if($isForceDown)
			{
				$this->GetApp()->AppExit();
			}
			return;
		}
		if($nextlocation==LOCATION_REFERER)
			$nextlocation = $this->GetApp()->GetEnv('referer');
		$message = OpenDev::ehtmlspecialchars($message);
		echo "<html><head><meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
			<script language=\"JavaScript\">";
		if($message)
			echo "	alert(\"$message\");";
		if($nextlocation==LOCATION_THIS)
		{
			echo "\n</script>";
		}
		elseif (!$nextlocation || $nextlocation==LOCATION_BACK )
		{
			echo "this.history.go(-1);
				</script>";
		}
		else 
		{
			$redirect_top = false;
			switch ($nextlocation)
			{
			case LOCATION_HOME:	$nextlocation = urlhelper("home"); $redirect_top=true; break;
			case LOCATION_UCP:	$nextlocation = urlhelper("ucp");$redirect_top=true;  break;
			case LOCATION_LOGIN: $nextlocation = urlhelper("login"); $redirect_top=true; break;
			default: break;
			}
			if ($redirect_top)
			{
				echo "window.location.href=\"$nextlocation\";
				</script>";
			}
			else 
			{
				echo "window.location.href=\"$nextlocation\";
				</script>";
			}
		}
		if ($isForceDown) $this->GetApp()->AppExit();
	}
	
	/**
	 * 显示消息提示框，非modal形式
	 * @param string $message
	 * @param string $nextlocation LOCATION_BACK
	 * @param bool $isForceDown
	 */
	public function message($message, $nextlocation=LOCATION_BACK, $isForceDown=ForceDown, $exData=null)
	{
		$btn_onclick = "";
		$url_location = $nextlocation; 
		if($nextlocation==LOCATION_THIS)
		{
				$url_location = ''; 
		}
		elseif (!$nextlocation || $nextlocation==LOCATION_BACK )
		{
			$btn_onclick = "window.history.go(-1);";
		}
		else 
		{
			switch ($nextlocation)
			{
			case LOCATION_HOME:	$url_location = urlhelper("home"); break;
			case LOCATION_UCP:	$url_location = urlhelper("ucp"); break;
			case LOCATION_LOGIN: $url_location = urlhelper("login"); break;
			case LOCATION_REFERER: $url_location = Fw::GetApp()->GetEnv('referer');
			default: break;
			}
			$btn_onclick = "window.location.href=\"$url_location\";";
		}

		if($message=='')
		{
			header("Location:$url_location");
			return;
		}
		
		$this -> getView() -> assign('url_location', $url_location);
		$this -> getView() -> assign('script_onclick', $btn_onclick);
		$this -> getView() -> assign('title', '提示');
		$this -> getView() -> assign('message', $message);
		$this -> getView() -> assign('exMessage', '');
		$this -> GetView() -> setLayout('layouts/emptyframe.php');
		$this -> GetView() -> display("alert.php");
		
		if($isForceDown)
		{
			Fw::app()->end();
		}
	}
	
	public function checkPrivilege($privilegeItem="", $isTerminate=true)
	{
		if (!$this->getUser() 
			|| $this->getUser()->GetId()==User::GUEST_USERID) 
		{
			if ($isTerminate)
			{
				throw new AuthException('请先登录 ', LOCATION_LOGIN);
			}
			else
			{
				return false;
			}
		}
		$hasPermission = $this->getUser()->hasPrivilege($privilegeItem, $isTerminate);
		if(!$hasPermission)
		{
			if ($isTerminate)
			{
				throw new AuthException('没有权限进行操作 ', LOCATION_LOGIN);
			}
			else
			{
				return false;
			}
		}
		return true;
	}
	
	public function getSortOption($defsortfield='', $defsorttype='asc')
	{
		$sortfield = $this->GetRequest()->GetInputParam('sortfield','string');
		$sorttype = $this->GetRequest()->GetInputParam('sorttype','string');
		$sortfield = $sortfield ? $sortfield : $defsortfield;
		$sort_option = null;
		if ($sortfield)
		{
			$sorttype = $sorttype ? $sorttype : $defsorttype;
			$sorttype = $sorttype ? $sorttype : 'asc';
			$sort_option[] = " $sortfield $sorttype";
		}
		return $sort_option;
	}
	
	public function getFilterOption($option_set)
	{
		if(!$option_set)
		{
			return array();
		}
		foreach ($option_set as $field => $field_op)
		{
			$table = null;
			$value = null;
			$op = null;
			if (strpos($field, '.')>0)
			{
				$arr = explode('.', $field);
				$table = $arr[0];
				$field = $arr[1];
			}
			$sval = $this->getRequest()->GetInput($field);
			if(strlen($sval)>0)
			{
				switch ($field_op)
				{
					case '==': 
						$value =  $this->GetRequest()->GetInputParam($field, DT_NUMBER); 
						$op = '='; 
						break;
					case '=': 
					case 'like': $value = $sval; $op = $field_op; break;
					case '%like': $value = "%$sval"; $op = 'like'; break;
					case 'like%': $value = "$sval%"; $op = 'like'; break;
					case '%like%': $value = "%$sval%"; $op = 'like'; break;
					default: throw new FwException("解析参数错误: 不合法的Filter参数");
						break;
				}
				$filter_options[] = array('table'=>$table, 'field'=>$field, 'value'=>$value, 'op'=>$op);
			}
		}
		return $filter_options;
	}
	
	/**
	 * 获取请求参数
	 * @param string $field 参数名
	 * @param string $rules 参数设置 如： int|require
	 * @param string $defaultOrRequireMsg 参数的默认值或参数不能为空时的数组
	 */
	public function getInput($field, $rules=null, $defaultOrRequireMsg=null)
	{
		return $this->getRequest()->getInput($field, $rules, $defaultOrRequireMsg);
	}
	
	/**
	 * 获取当前Url替换掉某参数的Url
	 * @param string $replaceItems
	 * @return string
	 */
	public function getReplacedUrl($replaceItems)
	{
		$url = $_SERVER['REQUEST_URI'];
		foreach($replaceItems as $findkey=>$newvalue)
		{
			$url = OpenDev::ReplaceQueryString($url, $findkey, $newvalue);
		}
		return $url;
	}
	
	/**
	 * 根据controller，action以及参数构造请求Url
	 * @param string $controller
	 * @param string $action
	 * @param string $params
	 * @param string $usage
	 * @return string
	 */
	public function getUrl($controller, $action='', $params=null,$usage='')
	{
		if(!$controller)
		{
			$controller = $this->GetControllerId();
		}
		$parts = explode('/', $controller);
		if(count($parts)==1)
		{
			$module = $this->GetModuleId();
			$controller = $parts[0];
		}
		else
		{
			$module = $parts[0];
			$controller = $parts[1];
		}
		$moduleController = "$module/$controller";
		//sets the usage
		if($this->GetInput('usage') && !$usage)
		{
			$usage = $this->GetInput('usage');
		}
		if($usage && !$params['usage'])
		{
			$params['usage'] = $usage;
		}
		$paramParts = '';
		if($params)
		{
			if(is_array($params))
			{
				foreach($params as $key=>$val)
				{
					$paramParts.="&$key=$val";
				}
			}
			else
			{
				$paramParts = $params;
			}
		}
		return urlhelper('action', $moduleController, $action, $paramParts);
	}
	
	/**
	 * 获取全部
	 * @return multitype:multitype:NULL number multitype:multitype:number unknown
	 */
	public function getAppMenu()
	{
		
	}
	
	public function bindBreadcrumbs($label, $url='')
	{
		if(!$this->breadcrumbs)
		{
			$this->breadcrumbs = new UIBreadcrumbs();
		}
		$this->breadcrumbs->links[$label] = $url;
		return $this;
	}
	
	public function getReferer($default='')
	{
		$referer = $this->GetInput('referer','string');
		if(!$referer)
		{
			$referer = $this->GetApp()->getRuntime()->referer;
		}
		return $referer ? $referer : $default;
	}
}