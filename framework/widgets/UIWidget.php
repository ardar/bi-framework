<?php
abstract class UIWidget implements IWidget
{
	private static $s_fwWidgetId = 0;
	private $type = null;
	
	protected $viewFile = null;

	public $uniqueId = null;
	
	public function getType($typename=__CLASS__)
	{
		$this->type = $typename;
	}

	/**
	 * 生成元素唯一Id
	 * @return number
	 */
	protected static function genUniqueId()
	{
		return ++self::$s_fwWidgetId;
	}
	
	public function __construct($id=null, $label=null, $parent=null)
	{
		//parent::__construct($id, $label, $parent);
		$this->uniqueId = static::genUniqueId();
	}
	
	public function getId()
	{
		return $this->GetType().'_'.$this->uniqueId;
	}
	
	public function getValue()
	{
 		return null;
	}
	
	public function getBody()
	{
 		ob_start();
 		$this->begin();
 		$this->end();
 		$body = ob_get_contents();
 		ob_end_clean();
 		return $body;
	}
	
	/**
	 * Begin tag of the widget. 
	 * Overring this method should also override the End method.
	 */
	public function begin()
	{
		//echo("Begin ".$this->GetId()."\n");
		ob_start();
	}
	
	/**
	 * Renders the body content of the widget 
	 * between the Begin and End tag.
	 */
	public function renderContent()
	{
		// Nothing todo
	}
	
	/**
	 * End tag of the widget.
	 * Overring this method should also override the End method.
	 */
	public function end()
	{
		$this->renderContent();
		
		$content = ob_get_contents();
		ob_end_clean();
		if($this->viewFile)
		{
			$this->render($this->viewFile,array('content'=>$content));
		}
		else
		{
			echo $content;
		}
		//echo("End} ".$this->GetId()."\n");
		//trace($this);
	}
	
	protected function render($template, $params=null)
	{
		if(strpos($template, '/')!==0)
		{
			$template = "widgets/{$template}";
		}
		//echo $template;
		$view = Fw::app()->getView();
		if(!($params['widget']))
		{
			$params['widget'] = $this;
		}
		$view->display($template, $params);
	}
	
	public function __toString()
	{
		try {
			return $this->getBody();
		} catch (Exception $e) {
			trace($e);
		}
	}
}