<?php
class UIDropdownButton extends UIWidget
{
	public $viewFile = 'uiDropdownButton.php';
	public $class = '';
	public $dropdowns = array();
	public $properties = array();
	
	public function __construct($id, $label,$properties=null)
	{
		parent::__construct($id, $label);
		
		$this->properties = $properties;
	}

	/**
	 * 
	 */
	public function BindButton($button)
	{
		$this->dropdowns[] = $button;
	}
	
	public function BindButtonEx($label, $link)
	{
		$this->dropdowns[] = array('link'=>$link, 'label'=>$label);
	}

	public function Begin()
	{
		ob_start();
	}
	
	public function End()
	{
		$content=ob_get_contents();
		ob_end_clean();
		
		$this->render($this->viewFile,array('content'=>$content));
	}
	
	
	public function GetBody()
	{
		ob_start();
		$this->Begin();
		$this->End();
		$content=ob_get_contents();
		ob_end_clean();
		return $content;
	}
}