<?php

class NativeView implements IView
{
	private $_setting;
	private $_template_dir;
	private $_cache_dir;
	private $_is_html_cache = false;
	private $_html_cache_lifetime = 30;//in seconds
	private $_debug_on = false;
	private $_assignedVals = array();
	private $_layout = null;
	
	public $controller = null;
	public $module = null;
	
	
	public function __construct()
	{
		
	}
	
	public function init($setting)
	{
		$this->_setting = $setting;
		$this->_template_dir = $this->_setting['template_dir'];
		//$this->_smarty -> compile_dir = $this->_setting['compile_dir'];
		//$this->_smarty -> config_dir = $this->_setting['config_dir'];
		$this->_cache_dir = $this->_setting['cache_dir'];
		$this->_is_html_cache = $this->_setting['cache_html'];
		$this->_html_cache_lifetime = $this->_setting['cache_html_lifetime'];
		$this->_debug_on = $this->_setting['debug_on'];
	}
	
	public function clean()
	{
	    unset($this->_assignedVals);
	}
	
	public function assign($field, $value)
	{
		$this->_assignedVals[$field] = &$value;
	}
	
	public function assignByRef($field, &$value)
	{
		$this->_assignedVals[$field] = &$value;
	}
	/**
	 * (non-PHPdoc)
	 * @see IView::GetLayout()
	 */
	public function getLayout()
	{
		return $this->_layout;
	}
	/**
	 * (non-PHPdoc)
	 * @see IView::SetLayout()
	 */
	public function setLayout($layout)
	{
		//echo "selayout $layout;";
		$this->_layout = $layout;
		return $this;
	}
	
	private function getTemplateStr()
	{
		$tpls = '';
		if(is_array($this->_template_dir))
		{
			foreach($this->_template_dir as $tpl)
			{
				if($tpl)
				{
					$tpls = $tpls ? $tpls.PATH_SEPARATOR.$tpl : $tpl;
				}
			}
		}
		else
		{
			$tpls = $this->_template_dir;
		}
		return $tpls;
	}
	
	public function display($template, $params=null, $cache_id=null)
	{
		try 
		{
			$exist_inlcude_path = get_include_path();
			//echo $this->getTemplateStr() . PATH_SEPARATOR .$exist_inlcude_path;
			set_include_path($this->getTemplateStr() . PATH_SEPARATOR .$exist_inlcude_path);
			extract($this->_assignedVals, EXTR_OVERWRITE|EXTR_REFS);
			if($params!=null)
			{
				extract($params, EXTR_OVERWRITE|EXTR_REFS);
			}
			if($this->_layout)
			{
				$content = $this->GetOutput($template, $params);
				require ($this->_layout);
			}
			else
			{
				require ($template);
			}
			set_include_path($exist_inlcude_path);
		}
		catch (ErrorException $e)
		{
			TRACE($e);exit;
			throw new FwException("解析模板错误: ".$e->getMessage(), LOCATION_REFERER, 0, $e);
		}
	}
	
	public function displayPartial($template, $params=null, $cache_id=null)
	{
		try 
		{
			$exist_inlcude_path = get_include_path();
			//echo  $this->getTemplateStr() . PATH_SEPARATOR .$exist_inlcude_path;
			set_include_path($this->getTemplateStr() . PATH_SEPARATOR .$exist_inlcude_path);
			extract($this->_assignedVals, EXTR_OVERWRITE|EXTR_REFS);
			if($params!=null)
			{
				extract($params, EXTR_OVERWRITE|EXTR_REFS);
			}
			require ($template);
			
			set_include_path($exist_inlcude_path);
		}
		catch (ErrorException $e)
		{
			TRACE($e);exit;
			throw new FwException("解析模板错误: ".$e->getMessage(), LOCATION_REFERER, 0, $e);
		}
	}
	
	public function getOutput($template, $params=null, $cache_id=null)
	{
		try 
		{
			ob_start();
			$exist_inlcude_path = get_include_path();
			set_include_path($this->getTemplateStr() . PATH_SEPARATOR .$exist_inlcude_path);
			extract($this->_assignedVals, EXTR_OVERWRITE|EXTR_REFS);
			if($params!=null)
			{
				extract($params, EXTR_OVERWRITE);
			}
			$tpl_file = $template;
			require ($tpl_file);
			$output = ob_get_contents();
			ob_end_clean();
			set_include_path($exist_inlcude_path);
			return $output;
		}
		catch (ErrorException $e)
		{
			echo ob_get_flush();
			TRACE($e);exit;
			throw new FwException("解析模板错误: ".$e->getMessage(), LOCATION_REFERER, 0, $e);
		}
	}
	
	public function widget($classname, $params=null)
	{
		$this->BeginWidget($classname, $params);
		return $this->EndWidget();
	}
	
	/**
	 * The current rendering widget.
	 * @var FwWidget
	 */
	private $currWidget = null;
	
	public function BeginWidget($classname, $params=null)
	{
		$this->currWidget = new $classname;
		if($params && is_array($params))
		{
			foreach($params as $field=>$param)
			{
				$this->currWidget->$field = $param;
			}
		}
		$this->currWidget->Begin();
		return $this->currWidget;
	}
	
	public function EndWidget()
	{
		$this->currWidget->End();
		$obj = $this->currWidget;
		$this->currWidget = null;
		return $obj;
	}
	
	protected $currLayout = null;
	protected $currLayoutParams = null;
	public function BeginLayout($layout, $params=null)
	{
		$this->currLayout = $layout;
		$this->currLayoutParams = $params;
		ob_start();
	}
	
	public function EndLayout()
	{
		$content = ob_get_contents();
		ob_end_clean();
		if(!$this->currLayoutParams['content'])
		{
			$this->currLayoutParams['content'] = $content;
		}
		$this->DisplayPartial($this->currLayout, $this->currLayoutParams);
		$this->currLayout = $this->currLayoutParams = null;
	}
}