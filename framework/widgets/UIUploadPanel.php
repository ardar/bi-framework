<?php
class UIUploadPanel extends UIWidget
{
	public $viewFile = "uiUploadPanel.php";
	public $handlerUrl = "";
	public $tabBar = '';
	public $properties = null;
	
	public function BindTabBar($tabBar)
	{
		$this->tabBar.=$tabBar;
	}
	
	public function __construct($id, $label, $handlerUrl, $properties=null)
	{
		parent::__construct($id, $label, null);
		$this->handlerUrl = $handlerUrl?$handlerUrl:'';
		if($properties['config']=='simple')
		{
			$this->viewFile = "uiUploadPanel_Simple.php";
			$properties['config'] = null;
		}
		$this->properties = $properties;
	}
	
	public function Begin()
	{
	}
	
	public function End()
	{
		//Fw::Html()->Header('cssfile', '3rdparty/fileupload/jquery.fileupload.css');
		//Fw::Html()->Header('jsfile', 'js/jquery.ui.js');
		//Fw::Html()->Header('jsfile', '3rdparty/fileupload/jquery.fileupload.temp.js');
		//Fw::Html()->Header('jsfile', '3rdparty/fileupload/jquery.fileupload.js');
		$this->render($this->viewFile);
	}
}