<?php
class UINavBar extends UIWidget
{
	public $viewFile = "uiNavBar.php";
	
	public $label;
	
	public $items = array();
	
	public $_quickSearcher;
	
	public $_searcher;
	
	public function UINavBar($id, $label, $items=null)
	{
		parent::__construct($id, $label, null);
		if($items)
		{
			if(is_array($items))
			{
				$this->items = $items;
			}
			else
			{
				$this->items[] = $items;
			}
		}
	}
	
	public function BindItem($item)
	{
		$this->items[]= $item;
	}
	
	public function BindItemEx($id, $label, $type, $url, $properties=null, $childs=null)
	{
		$this->items[] = array('id'=>$id,
				'label'=>$label,
				'type'=>$type,
				'url'=>$url,
				'childs'=>$childs,
				'properties'=>$properties);
	}

	public function BindQuickSearch($field='search', $route=null, $hint='输入查询关键字',$buttonLabel='查找')
	{
		$controller = $route['controller'] ? $route['controller'] 
			: Fw::GetApp()->GetRequest()->GetInput(FIELD_CONTROLLER);
		$action = $route['action'] ? $route['action'] 
			: Fw::GetApp()->GetRequest()->GetInput(FIELD_ACTION);
		$searchValue = Fw::GetApp()->GetRequest()->GetInput($field);
		$this->_quickSearcher = "
		<input class=\"input-medium\" type=\"text\" placeholder=\"$hint\" name='$field' value='$searchValue'>
		<button class=\"btn btn-primay\" type=\"submit\">$buttonLabel</button>
		<input type=\"hidden\" name=\"controller\" value=\"{$controller}\">
		<input type=\"hidden\" name=\"action\" value=\"{$action}\">
		";
		return $this;
	}
	
	public function BindAdvSearcher($field, $label, $defValue='',
			$edittype='text', $rules=null, $properties=null)
	{
		$formElement = new UIFormElement($field, $label, $edittype, $defValue, $rules, $properties);
		$this->_searcher[] = $formElement;
		return $this;
	}
	

// 	public function Begin()
// 	{
// 		ob_start();
// 	}
	
// 	public function End()
// 	{
// 		$content = ob_get_contents();
// 		ob_end_clean();
// 		$this->jsonData = $this->toJsData($this->data);
// 		$view = Fw::GetApp()->GetView('native');
// 		//TRACE($this->childs);
// 		$this->render($this->viewFile, array('content'=>$content));
// 		return $this;
// 	}
}