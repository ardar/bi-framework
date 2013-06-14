<?php
class UIPanel extends UIWidget
{
	protected $viewFile = 'uiPanel.php';
	
	public $label = '';
	public $icon = '';
	public $tabBar = '';
	public $properties = null;
	
	public function __construct($id=null, $label='', $properties=null)
	{
		parent::__construct($id, $label);
		$this->properties = $properties;
	}
	
	public function BindTabBar($tabBar)
	{
		$this->tabBar.=$tabBar;
	}
	
	public function GetHtmlOptions()
	{
		return Fw::Html()->FetchHtmlOptions($this->properties);
	}
}