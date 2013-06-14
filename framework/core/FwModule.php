<?php
/**
 * 模块定义基类型
 * @author ardar
 * @since 2.0
 */
abstract class FwModule implements IModule
{
	/**
	 * 系统设置参数集合
	 * @var array
	 */
	protected $_sysoptiondata;
	
	/**
	 * 用户设置参数集合
	 * @var array
	 */
	protected $_accoptiondata;
	
	/**
	 * 当前控制器对象
	 * @var FwController
	 */
	protected $_controller = null;
	
	/**
	 * (non-PHPdoc)
	 * @see IModule::init()
	 */
	public function init($options)
	{
		$depends = $this->getDependModules();
		if($depends)
		{
			foreach($depends as $dependname)
			{
				$dependMod = Fw::app()->getModule($dependname);
				if($dependMod==null)
				{
					throw new FwException(
							"invalid depending mod($dependname) of "
							.Fw::app()->getModuleId());
				}
	
				$basedir = APP_DIR."modules/".$dependname."/";
				Fw::app()->import($basedir."models/");
				Fw::app()->import($basedir."exts/");
			}
		}
	
		$baseDir = APP_DIR."modules/".$this->getId()."/";

		Fw::app()->import($baseDir."controllers/");
		Fw::app()->import($baseDir."models/");
		Fw::app()->import($baseDir."controllers/");
		Fw::app()->import($baseDir."exts/");
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IModule::dispatch()
	 */
	public function dispatch($route)
	{
		$ctrlname = $route[FIELD_CONTROLLER];
		$ctrlclassname = $ctrlname."Controller";
		
		$this->_controller = new $ctrlclassname();
		if (!$this->_controller || !($this->_controller instanceof FwController))
		{
			throw new FwException("The ".$ctrlname." is not a Controller", LOCATION_HOME);
		}
		
		$this->_controller->init($this);
			
		$this->_controller->dispatch($route[FIELD_ACTION]);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IModule::getController()
	 */
	public function getController()
	{
		return $this->_controller;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IModule::getDependModules()
	 */
	public function getDependModules()
	{
		return array();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IModule::getSysOptionFields()
	 */
	public function getSysOptionFields()
	{
		//field, fieldname, datatype, editctrl, options, defaultvalue, isrequired
		return null;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IModule::getAccOptionFields()
	 */
	public function getAccOptionFields()
	{
    	//field, fieldname, datatype, editctrl, options, defaultvalue, isrequired
		return null;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IModule::loadClass()
	 */
	public function loadClass($classname)
	{
		$models = $this->getModels();
		foreach($models as $model)
		{
			if($model==$classname)
			{
				$path = APP_DIR."modules/".$this->GetId()."/models/".$classname.".php";
				require_once($path);
				return true;
			}
		}
		$depends = $this->getDependModules();
		if($depends)
		{
			foreach($depends as $dependname)
			{
				$dependMod = Fw::GetApp()->getModule($dependname);
				$models = $dependMod->GetModels();
				//print_r($models);
				if($dependMod && in_array($classname, $models))
				{
					$path = APP_DIR."modules/".$dependname."/models/".$classname.".php";
					if(file_exists($path))
					{
						require_once($path);
					}
					break;
				}
			}
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IModule::getSysOptions()
	 */
	public function getSysOptions()
	{
		$this->_sysoptiondata = $this->GetSysOptionFields();
		if($this->_sysoptiondata!=null && count($this->_sysoptiondata)>0)
		{
			$valrss = SysOption::GetSysOptionData($this->GetId());
			foreach($this->_sysoptiondata as $rs)
			{
				if(isset($valrss[$rs['field']]))
				{
					$fieldvalue = $this->_parseSysOptionOutput($rs['field'], $valrss[$rs['field']]['fieldvalue']);
					$this->_sysoptiondata[$rs['field']]['fieldvalue'] = $fieldvalue; 
					$this->_sysoptiondata[$rs['field']]['isdefault'] = 0; 
					$this->_sysoptiondata[$rs['field']]['optionid'] = $valrss[$rs['field']]['optionid'];
				}
				else
				{
					$fieldvalue = $this->_parseSysOptionOutput($rs['field'], $rs['defaultvalue']);
					$this->_sysoptiondata[$rs['field']]['fieldvalue'] = $fieldvalue; 
					$this->_sysoptiondata[$rs['field']]['isdefault'] = 1; 
				}
			}
		}
		//TRACE($this->_sysoptiondata);
		return $this->_sysoptiondata;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IModule::getSysOption()
	 */
	public function getSysOption($field)
	{
		if(!$this->_sysoptiondata)
		{
			$this->GetSysOptions();
		}
		return $this->_sysoptiondata[$field]['fieldvalue'];
	}

	/**
	 * (non-PHPdoc)
	 * @see IModule::setSysOption()
	 */
	public function setSysOption($field, $fieldvalue)
	{
		if(!$this->_sysoptiondata)
		{
			$this->GetSysOptions();
		}
		$fieldvalue = $this->_parseSysOptionInput($field, $fieldvalue);
		//TRACE($this->_sysoptiondata);
		$option = new SysOption($this->_sysoptiondata[$field]);
		$option->field = $field;
		$option->fieldvalue = $fieldvalue;
		$option->scope = SysOption::ScopeGlobal;
		$option->module = $this->GetId();
		$option->SaveData();
		$this->_sysoptiondata[$field] = $option->GetData();
	}
	
	protected function _parseSysOptionInput($field, $value)
	{
		return $value;
	}
	
	protected function _parseSysOptionOutput($field, $value)
	{
		return $value;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IModule::getAccOptions()
	 */
	public function getAccOptions($userid)
	{
		$this->_accoptiondata[$userid] = $this->GetAccOptionFields();
		if($this->_accoptiondata[$userid]!=null && count($this->_accoptiondata[$userid])>0)
		{
			$valrss = SysOption::GetAccOptionData(
					$userid, $this->GetId());
			foreach($this->_accoptiondata[$userid] as $rs)
			{
				if(isset($valrss[$rs['field']]))
				{
					$fieldvalue = $this->_parseAccOptionOutput($rs['field'], $valrss[$rs['field']]['fieldvalue']);
					$this->_accoptiondata[$userid][$rs['field']]['fieldvalue'] = $fieldvalue; 
					$this->_accoptiondata[$userid][$rs['field']]['isdefault'] = 0; 
					$this->_accoptiondata[$userid][$rs['field']]['optionid'] = $valrss[$rs['field']]['optionid'];
				}
				else
				{
					$fieldvalue = $this->_parseAccOptionOutput($rs['field'], $rs['defaultvalue']);
					$this->_accoptiondata[$userid][$rs['field']]['fieldvalue'] = $fieldvalue; 
					$this->_accoptiondata[$userid][$rs['field']]['isdefault'] = 1; 
				}
			}
		}
		return $this->_accoptiondata[$userid];
	}
	/**
	 * (non-PHPdoc)
	 * @see IModule::getAccOption()
	 */
	public function getAccOption($userid, $field)
	{
		if(!$this->_accoptiondata[$userid])
		{
			$this->getAccOptions($userid);
		}
		return $this->_accoptiondata[$userid][$field]['fieldvalue'];
	}
	/**
	 * (non-PHPdoc)
	 * @see IModule::setAccOption()
	 */
	public function setAccOption($userid, $field, $fieldvalue)
	{
		if(!$this->_accoptiondata[$userid])
		{
			$this->getAccOptions($userid);
		}
		$fieldvalue = $this->_parseAccOptionInput($field, $fieldvalue);
		$option = new SysOption($this->_accoptiondata[$userid][$field]);
		$option->field = $field;
		$option->fieldvalue = $fieldvalue;
		$option->scope = SysOption::ScopeAccount;
		$option->module = $this->GetId();
		$option->scopeid = $userid;
		//TRACE($option);
		$option->SaveData();
		$this->_accoptiondata[$userid][$field] = $option->GetData();
	}
	
	protected function _parseAccOptionInput($field, $value)
	{
		return $value;
	}
	
	protected function _parseAccOptionOutput($field, $value)
	{
		return $value;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see IModule::search()
	 */
	public function search($keyword, $parameters=null)
	{
		
	}
}