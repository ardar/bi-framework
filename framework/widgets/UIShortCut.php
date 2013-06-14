<?php
class UIShortCut extends UIWidget
{
	protected $viewFile = "uiShortCut.php";
	public $label;
	public $labelClass;
	public $badge;
	public $badgeClass;
	public $icon='button';
	public $type = 'button';
	public $onclick;

	public function Begin()
	{
	}
	
	public function End()
	{
		if($this->type=='link')
		{
			$this->onclick = "window.location.href='{$this->onclick}';";
			$this->type='button';
		}
		$this->render($this->viewFile);
	}
	
}