<?php
class UITabs extends UIWidget
{
	protected $viewFile = 'uiTabs.php';
	public $placement = 'above';
	public $activePage = null;
	public $pages = array();
	public $properties = array();

	/**
	 * 
	 * @param unknown_type $pageId
	 * @param unknown_type $pageLabel
	 * @param unknown_type $pageContent
	 * @param array $options with param icon, class, others
	 */
	public function bindPage($pageId, $pageLabel, $pageContent, $pageType='html', $properties=null)
	{
		if($pageType=='link')
		{
			$link = $pageContent;
			$pageContent = '';
		}
		$this->pages[$pageId] = array('label'=>$pageLabel, 'type'=>$pageType, 'link'=>$link,
				'content'=>$pageContent, 'properties'=>$properties);

	}
	

	public function Begin()
	{
		ob_start();
	}
	
	public function End()
	{
		$content=ob_get_contents();
		ob_end_clean();
		
		if($this->activePage==null)
		{
			foreach($this->pages as $id=>$page)
			{
				$this->activePage = $id;
				break;
			}
		}
		$this->render($this->viewFile,array('content'=>$content));
	}
	
}